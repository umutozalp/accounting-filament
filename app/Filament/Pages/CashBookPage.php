<?php

namespace App\Filament\Pages;

use App\Models\AccountingConstant;
use Filament\Forms\Components\DatePicker; //
use Filament\Forms\Components\Select; //
use Filament\Forms\Concerns\InteractsWithForms; //
use Filament\Forms\Contracts\HasForms; //
use Filament\Forms\Form; //
use Filament\Pages\Page; //
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn; //
use Filament\Tables\Concerns\InteractsWithTable; //
use Filament\Tables\Table; //
use App\Models\Voucher;
use DateTime;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CashBookPage extends Page implements HasTable, HasForms
{

    protected static ?string $title = 'Kasa Defteri';
    protected static ?string $navigationIcon = 'heroicon-m-building-library';

    protected static string $view = 'filament.pages.cash-book-page';

    protected static ?int $navigationSort = 4;
    use InteractsWithForms, InteractsWithTable;

    public ?array $data = [];

    public $oda_birimi;
    public $baslangic_tarih;
    public $bitis_tarih;
    public $hesap_kod;

    public array $filters = [];
    public array $tempFilters = [];

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
                    ])->required()
                    ->disabled(),
                Select::make('hesap_kod')->columnSpanFull()
                    ->prefixIcon('heroicon-m-chevron-double-down')
                    ->label('Hesap kodu')
                    ->placeholder('Hesap seçiniz')

                    ->required()
                    ->options(AccountingConstant::where('hesap_turu','KASA')->pluck('hesap_kod','hesap_kod'))
                    ->columnSpan(1),

                DatePicker::make('baslangic_tarih')->label('Başlangıç Tarihi')->required(),
                DatePicker::make('bitis_tarih')->label('Bitiş Tarihi')->required(),

                Actions::make([
                    Action::make('applyFilters')
                        ->label('Ara')
                        ->icon('heroicon-m-magnifying-glass')
                        ->action(function () {
                            $this->applyFilters();
                        })
                        ->Icon('heroicon-m-magnifying-glass')
                        ->color('primary')
                        ->button()
                ])->columnSpanFull()

            ])->columns(3)
            ->statePath('tempFilters');
    }

    public function getTableQuery(): Builder
    {
        $query = Voucher::query()
            ->join('voucher_items', 'vouchers.id', '=', 'voucher_items.voucher_id')
            ->join('accounting_constants', 'voucher_items.accounting_constant_id', '=', 'accounting_constants.id')
            ->select('vouchers.*', 'voucher_items.*', 'accounting_constants.*');

        if (empty($this->filters)) {
            return $query->where('vouchers.id', '<', '0');
        }
        if (
            !empty($this->filters['oda_birimi']) && !empty($this->filters['baslangic_tarih']) &&
            !empty($this->filters['bitis_tarih']) && !empty($this->filters['hesap_kod'])
        ) {
            $query->where('vouchers.oda_birimi', $this->filters['oda_birimi'])
                ->whereBetween('vouchers.yevmiye_tarih', [
                    $this->filters['baslangic_tarih'],
                    $this->filters['bitis_tarih']
                ])->where('accounting_constants.hesap_kod_numeric', AccountingConstant::convertCodeToNumeric($this->filters['hesap_kod']));
        } else {
            $query->where('vouchers.id', '<', '0');
        }
        return $query->orderBy('vouchers.yevmiye_tarih', 'asc');
    }

    public function table(Table $table): Table
    {

        return $table
            ->emptyStateHeading('Kayıt bulunamadı')
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('yevmiye_tarih')->label('İşlem Tarihi')
                ->date('d/m/Y'),
                TextColumn::make('voucher_id')->label('Fiş No'),
                TextColumn::make('fis_kalemi_aciklama')->label('Açıklama'), 
                TextColumn::make('borc_tutar')->label('Giriş')
                ->money('TRY',locale:'tr')
                ->badge('')->color('success'),
                TextColumn::make('alacak_tutar')->label('Çıkış')
                ->money('TRY',locale:'tr')
                ->badge('')->color('danger'),
                TextColumn::make('islem_sonrasi_bakiye')->label('Bakiye')
                ->money('TRY',locale:'tr')

            ]);
    }
    public function applyFilters()
    {
        $this->form->validate();
        $this->filters = $this->tempFilters;
        $this->resetTable();
    }
}
