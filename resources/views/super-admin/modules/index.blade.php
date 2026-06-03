<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Super Admin - Modul Sistem</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
                Halaman ini hanya untuk Super Admin. Perubahan status modul akan mempengaruhi frontend dan backend secara langsung.
            </div>

            <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-3 py-2 text-left font-semibold">Modul</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $module)
                                <tr class="border-b">
                                    <td class="px-3 py-3 font-medium text-gray-800">{{ $module['label'] }}</td>
                                    <td class="px-3 py-3">
                                        @if ($module['enabled'])
                                            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                                        @else
                                            <span class="rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-800">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <form method="POST" action="{{ route('super-admin.modules.update', $module['key']) }}" class="inline-flex">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_enabled" value="{{ $module['enabled'] ? 0 : 1 }}">
                                            <button type="submit" class="rounded-md px-3 py-2 text-xs font-semibold text-white {{ $module['enabled'] ? 'bg-rose-700 hover:bg-rose-800' : 'bg-emerald-700 hover:bg-emerald-800' }}">
                                                {{ $module['enabled'] ? 'Disable Modul' : 'Enable Modul' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
