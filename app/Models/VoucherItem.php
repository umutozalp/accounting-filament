<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Services\VoucherItemService;

class VoucherItem extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'voucher_id',
        'borc_tutar',
        'alacak_tutar',
        'adet',
        'fis_kalemi_aciklama',
        'accounting_constant_id',
    ];

    // Type casting ekleyelim
    protected $casts = [
        'borc_tutar' => 'decimal:2',
        'alacak_tutar' => 'decimal:2',
    ];

    public static function boot()
    {

        parent::boot();
        static::creating(function ($voucherItem) {
            $voucherItem->created_at = Carbon::now()->toDateString();
        });
    }

    //
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function constant()
    {
        return $this->belongsTo(AccountingConstant::class, 'accounting_constant_id');
    }

    
}
