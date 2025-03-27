<?php

namespace App\Filament\Resources\AccountingConstantResource\Pages;

use App\Filament\Resources\AccountingConstantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListAccountingConstants extends ListRecords
{
    protected static string $resource = AccountingConstantResource::class;

    protected static ?string $title = "Hesap Kodları";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Ekle'),
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;  // Sayfa içeriği için tam genişlik
    }
}
