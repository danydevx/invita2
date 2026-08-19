<?php

namespace App\Http\Middleware;

use App\Services\FeatureService;
use App\Services\ModuleService;
use App\Services\ModuleVisibilityService;
use App\Services\SystemAnnouncementService;
use App\Services\TemplateRenderService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Modules\Businesses\Models\Business;

class HandleInertiaRequests extends Middleware
{
    public function rootView(Request $request): string
    {
        if ($request->is('b/*')) {
            return 'minisite.minisite';
        }

        return 'app';
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $featureService = app(FeatureService::class);
        $announcements = null;
        if ($user) {
            $templates = app(TemplateRenderService::class);
            $variables = [
                'user_name' => (string) ($user->name ?? ''),
                'user_email' => (string) ($user->email ?? ''),
                'app_name' => (string) config('app.name'),
                'support_email' => (string) (config('mail.from.address') ?? ''),
                'date' => now()->toDateString(),
            ];

            $announcements = app(SystemAnnouncementService::class)
                ->activeForUser($user)
                ->map(fn ($announcement) => [
                    'id' => $announcement->id,
                    'title' => $templates->renderText($announcement->title, $variables),
                    'message' => $templates->renderText($announcement->message, $variables),
                    'type' => $announcement->type,
                    'priority' => $announcement->priority,
                    'dismissible' => $announcement->dismissible,
                    'action_label' => $announcement->action_label,
                    'action_url' => $announcement->action_url,
                ])
                ->values();
        }

        $businessMenu = [];
        if ($user && $user->hasRole('member')) {
            $moduleVisibility = app(ModuleVisibilityService::class);
            $planModuleKeys = $moduleVisibility->getPlanModuleKeysForUser($user);
            $businessMenu = $this->buildBusinessMenu($user, $planModuleKeys, $moduleVisibility);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $user
                    ? $user->getAllPermissions()->pluck('name')->values()
                    : [],
                'roles' => $user
                    ? $user->getRoleNames()->values()
                    : [],
            ],
            'notificationUnreadCount' => $user
                ? $user->notifications()->where('is_read', false)->count()
                : 0,
            'features' => $user ? $featureService->allForUser($user) : [],
            'modules' => app(ModuleService::class)->forFrontend(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'extractResult' => fn () => $request->session()->get('extractResult'),
            ],
            'systemAnnouncements' => $announcements,
            'businessMenu' => $businessMenu,
        ];
    }

    private function buildBusinessMenu($user, array $planModuleKeys = [], $moduleVisibility = null): array
    {
        $businesses = Business::where('user_id', $user->id)
            ->with(['modules.moduleDefinition', 'industry'])
            ->get()
            ->map(fn ($biz) => [
                'id' => $biz->id,
                'name' => $biz->name,
                'slug' => $biz->slug,
                'modules' => $biz->modules
                    ->filter(fn ($m) => $m->is_enabled &&
                        $m->moduleDefinition &&
                        $m->moduleDefinition->show_in_menu &&
                        $m->moduleDefinition->menu_title &&
                        $this->canAccessModule($m, $planModuleKeys, $biz, $moduleVisibility)
                    )
                    ->map(fn ($m) => [
                        'key' => $m->module_key,
                        'title' => $m->moduleDefinition->menu_title,
                        'url' => '/member/businesses/'.$biz->id.'/'.$this->getModulePath($m->module_key),
                    ])->values(),
            ])
            ->filter(fn ($biz) => count($biz['modules']) > 0)
            ->values()
            ->toArray();

        return $businesses;
    }

    private function canAccessModule($module, array $planModuleKeys, $business, $moduleVisibility): bool
    {
        $isPremium = $module->moduleDefinition->is_premium;
        $planHasModule = in_array($module->module_key, $planModuleKeys);

        if ($isPremium && ! $planHasModule) {
            return false;
        }

        if ($business->industry && $moduleVisibility) {
            $industryModuleKeys = $business->industry->moduleDefinitions->pluck('key')->toArray();

            return in_array($module->module_key, $industryModuleKeys);
        }

        return true;
    }

    private function getModulePath(string $moduleKey): string
    {
        $paths = [
            'hero' => 'hero',
            'locations' => 'locations',
            'services' => 'services',
            'products' => 'products',
            'gallery' => 'galleries',
            'appointments' => 'appointments',
            'slots' => 'slots',
            'leads' => 'leads',
            'contact_form' => 'contact-forms',
            'reviews' => 'reviews',
            'promotions' => 'promotions',
            'restaurant_menu' => 'menu-products',
            'socialmedia' => 'social-networks',
            'about' => 'about',
            'features' => 'features',
            'ai_chatbot' => 'ai-chatbot',
            'faqs' => 'faqs',
            'seo' => 'seo',
            'branding' => 'branding',
            'tasks' => 'tasks',
            'clients' => 'clients',
            'minisite' => 'minisite/sections',
            'properties' => 'properties',
            'office_hours' => 'office-hours',
            'client_fidelity' => 'fidelity-cards',
        ];

        return $paths[$moduleKey] ?? $moduleKey;
    }
}
