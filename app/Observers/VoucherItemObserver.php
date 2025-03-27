<?php

namespace App\Observers;

use App\Models\AccountingConstant;
use App\Models\VoucherItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class VoucherItemObserver
{
    /**
     * Handle the VoucherItem "created" event.
     */
    public function created(VoucherItem $voucherItem): void
    {
        //
        DB::transaction(function () use ($voucherItem) {
            $this->computeBalanceAfterTransaction($voucherItem);
        });
    }

    /**
     * Handle the VoucherItem "updated" event.
     */
    public function updated(VoucherItem $voucherItem): void
    {
        //
    }

    /**
     * Handle the VoucherItem "deleted" event.
     */
    public function deleted(VoucherItem $voucherItem): void
    {
        //
    }

    /**
     * Handle the VoucherItem "restored" event.
     */
    public function restored(VoucherItem $voucherItem): void
    {
        //
    }

    /**
     * Handle the VoucherItem "force deleted" event.
     */
    public function forceDeleted(VoucherItem $voucherItem): void
    {
        //
    }
    /*protected function updateAccountBalance(VoucherItem $item)
    {
        $account = AccountingConstant::find($item->accounting_constant_id);
        if ($account) {
            $account->bakiye += $item->borc_tutar;
            $account->bakiye -= $item->alacak_tutar;
            $account->save();
        }
    }*/

    protected function computeBalanceAfterTransaction(VoucherItem $item)
    { 
        $account=AccountingConstant::find($item->accounting_constant_id);
        if($account)
        {
            $guncelBakiye=$account->bakiye + $item->borc_tutar - $item->alacak_tutar;

            $item->islem_sonrasi_bakiye=$guncelBakiye;
            $item->save();

            $account->bakiye = $guncelBakiye;
            $account->save();
        }

    }
}
