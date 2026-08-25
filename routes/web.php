<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ApiKeyController as AdminApiKeyController;
use App\Http\Controllers\Admin\AutomationController;
use App\Http\Controllers\Admin\ListingAiChatbotController;
use Modules\ListingAiChatbot\Http\Controllers\Admin\ChatbotPresetController;
use Modules\ListingAiChatbot\Http\Controllers\Admin\ChatbotPersonalityController;
use Modules\ListingAiChatbot\Http\Controllers\Admin\AiChatbotSettingsController;
use App\Http\Controllers\Admin\ListingContactFormController;
use App\Http\Controllers\Admin\ListingContentController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\ListingHeroController;
use App\Http\Controllers\Admin\ListingLeadsController;
use App\Http\Controllers\Admin\ListingModuleController;
use App\Http\Controllers\Admin\ModuleDefinitionController;
use App\Http\Controllers\Admin\ListingPromotionController;
use App\Http\Controllers\Admin\ListingReviewController;
use App\Http\Controllers\Admin\ListingSocialNetworkController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\HelpArticleController;

use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\LegalDocumentController;
use Modules\Locations\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuProductController;
use App\Http\Controllers\Admin\MenuProductImageController;
use App\Http\Controllers\Admin\MenuProductVariantController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\MinisiteThemeController;
use Modules\ListingMinisite\Http\Controllers\Admin\ListingMinisiteController as AdminListingMinisiteController;
use App\Http\Controllers\Admin\ModuleSettingsController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PlanFeatureFlagController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecurityEventController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SlotController as AdminSlotController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\SupportDepartmentController;
use App\Http\Controllers\Admin\SystemAnnouncementController as AdminSystemAnnouncementController;
use App\Http\Controllers\Admin\SystemErrorController;
use App\Http\Controllers\Admin\SystemModuleController;
use App\Http\Controllers\Admin\SystemMonitorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserSubscriptionController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\WebhookController as AdminWebhookController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LegalAcceptanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Wizard\BusinessController as WizardBusinessController;
use App\Http\Controllers\Member\AboutController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\ActivityController as MemberActivityController;
use App\Http\Controllers\Member\ApiKeyController as MemberApiKeyController;
use App\Http\Controllers\Member\AppointmentController;
use App\Http\Controllers\Member\AvailabilityController;
use App\Http\Controllers\Member\BillingController;
use App\Http\Controllers\Member\BrandingController;
use App\Http\Controllers\Member\CheckoutController;
use App\Http\Controllers\Member\ClientController;
use App\Http\Controllers\Member\ContactFormController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\FaqController;
use App\Http\Controllers\Member\FaqCategoryController;
use App\Http\Controllers\Member\GalleryController;
use App\Http\Controllers\Member\GalleryGroupController;
use App\Http\Controllers\Member\HelpArticleController as MemberHelpArticleController;
use App\Http\Controllers\Member\HeroController;
use App\Http\Controllers\Member\IntegrationController;
use App\Http\Controllers\Member\InvoiceController as MemberInvoiceController;
use App\Http\Controllers\Member\LeadController;
use App\Http\Controllers\Member\LocationController;
use App\Http\Controllers\Member\MediaFileController as MemberMediaFileController;
use App\Http\Controllers\Member\MenuCategoryController as MemberMenuCategoryController;
use App\Http\Controllers\Member\MenuProductController as MemberMenuProductController;
use App\Http\Controllers\Member\MenuProductImageController as MemberMenuProductImageController;
use App\Http\Controllers\Member\MenuProductVariantController as MemberMenuProductVariantController;
use App\Http\Controllers\Member\NotificationController;
use App\Http\Controllers\Member\NotificationPreferenceController;
use App\Http\Controllers\Member\OnboardingController;
use App\Http\Controllers\Member\PasswordController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\PlanSelectionController;
use App\Http\Controllers\Member\PreferenceController as MemberPreferenceController;
use App\Http\Controllers\Member\ProductCategoryController as MemberProductCategoryController;
use App\Http\Controllers\Member\ProductController;
use Modules\ListingProducts\Http\Controllers\ListingProductImageController;
use App\Http\Controllers\Member\PromotionController;
use App\Http\Controllers\Member\ReviewController;
use App\Http\Controllers\Member\SeoController;
use App\Http\Controllers\Member\ServiceController;
use App\Http\Controllers\Member\ServiceCategoryController;
use Modules\ListingServices\Http\Controllers\ServiceImageController;
use Modules\Properties\Http\Controllers\Member\PropertyImageController;
use App\Http\Controllers\Member\SessionController as MemberSessionController;
use App\Http\Controllers\Member\SlotController;
use App\Http\Controllers\Member\SocialNetworkController;
use App\Http\Controllers\Member\SupportTicketController as MemberSupportTicketController;
use App\Http\Controllers\Member\SystemAnnouncementController as MemberSystemAnnouncementController;
use App\Http\Controllers\Member\TeamMemberController;
use App\Http\Controllers\Member\TeamMemberPositionController;
use App\Http\Controllers\Member\PackageController;
use App\Http\Controllers\Member\WebhookController as MemberWebhookController;
use Modules\ListingMinisite\Http\Controllers\Member\ListingMinisiteController;
use Modules\ListingMinisite\Http\Controllers\Member\ListingMinisiteSectionController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\Public\BusinessController as PublicBusinessController;
use App\Http\Controllers\Public\DirectoryController;
use App\Http\Controllers\Public\MenuController;
use App\Http\Controllers\Public\PromotionVerificationController;
use App\Http\Controllers\StripeWebhookController;
use App\Services\SettingService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\ListingFeatures\Http\Controllers\Member\FeatureController;
use Modules\ListingFeatures\Http\Controllers\Public\FeatureController as PublicFeatureController;
use Modules\ListingTasks\Http\Controllers\Member\TaskController;

require __DIR__ . '/ai_chatbot.php';
require __DIR__ . '/minisite_ai_chatbot.php';

Route::get('/', [DirectoryController::class, 'index']);

Route::get('/health', HealthController::class)->name('health');

Route::get('/maintenance', function () {
    $settings = app(SettingService::class);

    return Inertia::render('Public/Maintenance/Index', [
        'message' => $settings->get('system.maintenance_message') ?: 'El sistema esta en mantenimiento. Intente nuevamente mas tarde.',
        'title' => $settings->get('system.maintenance_title') ?: 'Mantenimiento en progreso',
    ]);
})->name('maintenance');

