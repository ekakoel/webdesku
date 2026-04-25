<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Sambutan Kepala Desa</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.head-messages.store') }}" method="POST" enctype="multipart/form-data">
                        @include('admin.head-messages._form', ['submitLabel' => 'Simpan Sambutan', 'headMessage' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



