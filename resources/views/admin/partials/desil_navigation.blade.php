<li class="menu__item menu__item--dropdown">
    <a href="#" class="profile-dropdown-trigger">Statistik</a>
    <div class="menu__dropdown">
        <a href="{{ route('statistik') }}">Statistik Desa</a>
        @if (\App\Support\ModuleManager::isEnabled('desil'))
            <a href="{{ route('desil.index') }}">Analisis Desil</a>
        @endif
    </div>
</li>
