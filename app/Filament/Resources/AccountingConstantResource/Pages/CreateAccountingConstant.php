<?php

namespace App\Filament\Resources\AccountingConstantResource\Pages;

use App\Filament\Resources\AccountingConstantResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountingConstant extends CreateRecord
{
    protected static string $resource = AccountingConstantResource::class;

    protected static ?string $title = "Hesap Kodu Ekle";

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Hesap başarıyla eklendi';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getFormActions(): array
    {
        return [
            CreateAction::make("save")
                ->label("Ekle")
                ->icon('heroicon-m-plus')
                ->submit("save"),

            Action::make('cancel')
                ->label('İptal')
                ->icon('heroicon-m-x-mark')
                ->color('primary')
                ->url($this->getResource()::getUrl('index'))
        ];
    }
}
