<?php

namespace App\Filament\Resources\AccountingConstantResource\Pages;

use App\Filament\Resources\AccountingConstantResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewAccountingConstant extends ViewRecord
{
    protected static string $resource = AccountingConstantResource::class;
     

    protected static ?string $title = "sa";

    public function getFormActions(): array{
    return [
      Action::make('close')
      ->label('Kapat')
      ->url($this->getResource()::getUrl('index'))
    ];
  }
}
