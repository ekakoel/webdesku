<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Item APBDes</h2></x-slot>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg"><div class="p-6">
                <form action="{{ route('admin.village-apbdes-items.update', $item) }}" method="POST">
                    @method('PUT')
                    @include('admin.village-apbdes-items._form', ['submitLabel' => 'Perbarui Item'])
                </form>
            </div></div>
        </div>
    </div>
</x-app-layout>

