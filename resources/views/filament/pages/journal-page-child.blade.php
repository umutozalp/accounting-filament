<div class="p-4 bg-white shadow rounded-lg">
    <div class="flex justify-between">
        <span class="font-bold">{{ $record->yevmiye_tarih }}</span>
        <span class="text-gray-600">Fiş No: {{ $record->voucher_id }}</span>
    </div>
    <div class="mt-2">
        <p><strong>Hesap Kodu:</strong> {{ $record->hesap_kod }}</p>
        <p><strong>Hesap Adı:</strong> {{ $record->hesap_ad }}</p>
        <p><strong>Borç:</strong> {{ number_format($record->borc_tutar, 2) }}</p>
        <p><strong>Alacak:</strong> {{ number_format($record->alacak_tutar, 2) }}</p>
        <p><strong>Oda Birimi:</strong> {{ $record->oda_birimi }}</p>
    </div>
</div>