<?php

namespace App\Providers;

use App\Support\ModuleManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('web.*', function ($view): void {
            $view->with('moduleStates', [
                'services' => ModuleManager::isEnabled('services'),
                'complaints' => ModuleManager::isEnabled('complaints'),
                'news' => ModuleManager::isEnabled('news'),
                'agendas' => ModuleManager::isEnabled('agendas'),
                'announcements' => ModuleManager::isEnabled('announcements'),
                'galleries' => ModuleManager::isEnabled('galleries'),
                'transparency' => ModuleManager::isEnabled('transparency'),
                'infographics' => ModuleManager::isEnabled('infographics'),
                'profile' => ModuleManager::isEnabled('profile'),
                'regulations' => ModuleManager::isEnabled('regulations'),
            ]);
        });
    }
}
