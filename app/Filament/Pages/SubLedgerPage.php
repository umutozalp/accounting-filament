<?php

namespace App\Filament\Pages;

use App\Models\AccountingConstant;
use Filament\Pages\Page;
use App\Models\Voucher;
use Dom\Text;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class SubLedgerPage extends Page implements HasForms, HasTable
{
    public ?array $data = [];

    use InteractsWithTable, InteractsWithForms;

    public $oda_birimi;
    public $baslangic_tarih;
    public $bitis_tarih;
    public $hesap_kod;
    protected static ?string $navigationIcon = 'heroicon-m-minus-circle';
    protected static string $view = 'filament.pages.sub-ledger-page';

    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Muavin Defteri';

    public array $filters = [];
    public array $tempFilters = [];

    public function mount(): void
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
                Select::make('hesap_kod')->columnSpanFull()
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->label('Hesap kodu')
                    ->placeholder('Hesap seçiniz')
                    ->required()
                    ->options(AccountingConstant::orderBy('hesap_kod','asc')->pluck('hesap_kod', 'hesap_kod')->toArray()),

                Actions::make([
                    Action::make('applyFilters')
                        ->label('Ara')
                        ->action(function () {
                            $this->applyFilters();
                        })
                        ->Icon('heroicon-m-magnifying-glass')
                        ->button()
                        ->color('success')

                ])

            ])->columns(2)->statePath('tempFilters');
    }

    public function getTableQuery()
    {
        $query = Voucher::query()
            ->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
            ->join('accounting_constants', 'voucher_items.accounting_constant_id', '=', 'accounting_constants.id')
            ->select('vouchers.*', 'voucher_items.*', 'accounting_constants.*');

        if (empty($this->filters)) {
            return $query->where('vouchers.id', '<', '0');
        }
        if (
            !empty($this->filters['oda_birimi']) &&
            !empty($this->filters['baslangic_tarih']) &&
            !empty($this->filters['bitis_tarih']) &&
            !empty($this->filters['hesap_kod'])
        ) {
            $query->where('vouchers.oda_birimi', $this->filters['oda_birimi'])
                ->whereBetween('vouchers.yevmiye_tarih', [
                    $this->filters['baslangic_tarih'],
                    $this->filters['bitis_tarih'],
                ])
                ->where('accounting_constants.hesap_kod', $this->filters['hesap_kod']);
        } else {
            return $query->where('vouchers.id', '<', '0');
        }
        return $query->orderBy('vouchers.yevmiye_tarih', 'asc');
    }


    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Kayıt bulunumadı')
            ->emptyStateDescription('Lütfen arama yapınız')
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('yevmiye_tarih')->label('İşlem Tarihi'),
                TextColumn::make('voucher_id')->label('Fiş No'),
                TextColumn::make('hesap_ad')->label('Hesap Adı'),
                TextColumn::make('fis_kalemi_aciklama')->label('Açıklama')
                ->extraCellAttributes(['style' => 'padding-right: 5em;'])
                ->extraHeaderAttributes(['style' => 'padding-right: 5em;']),
                TextColumn::make('borc_tutar')->label('Borç Tutar')
                ->money('TRY', locale: 'tr')
                    ->badge('')->color('success'),
                TextColumn::make('alacak_tutar')->label('Alacak Tutar')
                ->money('TRY', locale: 'tr')
                    ->badge('')->color('danger'),
                TextColumn::make('bakiye')->label('Bakiye')
                ->money('TRY', locale: 'tr'),

            ]);
    }


    public function applyFilters()
    {
        $this->form->validate();
        $this->filters = $this->tempFilters;
        $this->resetTable();
    }
}
