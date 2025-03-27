<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountingConstant extends Model
{
    //
    public $timestamps  = false;
    protected $fillable = [
        "hesap_ad",
        "hesap_kod",
        'bakiye',
        'hesap_turu',
    ];

    protected $casts = [
        'bakiye' => 'decimal:2',
    ];

    public static function boot()
    {

        parent::boot();

        static::creating(function ($accountingConstant) {

            $accountingConstant->created_at = Carbon::now()->toDateString();
            $accountingConstant->hesap_kod_numeric=self::convertCodeToNumeric($accountingConstant->hesap_kod);
            
        });
    }

    public static function convertCodeToNumeric($accountCode)
    {
        // Kodu parçalara ayır
        $parts = explode('.', $accountCode);
        
        // Her bir parçayı 5 haneli yap
        $formattedParts = array_map(function($part) {
            return str_pad($part, 5, '0', STR_PAD_RIGHT);
        }, $parts);
        
        // Eksik seviyeleri 00000 ile doldur (3 seviyeye kadar)
        while (count($formattedParts) < 3) {
            $formattedParts[] = '00000';
        }
        
        // Birleştir
        $result = implode('', $formattedParts);
        
        return (int) $result;
    }

    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }
}
