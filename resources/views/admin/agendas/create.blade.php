<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Agenda</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.agendas.store') }}" method="POST" enctype="multipart/form-data">
                        @include('admin.agendas._form', ['submitLabel' => 'Simpan Agenda', 'agenda' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


