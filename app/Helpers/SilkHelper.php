<?php

namespace App\Helpers;

use App\Enums\SilkTypeIsroEnum;
use Illuminate\Support\Facades\Auth;
use SilkPanel\SilkroadModels\Models\Account\SkSilk;
use SilkPanel\SilkroadModels\Models\Account\SkSilkBuyList;
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

        SkSilkBuyList::create([
            'UserJID' => $jid,
            'Silk_Type' => SkSilkBuyList::SILK_TYPE_WEB,
            'Silk_Reason' => SkSilkBuyList::SILK_REASON_WEB,
            'Silk_Offset' => $silkOwn,
            'Silk_Remain' => $amount >= 0 ? $silkOwn + $amount : $silkOwn - abs($amount),
            'ID' => $jid,
            'BuyQuantity' => $amount,
            'OrderNumber' => 0,
            'AuthDate' => $now->format('Y-m-d H:i:s'),
            'SubJID' => Auth::id(),
            'SlipPaper' => 'Web Admin Adjustment',
            'IP' => $ip,
            'RegDate' => $now->format('Y-m-d H:i:s')
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
     */
    private function addSilkIsro(int $jid, int $amount, string $type, ?string $ip = null): void
    {
        $isroSilkType = match ($type) {
            '1' => SilkTypeIsroEnum::SILK_TYPE_NORMAL->value,
            '3' => SilkTypeIsroEnum::SILK_TYPE_PREMIUM->value,
            default => SilkTypeIsroEnum::SILK_TYPE_NORMAL->value,
        };

        AphChangedSilk::create([
            'JID' => $jid,
            'PTInvoiceID' => null,
            'RemainedSilk' => abs($amount),
            'ChangedSilk' => $amount < 0 ? $amount : 0,
            'SilkType' => $isroSilkType,
            'SellingTypeID' => 2,
            'ChangeDate' => now(),
            'AvailableDate' => now()->addYears(1),
            'AvailableStatus' => $amount < 0 ? 'N' : 'Y',
        ]);
    }
}