Route::get('/login', [LoginController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::post('/pricing/select/{plan}', [PricingController::class, 'select'])->name('pricing.select');
Route::get('/plans', function () {
    return redirect('/pricing');
})->name('plans');

Route::get('/negocios', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/negocios/{slug}', [DirectoryController::class, 'show'])->name('directory.show');
Route::post('/negocios/{slug}/appointment', [DirectoryController::class, 'storeAppointment'])->name('directory.appointment.store');
Route::post('/negocios/{slug}/contact', [DirectoryController::class, 'storeContact'])->name('directory.contact.store');

Route::get('/b/{slug}', [PublicBusinessController::class, 'show'])->name('public.business.show');
Route::get('/b/{slug}/locations', [PublicBusinessController::class, 'locations'])->name('public.business.locations');
Route::get('/b/{slug}/services', [PublicBusinessController::class, 'services'])->name('public.business.services');
Route::get('/b/{slug}/gallery', [PublicBusinessController::class, 'gallery'])->name('public.business.gallery');
Route::get('/b/{slug}/products', [PublicBusinessController::class, 'products'])->name('public.business.products');
Route::get('/b/{slug}/packages', [PublicBusinessController::class, 'packages'])->name('public.business.packages');
Route::get('/b/{slug}/features', [PublicFeatureController::class, 'index'])->name('public.business.features.index');
Route::get('/b/{slug}/book', [PublicBusinessController::class, 'book'])->name('public.business.book');
Route::post('/b/{slug}/book', [PublicBusinessController::class, 'storeBooking'])->name('public.business.booking.store');
Route::get('/b/{slug}/book/success', [PublicBusinessController::class, 'bookingSuccess'])->name('public.business.booking.success');
Route::get('/b/{slug}/contact', [PublicBusinessController::class, 'contact'])->name('public.business.contact');
Route::post('/b/{slug}/contact', [PublicBusinessController::class, 'storeContact'])->name('public.business.contact.store');
Route::get('/b/{slug}/form/{shortcode}', [PublicBusinessController::class, 'formByShortcode'])->name('public.business.form.shortcode');
Route::post('/b/{slug}/form/{shortcode}', [PublicBusinessController::class, 'storeFormByShortcode'])->name('public.business.form.shortcode.store');
Route::get('/b/{slug}/menu', [MenuController::class, 'show'])->name('public.menu.show');
Route::get('/b/{slug}/verify/{promotionId}/{couponCode}', [PromotionVerificationController::class, 'verify'])->name('public.promotion.verify');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::get('/dev/booking-test', function () {
    return view('dev.booking-test');
})->name('dev.booking-test');

Route::get('/register', [RegisterController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

Route::get('/legal/accept', [LegalAcceptanceController::class, 'show'])
    ->middleware('auth')
    ->name('legal.accept');
Route::post('/legal/accept', [LegalAcceptanceController::class, 'store'])
    ->middleware('auth')
    ->name('legal.accept.store');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware(['guest', 'throttle:login'])
    ->name('login.store');

Route::post('/register/wizard', [RegisterController::class, 'storeWizard'])
    ->middleware(['guest', 'throttle:register'])
    ->name('register.wizard.store');

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware(['guest', 'throttle:register'])
    ->name('register.store');

Route::get('/onboarding/business', [WizardBusinessController::class, 'show'])
    ->middleware(['auth'])
    ->name('wizard.business');

Route::post('/onboarding/business', [WizardBusinessController::class, 'store'])
    ->middleware(['auth'])
    ->name('wizard.business.store');

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
    ->middleware('guest')
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->middleware('guest')
    ->name('social.callback');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware(['guest', 'throttle:password-email'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showVerifyCode'])
    ->middleware('guest')
    ->name('password.verify');
Route::post('/reset-password/{token}/verify-code', [PasswordResetController::class, 'verifyCode'])
    ->middleware(['guest', 'throttle:password-verify'])
    ->name('password.verify-code');
Route::get('/reset-password/{token}/new-password', [PasswordResetController::class, 'showResetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
    ->middleware(['guest', 'throttle:password-reset'])
    ->name('password.update');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:email-verify'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:verification-resend'])
    ->name('verification.send');

Route::post('/logout', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/member', fn () => redirect()->route('member.dashboard'))
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member');

Route::get('/member/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.dashboard');

Route::get('/member/business-modules', fn () => redirect()->route('member.listings.index'))
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business-modules.index');
Route::get('/member/listings', [App\Http\Controllers\Member\ListingModuleController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.index');
Route::get('/member/listings/{listing}/modules', [App\Http\Controllers\Member\ListingModulesController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.modules');
Route::get('/member/listings/create', [App\Http\Controllers\Member\BusinessController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.create');
Route::post('/member/listings', [App\Http\Controllers\Member\BusinessController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.store');
Route::get('/member/listings/{listing}/modules', [App\Http\Controllers\Member\ListingModuleController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business-modules.edit');

Route::get('/member/listings/{listing}/edit', [App\Http\Controllers\Member\BusinessController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.edit');
Route::put('/member/listings/{listing}', [App\Http\Controllers\Member\BusinessController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.update');

Route::get('/member/listings/{listing}/locations', [LocationController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.index');
Route::get('/member/listings/{listing}/locations/create', [LocationController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.create');
Route::post('/member/listings/{listing}/locations', [LocationController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.store');
Route::get('/member/listings/{listing}/locations/{location}/edit', [LocationController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.edit');
Route::put('/member/listings/{listing}/locations/{location}', [LocationController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.update');
Route::delete('/member/listings/{listing}/locations/{location}', [LocationController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.destroy');
Route::post('/member/listings/{listing}/locations/bulk-delete', [LocationController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.locations.bulk-delete');

Route::get('/member/listings/{listing}/services', [ServiceController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.index');
Route::get('/member/listings/{listing}/services/create', [ServiceController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.create');
Route::post('/member/listings/{listing}/services', [ServiceController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.store');
Route::get('/member/listings/{listing}/services/{service}/edit', [ServiceController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.edit');
Route::put('/member/listings/{listing}/services/{service}', [ServiceController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.update');
Route::delete('/member/listings/{listing}/services/{service}', [ServiceController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.destroy');
Route::post('/member/listings/{listing}/services/{service}/clone', [ServiceController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.clone');
Route::post('/member/listings/{listing}/services/reorder', [ServiceController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.reorder');
Route::post('/member/listings/{listing}/services/bulk-delete', [ServiceController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.bulk-delete');
Route::post('/member/listings/{listing}/services/{service}/images', [ServiceImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.images.store');
Route::delete('/member/listings/{listing}/services/{service}/images/{image}', [ServiceImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.services.images.destroy');

Route::get('/member/listings/{listing}/service-categories', [ServiceCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.service-categories.index');
Route::post('/member/listings/{listing}/service-categories', [ServiceCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.service-categories.store');
Route::put('/member/listings/{listing}/service-categories/{category}', [ServiceCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.service-categories.update');
Route::delete('/member/listings/{listing}/service-categories/{category}', [ServiceCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.service-categories.destroy');

Route::post('/member/listings/{listing}/properties/{property}/images', [PropertyImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.properties.images.store');
Route::delete('/member/listings/{listing}/properties/{property}/images/{image}', [PropertyImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.properties.images.destroy');
Route::put('/member/listings/{listing}/properties/{property}/images/{image}/set-main', [PropertyImageController::class, 'setMain'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.properties.images.set-main');

Route::get('/member/listings/{listing}/team-members', [TeamMemberController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.index');
Route::get('/member/listings/{listing}/team-members/create', [TeamMemberController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.create');
Route::post('/member/listings/{listing}/team-members', [TeamMemberController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.store');
Route::get('/member/listings/{listing}/team-members/{member}/edit', [TeamMemberController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.edit');
Route::post('/member/listings/{listing}/team-members/{member}', [TeamMemberController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.update');
Route::delete('/member/listings/{listing}/team-members/{member}', [TeamMemberController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.destroy');
Route::post('/member/listings/{listing}/team-members/reorder', [TeamMemberController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.reorder');
Route::post('/member/listings/{listing}/team-members/bulk-delete', [TeamMemberController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-members.bulk-delete');

Route::get('/member/listings/{listing}/team-member-positions', [TeamMemberPositionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.index');
Route::get('/member/listings/{listing}/team-member-positions/create', [TeamMemberPositionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.create');
Route::post('/member/listings/{listing}/team-member-positions', [TeamMemberPositionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.store');
Route::post('/member/listings/{listing}/team-member-positions/reorder', [TeamMemberPositionController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.reorder');
Route::get('/member/listings/{listing}/team-member-positions/{position}/edit', [TeamMemberPositionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.edit');
Route::put('/member/listings/{listing}/team-member-positions/{position}', [TeamMemberPositionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.update');
Route::delete('/member/listings/{listing}/team-member-positions/{position}', [TeamMemberPositionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.team-member-positions.destroy');

Route::get('/member/listings/{listing}/packages', [PackageController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.index');
Route::get('/member/listings/{listing}/packages/create', [PackageController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.create');
Route::post('/member/listings/{listing}/packages', [PackageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.store');
Route::post('/member/listings/{listing}/packages/reorder', [PackageController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.reorder');
Route::post('/member/listings/{listing}/packages/bulk-delete', [PackageController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.bulk-delete');
Route::get('/member/listings/{listing}/packages/{package}/edit', [PackageController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.edit');
Route::post('/member/listings/{listing}/packages/{package}', [PackageController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.update');
Route::delete('/member/listings/{listing}/packages/{package}', [PackageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.destroy');
Route::post('/member/listings/{listing}/packages/{package}/clone', [PackageController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.packages.clone');

Route::get('/member/listings/{listing}/faqs', [FaqController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.index');
Route::get('/member/listings/{listing}/faqs/create', [FaqController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.create');
Route::post('/member/listings/{listing}/faqs', [FaqController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.store');
Route::get('/member/listings/{listing}/faqs/{faq}/edit', [FaqController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.edit');
Route::put('/member/listings/{listing}/faqs/{faq}', [FaqController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.update');
Route::delete('/member/listings/{listing}/faqs/{faq}', [FaqController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.destroy');
Route::post('/member/listings/{listing}/faqs/reorder', [FaqController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.reorder');
Route::post('/member/listings/{listing}/faqs/bulk-delete', [FaqController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.bulk-delete');
Route::post('/member/listings/{listing}/faqs/{faq}/clone', [FaqController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faqs.clone');

Route::get('/member/listings/{listing}/faq-categories', [FaqCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faq-categories.index');
Route::post('/member/listings/{listing}/faq-categories', [FaqCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faq-categories.store');
Route::put('/member/listings/{listing}/faq-categories/{category}', [FaqCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faq-categories.update');
Route::delete('/member/listings/{listing}/faq-categories/{category}', [FaqCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.faq-categories.destroy');

Route::get('/member/listings/{listing}/minisite', [ListingMinisiteController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.index');
Route::post('/member/listings/{listing}/minisite', [ListingMinisiteController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.store');
Route::put('/member/listings/{listing}/minisite', [ListingMinisiteController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.update');

Route::get('/member/listings/{listing}/minisite/sections', [ListingMinisiteSectionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.index');
Route::get('/member/listings/{listing}/minisite/sections/create', [ListingMinisiteSectionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.create');
Route::post('/member/listings/{listing}/minisite/sections', [ListingMinisiteSectionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.store');
Route::get('/member/listings/{listing}/minisite/sections/{section}/edit', [ListingMinisiteSectionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.edit');
Route::put('/member/listings/{listing}/minisite/sections/{section}', [ListingMinisiteSectionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.update');
Route::delete('/member/listings/{listing}/minisite/sections/{section}', [ListingMinisiteSectionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.destroy');
Route::post('/member/listings/{listing}/minisite/sections/reorder', [ListingMinisiteSectionController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.minisite.sections.reorder');

Route::get('/member/listings/{listing}/seo', [SeoController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.seo.index');
Route::post('/member/listings/{listing}/seo', [SeoController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.seo.update');

Route::get('/member/listings/{listing}/branding', [BrandingController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.branding.index');
Route::post('/member/listings/{listing}/branding', [BrandingController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.branding.update');

Route::get('/member/listings/{listing}/hero', [HeroController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.hero.index');
Route::post('/member/listings/{listing}/hero', [HeroController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.hero.update');

Route::get('/member/listings/{listing}/about', [AboutController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.about.index');
Route::post('/member/listings/{listing}/about', [AboutController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.about.update');

Route::get('/member/listings/{listing}/social-networks', [SocialNetworkController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.social-networks.index');
Route::post('/member/listings/{listing}/social-networks', [SocialNetworkController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.social-networks.store');
Route::post('/member/listings/{listing}/social-networks/reorder', [SocialNetworkController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.social-networks.reorder');
Route::post('/member/listings/{listing}/social-networks/{socialNetwork}', [SocialNetworkController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.social-networks.update');
Route::delete('/member/listings/{listing}/social-networks/{socialNetwork}', [SocialNetworkController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.social-networks.destroy');

Route::get('/member/listings/{listing}/tasks', [TaskController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.index');
Route::post('/member/listings/{listing}/tasks', [TaskController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.store');
Route::put('/member/listings/{listing}/tasks/{task}', [TaskController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.update');
Route::delete('/member/listings/{listing}/tasks/{task}', [TaskController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.destroy');
Route::post('/member/listings/{listing}/tasks/{task}/archive', [TaskController::class, 'archive'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.archive');
Route::post('/member/listings/{listing}/tasks/reorder', [TaskController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.tasks.reorder');

Route::get('/member/listings/{listing}/clients', [ClientController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.index');
Route::get('/member/listings/{listing}/clients/create', [ClientController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.create');
Route::post('/member/listings/{listing}/clients', [ClientController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.store');
Route::get('/member/listings/{listing}/clients/{client}/edit', [ClientController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.edit');
Route::put('/member/listings/{listing}/clients/{client}', [ClientController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.update');
Route::delete('/member/listings/{listing}/clients/{client}', [ClientController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.destroy');
Route::post('/member/listings/{listing}/clients/bulk-delete', [ClientController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.bulk-delete');
Route::post('/member/listings/{listing}/clients/{client}/clone', [ClientController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.clients.clone');

Route::get('/member/listings/{listing}/galleries', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.index');
Route::get('/member/listings/{listing}/galleries/create', [GalleryGroupController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.create');
Route::post('/member/listings/{listing}/galleries', [GalleryGroupController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.store');
Route::get('/member/listings/{listing}/galleries/{gallery}/edit', [GalleryGroupController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.edit');
Route::put('/member/listings/{listing}/galleries/{gallery}', [GalleryGroupController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.update');
Route::delete('/member/listings/{listing}/galleries/{gallery}', [GalleryGroupController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.destroy');
Route::post('/member/listings/{listing}/galleries/{gallery}/set-primary', [GalleryGroupController::class, 'setPrimary'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.galleries.set-primary');

Route::get('/member/listings/{listing}/gallery', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.index.legacy');
Route::get('/member/listings/{listing}/gallery/{gallery}', [GalleryController::class, 'show'])
    ->where('gallery', '[0-9]+')
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.show.legacy');
Route::get('/member/listings/{listing}/gallery', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.index.legacy');
Route::get('/member/listings/{listing}/gallery/{gallery}', [GalleryController::class, 'show'])
    ->where('gallery', '[0-9]+')
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.show.legacy');
Route::post('/member/listings/{listing}/gallery', [GalleryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.store');
Route::put('/member/listings/{listing}/gallery/{image}', [GalleryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.update');
Route::delete('/member/listings/{listing}/gallery/{image}', [GalleryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.destroy');
Route::post('/member/listings/{listing}/gallery/reorder', [GalleryController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.reorder');
Route::post('/member/listings/{listing}/gallery/bulk-delete', [GalleryController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.gallery.bulk-delete');

Route::get('/member/listings/{listing}/products', [ProductController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.index');
Route::get('/member/listings/{listing}/products/create', [ProductController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.create');
Route::post('/member/listings/{listing}/products', [ProductController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.store');
Route::get('/member/listings/{listing}/products/{product}/edit', [ProductController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.edit');
Route::put('/member/listings/{listing}/products/{product}', [ProductController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.update');
Route::delete('/member/listings/{listing}/products/{product}', [ProductController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.destroy');
Route::post('/member/listings/{listing}/products/{product}/clone', [ProductController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.clone');
Route::post('/member/listings/{listing}/products/reorder', [ProductController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.reorder');
Route::post('/member/listings/{listing}/products/bulk-delete', [ProductController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.bulk-delete');
Route::post('/member/listings/{listing}/products/{product}/images', [ListingProductImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.images.store');
Route::delete('/member/listings/{listing}/products/{product}/images/{image}', [ListingProductImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.products.images.destroy');

Route::get('/member/listings/{listing}/appointments', [AppointmentController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.index');
Route::get('/member/listings/{listing}/appointments/create', [AppointmentController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.create');
Route::post('/member/listings/{listing}/appointments', [AppointmentController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.store');

Route::get('/member/listings/{listing}/appointments/availability', [AvailabilityController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.availability');
Route::put('/member/listings/{listing}/appointments/availability/weekly', [AvailabilityController::class, 'updateWeekly'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.availability.weekly');
Route::post('/member/listings/{listing}/appointments/availability/exceptions', [AvailabilityController::class, 'storeException'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.availability.exceptions.store');
Route::delete('/member/listings/{listing}/appointments/availability/exceptions/{exception}', [AvailabilityController::class, 'destroyException'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.availability.exceptions.destroy');

Route::get('/member/listings/{listing}/appointments/{appointment}', [AppointmentController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.show');
Route::get('/member/listings/{listing}/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.edit');
Route::put('/member/listings/{listing}/appointments/{appointment}', [AppointmentController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.update');
Route::delete('/member/listings/{listing}/appointments/{appointment}', [AppointmentController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.destroy');
Route::post('/member/listings/{listing}/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.cancel');
Route::put('/member/listings/{listing}/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.reschedule');
Route::post('/member/listings/{listing}/appointments/bulk-delete', [AppointmentController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.appointments.bulk-delete');

Route::get('/member/listings/{listing}/slots', [SlotController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.slots.index');
Route::post('/member/listings/{listing}/slots', [SlotController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.slots.store');
Route::put('/member/listings/{listing}/slots/{slot}', [SlotController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.slots.update');
Route::delete('/member/listings/{listing}/slots/{slot}', [SlotController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.slots.destroy');

Route::get('/member/listings/{listing}/leads', [LeadController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.index');
Route::get('/member/listings/{listing}/leads/create', [LeadController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.create');
Route::post('/member/listings/{listing}/leads', [LeadController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.store');
Route::get('/member/listings/{listing}/leads/export', [LeadController::class, 'export'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.export');
Route::get('/member/listings/{listing}/leads/{lead}', [LeadController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.show');
Route::get('/member/listings/{listing}/leads/{lead}/edit', [LeadController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.edit');
Route::put('/member/listings/{listing}/leads/{lead}', [LeadController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.update');
Route::delete('/member/listings/{listing}/leads/{lead}', [LeadController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.destroy');
Route::post('/member/listings/{listing}/leads/bulk-delete', [LeadController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.leads.bulk-delete');

Route::get('/member/listings/{listing}/contact-forms', [ContactFormController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.index');
Route::get('/member/listings/{listing}/contact-forms/create', [ContactFormController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.create');
Route::post('/member/listings/{listing}/contact-forms', [ContactFormController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.store');
Route::get('/member/listings/{listing}/contact-forms/{form}/edit', [ContactFormController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.edit');
Route::put('/member/listings/{listing}/contact-forms/{form}', [ContactFormController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.update');
Route::delete('/member/listings/{listing}/contact-forms/{form}', [ContactFormController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.destroy');
Route::post('/member/listings/{listing}/contact-forms/{form}/fields', [ContactFormController::class, 'storeField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.store');
Route::put('/member/listings/{listing}/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'updateField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.update');
Route::delete('/member/listings/{listing}/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'destroyField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.destroy');
Route::post('/member/listings/{listing}/contact-forms/{form}/reorder', [ContactFormController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.reorder');
Route::get('/member/listings/{listing}/contact-forms/{form}/submissions', [ContactFormController::class, 'submissions'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.submissions');
Route::get('/member/listings/{listing}/contact-forms/export', [ContactFormController::class, 'export'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.export');
Route::get('/member/listings/{listing}/contact-forms/{form}/preview', [ContactFormController::class, 'preview'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.preview');

Route::get('/member/listings/{listing}/reviews', [ReviewController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.index');
Route::get('/member/listings/{listing}/reviews/create', [ReviewController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.create');
Route::post('/member/listings/{listing}/reviews', [ReviewController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.store');
Route::post('/member/listings/{listing}/reviews/reorder', [ReviewController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.reorder');
Route::post('/member/listings/{listing}/reviews/bulk-delete', [ReviewController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.bulk-delete');
Route::post('/member/listings/{listing}/reviews/{review}/clone', [ReviewController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.clone');
Route::get('/member/listings/{listing}/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.edit');
Route::put('/member/listings/{listing}/reviews/{review}', [ReviewController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.update');
Route::delete('/member/listings/{listing}/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.reviews.destroy');

Route::get('/member/listings/{listing}/promotions', [PromotionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.index');
Route::get('/member/listings/{listing}/promotions/create', [PromotionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.create');
Route::post('/member/listings/{listing}/promotions', [PromotionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.store');
Route::get('/member/listings/{listing}/promotions/{promotion}/edit', [PromotionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.edit');
Route::put('/member/listings/{listing}/promotions/{promotion}', [PromotionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.update');
Route::delete('/member/listings/{listing}/promotions/{promotion}', [PromotionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.destroy');
Route::post('/member/listings/{listing}/promotions/reorder', [PromotionController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.reorder');
Route::post('/member/listings/{listing}/promotions/bulk-delete', [PromotionController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.bulk-delete');
Route::post('/member/listings/{listing}/promotions/{promotion}/clone', [PromotionController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.clone');
Route::post('/member/listings/{listing}/promotions/{promotion}/regenerate-qr', [PromotionController::class, 'regenerateQrCode'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.promotions.regenerate-qr');

Route::get('/member/listings/{listing}/features', [FeatureController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.index');
Route::post('/member/listings/{listing}/features', [FeatureController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.store');
Route::post('/member/listings/{listing}/features/import', [FeatureController::class, 'importBulk'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.import-bulk');
Route::post('/member/listings/{listing}/features/import/{feature}', [FeatureController::class, 'import'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.import');
Route::put('/member/listings/{listing}/features/{feature}', [FeatureController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.update');
Route::delete('/member/listings/{listing}/features/{feature}', [FeatureController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.destroy');
Route::put('/member/listings/{listing}/feature-assignments', [FeatureController::class, 'updateAssignment'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.feature-assignments.update');
Route::delete('/member/listings/{listing}/feature-assignments/{assignment}', [FeatureController::class, 'removeAssignment'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.feature-assignments.remove');
Route::post('/member/listings/{listing}/features/reorder', [FeatureController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.listings.features.reorder');

Route::get('/member/listings/{listing}/menu-categories', [MemberMenuCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.index');
Route::post('/member/listings/{listing}/menu-categories', [MemberMenuCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.store');
Route::put('/member/listings/{listing}/menu-categories/{category}', [MemberMenuCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.update');
Route::delete('/member/listings/{listing}/menu-categories/{category}', [MemberMenuCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.destroy');

Route::get('/member/listings/{listing}/product-categories', [MemberProductCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.index');
Route::post('/member/listings/{listing}/product-categories', [MemberProductCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.store');
Route::put('/member/listings/{listing}/product-categories/{category}', [MemberProductCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.update');
Route::delete('/member/listings/{listing}/product-categories/{category}', [MemberProductCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.destroy');

Route::get('/member/listings/{listing}/menu-products', [MemberMenuProductController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.index');
Route::get('/member/listings/{listing}/menu-products/create', [MemberMenuProductController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.create');
Route::post('/member/listings/{listing}/menu-products', [MemberMenuProductController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.store');
Route::post('/member/listings/{listing}/menu-products/reorder', [MemberMenuProductController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.reorder');
Route::post('/member/listings/{listing}/menu-products/bulk-delete', [MemberMenuProductController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.bulk-delete');
Route::get('/member/listings/{listing}/menu-products/{product}/edit', [MemberMenuProductController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.edit');
Route::put('/member/listings/{listing}/menu-products/{product}', [MemberMenuProductController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.update');
Route::delete('/member/listings/{listing}/menu-products/{product}', [MemberMenuProductController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.destroy');
Route::post('/member/listings/{listing}/menu-products/{product}/clone', [MemberMenuProductController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.clone');
Route::post('/member/listings/{listing}/menu-products/{product}/variants', [MemberMenuProductVariantController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.store');
Route::put('/member/listings/{listing}/menu-products/{product}/variants/{variant}', [MemberMenuProductVariantController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.update');
Route::delete('/member/listings/{listing}/menu-products/{product}/variants/{variant}', [MemberMenuProductVariantController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.destroy');
Route::post('/member/listings/{listing}/menu-products/{product}/images', [MemberMenuProductImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.store');
Route::put('/member/listings/{listing}/menu-products/{product}/images/{image}', [MemberMenuProductImageController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.update');
Route::delete('/member/listings/{listing}/menu-products/{product}/images/{image}', [MemberMenuProductImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.destroy');

Route::get('/member/listings/{listing}/minisite-theme', [MinisiteThemeController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.minisite-theme.index');
Route::put('/member/listings/{listing}/minisite-theme/{theme}', [MinisiteThemeController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.minisite-theme.update');

Route::get('/member/account', [AccountController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.account.show');

Route::post('/member/billing/portal', [BillingController::class, 'portal'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing', 'throttle:billing-portal'])
    ->name('member.billing.portal');

Route::post('/member/checkout/{plan}', [CheckoutController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing', 'throttle:checkout-create'])
    ->name('member.checkout.create');
Route::post('/member/checkout/coupon/validate', [CheckoutController::class, 'validateCoupon'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing', 'throttle:checkout-coupon'])
    ->name('member.checkout.coupon.validate');
Route::put('/member/checkout/coupon/clear', [CheckoutController::class, 'clearCoupon'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.checkout.coupon.clear');
Route::get('/member/checkout/success', [CheckoutController::class, 'success'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.checkout.success');
Route::get('/member/checkout/cancel', [CheckoutController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.checkout.cancel');

Route::get('/member/plan-selection', [PlanSelectionController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.plan-selection.show');

Route::get('/member/integrations', [IntegrationController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:integrations'])
    ->name('member.integrations.index');
Route::get('/member/integrations/docs', [IntegrationController::class, 'apiDocumentation'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:integrations'])
    ->name('member.integrations.docs');
Route::put('/member/plan-selection/clear', [PlanSelectionController::class, 'clear'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.plan-selection.clear');

Route::get('/member/profile', [UserProfileController::class, 'editMember'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.profile.edit');

Route::get('/member/password', [PasswordController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.password.edit');
Route::put('/member/password', [PasswordController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.password.update');

Route::put('/member/onboarding/complete', [OnboardingController::class, 'complete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.onboarding.complete');

Route::get('/member/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notifications.index');
Route::get('/member/notifications/unread-count', [NotificationController::class, 'unreadCount'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notifications.unread-count');
Route::put('/member/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notifications.read');
Route::put('/member/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notifications.read-all');

Route::get('/member/announcements/active', [MemberSystemAnnouncementController::class, 'active'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:announcements'])
    ->name('member.announcements.active');
Route::put('/member/announcements/{announcement}/dismiss', [MemberSystemAnnouncementController::class, 'dismiss'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:announcements'])
    ->name('member.announcements.dismiss');

Route::get('/member/notification-preferences', [NotificationPreferenceController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notification-preferences.edit');
Route::put('/member/notification-preferences', [NotificationPreferenceController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:notifications'])
    ->name('member.notification-preferences.update');

Route::get('/member/activity', [MemberActivityController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:activity'])
    ->name('member.activity.index');

Route::get('/member/payments', [MemberPaymentController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.payments.index');

Route::get('/member/invoices', [MemberInvoiceController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.invoices.index');
Route::get('/member/invoices/{invoice}', [MemberInvoiceController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.invoices.show');
Route::get('/member/invoices/{invoice}/download', [MemberInvoiceController::class, 'download'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:billing'])
    ->name('member.invoices.download');

Route::get('/member/support', [MemberSupportTicketController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support'])
    ->name('member.support.index');
Route::get('/member/support/create', [MemberSupportTicketController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support'])
    ->name('member.support.create');
Route::post('/member/support', [MemberSupportTicketController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support', 'throttle:ticket-create'])
    ->name('member.support.store');
Route::get('/member/support/{ticket}', [MemberSupportTicketController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support'])
    ->name('member.support.show');
Route::post('/member/support/{ticket}/reply', [MemberSupportTicketController::class, 'reply'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support', 'throttle:ticket-reply'])
    ->name('member.support.reply');

Route::get('/member/api-keys', [MemberApiKeyController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage', 'module:api'])
    ->name('member.api-keys.index');
Route::post('/member/api-keys', [MemberApiKeyController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage', 'module:api', 'throttle:api-keys-create'])
    ->name('member.api-keys.store');
Route::put('/member/api-keys/{apiKey}', [MemberApiKeyController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage', 'module:api'])
    ->name('member.api-keys.update');
Route::delete('/member/api-keys/{apiKey}', [MemberApiKeyController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:api-keys.manage', 'module:api'])
    ->name('member.api-keys.destroy');

Route::get('/member/webhooks', [MemberWebhookController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.index');
Route::post('/member/webhooks', [MemberWebhookController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.store');
Route::put('/member/webhooks/{webhook}', [MemberWebhookController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.update');
Route::delete('/member/webhooks/{webhook}', [MemberWebhookController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.destroy');
Route::post('/member/webhooks/{webhook}/test', [MemberWebhookController::class, 'test'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.test');
Route::post('/member/webhooks/{webhook}/regenerate-secret', [MemberWebhookController::class, 'regenerateSecret'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.regenerate-secret');
Route::get('/member/webhooks/{webhook}/deliveries', [MemberWebhookController::class, 'deliveries'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.deliveries');
Route::post('/member/webhooks/deliveries/{delivery}/retry', [MemberWebhookController::class, 'retryDelivery'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'permission:webhooks.manage', 'module:webhooks'])
    ->name('member.webhooks.deliveries.retry');

Route::get('/member/help', [MemberHelpArticleController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support'])
    ->name('member.help.index');
Route::get('/member/help/{slug}', [MemberHelpArticleController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:support'])
    ->name('member.help.show');

Route::get('/member/preferences', [MemberPreferenceController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.preferences.edit');
Route::put('/member/preferences', [MemberPreferenceController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.preferences.update');

Route::get('/member/files', [MemberMediaFileController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:media'])
    ->name('member.files.index');
Route::post('/member/files', [MemberMediaFileController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:media'])
    ->name('member.files.store');
Route::get('/member/files/{file}', [MemberMediaFileController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:media'])
    ->name('member.files.show');
Route::get('/member/files/{file}/download', [MemberMediaFileController::class, 'download'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:media'])
    ->name('member.files.download');
Route::delete('/member/files/{file}', [MemberMediaFileController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member', 'module:media'])
    ->name('member.files.destroy');

Route::get('/member/sessions', [MemberSessionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.index');
Route::delete('/member/sessions/others', [MemberSessionController::class, 'destroyOthers'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.destroy-others');
Route::delete('/member/sessions/{session}', [MemberSessionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.sessions.destroy');

Route::get('/profile', [UserProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');
Route::post('/profile', [UserProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::get('/admin/profile', [UserProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('admin.profile.edit');
Route::post('/admin/profile', [UserProfileController::class, 'update'])
    ->middleware('auth')
    ->name('admin.profile.update');

Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'admin_or_user:1'])
    ->name('admin.dashboard');

Route::get('/admin/api-explorer', [App\Http\Controllers\Admin\ApiExplorerController::class, 'index'])
    ->middleware(['auth', 'admin_or_user:1'])
    ->name('admin.api-explorer.index');

Route::post('/admin/api-explorer/fetch', [App\Http\Controllers\Admin\ApiExplorerController::class, 'fetch'])
    ->middleware(['auth', 'admin_or_user:1'])
    ->name('admin.api-explorer.fetch');

Route::prefix('admin')->middleware(['auth', 'admin_or_user:1'])->group(function () {

    Route::get('/locations', [AdminLocationController::class, 'index'])
        ->name('admin.locations.index');
    Route::get('/locations/countries', [AdminLocationController::class, 'countriesIndex'])
        ->name('admin.locations.countries.index');
    Route::post('/locations/countries', [AdminLocationController::class, 'countriesStore'])
        ->name('admin.locations.countries.store');
    Route::put('/locations/countries/{country}', [AdminLocationController::class, 'countriesUpdate'])
        ->name('admin.locations.countries.update');

    Route::get('/locations/states', [AdminLocationController::class, 'statesIndex'])
        ->name('admin.locations.states.index');
    Route::post('/locations/states', [AdminLocationController::class, 'statesStore'])
        ->name('admin.locations.states.store');
    Route::put('/locations/states/{state}', [AdminLocationController::class, 'statesUpdate'])
        ->name('admin.locations.states.update');

    Route::get('/locations/municipalities', [AdminLocationController::class, 'municipalitiesIndex'])
        ->name('admin.locations.municipalities.index');
    Route::post('/locations/municipalities', [AdminLocationController::class, 'municipalitiesStore'])
        ->name('admin.locations.municipalities.store');
    Route::put('/locations/municipalities/{municipality}', [AdminLocationController::class, 'municipalitiesUpdate'])
        ->name('admin.locations.municipalities.update');

    Route::get('/listings/{listing}/modules', [ListingModuleController::class, 'edit'])
        ->name('admin.business-modules.edit');
    Route::put('/listings/{listing}/modules', [ListingModuleController::class, 'update'])
        ->name('admin.business-modules.update');

    Route::get('/listings', [ListingController::class, 'index'])
        ->name('admin.listings.index');
    Route::get('/listings/create', [ListingController::class, 'create'])
        ->name('admin.listings.create');
    Route::post('/listings', [ListingController::class, 'store'])
        ->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])
        ->name('admin.listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])
        ->name('admin.listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])
        ->name('admin.listings.destroy');

    Route::get('/listings/{listing}/hero', [ListingHeroController::class, 'index'])
        ->name('admin.business.hero.index');
    Route::post('/listings/{listing}/hero', [ListingHeroController::class, 'update'])
        ->name('admin.business.hero.update');

    Route::get('/listings/{listing}/social-networks', [ListingSocialNetworkController::class, 'index'])
        ->name('admin.business.social-networks.index');
    Route::post('/listings/{listing}/social-networks', [ListingSocialNetworkController::class, 'store'])
        ->name('admin.business.social-networks.store');
    Route::post('/listings/{listing}/social-networks/{socialNetwork}', [ListingSocialNetworkController::class, 'update'])
        ->name('admin.business.social-networks.update');
    Route::delete('/listings/{listing}/social-networks/{socialNetwork}', [ListingSocialNetworkController::class, 'destroy'])
        ->name('admin.business.social-networks.destroy');

    Route::get('/listings/{listing}/locations', [ListingContentController::class, 'locationsIndex'])
        ->name('admin.business.locations.index');
    Route::get('/listings/{listing}/locations/create', [ListingContentController::class, 'locationsCreate'])
        ->name('admin.business.locations.create');
    Route::post('/listings/{listing}/locations', [ListingContentController::class, 'locationsStore'])
        ->name('admin.business.locations.store');
    Route::get('/listings/{listing}/locations/{location}/edit', [ListingContentController::class, 'locationsEdit'])
        ->name('admin.business.locations.edit');
    Route::put('/listings/{listing}/locations/{location}', [ListingContentController::class, 'locationsUpdate'])
        ->name('admin.business.locations.update');
    Route::delete('/listings/{listing}/locations/{location}', [ListingContentController::class, 'locationsDestroy'])
        ->name('admin.business.locations.destroy');

    Route::get('/listings/{listing}/services', [ListingContentController::class, 'servicesIndex'])
        ->name('admin.business.services.index');
    Route::get('/listings/{listing}/services/create', [ListingContentController::class, 'servicesCreate'])
        ->name('admin.business.services.create');
    Route::post('/listings/{listing}/services', [ListingContentController::class, 'servicesStore'])
        ->name('admin.business.services.store');
    Route::get('/listings/{listing}/services/{service}/edit', [ListingContentController::class, 'servicesEdit'])
        ->name('admin.business.services.edit');
    Route::put('/listings/{listing}/services/{service}', [ListingContentController::class, 'servicesUpdate'])
        ->name('admin.business.services.update');
    Route::delete('/listings/{listing}/services/{service}', [ListingContentController::class, 'servicesDestroy'])
        ->name('admin.business.services.destroy');
    Route::post('/listings/{listing}/services/{service}/images', [ServiceImageController::class, 'store'])
        ->name('admin.business.services.images.store');
    Route::delete('/listings/{listing}/services/{service}/images/{image}', [ServiceImageController::class, 'destroy'])
        ->name('admin.business.services.images.destroy');

    Route::get('/listings/{listing}/service-categories', [ListingContentController::class, 'serviceCategoriesIndex'])
        ->name('admin.business.service-categories.index');
    Route::post('/listings/{listing}/service-categories', [ListingContentController::class, 'serviceCategoriesStore'])
        ->name('admin.business.service-categories.store');
    Route::put('/listings/{listing}/service-categories/{category}', [ListingContentController::class, 'serviceCategoriesUpdate'])
        ->name('admin.business.service-categories.update');
    Route::delete('/listings/{listing}/service-categories/{category}', [ListingContentController::class, 'serviceCategoriesDestroy'])
        ->name('admin.business.service-categories.destroy');

    Route::get('/listings/{listing}/faqs', [ListingContentController::class, 'faqsIndex'])
        ->name('admin.business.faqs.index');
    Route::get('/listings/{listing}/faqs/create', [ListingContentController::class, 'faqsCreate'])
        ->name('admin.business.faqs.create');
    Route::post('/listings/{listing}/faqs', [ListingContentController::class, 'faqsStore'])
        ->name('admin.business.faqs.store');
    Route::get('/listings/{listing}/faqs/{faq}/edit', [ListingContentController::class, 'faqsEdit'])
        ->name('admin.business.faqs.edit');
    Route::put('/listings/{listing}/faqs/{faq}', [ListingContentController::class, 'faqsUpdate'])
        ->name('admin.business.faqs.update');
    Route::delete('/listings/{listing}/faqs/{faq}', [ListingContentController::class, 'faqsDestroy'])
        ->name('admin.business.faqs.destroy');

    Route::get('/listings/{listing}/faq-categories', [ListingContentController::class, 'faqCategoriesIndex'])
        ->name('admin.business.faq-categories.index');
    Route::post('/listings/{listing}/faq-categories', [ListingContentController::class, 'faqCategoriesStore'])
        ->name('admin.business.faq-categories.store');
    Route::put('/listings/{listing}/faq-categories/{category}', [ListingContentController::class, 'faqCategoriesUpdate'])
        ->name('admin.business.faq-categories.update');
    Route::delete('/listings/{listing}/faq-categories/{category}', [ListingContentController::class, 'faqCategoriesDestroy'])
        ->name('admin.business.faq-categories.destroy');

    Route::get('/listings/{listing}/products', [ListingContentController::class, 'productsIndex'])
        ->name('admin.business.products.index');
    Route::get('/listings/{listing}/products/create', [ListingContentController::class, 'productsCreate'])
        ->name('admin.business.products.create');
    Route::post('/listings/{listing}/products', [ListingContentController::class, 'productsStore'])
        ->name('admin.business.products.store');
    Route::get('/listings/{listing}/products/{product}/edit', [ListingContentController::class, 'productsEdit'])
        ->name('admin.business.products.edit');
    Route::put('/listings/{listing}/products/{product}', [ListingContentController::class, 'productsUpdate'])
        ->name('admin.business.products.update');
    Route::delete('/listings/{listing}/products/{product}', [ListingContentController::class, 'productsDestroy'])
        ->name('admin.business.products.destroy');

    Route::get('/listings/{listing}/product-categories', [ListingContentController::class, 'productCategoriesIndex'])
        ->name('admin.business.product-categories.index');
    Route::post('/listings/{listing}/product-categories', [ListingContentController::class, 'productCategoriesStore'])
        ->name('admin.business.product-categories.store');
    Route::put('/listings/{listing}/product-categories/{category}', [ListingContentController::class, 'productCategoriesUpdate'])
        ->name('admin.business.product-categories.update');
    Route::delete('/listings/{listing}/product-categories/{category}', [ListingContentController::class, 'productCategoriesDestroy'])
        ->name('admin.business.product-categories.destroy');

    Route::get('/listings/{listing}/galleries', [ListingContentController::class, 'galleriesIndex'])
        ->name('admin.business.galleries.index');
    Route::get('/listings/{listing}/galleries/create', [ListingContentController::class, 'galleriesCreate'])
        ->name('admin.business.galleries.create');
    Route::post('/listings/{listing}/galleries', [ListingContentController::class, 'galleriesStore'])
        ->name('admin.business.galleries.store');
    Route::get('/listings/{listing}/galleries/{gallery}/edit', [ListingContentController::class, 'galleriesEdit'])
        ->name('admin.business.galleries.edit');
    Route::put('/listings/{listing}/galleries/{gallery}', [ListingContentController::class, 'galleriesUpdate'])
        ->name('admin.business.galleries.update');
    Route::delete('/listings/{listing}/galleries/{gallery}', [ListingContentController::class, 'galleriesDestroy'])
        ->name('admin.business.galleries.destroy');
    Route::post('/listings/{listing}/galleries/{gallery}/set-primary', [ListingContentController::class, 'galleriesSetPrimary'])
        ->name('admin.business.galleries.set-primary');

    Route::get('/listings/{listing}/gallery', [ListingContentController::class, 'galleryIndex'])
        ->name('admin.business.gallery.index');
    Route::get('/listings/{listing}/gallery/{gallery}', [ListingContentController::class, 'galleryIndex'])
        ->where('gallery', '[0-9]+')
        ->name('admin.business.gallery.show');
    Route::post('/listings/{listing}/gallery', [ListingContentController::class, 'galleryStore'])
        ->name('admin.business.gallery.store');
    Route::put('/listings/{listing}/gallery/{image}', [ListingContentController::class, 'galleryUpdate'])
        ->name('admin.business.gallery.update');
    Route::delete('/listings/{listing}/gallery/{image}', [ListingContentController::class, 'galleryDestroy'])
        ->name('admin.business.gallery.destroy');

    Route::get('/listings/{listing}/appointments', [ListingContentController::class, 'appointmentsIndex'])
        ->name('admin.business.appointments.index');
    Route::get('/listings/{listing}/appointments/create', [ListingContentController::class, 'appointmentsCreate'])
        ->name('admin.business.appointments.create');
    Route::post('/listings/{listing}/appointments', [ListingContentController::class, 'appointmentsStore'])
        ->name('admin.business.appointments.store');
    Route::get('/listings/{listing}/appointments/{appointment}', [ListingContentController::class, 'appointmentsShow'])
        ->name('admin.business.appointments.show');
    Route::get('/listings/{listing}/appointments/{appointment}/edit', [ListingContentController::class, 'appointmentsEdit'])
        ->name('admin.business.appointments.edit');
    Route::put('/listings/{listing}/appointments/{appointment}', [ListingContentController::class, 'appointmentsUpdate'])
        ->name('admin.business.appointments.update');
    Route::delete('/listings/{listing}/appointments/{appointment}', [ListingContentController::class, 'appointmentsDestroy'])
        ->name('admin.business.appointments.destroy');
    Route::post('/listings/{listing}/appointments/{appointment}/cancel', [ListingContentController::class, 'appointmentsCancel'])
        ->name('admin.business.appointments.cancel');

    Route::get('/listings/{listing}/slots', [AdminSlotController::class, 'index'])
        ->name('admin.business.slots.index');
    Route::post('/listings/{listing}/slots', [AdminSlotController::class, 'store'])
        ->name('admin.business.slots.store');
    Route::put('/listings/{listing}/slots/{slot}', [AdminSlotController::class, 'update'])
        ->name('admin.business.slots.update');
    Route::delete('/listings/{listing}/slots/{slot}', [AdminSlotController::class, 'destroy'])
        ->name('admin.business.slots.destroy');

    Route::get('/listings/{listing}/leads', [ListingLeadsController::class, 'index'])
        ->name('admin.business.leads.index');
    Route::get('/listings/{listing}/leads/create', [ListingLeadsController::class, 'create'])
        ->name('admin.business.leads.create');
    Route::post('/listings/{listing}/leads', [ListingLeadsController::class, 'store'])
        ->name('admin.business.leads.store');
    Route::get('/listings/{listing}/leads/{lead}', [ListingLeadsController::class, 'show'])
        ->name('admin.business.leads.show');
    Route::get('/listings/{listing}/leads/{lead}/edit', [ListingLeadsController::class, 'edit'])
        ->name('admin.business.leads.edit');
    Route::put('/listings/{listing}/leads/{lead}', [ListingLeadsController::class, 'update'])
        ->name('admin.business.leads.update');
    Route::delete('/listings/{listing}/leads/{lead}', [ListingLeadsController::class, 'destroy'])
        ->name('admin.business.leads.destroy');

    Route::get('/listings/{listing}/contact-form/submissions', [ListingContactFormController::class, 'submissions'])
        ->name('admin.business.contact-form.submissions');

    Route::get('/listings/{listing}/ai-chatbot', [ListingAiChatbotController::class, 'index'])
        ->name('admin.business.ai-chatbot.index');

    Route::get('/modules/ai_chatbot/settings', [AiChatbotSettingsController::class, 'show'])
        ->name('admin.modules.ai-chatbot.settings');

    Route::get('/modules/ai_chatbot/presets', [ChatbotPresetController::class, 'index'])
        ->name('admin.modules.ai-chatbot.presets.index');
    Route::get('/modules/ai_chatbot/presets/create', [ChatbotPresetController::class, 'create'])
        ->name('admin.modules.ai-chatbot.presets.create');
    Route::post('/modules/ai_chatbot/presets', [ChatbotPresetController::class, 'store'])
        ->name('admin.modules.ai-chatbot.presets.store');
    Route::get('/modules/ai_chatbot/presets/{preset}/edit', [ChatbotPresetController::class, 'edit'])
        ->name('admin.modules.ai-chatbot.presets.edit');
    Route::put('/modules/ai_chatbot/presets/{preset}', [ChatbotPresetController::class, 'update'])
        ->name('admin.modules.ai-chatbot.presets.update');
    Route::delete('/modules/ai_chatbot/presets/{preset}', [ChatbotPresetController::class, 'destroy'])
        ->name('admin.modules.ai-chatbot.presets.destroy');
    Route::post('/modules/ai_chatbot/presets/{preset}/toggle', [ChatbotPresetController::class, 'toggle'])
        ->name('admin.modules.ai-chatbot.presets.toggle');
    Route::post('/modules/ai_chatbot/presets/{preset}/duplicate', [ChatbotPresetController::class, 'duplicate'])
        ->name('admin.modules.ai-chatbot.presets.duplicate');

    Route::get('/modules/ai_chatbot/personalities', [ChatbotPersonalityController::class, 'index'])
        ->name('admin.modules.ai-chatbot.personalities.index');
    Route::get('/modules/ai_chatbot/personalities/create', [ChatbotPersonalityController::class, 'create'])
        ->name('admin.modules.ai-chatbot.personalities.create');
    Route::post('/modules/ai_chatbot/personalities', [ChatbotPersonalityController::class, 'store'])
        ->name('admin.modules.ai-chatbot.personalities.store');
    Route::get('/modules/ai_chatbot/personalities/{personality}/edit', [ChatbotPersonalityController::class, 'edit'])
        ->name('admin.modules.ai-chatbot.personalities.edit');
    Route::put('/modules/ai_chatbot/personalities/{personality}', [ChatbotPersonalityController::class, 'update'])
        ->name('admin.modules.ai-chatbot.personalities.update');
    Route::delete('/modules/ai_chatbot/personalities/{personality}', [ChatbotPersonalityController::class, 'destroy'])
        ->name('admin.modules.ai-chatbot.personalities.destroy');

    // Redirects from old routes to new routes
    Route::get('/chatbot-presets', function () {
        return redirect()->route('admin.modules.ai-chatbot.presets.index');
    });
    Route::get('/chatbot-presets/create', function () {
        return redirect()->route('admin.modules.ai-chatbot.presets.create');
    });
    Route::get('/chatbot-personalities', function () {
        return redirect()->route('admin.modules.ai-chatbot.personalities.index');
    });
    Route::get('/chatbot-personalities/create', function () {
        return redirect()->route('admin.modules.ai-chatbot.personalities.create');
    });

    Route::get('/listings/{listing}/reviews', [ListingReviewController::class, 'index'])
        ->name('admin.business.reviews.index');
    Route::get('/listings/{listing}/reviews/create', [ListingReviewController::class, 'create'])
        ->name('admin.business.reviews.create');
    Route::post('/listings/{listing}/reviews', [ListingReviewController::class, 'store'])
        ->name('admin.business.reviews.store');
    Route::get('/listings/{listing}/reviews/{review}/edit', [ListingReviewController::class, 'edit'])
        ->name('admin.business.reviews.edit');
    Route::put('/listings/{listing}/reviews/{review}', [ListingReviewController::class, 'update'])
        ->name('admin.business.reviews.update');
    Route::delete('/listings/{listing}/reviews/{review}', [ListingReviewController::class, 'destroy'])
        ->name('admin.business.reviews.destroy');

    Route::get('/listings/{listing}/promotions', [ListingPromotionController::class, 'index'])
        ->name('admin.business.promotions.index');
    Route::get('/listings/{listing}/promotions/create', [ListingPromotionController::class, 'create'])
        ->name('admin.business.promotions.create');
    Route::post('/listings/{listing}/promotions', [ListingPromotionController::class, 'store'])
        ->name('admin.business.promotions.store');
    Route::get('/listings/{listing}/promotions/{promotion}/edit', [ListingPromotionController::class, 'edit'])
        ->name('admin.business.promotions.edit');
    Route::put('/listings/{listing}/promotions/{promotion}', [ListingPromotionController::class, 'update'])
        ->name('admin.business.promotions.update');
    Route::delete('/listings/{listing}/promotions/{promotion}', [ListingPromotionController::class, 'destroy'])
        ->name('admin.business.promotions.destroy');

    Route::get('/listings/{listing}/menu-categories', [MenuCategoryController::class, 'index'])
        ->name('admin.menu.categories.index');
    Route::post('/listings/{listing}/menu-categories', [MenuCategoryController::class, 'store'])
        ->name('admin.menu.categories.store');
    Route::put('/listings/{listing}/menu-categories/{category}', [MenuCategoryController::class, 'update'])
        ->name('admin.menu.categories.update');
    Route::delete('/listings/{listing}/menu-categories/{category}', [MenuCategoryController::class, 'destroy'])
        ->name('admin.menu.categories.destroy');

    Route::get('/listings/{listing}/menu-products', [MenuProductController::class, 'index'])
        ->name('admin.menu.products.index');
    Route::post('/listings/{listing}/menu-products', [MenuProductController::class, 'store'])
        ->name('admin.menu.products.store');
    Route::put('/listings/{listing}/menu-products/{product}', [MenuProductController::class, 'update'])
        ->name('admin.menu.products.update');
    Route::delete('/listings/{listing}/menu-products/{product}', [MenuProductController::class, 'destroy'])
        ->name('admin.menu.products.destroy');
    Route::post('/listings/{listing}/menu-products/{product}/variants', [MenuProductVariantController::class, 'store'])
        ->name('admin.menu.products.variants.store');
    Route::put('/listings/{listing}/menu-products/{product}/variants/{variant}', [MenuProductVariantController::class, 'update'])
        ->name('admin.menu.products.variants.update');
    Route::delete('/listings/{listing}/menu-products/{product}/variants/{variant}', [MenuProductVariantController::class, 'destroy'])
        ->name('admin.menu.products.variants.destroy');
    Route::post('/listings/{listing}/menu-products/{product}/images', [MenuProductImageController::class, 'store'])
        ->name('admin.menu.products.images.store');
    Route::put('/listings/{listing}/menu-products/{product}/images/{image}', [MenuProductImageController::class, 'update'])
        ->name('admin.menu.products.images.update');
    Route::delete('/listings/{listing}/menu-products/{product}/images/{image}', [MenuProductImageController::class, 'destroy'])
        ->name('admin.menu.products.images.destroy');

    Route::get('/settings', [SettingController::class, 'index'])
        ->middleware('permission_or_user:settings.view,1')
        ->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])
        ->middleware('permission_or_user:settings.update,1')
        ->name('admin.settings.update');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission_or_user:users.view,1')
        ->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission_or_user:users.create,1')
        ->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission_or_user:users.create,1')
        ->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission_or_user:users.update,1')
        ->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission_or_user:users.update,1')
        ->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission_or_user:users.delete,1')
        ->name('admin.users.destroy');
    Route::put('/users/{user}/activate', [UserController::class, 'activate'])
        ->middleware('permission_or_user:users.activate,1')
        ->name('admin.users.activate');
    Route::put('/users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->middleware('permission_or_user:users.deactivate,1')
        ->name('admin.users.deactivate');
    Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])
        ->middleware('permission_or_user:users.resend_verification,1')
        ->name('admin.users.resend-verification');
    Route::put('/users/{user}/verify-email', [UserController::class, 'verifyEmail'])
        ->middleware('permission_or_user:users.update,1')
        ->name('admin.users.verify-email');
    Route::get('/users/archived', [UserController::class, 'archived'])
        ->middleware('permission_or_user:users.view,1')
        ->name('admin.users.archived');
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])
        ->middleware('permission_or_user:users.restore,1')
        ->name('admin.users.restore');
    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy'])
        ->middleware('permission_or_user:users.force_delete,1')
        ->name('admin.users.force-destroy');

    Route::get('/users/{user}/subscriptions', [UserSubscriptionController::class, 'index'])
        ->middleware('permission_or_user:users.view,1')
        ->name('admin.users.subscriptions.index');
    Route::post('/users/{user}/subscriptions', [UserSubscriptionController::class, 'store'])
        ->middleware('permission_or_user:users.edit,1')
        ->name('admin.users.subscriptions.store');
    Route::put('/users/{user}/subscriptions', [UserSubscriptionController::class, 'update'])
        ->middleware('permission_or_user:users.edit,1')
        ->name('admin.users.subscriptions.update');
    Route::delete('/users/{user}/subscriptions', [UserSubscriptionController::class, 'destroy'])
        ->middleware('permission_or_user:users.edit,1')
        ->name('admin.users.subscriptions.destroy');

    Route::get('/legal-documents', [LegalDocumentController::class, 'index'])
        ->middleware(['permission_or_user:legal-documents.view,1', 'module:legal'])
        ->name('admin.legal-documents.index');
    Route::get('/legal-documents/create', [LegalDocumentController::class, 'create'])
        ->middleware(['permission_or_user:legal-documents.create,1', 'module:legal'])
        ->name('admin.legal-documents.create');
    Route::post('/legal-documents', [LegalDocumentController::class, 'store'])
        ->middleware(['permission_or_user:legal-documents.create,1', 'module:legal'])
        ->name('admin.legal-documents.store');
    Route::get('/legal-documents/{document}', [LegalDocumentController::class, 'show'])
        ->middleware(['permission_or_user:legal-documents.view,1', 'module:legal'])
        ->name('admin.legal-documents.show');
    Route::get('/legal-documents/{document}/edit', [LegalDocumentController::class, 'edit'])
        ->middleware(['permission_or_user:legal-documents.update,1', 'module:legal'])
        ->name('admin.legal-documents.edit');
    Route::put('/legal-documents/{document}', [LegalDocumentController::class, 'update'])
        ->middleware(['permission_or_user:legal-documents.update,1', 'module:legal'])
        ->name('admin.legal-documents.update');
    Route::delete('/legal-documents/{document}', [LegalDocumentController::class, 'destroy'])
        ->middleware(['permission_or_user:legal-documents.delete,1', 'module:legal'])
        ->name('admin.legal-documents.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/activity', [AdminActivityController::class, 'index'])
        ->middleware(['permission_or_user:activity.view,1', 'module:activity'])
        ->name('admin.activity.index');

    Route::get('/exports', [ExportController::class, 'index'])
        ->middleware(['permission_or_user:exports.view,1', 'module:exports'])
        ->name('admin.exports.index');
    Route::get('/exports/users', [ExportController::class, 'users'])
        ->middleware(['permission_or_user:exports.download,1', 'module:exports'])
        ->name('admin.exports.users');
    Route::get('/exports/subscriptions', [ExportController::class, 'subscriptions'])
        ->middleware(['permission_or_user:exports.download,1', 'module:exports'])
        ->name('admin.exports.subscriptions');
    Route::get('/exports/payments', [ExportController::class, 'payments'])
        ->middleware(['permission_or_user:exports.download,1', 'module:exports'])
        ->name('admin.exports.payments');
    Route::get('/exports/tickets', [ExportController::class, 'tickets'])
        ->middleware(['permission_or_user:exports.download,1', 'module:exports'])
        ->name('admin.exports.tickets');
    Route::get('/exports/activities', [ExportController::class, 'activities'])
        ->middleware(['permission_or_user:exports.download,1', 'module:exports'])
        ->name('admin.exports.activities');

    Route::get('/system-errors', [SystemErrorController::class, 'index'])
        ->middleware(['permission_or_user:system-errors.view,1', 'module:system-errors'])
        ->name('admin.system-errors.index');
    Route::get('/system-errors/{error}', [SystemErrorController::class, 'show'])
        ->middleware(['permission_or_user:system-errors.view,1', 'module:system-errors'])
        ->name('admin.system-errors.show');
    Route::put('/system-errors/{error}/resolve', [SystemErrorController::class, 'resolve'])
        ->middleware(['permission_or_user:system-errors.update,1', 'module:system-errors'])
        ->name('admin.system-errors.resolve');

    Route::get('/api-keys', [AdminApiKeyController::class, 'index'])
        ->middleware(['permission_or_user:api-keys.view,1', 'module:api'])
        ->name('admin.api-keys.index');
    Route::get('/api-keys/{apiKey}', [AdminApiKeyController::class, 'show'])
        ->middleware(['permission_or_user:api-keys.view,1', 'module:api'])
        ->name('admin.api-keys.show');
    Route::put('/api-keys/{apiKey}/revoke', [AdminApiKeyController::class, 'revoke'])
        ->middleware(['permission_or_user:api-keys.revoke,1', 'module:api'])
        ->name('admin.api-keys.revoke');

    Route::get('/webhooks', [AdminWebhookController::class, 'index'])
        ->middleware(['permission_or_user:webhooks.view,1', 'module:webhooks'])
        ->name('admin.webhooks.index');
    Route::get('/webhooks/{webhook}', [AdminWebhookController::class, 'show'])
        ->middleware(['permission_or_user:webhooks.view,1', 'module:webhooks'])
        ->name('admin.webhooks.show');
    Route::get('/webhooks/{webhook}/deliveries', [AdminWebhookController::class, 'deliveries'])
        ->middleware(['permission_or_user:webhooks.view,1', 'module:webhooks'])
        ->name('admin.webhooks.deliveries');

    Route::get('/queues', [QueueController::class, 'index'])
        ->middleware(['permission_or_user:queues.view,1', 'module:queues'])
        ->name('admin.queues.index');
    Route::get('/failed-jobs', [QueueController::class, 'failed'])
        ->middleware(['permission_or_user:queues.view,1', 'module:queues'])
        ->name('admin.queues.failed');
    Route::post('/failed-jobs/{id}/retry', [QueueController::class, 'retry'])
        ->middleware(['permission_or_user:queues.retry,1', 'module:queues'])
        ->name('admin.queues.retry');
    Route::delete('/failed-jobs/{id}', [QueueController::class, 'destroy'])
        ->middleware(['permission_or_user:queues.flush-failed,1', 'module:queues'])
        ->name('admin.queues.destroy');
    Route::post('/failed-jobs/retry-all', [QueueController::class, 'retryAll'])
        ->middleware(['permission_or_user:queues.retry,1', 'module:queues'])
        ->name('admin.queues.retry-all');
    Route::delete('/failed-jobs/flush', [QueueController::class, 'flush'])
        ->middleware(['permission_or_user:queues.flush-failed,1', 'module:queues'])
        ->name('admin.queues.flush');

    Route::get('/system-monitor', [SystemMonitorController::class, 'index'])
        ->middleware('permission_or_user:reports.view,1')
        ->name('admin.system-monitor.index');

    Route::get('/security-events', [SecurityEventController::class, 'index'])
        ->middleware(['permission_or_user:security-events.view,1', 'module:security-events'])
        ->name('admin.security-events.index');
    Route::get('/security-events/{event}', [SecurityEventController::class, 'show'])
        ->middleware(['permission_or_user:security-events.view,1', 'module:security-events'])
        ->name('admin.security-events.show');

    Route::get('/feature-flags', [FeatureFlagController::class, 'index'])
        ->middleware(['permission_or_user:feature-flags.view,1', 'module:feature-flags'])
        ->name('admin.feature-flags.index');
    Route::get('/feature-flags/create', [FeatureFlagController::class, 'create'])
        ->middleware(['permission_or_user:feature-flags.create,1', 'module:feature-flags'])
        ->name('admin.feature-flags.create');
    Route::post('/feature-flags', [FeatureFlagController::class, 'store'])
        ->middleware(['permission_or_user:feature-flags.create,1', 'module:feature-flags'])
        ->name('admin.feature-flags.store');
    Route::get('/feature-flags/{flag}/edit', [FeatureFlagController::class, 'edit'])
        ->middleware(['permission_or_user:feature-flags.update,1', 'module:feature-flags'])
        ->name('admin.feature-flags.edit');
    Route::put('/feature-flags/{flag}', [FeatureFlagController::class, 'update'])
        ->middleware(['permission_or_user:feature-flags.update,1', 'module:feature-flags'])
        ->name('admin.feature-flags.update');

    Route::get('/announcements', [AdminSystemAnnouncementController::class, 'index'])
        ->middleware(['permission_or_user:announcements.view,1', 'module:announcements'])
        ->name('admin.announcements.index');
    Route::get('/announcements/create', [AdminSystemAnnouncementController::class, 'create'])
        ->middleware(['permission_or_user:announcements.create,1', 'module:announcements'])
        ->name('admin.announcements.create');
    Route::post('/announcements', [AdminSystemAnnouncementController::class, 'store'])
        ->middleware(['permission_or_user:announcements.create,1', 'module:announcements'])
        ->name('admin.announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminSystemAnnouncementController::class, 'edit'])
        ->middleware(['permission_or_user:announcements.update,1', 'module:announcements'])
        ->name('admin.announcements.edit');
    Route::put('/announcements/{announcement}', [AdminSystemAnnouncementController::class, 'update'])
        ->middleware(['permission_or_user:announcements.update,1', 'module:announcements'])
        ->name('admin.announcements.update');
    Route::delete('/announcements/{announcement}', [AdminSystemAnnouncementController::class, 'destroy'])
        ->middleware(['permission_or_user:announcements.delete,1', 'module:announcements'])
        ->name('admin.announcements.destroy');

    Route::get('/plans/{plan}/features', [PlanFeatureFlagController::class, 'edit'])
        ->middleware(['permission_or_user:feature-flags.update,1', 'module:feature-flags'])
        ->name('admin.plans.features.edit');
    Route::put('/plans/{plan}/features', [PlanFeatureFlagController::class, 'update'])
        ->middleware(['permission_or_user:feature-flags.update,1', 'module:feature-flags'])
        ->name('admin.plans.features.update');

    Route::get('/plans', [PlanController::class, 'index'])
        ->middleware(['permission_or_user:plans.view,1', 'module:billing'])
        ->name('admin.plans.index');
    Route::get('/plans/create', [PlanController::class, 'create'])
        ->middleware(['permission_or_user:plans.create,1', 'module:billing'])
        ->name('admin.plans.create');
    Route::post('/plans', [PlanController::class, 'store'])
        ->middleware(['permission_or_user:plans.create,1', 'module:billing'])
        ->name('admin.plans.store');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])
        ->middleware(['permission_or_user:plans.update,1', 'module:billing'])
        ->name('admin.plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])
        ->middleware(['permission_or_user:plans.update,1', 'module:billing'])
        ->name('admin.plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
        ->middleware(['permission_or_user:plans.delete,1', 'module:billing'])
        ->name('admin.plans.destroy');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->middleware(['permission_or_user:subscriptions.view,1', 'module:billing'])
        ->name('admin.subscriptions.index');

    Route::get('/business-module-definitions', [ModuleDefinitionController::class, 'index'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.index');
    Route::get('/business-module-definitions/create', [ModuleDefinitionController::class, 'create'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.create');
    Route::post('/business-module-definitions', [ModuleDefinitionController::class, 'store'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.store');
    Route::get('/business-module-definitions/{definition}/edit', [ModuleDefinitionController::class, 'edit'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.edit');
    Route::put('/business-module-definitions/{definition}', [ModuleDefinitionController::class, 'update'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.update');
    Route::delete('/business-module-definitions/{definition}', [ModuleDefinitionController::class, 'destroy'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.destroy');

    Route::get('/listings/{listing}/minisite', [AdminListingMinisiteController::class, 'index'])
        ->name('admin.business.minisite.index');
    Route::post('/listings/{listing}/minisite', [AdminListingMinisiteController::class, 'update'])
        ->name('admin.business.minisite.update');

    Route::get('/modules/{moduleKey}/settings', [ModuleSettingsController::class, 'show'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.module-settings.show');
    Route::put('/modules/{moduleKey}/settings', [ModuleSettingsController::class, 'update'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.module-settings.update');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware(['permission_or_user:payments.view,1', 'module:billing'])
        ->name('admin.payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])
        ->middleware(['permission_or_user:payments.create,1', 'module:billing'])
        ->name('admin.payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])
        ->middleware(['permission_or_user:payments.create,1', 'module:billing'])
        ->name('admin.payments.store');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])
        ->middleware(['permission_or_user:payments.update,1', 'module:billing'])
        ->name('admin.payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])
        ->middleware(['permission_or_user:payments.update,1', 'module:billing'])
        ->name('admin.payments.update');

    Route::get('/coupons', [CouponController::class, 'index'])
        ->middleware(['permission_or_user:coupons.view,1', 'module:billing'])
        ->name('admin.coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])
        ->middleware(['permission_or_user:coupons.create,1', 'module:billing'])
        ->name('admin.coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])
        ->middleware(['permission_or_user:coupons.create,1', 'module:billing'])
        ->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])
        ->middleware(['permission_or_user:coupons.update,1', 'module:billing'])
        ->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])
        ->middleware(['permission_or_user:coupons.update,1', 'module:billing'])
        ->name('admin.coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])
        ->middleware(['permission_or_user:coupons.delete,1', 'module:billing'])
        ->name('admin.coupons.destroy');

    Route::get('/invoices', [AdminInvoiceController::class, 'index'])
        ->middleware(['permission_or_user:invoices.view,1', 'module:billing'])
        ->name('admin.invoices.index');
    Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])
        ->middleware(['permission_or_user:invoices.view,1', 'module:billing'])
        ->name('admin.invoices.show');
    Route::get('/invoices/{invoice}/download', [AdminInvoiceController::class, 'download'])
        ->middleware(['permission_or_user:invoices.download,1', 'module:billing'])
        ->name('admin.invoices.download');

    Route::get('/support', [AdminSupportTicketController::class, 'index'])
        ->middleware(['permission_or_user:support.view,1', 'module:support'])
        ->name('admin.support.index');
    Route::get('/support/{ticket}', [AdminSupportTicketController::class, 'show'])
        ->middleware(['permission_or_user:support.view,1', 'module:support'])
        ->name('admin.support.show');
    Route::post('/support/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])
        ->middleware(['permission_or_user:support.reply,1', 'module:support'])
        ->name('admin.support.reply');
    Route::put('/support/{ticket}', [AdminSupportTicketController::class, 'update'])
        ->middleware(['permission_or_user:support.update,1', 'module:support'])
        ->name('admin.support.update');

    Route::get('/support/departments', [SupportDepartmentController::class, 'index'])
        ->middleware(['permission_or_user:support.view,1', 'module:support'])
        ->name('admin.support.departments.index');
    Route::post('/support/departments', [SupportDepartmentController::class, 'store'])
        ->middleware(['permission_or_user:support.update,1', 'module:support'])
        ->name('admin.support.departments.store');
    Route::put('/support/departments/{department}', [SupportDepartmentController::class, 'update'])
        ->middleware(['permission_or_user:support.update,1', 'module:support'])
        ->name('admin.support.departments.update');
    Route::delete('/support/departments/{department}', [SupportDepartmentController::class, 'destroy'])
        ->middleware(['permission_or_user:support.delete,1', 'module:support'])
        ->name('admin.support.departments.destroy');

    Route::get('/help', [HelpArticleController::class, 'index'])
        ->middleware(['permission_or_user:help.view,1', 'module:support'])
        ->name('admin.help.index');
    Route::get('/help/create', [HelpArticleController::class, 'create'])
        ->middleware(['permission_or_user:help.create,1', 'module:support'])
        ->name('admin.help.create');
    Route::post('/help', [HelpArticleController::class, 'store'])
        ->middleware(['permission_or_user:help.create,1', 'module:support'])
        ->name('admin.help.store');
    Route::get('/help/{article}/edit', [HelpArticleController::class, 'edit'])
        ->middleware(['permission_or_user:help.update,1', 'module:support'])
        ->name('admin.help.edit');
    Route::put('/help/{article}', [HelpArticleController::class, 'update'])
        ->middleware(['permission_or_user:help.update,1', 'module:support'])
        ->name('admin.help.update');
    Route::delete('/help/{article}', [HelpArticleController::class, 'destroy'])
        ->middleware(['permission_or_user:help.delete,1', 'module:support'])
        ->name('admin.help.destroy');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission_or_user:reports.view,1')
        ->name('admin.reports.index');

    Route::get('/automations', [AutomationController::class, 'index'])
        ->middleware('permission_or_user:automations.view,1')
        ->name('admin.automations.index');
    Route::get('/automations/{automation}', [AutomationController::class, 'show'])
        ->middleware('permission_or_user:automations.view,1')
        ->name('admin.automations.show');
    Route::put('/automations/{automation}', [AutomationController::class, 'update'])
        ->middleware('permission_or_user:automations.update,1')
        ->name('admin.automations.update');

    Route::get('/message-templates', [MessageTemplateController::class, 'index'])
        ->middleware('permission_or_user:templates.view,1')
        ->name('admin.message-templates.index');
    Route::get('/message-templates/create', [MessageTemplateController::class, 'create'])
        ->middleware('permission_or_user:templates.create,1')
        ->name('admin.message-templates.create');
    Route::post('/message-templates', [MessageTemplateController::class, 'store'])
        ->middleware('permission_or_user:templates.create,1')
        ->name('admin.message-templates.store');
    Route::get('/message-templates/{template}/edit', [MessageTemplateController::class, 'edit'])
        ->middleware('permission_or_user:templates.update,1')
        ->name('admin.message-templates.edit');
    Route::put('/message-templates/{template}', [MessageTemplateController::class, 'update'])
        ->middleware('permission_or_user:templates.update,1')
        ->name('admin.message-templates.update');
    Route::delete('/message-templates/{template}', [MessageTemplateController::class, 'destroy'])
        ->middleware('permission_or_user:templates.delete,1')
        ->name('admin.message-templates.destroy');

    Route::get('/modules', [SystemModuleController::class, 'index'])
        ->name('admin.modules.index');
    Route::put('/modules/{module}', [SystemModuleController::class, 'update'])
        ->name('admin.modules.update');

});

Route::prefix('admin')->middleware(['auth', 'admin_or_user:1', 'permission_or_user:permissions.view,1'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::post('/permissions/reorder', [PermissionController::class, 'reorder'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.reorder');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission_or_user:permissions.create,1')
        ->name('admin.permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->middleware('permission_or_user:permissions.create,1')
        ->name('admin.permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission_or_user:permissions.edit,1')
        ->name('admin.permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission_or_user:permissions.delete,1')
        ->name('admin.permissions.destroy');
});

Route::get('/ai/test', [App\Http\Controllers\AiTestController::class, 'test'])
    ->name('ai.test');

Route::post('/ai/chat', [App\Http\Controllers\AiTestController::class, 'chat'])
    ->name('ai.chat');
