<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Voucher extends Model
{
    protected $fillable=[

        'fis_turu',
        'oda_birimi',
        'aciklama',
        'yevmiye_tarih',
        'borc_tutar',
        'alacak_tutar',
        'fis_kalemi_aciklama',
    ];

     public $timestamps=false;
    public static function boot()
    {
        parent::boot();


        static::creating(function ($voucher) {

            $voucher->created_at=Carbon::now()->toDateString();
           
            $sonYevmiyeNo = self::max('yevmiye_no');
            $voucher->yevmiye_no = $sonYevmiyeNo ? $sonYevmiyeNo + 1 : 1;

            /*$sonMakbuzNo = self::max('makbuz_no'); 
            $voucher->makbuz_no = $sonMakbuzNo ? $sonMakbuzNo + 1 : 1; //nasıl oluyor ögren */

            if (!$voucher->makbuz_no) {
              
                $voucher->makbuz_no = 'INV.' . ($voucher->yevmiye_no ?? 1);  // Eğer id null ise 1 koy
            }


                // Eğer kullanıcı oturum açmışsa işlemi yapan kişiyi ekle
                if (Auth::check()) {
                    $voucher->islemi_yapan = Auth::user()->name;
                }

               


                
        });
    }
    public function items(){
        return $this->hasMany(VoucherItem ::class);
    }



}
