<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Filter Rekap Nilai
            </x-slot>

            <x-slot name="description">
                Pilih kelas, mata pelajaran, dan paket ujian seperti UTS, UAS, atau remedial.
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        <x-filament::section style="margin-top: 1rem">
            <x-slot name="heading">
                Data Nilai Siswa
            </x-slot>

            <x-slot name="description">
                Nilai dihitung dari jumlah benar / jumlah soal × 100.
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
