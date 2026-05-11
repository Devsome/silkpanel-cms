<?php

namespace App\Livewire;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use App\Models\Setting;
use App\Models\WebmallCategory;
use App\Models\WebmallCategoryItem;
use App\Services\WebmallPurchaseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;
use SilkPanel\SilkroadModels\Models\Shard\RefObjItem;

class Webmall extends Component
{
    public ?int $selectedCharId   = null;
    public string $selectedCharName = '';

    public string $activeTab = '';

    // Confirmation modal state
    public bool $showConfirmModal  = false;
    public ?int $confirmItemId     = null;

    protected WebmallPurchaseService $purchaseService;

    public function boot(WebmallPurchaseService $purchaseService): void
    {
        $this->purchaseService = $purchaseService;
    }

    public function mount(): void
    {
        $user = Auth::user();
        $first = $this->getCharacters()->first();
        if ($first) {
            $this->selectedCharId   = $first->CharID;
            $this->selectedCharName = $first->CharName16;
        }
    }

    public function selectCharacter(int $charId, string $charName): void
    {
        $this->selectedCharId   = $charId;
        $this->selectedCharName = $charName;
        $this->cancelConfirm();
    }

    public function confirmPurchase(int $itemId): void
    {
        if ((bool) Setting::get('webmall_require_logout', false) && $this->selectedCharId) {
            $user = Auth::user();
            $char = $user->shardUsers()->wherePivot('CharID', $this->selectedCharId)->first();
            if ($char && $char->isOnline) {
                session()->flash('webmall_error', __('webmall.error.character_must_be_offline'));
                return;
            }
        }

        $this->confirmItemId    = $itemId;
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmItemId    = null;
    }

    public function executePurchase(): void
    {
        if (!$this->confirmItemId || !$this->selectedCharId) {
            $this->cancelConfirm();
            return;
        }

        $user = Auth::user();

        // Guard: character must be offline if the setting requires it
        if ((bool) Setting::get('webmall_require_logout', false)) {
            $char = $user->shardUsers()->wherePivot('CharID', $this->selectedCharId)->first();
            if ($char && $char->isOnline) {
                session()->flash('webmall_error', __('webmall.error.character_must_be_offline'));
                $this->cancelConfirm();
                return;
            }
        }

        $item = WebmallCategoryItem::find($this->confirmItemId);

        if (!$item || !$item->isAvailable()) {
            session()->flash('webmall_error', __('webmall.error.item_unavailable'));
            $this->cancelConfirm();
            return;
        }

        $result = $this->purchaseService->purchase(
            user: $user,
            characterId: $this->selectedCharId,
            characterName: $this->selectedCharName,
            item: $item,
        );

        $this->cancelConfirm();

        if ($result['success']) {
            session()->flash('webmall_success', __('webmall.success.purchase', [
                'item'        => $item->item_name_snapshot ?? $item->ref_item_id,
                'destination' => $result['destination'] ?? '—',
            ]));
        } else {
            $errorKey = match ($result['error']) {
                'insufficient_balance' => 'webmall.error.insufficient_balance',
                'item_delivery_failed' => 'webmall.error.item_delivery_failed',
                'item_unavailable'     => 'webmall.error.item_unavailable',
                default                => 'webmall.error.unexpected',
            };
            session()->flash('webmall_error', __($errorKey));
        }
    }

