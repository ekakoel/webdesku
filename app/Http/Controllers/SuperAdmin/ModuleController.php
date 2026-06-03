<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Support\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index(): View
    {
        $modules = collect(ModuleManager::allModules())
            ->map(function (string $label, string $key) {
                return [
                    'key' => $key,
                    'label' => $label,
                    'enabled' => ModuleManager::isEnabled($key),
                ];
            })->values();

        return view('super-admin.modules.index', [
            'modules' => $modules,
        ]);
    }

    public function update(Request $request, string $module): RedirectResponse
    {
        $allModules = ModuleManager::allModules();
        if (!array_key_exists($module, $allModules)) {
            abort(404);
        }

        $validated = $request->validate([
            'is_enabled' => ['required', 'boolean'],
        ]);

        ModuleManager::setEnabled($module, (bool) $validated['is_enabled']);

        return redirect()
            ->route('super-admin.modules.index')
            ->with('status', "Status modul {$allModules[$module]} berhasil diperbarui.");
    }
}
