<?php

namespace App\Filament\Pages;

use App\Models\AccountingConstant;
use App\Models\Voucher;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;


class JournalPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-m-book-open';
    protected static string $view = 'filament.pages.journal-page';
    protected static ?string $navigationLabel = 'Yevmiye Defteri';
    protected static ?string $title = 'Yevmiye Defteri';
    protected static ?int $navigationSort = 3;

    public ?array $filters = [];
    public ?array $deferredFilters = [];

    public function mount(): void
    {
        $this->deferredFilters = [
            'oda_birimi' => 'Merkez',
            'baslangic_tarih' => now()->startOfMonth()->format('Y-m-d'),
            'bitis_tarih' => now()->endOfMonth()->format('Y-m-d'),
        ];
        $this->filters = [];
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('oda_birimi')
                    ->label('Birim')
                    ->disabled()
                    ->options([
                        'Merkez' => 'Merkez'
                        //veritabanındaki değer - //Arayüzde görünen değer
                    ])->columnSpanFull()
                    ->required()
                    ->placeholder('Birim Seçiniz'),

                DatePicker::make('baslangic_tarih')
                    ->label('Başlangıç Tarihi')
                    ->required(),

                DatePicker::make('bitis_tarih')
                    ->label('Bitiş Tarihi')
                    ->required(),

                Select::make('baslangic_hesap_kod')
                    ->label('Başlangıç Hesap Kodu')
                    ->options(AccountingConstant::orderBy('hesap_kod', 'asc')->pluck('hesap_kod', 'hesap_kod'))
                    ->placeholder('Hesap seçiniz')
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->required(),

                Select::make('bitis_hesap_kod')
                    ->label('Bitiş Hesap Kodu')
                    ->options(AccountingConstant::orderBy('hesap_kod', 'asc')->pluck('hesap_kod', 'hesap_kod'))
                    ->placeholder('Hesap seçiniz')
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->required(),

                Actions::make([
                    Action::make('applyFilters')
                        ->label('Ara')
                        ->Icon('heroicon-m-magnifying-glass')
                        ->action(function () {
                            $this->applyFilters();
                        })
                        ->button()
                        ->color('primary'),
                ])->columnSpanFull()
            ])
            ->columns(2)
            ->statePath('deferredFilters');
    }

    protected function getTableQuery(): Builder
    {
        $query = Voucher::query()
            ->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
            ->join('accounting_constants', 'voucher_items.accounting_constant_id', '=', 'accounting_constants.id')
            ->select(
                'vouchers.*',
                'voucher_items.*',
                'accounting_constants.*',
            );

        if (empty($this->filters)) {
            return $query->where('vouchers.id', '<', 0); // Hiç kayıt getirmeyen sorgu
        }

        // Filtreleri uygula
        if (
            !empty($this->filters['oda_birimi']) && !empty($this->filters['baslangic_tarih']) &&
            !empty($this->filters['bitis_tarih']) && !empty($this->filters['baslangic_hesap_kod']) && !empty($this->filters['bitis_hesap_kod'])
        ) {
            
        
            $query->where('vouchers.oda_birimi', $this->filters['oda_birimi'])
                ->whereBetween('vouchers.yevmiye_tarih', [
                    $this->filters['baslangic_tarih'],
                    $this->filters['bitis_tarih']
                ])
                ->whereBetween('accounting_constants.hesap_kod_numeric', [
                    AccountingConstant::convertCodeToNumeric($this->filters['baslangic_hesap_kod']),
                    AccountingConstant::convertCodeToNumeric($this->filters['bitis_hesap_kod']),
                ]);
        } else {
            // Eksik filtre varsa hiç kayıt getirme
            return $query->where('vouchers.id', '<', 0);
        }

        return $query->orderBy('vouchers.yevmiye_tarih', 'asc');
    }



    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->groups([
                Group::make('yevmiye_tarih')
                    ->label('Tarih')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('yevmiye_tarih')
                    ->label('Tarih')
                    ->date('d/m/Y'),
                TextColumn::make('voucher_id')
                    ->label('Fiş No'),
                    TextColumn::make('makbuz_no')
                    ->label('Makbuz No'),
                TextColumn::make('hesap_kod')
                    ->label('Hesap Kodu')
                    ->searchable(),
                TextColumn::make('hesap_ad')
                    ->label('Hesap Adı')
                    ->searchable()
                    ->extraCellAttributes(['style' => 'padding-right: 2em;'])
                    ->extraHeaderAttributes(['style' => 'padding-right: 2em;']),
                TextColumn::make('borc_tutar')
                    ->label('Borç Tutar')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('success'),
                TextColumn::make('alacak_tutar')
                    ->label('Alacak Tutar')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('danger'),
                TextColumn::make('oda_birimi')
                    ->label('Birim')
            ])
            ->emptyStateHeading('Kayıt bulunumadı')
            ->emptyStateIcon('heroicon-m-face-frown')
            ->paginated([10, 25, 50, 100]);
    }
    public function applyFilters(): void
    {

        $this->form->validate();
        $this->filters = $this->deferredFilters;
        $this->resetTable(); // Tabloyu yenile //Filamentin
    }
}
