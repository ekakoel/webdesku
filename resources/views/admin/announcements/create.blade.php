<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengumuman</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.announcements.store') }}" method="POST">
                        @include('admin.announcements._form', ['submitLabel' => 'Simpan Pengumuman', 'announcement' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


