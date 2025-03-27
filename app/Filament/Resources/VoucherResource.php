<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\AccountingConstant;
use App\Models\Voucher;
use Carbon\Traits\ToStringFormat;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\DateFilter;



class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Fieldset::make('Fiş Türü')
                        ->schema([
                            Radio::make('fis_turu')->required()->label('')
                                ->options([
                                    'Tediye' => 'Tediye',
                                    'Tahsil' => 'Tahsil',
                                    'Mahsup' => 'Mahsup',
                                ])->required(),
                        ])->columnSpan(1),
                    Group::make()->schema([
                        Select::make('oda_birimi')->label('Birim')
                            ->placeholder('Birim seçiniz')
                            ->options([
                                'Merkez' => 'Merkez',
                            ])->required()->default('Merkez')->disabled()->dehydrated(true),
                        TextInput::make('aciklama')->label('Açıklama')->default('')->alpha()

                    ]),

                    DatePicker::make('yevmiye_tarih')->label('Yevmiye Tarihi')
                    ->default(now())
                    ->columnSpanFull()
                    ->readOnly()

                    
                ])->columns('2'),


                Repeater::make('items')
                    ->label('Fiş Kalemleri')
                    ->relationship('items')
                    ->schema([
                        Select::make('accounting_constant_id')
                            ->label('Hesap Kodu')
                            ->options(AccountingConstant::orderBy('hesap_kod', 'asc')->pluck('hesap_kod', 'hesap_kod'))
                            ->placeholder('Hesap seçiniz')
                             ->prefixIcon('heroicon-m-chevron-double-down')
                            ->required(),
                        Group::make()->schema([
                            TextInput::make('borc_tutar')->label('Borç Tutar')->inputMode('decimal')->step('none')->required()->suffix('TL'),
                            TextInput::make('alacak_tutar')->label('Alacak Tutar')->inputMode('decimal')->required()->suffix('TL'),
                        ])->columns(2),

                        TextInput::make('fis_kalemi_aciklama')->columnSpanFull()->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Yeni Kalem Ekle')
                    ->columnSpanFull()
                    ->columns(2)


            ]);
    }



    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Fiş Listesi')
                ->icon('heroicon-m-queue-list')  // Liste ikonu
                ->url(static::getUrl('index'))
                ->sort(1),  // Sıralama

            NavigationItem::make('Yeni Fiş')
                ->icon('heroicon-m-plus-circle')  // Artı ikonu
                ->url(static::getUrl('create'))
                ->sort(2),  // Liste'den sonra göster
        ];
    }

    public static function table(Table $table): Table
    {
        return $table

            ->emptyStateHeading('Kayıt bulunumadı')
            ->columns([
                TextColumn::make('id')->label('Fiş No'),
                TextColumn::make('fis_turu')->label('Tür')->searchable(),
                TextColumn::make('yevmiye_no')->label('Yevmiye No')->sortable(),
                TextColumn::make('makbuz_no')->label('Makbuz No')->searchable()->sortable()
                ->extraCellAttributes(['style' => 'padding-right: 5em;'])
                ->extraHeaderAttributes(['style' => 'padding-right: 5em;']),
                TextColumn::make('islemi_yapan')->label('İşlemi Yapan')->searchable()->badge()->color('warning'),
                TextColumn::make('aciklama')->label('Açıklama'),
                TextColumn::make('yevmiye_tarih')->label('Yevmiye Tarihi')->searchable()->sortable()->date('d/m/Y'),


                //
            ])
            ->searchPlaceholder('Ara')
            ->paginated()
            ->filters([
                SelectFilter::make('fis_turu')->label('Fiş Türü')
                    ->multiple()
                    ->placeholder('Ara')
                    ->options([
                        'tediye' => 'Tediye',
                        'tahsil' => 'Tahsil',
                        'mahsup' => 'Mahsup',
                    ]),




            ])
            ->actions([
                ViewAction::make()->label('İncele')
                    ->modalHeading('')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('primary'),
                EditAction::make('Kayıt detayları')->label('Düzenle')
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
