<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dasbor Admin Desa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold">Selamat datang, {{ auth()->user()->name }}.</p>
                    <p class="mt-2 text-sm text-gray-600">
                        Anda login sebagai aparat desa. Gunakan menu backend untuk mengelola data yang tampil di website publik.
                    </p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="{{ route('admin.news.index') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                            Kelola Berita
                        </a>
                        <a href="{{ route('admin.agendas.index') }}" class="inline-flex items-center rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
                            Kelola Agenda
                        </a>
                        <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                            Kelola Pengumuman
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center rounded-md bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">
                            Kelola Layanan
                        </a>
                        <a href="{{ route('admin.service-requests.index') }}" class="inline-flex items-center rounded-md bg-cyan-800 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-900">
                            Pengajuan Layanan
                        </a>
                        <a href="{{ route('admin.galleries.index') }}" class="inline-flex items-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                            Kelola Galeri
                        </a>
                        <a href="{{ route('admin.village-assets.index') }}" class="inline-flex items-center rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">
                            Kelola Infografis Desa
                        </a>
                        <a href="{{ route('admin.village-populations.index') }}" class="inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                            Kelola Penduduk (Infografis)
                        </a>
                        <a href="{{ route('admin.village-apbdes-items.index') }}" class="inline-flex items-center rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">
                            Kelola APBDes (Infografis)
                        </a>
                        <a href="{{ route('admin.village-infographic-items.index') }}" class="inline-flex items-center rounded-md bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Kelola Infografis Lainnya
                        </a>
                        <a href="{{ route('admin.head-messages.index') }}" class="inline-flex items-center rounded-md bg-blue-900 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-950">
                            Kelola Sambutan Kades
                        </a>
                        <a href="{{ route('admin.officials.index') }}" class="inline-flex items-center rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                            Kelola Aparatur Desa
                        </a>
                        <a href="{{ route('admin.sliders.index') }}" class="inline-flex items-center rounded-md bg-violet-700 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-800">
                            Kelola Slider Beranda
                        </a>
                        <a href="{{ route('admin.village-map.edit') }}" class="inline-flex items-center rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                            Kelola Map Desa
                        </a>
                        <a href="{{ route('admin.profile-pages.index') }}" class="inline-flex items-center rounded-md bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Kelola Halaman Profil Desa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



