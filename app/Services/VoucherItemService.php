<?php

namespace App\Services;

use App\Models\VoucherItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherItemService
{
    public function updateAccountBalance(VoucherItem $item)
    {
        return DB::transaction(function () use ($item) {
            try {
                $account = $item->constant;

                if (!$account) {
                    throw new \Exception("Hesap bulunamadı.");
                }

                $eskiBakiye = $account->bakiye;
                
                // Borç ve alacak tutarlarını hesapla
                $borc = $item->borc_tutar ?? 0;
                $alacak = $item->alacak_tutar ?? 0;

                // Bakiye güncellemesi (borç artırır, alacak azaltır)
                $account->bakiye = $eskiBakiye + $borc - $alacak;
                $account->save();

                // İşlemi logla
                Log::info('Hesap bakiyesi güncellendi', [
                    'voucher_item_id' => $item->id,
                    'account_id' => $account->id,
                    'eski_bakiye' => $eskiBakiye,
                    'yeni_bakiye' => $account->bakiye,
                    'borc' => $borc,
                    'alacak' => $alacak
                ]);

                return $account;

            } catch (\Exception $e) {
                Log::error('Bakiye güncellenirken hata oluştu', [
                    'voucher_item_id' => $item->id,
                    'hata' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }
}
