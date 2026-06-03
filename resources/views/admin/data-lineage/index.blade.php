<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Lineage & Governance</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <p class="text-sm text-slate-600">
                        Halaman ini menampilkan sumber data resmi per modul publik beserta status datanya secara live dari database.
                    </p>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-3 py-2 text-left">Modul</th>
                                    <th class="px-3 py-2 text-left">Sumber Tabel</th>
                                    <th class="px-3 py-2 text-left">Halaman Publik</th>
                                    <th class="px-3 py-2 text-left">Total Record</th>
                                    <th class="px-3 py-2 text-left">Published</th>
                                    <th class="px-3 py-2 text-left">Terakhir Update</th>
                                    <th class="px-3 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="border-b">
                                        <td class="px-3 py-3">
                                            <strong>{{ $row['label'] }}</strong>
                                        </td>
                                        <td class="px-3 py-3">
                                            <code>{{ $row['table'] }}</code>
                                        </td>
                                        <td class="px-3 py-3">{{ $row['public_page'] }}</td>
                                        <td class="px-3 py-3">{{ number_format((int) $row['count'], 0, ',', '.') }}</td>
                                        <td class="px-3 py-3">{{ number_format((int) $row['published_count'], 0, ',', '.') }}</td>
                                        <td class="px-3 py-3">
                                            {{ $row['last_updated'] ? \Carbon\Carbon::parse($row['last_updated'])->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            @if (\Illuminate\Support\Facades\Route::has($row['admin_route']))
                                                <a href="{{ route($row['admin_route']) }}" class="text-blue-700 hover:underline">Kelola</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
