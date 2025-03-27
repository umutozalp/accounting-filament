<?php

namespace App\Filament\Resources\AccountingConstantResource\Pages;

use App\Filament\Resources\AccountingConstantResource;

use Filament\Support\Enums\MaxWidth;
use Filament\Actions;
use Filament\Actions\Action;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\EditRecord;

class EditAccountingConstant extends EditRecord
{
    protected static string $resource = AccountingConstantResource::class;

    protected static ?string $title = "Hesap Kodu Düzenle";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label("Sil"),
        ];
    }

    public function getSavedNotificationTitle(): ?String{
        return 'Değişiklikler kaydedildi';
    }
    protected function getFormActions(): array{
        return [
            EditAction::make()->label("Değişiklikleri kaydet")
            ->icon('heroicon-m-check')
            ->submit('update'),
            Action::make('cancel_button')
            ->label("İptal")
            ->icon("heroicon-m-x-mark")
            ->url($this->getResource()::getUrl('index')),
            
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full; // Tam genişlik yap
    }
}