    private function getBalanceForPriceType(\App\Models\User $user, string $priceType): ?int
    {
        try {
            if ($priceType === 'gold') {
                $char = $user->shardUsers()->wherePivot('CharID', $this->selectedCharId)->first()
                    ?? $user->shardUsers()->where('CharID', '!=', 0)->first();
                return $char ? (int) $char->RemainGold : null;
            }

            if (config('silkpanel.version') === 'isro') {
                $jcash = $user->muuser?->JCash;
                if (!$jcash) return null;
                return match (SilkTypeIsroEnum::tryFrom((int) $priceType)) {
                    SilkTypeIsroEnum::SILK_TYPE_NORMAL  => (int) $jcash->Silk,
                    SilkTypeIsroEnum::SILK_TYPE_PREMIUM => (int) $jcash->PremiumSilk,
                    default                             => null,
                };
            }

            $skSilk = $user->getSkSilk;
            if (!$skSilk) return null;
            return match (SilkTypeEnum::tryFrom($priceType)) {
                SilkTypeEnum::SILK_OWN   => (int) $skSilk->silk_own,
                SilkTypeEnum::SILK_GIFT  => (int) $skSilk->silk_gift,
                SilkTypeEnum::SILK_POINT => (int) $skSilk->silk_point,
                default                  => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    private function getCharacters(): \Illuminate\Support\Collection
    {
        try {
            return Auth::user()
                ->shardUsers()
                ->get()
                ->filter(fn($c) => $c->CharID != 0 && $c->CharName16 !== 'dummy')
                ->sortByDesc('CurLevel')
                ->values();
        } catch (\Throwable) {
            return collect();
        }
    }

    public function render()
    {
        $user       = Auth::user();
        $characters = $this->getCharacters();
        $todayIso   = now()->dayOfWeekIso; // 1=Mon ... 7=Sun
        $categories = WebmallCategory::where('enabled', true)
            ->where(function ($q) {
                $q->whereNull('available_from')->orWhere('available_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('available_until')->orWhere('available_until', '>=', now());
            })
            ->with('activeItems')
            ->orderBy('order')
            ->get()
            ->filter(function (WebmallCategory $category) use ($todayIso) {
                $days = $category->schedule_days;
                if (empty($days)) {
                    return true;
                }
                // cast all stored values to int to match regardless of JSON type
                return in_array($todayIso, array_map('intval', $days), true);
            });

        // Single bulk query for all RefObjCommon data (avoids cross-connection eager loading issues)
        $allRefItemIds = $categories
            ->flatMap(fn($c) => $c->activeItems->pluck('ref_item_id'))
            ->unique()
            ->values()
            ->all();

        // Auto-select first tab if not set or no longer valid
        $slugs = $categories->pluck('slug')->all();
        if (empty($this->activeTab) || !in_array($this->activeTab, $slugs, true)) {
            $this->activeTab = $slugs[0] ?? '';
        }

        if ($this->confirmItemId) {
            $confirmBase = WebmallCategoryItem::find($this->confirmItemId);
            if ($confirmBase) {
                $allRefItemIds[] = $confirmBase->ref_item_id;
            }
        }

        $refObjs = RefObjCommon::select(['ID', 'CodeName128', 'TypeID2', 'TypeID3', 'AssocFileIcon128', 'ReqLevel1', 'CanTrade', 'Link'])
            ->whereIn('ID', array_unique(array_filter($allRefItemIds)))
            ->get()
            ->keyBy('ID');

        // Fetch ReqGender from _RefObjItem (linked via _RefObjCommon.Link → _RefObjItem.ID)
        $linkIds = $refObjs->pluck('Link')->filter()->unique()->values()->all();
        if (!empty($linkIds)) {
            $itemMap = RefObjItem::select(['ID', 'ReqGender', 'ItemClass'])
                ->whereIn('ID', $linkIds)
                ->get()
                ->keyBy('ID');
            $soxConfig = config('item.sox_type', []);
            $refObjs->each(function ($obj) use ($itemMap, $soxConfig) {
                $refItem = $itemMap->get((int) $obj->Link);
                $obj->ReqGender = $refItem?->ReqGender ?? null;
                $itemClass = (int) ($refItem?->ItemClass ?? 0);
                $soxType = 'Normal';
                foreach ($soxConfig as $minClass => $codeNames) {
                    if ($itemClass > $minClass) {
                        foreach ($codeNames as $key => $value) {
                            if (str_contains($obj->CodeName128, $key)) {
                                $soxType = $value;
                                break 2;
                            }
                        }
                    }
                }
                $obj->SoxType = $soxType;
            });
        }

        $confirmItem = $this->confirmItemId
            ? WebmallCategoryItem::find($this->confirmItemId)
            : null;

        $confirmBalance = $confirmItem
            ? $this->getBalanceForPriceType($user, $confirmItem->price_type)
            : null;

        $requireLogout = (bool) Setting::get('webmall_require_logout', false);
        $selectedCharOnline = false;
        if ($requireLogout && $this->selectedCharId) {
            try {
                $selectedChar = $user->shardUsers()->wherePivot('CharID', $this->selectedCharId)->first();
                $selectedCharOnline = $selectedChar?->isOnline ?? false;
            } catch (\Throwable) {
                $selectedCharOnline = false;
            }
        }

        return view('template::livewire.webmall', compact('categories', 'characters', 'confirmItem', 'confirmBalance', 'refObjs', 'requireLogout', 'selectedCharOnline'));
    }
}
