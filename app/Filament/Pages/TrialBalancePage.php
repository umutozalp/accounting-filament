<?php

namespace App\Filament\Pages;

use App\Models\AccountingConstant;
use Filament\Pages\Page;
use App\Models\Voucher;
use App\Models\VoucherItem;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TrialBalancePage extends Page implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    public $oda_birimi;
    public $baslangic_tarih;
    public $bitis_tarih;
    public $hesap_kod;
    protected static ?string $navigationIcon = 'heroicon-m-document-currency-bangladeshi';
    protected static string $view = 'filament.pages.sub-ledger-page';
    protected static ?int $navigationSort = 6;
    protected static ?string $title = 'Mizan Raporu';

    public array $filters = [];
    public array $tempFilters = [];
    public float $toplam_alacak_tutar;
    public float $toplam_borc_tutar;

    public function mount()
    {
        $this->tempFilters = [
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
                Select::make('oda_birimi')->label('Birim')->columnSpanFull()
                    ->placeholder('Birim Seçiniz')
                    ->options([
                        'Merkez' => 'Merkez',
                    ])->required()->disabled(),
                DatePicker::make('baslangic_tarih')->label('Başlangıç Tarihi')->required(),
                DatePicker::make('bitis_tarih')->label('Bitiş Tarihi')->required(),
                Select::make('baslangic_hesap_kod')
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->label('Başlangıç hesap kodu')
                    ->placeholder('Hesap seçiniz')
                    ->required()
                    ->options(AccountingConstant::orderBy('hesap_kod', 'asc')->pluck('hesap_kod', 'hesap_kod')->toArray()),
                Select::make('bitis_hesap_kod')
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->label('Bitiş hesap kodu')
                    ->placeholder('Hesap seçiniz')
                    ->required()
                    ->options(AccountingConstant::orderBy('hesap_kod', 'asc')->pluck('hesap_kod', 'hesap_kod')->toArray()),

                Actions::make([
                    Action::make('applyFilters')
                        ->label('Ara')
                        ->button()
                        ->action(function () {
                            $this->applyFilters();
                        })
                        ->Icon('heroicon-m-magnifying-glass')
                        ->color('success')
                ])
            ])->columns(2)->statePath('tempFilters');
    }

    public function getTableQuery()
    {
        $query = Voucher::query()
            ->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
            ->join('accounting_constants', 'voucher_items.accounting_constant_id', '=', 'accounting_constants.id')
            ->select(
                DB::raw('accounting_constants.hesap_kod || \'-\' || vouchers.oda_birimi AS id'),
                'accounting_constants.hesap_kod',
                'accounting_constants.hesap_ad',
                'accounting_constants.hesap_kod_numeric',
                DB::raw('SUM(voucher_items.borc_tutar) as toplam_borc_tutar'),
                DB::raw('SUM(voucher_items.alacak_tutar) as toplam_alacak_tutar'),

                DB::raw('CASE 
                    WHEN SUM(voucher_items.borc_tutar) - SUM(voucher_items.alacak_tutar) > 0 
                    THEN SUM(voucher_items.borc_tutar) - SUM(voucher_items.alacak_tutar) 
                    ELSE 0 
                    END as borc_bakiye_tutar'),
                DB::raw('CASE 
                    WHEN SUM(voucher_items.alacak_tutar) - SUM(voucher_items.borc_tutar) > 0 
                    THEN SUM(voucher_items.alacak_tutar) - SUM(voucher_items.borc_tutar) 
                    ELSE 0 
                    END as alacak_bakiye_tutar')
            )
            ->groupBy(
                'accounting_constants.hesap_kod',
                'accounting_constants.hesap_ad',
                'accounting_constants.hesap_kod_numeric',
                'vouchers.oda_birimi'
            );

        if (empty($this->filters)) {
            return $query->where('vouchers.id', '<', '0');
        }

        if (
            !empty($this->filters['oda_birimi']) &&
            !empty($this->filters['baslangic_tarih']) &&
            !empty($this->filters['bitis_tarih']) &&
            !empty($this->filters['baslangic_hesap_kod']) &&
            !empty($this->filters['bitis_hesap_kod'])
        ) {
            $query->where('vouchers.oda_birimi', $this->filters['oda_birimi'])
                ->whereBetween('vouchers.yevmiye_tarih', [
                    $this->filters['baslangic_tarih'],
                    $this->filters['bitis_tarih'],
                ])
                ->whereBetween('accounting_constants.hesap_kod_numeric', [
                    AccountingConstant::convertCodeToNumeric($this->filters['baslangic_hesap_kod']),
                    AccountingConstant::convertCodeToNumeric($this->filters['bitis_hesap_kod']),
                ]);
        } else {
            return $query->where('vouchers.id', '<', '0');
        }

        return $query->orderBy('accounting_constants.hesap_kod_numeric', 'asc');
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Kayıt bulunamadı')
            ->emptyStateDescription('Lütfen arama yapınız')
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('hesap_kod')->label('Hesap Kodu'),
                TextColumn::make('hesap_ad')->label('Hesap Adı')
                    ->extraCellAttributes(['style' => 'padding-right: 5em;'])
                    ->extraHeaderAttributes(['style' => 'padding-right: 5em;']),
                TextColumn::make('toplam_borc_tutar')->label('Toplam Borç Tutarı')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('success'),
                TextColumn::make('toplam_alacak_tutar')->label('Toplam Alacak Tutarı')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('danger')
                    ->extraCellAttributes(['style' => 'padding-right: 5em;'])
                    ->extraHeaderAttributes(['style' => 'padding-right: 5em;']),
                TextColumn::make('borc_bakiye_tutar')->label('Borç Bakiye Tutar')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('success'),
                TextColumn::make('alacak_bakiye_tutar')->label('Alacak Bakiye Tutar')
                    ->money('TRY', locale: 'tr')
                    ->badge('')->color('danger'),
            ]);
    }

    public function applyFilters()
    {
        $this->form->validate();
        $this->filters = $this->tempFilters;
        $this->resetTable();
    }
}
