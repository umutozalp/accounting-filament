<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;


class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;

    protected static ?string $title = "Yeni Fiş Oluştur";
    protected static ?int $navigationSort = -1;

   

    //varsayılan olarak kayıt oluşturulduktan sonra edit sayfasına yönlendirmeyi devre dısı bırakıp istediğimiz sayfaya yönlendirdik
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Fiş başarıyla oluşturuldu';
    }
    protected function getFormActions(): array
    {

        return [
            CreateAction::make('save')
                ->label('Oluştur')
                ->submit('save')
                ->size('xl')
                ->icon('heroicon-m-plus'),

        ];
    }
}
