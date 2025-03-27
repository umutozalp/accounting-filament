<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\EditRecord;

class EditVoucher extends EditRecord
{
    protected static string $resource = VoucherResource::class;
    protected static ?string $title = "Fiş Düzenle";
    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    public function getSavedNotificationTitle(): ?String{
        return 'Değişiklikler kaydedildi';
    }

    protected function getFormActions(): array
    {
        return [
            EditAction::make()->label('Değişiklikleri Kaydet')->submit('save'),
            Action::make('cancel_button')->label('İptal')->url($this->getResource()::getUrl('index')), 
        ];
    }
}
