<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Layanan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.services.store') }}" method="POST">
                        @include('admin.services._form', ['submitLabel' => 'Simpan Layanan', 'service' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


