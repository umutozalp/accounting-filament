<?php

namespace App\Filament\Resources\VoucherResource\Widgets;

use App\Models\Voucher;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoucherWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Fişler',Voucher::count())
            ->description('Oluşturulan yeni fişler')
            ->descriptionIcon('heroicon-m-user-group',IconPosition::Before)
            ->chart([7,2,10,3,15,4,17])
            ->color('success')
        ];
    }
}
