<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Resources\Pages\ListRecords;
class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected static ?string $title = "";
    protected static ?int $navigationSort = 0;



    protected function getHeaderWidgets(): array
    {
        return [
            VoucherResource\Widgets\VoucherWidget::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }
    
}
