<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Peraturan Desa</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.regulations.update', $regulation) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @include('admin.regulations._form', ['submitLabel' => 'Perbarui Peraturan', 'regulation' => $regulation])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

