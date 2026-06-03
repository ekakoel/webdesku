<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Dokumen Transparansi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.village-transparency-documents.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @include('admin.village-transparency-documents._form', ['submitLabel' => 'Perbarui Dokumen'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
