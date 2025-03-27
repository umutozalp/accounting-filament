<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Resources\AccountingConstantResource\Pages;
use App\Filament\Resources\AccountingConstantResource\RelationManagers;
use App\Models\AccountingConstant;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountingConstantResource extends Resource
{
    protected static ?string $model = AccountingConstant::class;

    protected static ?string $navigationIcon = 'heroicon-m-cog-8-tooth';

    protected static ?string $navigationLabel = 'Hesap Kodu';

    protected static ?string $navigationGroup = 'Muhasebe Sabitleri';

    protected static ?int $navigationSort = 7;




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('hesap_kod')->label('Hesap Kodu')->required(),
                TextInput::make('hesap_ad')->label('Hesap Adı')->required(),
                Select::make('hesap_turu')->label('Hesap türü')
                ->placeholder('Tür seçiniz')
                    ->options([
                        'BANKA' => 'Banka Hesabı', //Nasıl yapılacabileceği sorulacak (Enum?)
                        'KASA' => 'Kasa Hesabı',
                        'GELİR' => 'Gelir Hesabı',
                        'GİDER' => 'Gider Hesabı'
                    ])->required(),
                TextInput::make('bakiye')->label('Bakiye')->type('number')->step(5)->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Kayıt bulunumadı')
            ->columns([
                TextColumn::make('hesap_kod')->label('Hesap Kodu'),
                TextColumn::make('hesap_ad')->label('Hesap Adı'),
                TextColumn::make('bakiye')->label('Hesap Bakiyesi')
                ->money('TRY',locale:'tr'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label('İncele')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('primary'),
                EditAction::make()
                    ->label('Düzenle')
                    ->icon('heroicon-m-pencil'),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountingConstants::route('/'),
            'create' => Pages\CreateAccountingConstant::route('/create'),
            'edit' => Pages\EditAccountingConstant::route('/{record}/edit'),
        ];
    }
}
