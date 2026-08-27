<div class="mb-8">
    <div class="inline-flex w-full sm:w-auto rounded-xl border border-slate-200 bg-white p-1 shadow-sm">

        <a
            href="{{ route('statistik') }}"
            class="
                inline-flex items-center justify-center
                gap-2 rounded-lg px-4 py-2.5
                text-sm font-semibold transition
                {{ request()->routeIs('statistik')
                    ? 'bg-blue-700 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                }}
            "
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M3 3v18h18"/>
                <path d="M7 16l4-4 3 3 5-6"/>
            </svg>

            Statistik Desa
        </a>


        <a
            href="{{ route('admin.village-household-welfares.index') }}"
            class="
                inline-flex items-center justify-center
                gap-2 rounded-lg px-4 py-2.5
                text-sm font-semibold transition
                {{ request()->routeIs('admin.village-household-welfares.*', 'admin.village-hamlets.*')
                    ? 'bg-blue-700 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                }}
            "
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M4 19V5"/>
                <path d="M4 19h16"/>
                <path d="M8 16v-4"/>
                <path d="M12 16V8"/>
                <path d="M16 16v-6"/>
            </svg>

            Analisis Desil
        </a>

    </div>
</div>
