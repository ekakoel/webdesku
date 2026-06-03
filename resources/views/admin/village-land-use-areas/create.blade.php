<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Data Luas Wilayah</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.village-land-use-areas.store') }}" method="POST">
                        @include('admin.village-land-use-areas._form', ['submitLabel' => 'Simpan Data'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
