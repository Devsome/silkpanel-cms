<?php

namespace App\Helpers;

use App\Enums\SilkTypeIsroEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SilkPanel\SilkroadModels\Enums\SilkroadSilksEnum;
use SilkPanel\SilkroadModels\Models\Account\SkSilk;
use SilkPanel\SilkroadModels\Models\Account\SkSilkBuyList;
use SilkPanel\SilkroadModels\Models\Account\SkSilkChangeByWeb;
use SilkPanel\SilkroadModels\Models\Portal\AphChangedSilk;

class SilkHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Adding silk to a user and log the transaction in the SkSilkBuyList table.
     *
     * @param int $jid
     * @param int $amount
     * @param string $type
     * @param string|null $ip
     * @return void
     */
    public static function addSilk(int $jid, int $amount, string $type, ?string $ip = null): void
    {
        $helper = new self();
        $ip = Str::limit($ip, 16, '');

        match (config('silkpanel.version')) {
            'isro' => $helper->addSilkIsro($jid, $amount, $type, $ip),
            default => $helper->addSilkVsro($jid, $amount, $type, $ip),
        };
    }

    /**
     * Adding silk for VSRO (SkSilkBuyList)
     */
    private function addSilkVsro(int $jid, int $amount, string $type, ?string $ip = null): void
    {
        $now = now();
        $silkOwn = SkSilk::where('JID', $jid)->pluck($type)->first();
        $orderNumber = Str::random(30);

        $silkType = match ($type) {
            'silk_own' => SilkroadSilksEnum::OWN,
            'silk_gift' => SilkroadSilksEnum::GIFT,
            'silk_point' => SilkroadSilksEnum::POINT,
        };

        SkSilkBuyList::create([
            'UserJID' => $jid,
            'Silk_Type' => $silkType, // 1 = adding
            'Silk_Reason' => SkSilkBuyList::SILK_REASON_WEB,
            'Silk_Offset' => $silkOwn,
            'Silk_Remain' => $amount >= 0 ? $silkOwn + $amount : $silkOwn - abs($amount),
            'ID' => $jid,
            'BuyQuantity' => $amount,
            'OrderNumber' => $orderNumber,
            'AuthDate' => $now->format('Y-m-d H:i:s'),
            'SubJID' => Auth::id(),
            'SlipPaper' => 'Web Admin',
            'IP' => $ip,
            'RegDate' => $now->format('Y-m-d H:i:s')
        ]);

        SkSilkChangeByWeb::create([
            'JID' => $jid,
            'silk_remain' => $amount >= 0 ? $silkOwn + $amount : $silkOwn - abs($amount),
            'silk_offset' => $amount,
            'silk_type' => $silkType,
            'reason' => 0,
        ]);

        if ($amount >= 0) {
            SkSilk::where('JID', $jid)
                ->increment($type, $amount);
        } else {
            SkSilk::where('JID', $jid)
                ->decrement($type, abs($amount));
        }
    }

    /**
     * Adding silk for ISRO (AphChangedSilk)
     *
     * For additions: insert a new 'Y' entry → B_GetJCash adds it to the balance.
     * For deductions: consume existing 'Y' entries oldest-first until the amount is
     * covered. Fully consumed rows are marked 'N'; partially consumed rows have their
     * RemainedSilk reduced in-place. A new 'N' row is inserted for the log.
     */
    private function addSilkIsro(int $jid, int $amount, string $type, ?string $ip = null): void
    {
        $isroSilkType = match ($type) {
            '1' => SilkTypeIsroEnum::SILK_TYPE_NORMAL->value,
            '3' => SilkTypeIsroEnum::SILK_TYPE_PREMIUM->value,
            default => SilkTypeIsroEnum::SILK_TYPE_NORMAL->value,
        };

        if ($amount >= 0) {
            AphChangedSilk::create([
                'JID'             => $jid,
                'PTInvoiceID'     => null,
                'RemainedSilk'    => $amount,
                'ChangedSilk'     => 0,
                'SilkType'        => $isroSilkType,
                'SellingTypeID'   => 2,
                'ChangeDate'      => now(),
                'AvailableDate'   => now()->addYears(1),
                'AvailableStatus' => 'Y',
            ]);
        } else {
            // Consume existing 'Y' entries oldest-first.
            $toDeduct = abs($amount);

            $availableEntries = AphChangedSilk::where('JID', $jid)
                ->where('SilkType', $isroSilkType)
                ->where('AvailableStatus', 'Y')
                ->where('RemainedSilk', '>', 0)
                ->orderBy('CSID', 'asc')
                ->get();

            $totalAvailable = $availableEntries->sum('RemainedSilk');

            if ($toDeduct > $totalAvailable) {
                return;
            }

            $remaining = $toDeduct;

            foreach ($availableEntries as $entry) {
                if ($remaining <= 0) {
                    break;
                }

                if ($entry->RemainedSilk <= $remaining) {
                    // Fully consume this entry
                    $remaining -= $entry->RemainedSilk;
                    $entry->update([
                        'RemainedSilk'    => 0,
                        'ChangedSilk'     => -$entry->RemainedSilk,
                        'AvailableStatus' => 'N',
                    ]);
                } else {
                    // Partially consume this entry
                    $newRemained = $entry->RemainedSilk - $remaining;
                    $entry->update([
                        'RemainedSilk' => $newRemained,
                        'ChangedSilk'  => -$remaining,
                    ]);

                    $remaining = 0;
                }
            }
        }

        SkSilkChangeByWeb::create([
            'JID'         => $jid,
            'silk_remain' => abs($amount),
            'silk_offset' => $amount < 0 ? $amount : 0,
            'silk_type'   => $isroSilkType,
            'reason'      => 0,
        ]);
    }
}
