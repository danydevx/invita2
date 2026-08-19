<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ApiKeyController as AdminApiKeyController;
use App\Http\Controllers\Admin\AutomationController;
use App\Http\Controllers\Admin\BusinessAiChatbotController;
use Modules\AiChatbot\Http\Controllers\Admin\ChatbotPresetController;
use Modules\AiChatbot\Http\Controllers\Admin\ChatbotPersonalityController;
use Modules\AiChatbot\Http\Controllers\Admin\AiChatbotSettingsController;
use App\Http\Controllers\Admin\BusinessContactFormController;
use App\Http\Controllers\Admin\BusinessContentController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\BusinessHeroController;
use App\Http\Controllers\Admin\BusinessLeadsController;
use App\Http\Controllers\Admin\BusinessModuleController;
use App\Http\Controllers\Admin\BusinessModuleDefinitionController;
use App\Http\Controllers\Admin\BusinessPromotionController;
use App\Http\Controllers\Admin\BusinessReviewController;
use App\Http\Controllers\Admin\BusinessSocialNetworkController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\HelpArticleController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\LegalDocumentController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuProductController;
use App\Http\Controllers\Admin\MenuProductImageController;
use App\Http\Controllers\Admin\MenuProductVariantController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\MinisiteThemeController;
use Modules\Minisite\Http\Controllers\Admin\MinisiteController as AdminMinisiteController;
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
use App\Http\Controllers\Admin\SlotController as AdminSlotController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\SystemAnnouncementController as AdminSystemAnnouncementController;
use App\Http\Controllers\Admin\SystemErrorController;
use App\Http\Controllers\Admin\SystemModuleController;
use App\Http\Controllers\Admin\SystemMonitorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\WebhookController as AdminWebhookController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\InvitationAcceptController;
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
use Modules\Products\Http\Controllers\ProductImageController;
use App\Http\Controllers\Member\PromotionController;
use App\Http\Controllers\Member\ReviewController;
use App\Http\Controllers\Member\SeoController;
use App\Http\Controllers\Member\ServiceController;
use Modules\Services\Http\Controllers\ServiceImageController;
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
use Modules\Minisite\Http\Controllers\Member\MinisiteController;
use Modules\Minisite\Http\Controllers\Member\MinisiteSectionController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\Public\BusinessController as PublicBusinessController;
use App\Http\Controllers\Public\DirectoryController;
use App\Http\Controllers\Public\MenuController;
use App\Http\Controllers\Public\PromotionVerificationController;
use App\Http\Controllers\StripeWebhookController;
use App\Services\SettingService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Features\Http\Controllers\Member\FeatureController;
use Modules\Features\Http\Controllers\Public\FeatureController as PublicFeatureController;
use Modules\Tasks\Http\Controllers\Member\TaskController;

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

Route::get('/invite/{token}', [InvitationAcceptController::class, 'show'])->name('invite.show');
Route::post('/invite/{token}/accept', [InvitationAcceptController::class, 'accept'])->name('invite.accept');

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

Route::get('/member/business-modules', fn () => redirect()->route('member.businesses.index'))
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business-modules.index');
Route::get('/member/businesses', [App\Http\Controllers\Member\BusinessModuleController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.index');
Route::get('/member/businesses/{business}/modules', [BusinessModulesController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.modules');
Route::get('/member/businesses/create', [App\Http\Controllers\Member\BusinessController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.create');
Route::post('/member/businesses', [App\Http\Controllers\Member\BusinessController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.store');
Route::get('/member/businesses/{business}/modules', [App\Http\Controllers\Member\BusinessModuleController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business-modules.edit');
Route::put('/member/businesses/{business}/modules', [App\Http\Controllers\Member\BusinessModuleController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business-modules.update');
Route::get('/member/businesses/{business}/edit', [App\Http\Controllers\Member\BusinessController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.edit');
Route::put('/member/businesses/{business}', [App\Http\Controllers\Member\BusinessController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.update');

Route::get('/member/businesses/{business}/locations', [LocationController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.index');
Route::get('/member/businesses/{business}/locations/create', [LocationController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.create');
Route::post('/member/businesses/{business}/locations', [LocationController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.store');
Route::get('/member/businesses/{business}/locations/{location}/edit', [LocationController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.edit');
Route::put('/member/businesses/{business}/locations/{location}', [LocationController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.update');
Route::delete('/member/businesses/{business}/locations/{location}', [LocationController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.destroy');
Route::post('/member/businesses/{business}/locations/bulk-delete', [LocationController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.locations.bulk-delete');

Route::get('/member/businesses/{business}/services', [ServiceController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.index');
Route::get('/member/businesses/{business}/services/create', [ServiceController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.create');
Route::post('/member/businesses/{business}/services', [ServiceController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.store');
Route::get('/member/businesses/{business}/services/{service}/edit', [ServiceController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.edit');
Route::put('/member/businesses/{business}/services/{service}', [ServiceController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.update');
Route::delete('/member/businesses/{business}/services/{service}', [ServiceController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.destroy');
Route::post('/member/businesses/{business}/services/{service}/clone', [ServiceController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.clone');
Route::post('/member/businesses/{business}/services/reorder', [ServiceController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.reorder');
Route::post('/member/businesses/{business}/services/bulk-delete', [ServiceController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.bulk-delete');
Route::post('/member/businesses/{business}/services/{service}/images', [ServiceImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.images.store');
Route::delete('/member/businesses/{business}/services/{service}/images/{image}', [ServiceImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.services.images.destroy');
Route::post('/member/businesses/{business}/properties/{property}/images', [PropertyImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.properties.images.store');
Route::delete('/member/businesses/{business}/properties/{property}/images/{image}', [PropertyImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.properties.images.destroy');

Route::get('/member/businesses/{business}/team-members', [TeamMemberController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.index');
Route::get('/member/businesses/{business}/team-members/create', [TeamMemberController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.create');
Route::post('/member/businesses/{business}/team-members', [TeamMemberController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.store');
Route::get('/member/businesses/{business}/team-members/{member}/edit', [TeamMemberController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.edit');
Route::post('/member/businesses/{business}/team-members/{member}', [TeamMemberController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.update');
Route::delete('/member/businesses/{business}/team-members/{member}', [TeamMemberController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.destroy');
Route::post('/member/businesses/{business}/team-members/reorder', [TeamMemberController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.reorder');
Route::post('/member/businesses/{business}/team-members/bulk-delete', [TeamMemberController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-members.bulk-delete');

Route::get('/member/businesses/{business}/team-member-positions', [TeamMemberPositionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.index');
Route::get('/member/businesses/{business}/team-member-positions/create', [TeamMemberPositionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.create');
Route::post('/member/businesses/{business}/team-member-positions', [TeamMemberPositionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.store');
Route::get('/member/businesses/{business}/team-member-positions/{position}/edit', [TeamMemberPositionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.edit');
Route::put('/member/businesses/{business}/team-member-positions/{position}', [TeamMemberPositionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.update');
Route::delete('/member/businesses/{business}/team-member-positions/{position}', [TeamMemberPositionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.team-member-positions.destroy');

Route::get('/member/businesses/{business}/packages', [PackageController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.index');
Route::get('/member/businesses/{business}/packages/create', [PackageController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.create');
Route::post('/member/businesses/{business}/packages', [PackageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.store');
Route::post('/member/businesses/{business}/packages/reorder', [PackageController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.reorder');
Route::post('/member/businesses/{business}/packages/bulk-delete', [PackageController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.bulk-delete');
Route::get('/member/businesses/{business}/packages/{package}/edit', [PackageController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.edit');
Route::post('/member/businesses/{business}/packages/{package}', [PackageController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.update');
Route::delete('/member/businesses/{business}/packages/{package}', [PackageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.destroy');
Route::post('/member/businesses/{business}/packages/{package}/clone', [PackageController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.packages.clone');

Route::get('/member/businesses/{business}/faqs', [FaqController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.index');
Route::get('/member/businesses/{business}/faqs/create', [FaqController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.create');
Route::post('/member/businesses/{business}/faqs', [FaqController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.store');
Route::get('/member/businesses/{business}/faqs/{faq}/edit', [FaqController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.edit');
Route::put('/member/businesses/{business}/faqs/{faq}', [FaqController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.update');
Route::delete('/member/businesses/{business}/faqs/{faq}', [FaqController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.destroy');
Route::post('/member/businesses/{business}/faqs/reorder', [FaqController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.reorder');
Route::post('/member/businesses/{business}/faqs/bulk-delete', [FaqController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.bulk-delete');
Route::post('/member/businesses/{business}/faqs/{faq}/clone', [FaqController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faqs.clone');

Route::get('/member/businesses/{business}/faq-categories', [FaqCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faq-categories.index');
Route::post('/member/businesses/{business}/faq-categories', [FaqCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faq-categories.store');
Route::put('/member/businesses/{business}/faq-categories/{category}', [FaqCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faq-categories.update');
Route::delete('/member/businesses/{business}/faq-categories/{category}', [FaqCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.faq-categories.destroy');

Route::get('/member/businesses/{business}/minisite', [MinisiteController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.index');
Route::post('/member/businesses/{business}/minisite', [MinisiteController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.store');
Route::put('/member/businesses/{business}/minisite', [MinisiteController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.update');

Route::get('/member/businesses/{business}/minisite/sections', [MinisiteSectionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.index');
Route::get('/member/businesses/{business}/minisite/sections/create', [MinisiteSectionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.create');
Route::post('/member/businesses/{business}/minisite/sections', [MinisiteSectionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.store');
Route::get('/member/businesses/{business}/minisite/sections/{section}/edit', [MinisiteSectionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.edit');
Route::put('/member/businesses/{business}/minisite/sections/{section}', [MinisiteSectionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.update');
Route::delete('/member/businesses/{business}/minisite/sections/{section}', [MinisiteSectionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.destroy');
Route::post('/member/businesses/{business}/minisite/sections/reorder', [MinisiteSectionController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.minisite.sections.reorder');

Route::get('/member/businesses/{business}/seo', [SeoController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.seo.index');
Route::post('/member/businesses/{business}/seo', [SeoController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.seo.update');

Route::get('/member/businesses/{business}/branding', [BrandingController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.branding.index');
Route::post('/member/businesses/{business}/branding', [BrandingController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.branding.update');

Route::get('/member/businesses/{business}/hero', [HeroController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.hero.index');
Route::post('/member/businesses/{business}/hero', [HeroController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.hero.update');

Route::get('/member/businesses/{business}/about', [AboutController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.about.index');
Route::post('/member/businesses/{business}/about', [AboutController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.about.update');

Route::get('/member/businesses/{business}/social-networks', [SocialNetworkController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.social-networks.index');
Route::post('/member/businesses/{business}/social-networks', [SocialNetworkController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.social-networks.store');
Route::post('/member/businesses/{business}/social-networks/{socialNetwork}', [SocialNetworkController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.social-networks.update');
Route::delete('/member/businesses/{business}/social-networks/{socialNetwork}', [SocialNetworkController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.social-networks.destroy');
Route::post('/member/businesses/{business}/social-networks/reorder', [SocialNetworkController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.social-networks.reorder');

Route::get('/member/businesses/{business}/tasks', [TaskController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.tasks.index');
Route::post('/member/businesses/{business}/tasks', [TaskController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.tasks.store');
Route::put('/member/businesses/{business}/tasks/{task}', [TaskController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.tasks.update');
Route::delete('/member/businesses/{business}/tasks/{task}', [TaskController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.tasks.destroy');
Route::post('/member/businesses/{business}/tasks/reorder', [TaskController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.tasks.reorder');

Route::get('/member/businesses/{business}/clients', [ClientController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.index');
Route::get('/member/businesses/{business}/clients/create', [ClientController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.create');
Route::post('/member/businesses/{business}/clients', [ClientController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.store');
Route::get('/member/businesses/{business}/clients/{client}/edit', [ClientController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.edit');
Route::put('/member/businesses/{business}/clients/{client}', [ClientController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.update');
Route::delete('/member/businesses/{business}/clients/{client}', [ClientController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.destroy');
Route::post('/member/businesses/{business}/clients/bulk-delete', [ClientController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.bulk-delete');
Route::post('/member/businesses/{business}/clients/{client}/clone', [ClientController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.clients.clone');

Route::get('/member/businesses/{business}/galleries', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.index');
Route::get('/member/businesses/{business}/galleries/create', [GalleryGroupController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.create');
Route::post('/member/businesses/{business}/galleries', [GalleryGroupController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.store');
Route::get('/member/businesses/{business}/galleries/{gallery}/edit', [GalleryGroupController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.edit');
Route::put('/member/businesses/{business}/galleries/{gallery}', [GalleryGroupController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.update');
Route::delete('/member/businesses/{business}/galleries/{gallery}', [GalleryGroupController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.destroy');
Route::post('/member/businesses/{business}/galleries/{gallery}/set-primary', [GalleryGroupController::class, 'setPrimary'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.galleries.set-primary');

Route::get('/member/businesses/{business}/gallery', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.index.legacy');
Route::get('/member/businesses/{business}/gallery/{gallery}', [GalleryController::class, 'show'])
    ->where('gallery', '[0-9]+')
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.show.legacy');
Route::get('/member/businesses/{business}/gallery', [GalleryGroupController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.index.legacy');
Route::get('/member/businesses/{business}/gallery/{gallery}', [GalleryController::class, 'show'])
    ->where('gallery', '[0-9]+')
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.show.legacy');
Route::post('/member/businesses/{business}/gallery', [GalleryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.store');
Route::put('/member/businesses/{business}/gallery/{image}', [GalleryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.update');
Route::delete('/member/businesses/{business}/gallery/{image}', [GalleryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.destroy');
Route::post('/member/businesses/{business}/gallery/reorder', [GalleryController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.reorder');
Route::post('/member/businesses/{business}/gallery/bulk-delete', [GalleryController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.gallery.bulk-delete');

Route::get('/member/businesses/{business}/products', [ProductController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.index');
Route::get('/member/businesses/{business}/products/create', [ProductController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.create');
Route::post('/member/businesses/{business}/products', [ProductController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.store');
Route::get('/member/businesses/{business}/products/{product}/edit', [ProductController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.edit');
Route::put('/member/businesses/{business}/products/{product}', [ProductController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.update');
Route::delete('/member/businesses/{business}/products/{product}', [ProductController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.destroy');
Route::post('/member/businesses/{business}/products/{product}/clone', [ProductController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.clone');
Route::post('/member/businesses/{business}/products/reorder', [ProductController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.reorder');
Route::post('/member/businesses/{business}/products/bulk-delete', [ProductController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.bulk-delete');
Route::post('/member/businesses/{business}/products/{product}/images', [ProductImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.images.store');
Route::delete('/member/businesses/{business}/products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.products.images.destroy');

Route::get('/member/businesses/{business}/appointments', [AppointmentController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.index');
Route::get('/member/businesses/{business}/appointments/create', [AppointmentController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.create');
Route::post('/member/businesses/{business}/appointments', [AppointmentController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.store');

Route::get('/member/businesses/{business}/appointments/availability', [AvailabilityController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.availability');
Route::put('/member/businesses/{business}/appointments/availability/weekly', [AvailabilityController::class, 'updateWeekly'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.availability.weekly');
Route::post('/member/businesses/{business}/appointments/availability/exceptions', [AvailabilityController::class, 'storeException'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.availability.exceptions.store');
Route::delete('/member/businesses/{business}/appointments/availability/exceptions/{exception}', [AvailabilityController::class, 'destroyException'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.availability.exceptions.destroy');

Route::get('/member/businesses/{business}/appointments/{appointment}', [AppointmentController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.show');
Route::get('/member/businesses/{business}/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.edit');
Route::put('/member/businesses/{business}/appointments/{appointment}', [AppointmentController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.update');
Route::delete('/member/businesses/{business}/appointments/{appointment}', [AppointmentController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.destroy');
Route::post('/member/businesses/{business}/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.cancel');
Route::put('/member/businesses/{business}/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.reschedule');
Route::post('/member/businesses/{business}/appointments/bulk-delete', [AppointmentController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.appointments.bulk-delete');

Route::get('/member/businesses/{business}/slots', [SlotController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.slots.index');
Route::post('/member/businesses/{business}/slots', [SlotController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.slots.store');
Route::put('/member/businesses/{business}/slots/{slot}', [SlotController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.slots.update');
Route::delete('/member/businesses/{business}/slots/{slot}', [SlotController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.slots.destroy');

Route::get('/member/businesses/{business}/leads', [LeadController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.index');
Route::get('/member/businesses/{business}/leads/create', [LeadController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.create');
Route::post('/member/businesses/{business}/leads', [LeadController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.store');
Route::get('/member/businesses/{business}/leads/export', [LeadController::class, 'export'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.export');
Route::get('/member/businesses/{business}/leads/{lead}', [LeadController::class, 'show'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.show');
Route::get('/member/businesses/{business}/leads/{lead}/edit', [LeadController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.edit');
Route::put('/member/businesses/{business}/leads/{lead}', [LeadController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.update');
Route::delete('/member/businesses/{business}/leads/{lead}', [LeadController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.destroy');
Route::post('/member/businesses/{business}/leads/bulk-delete', [LeadController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.leads.bulk-delete');

Route::get('/member/businesses/{business}/contact-forms', [ContactFormController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.index');
Route::get('/member/businesses/{business}/contact-forms/create', [ContactFormController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.create');
Route::post('/member/businesses/{business}/contact-forms', [ContactFormController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.store');
Route::get('/member/businesses/{business}/contact-forms/{form}/edit', [ContactFormController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.edit');
Route::put('/member/businesses/{business}/contact-forms/{form}', [ContactFormController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.update');
Route::delete('/member/businesses/{business}/contact-forms/{form}', [ContactFormController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.destroy');
Route::post('/member/businesses/{business}/contact-forms/{form}/fields', [ContactFormController::class, 'storeField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.store');
Route::put('/member/businesses/{business}/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'updateField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.update');
Route::delete('/member/businesses/{business}/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'destroyField'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.fields.destroy');
Route::post('/member/businesses/{business}/contact-forms/{form}/reorder', [ContactFormController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.reorder');
Route::get('/member/businesses/{business}/contact-forms/{form}/submissions', [ContactFormController::class, 'submissions'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.submissions');
Route::get('/member/businesses/{business}/contact-forms/export', [ContactFormController::class, 'export'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.export');
Route::get('/member/businesses/{business}/contact-forms/{form}/preview', [ContactFormController::class, 'preview'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.contact-forms.preview');

Route::get('/member/businesses/{business}/reviews', [ReviewController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.index');
Route::get('/member/businesses/{business}/reviews/create', [ReviewController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.create');
Route::post('/member/businesses/{business}/reviews', [ReviewController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.store');
Route::post('/member/businesses/{business}/reviews/reorder', [ReviewController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.reorder');
Route::post('/member/businesses/{business}/reviews/bulk-delete', [ReviewController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.bulk-delete');
Route::post('/member/businesses/{business}/reviews/{review}/clone', [ReviewController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.clone');
Route::get('/member/businesses/{business}/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.edit');
Route::put('/member/businesses/{business}/reviews/{review}', [ReviewController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.update');
Route::delete('/member/businesses/{business}/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.reviews.destroy');

Route::get('/member/businesses/{business}/promotions', [PromotionController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.index');
Route::get('/member/businesses/{business}/promotions/create', [PromotionController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.create');
Route::post('/member/businesses/{business}/promotions', [PromotionController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.store');
Route::get('/member/businesses/{business}/promotions/{promotion}/edit', [PromotionController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.edit');
Route::put('/member/businesses/{business}/promotions/{promotion}', [PromotionController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.update');
Route::delete('/member/businesses/{business}/promotions/{promotion}', [PromotionController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.destroy');
Route::post('/member/businesses/{business}/promotions/reorder', [PromotionController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.reorder');
Route::post('/member/businesses/{business}/promotions/bulk-delete', [PromotionController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.bulk-delete');
Route::post('/member/businesses/{business}/promotions/{promotion}/clone', [PromotionController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.clone');
Route::post('/member/businesses/{business}/promotions/{promotion}/regenerate-qr', [PromotionController::class, 'regenerateQrCode'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.promotions.regenerate-qr');

Route::get('/member/businesses/{business}/features', [FeatureController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.index');
Route::post('/member/businesses/{business}/features', [FeatureController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.store');
Route::post('/member/businesses/{business}/features/import', [FeatureController::class, 'importBulk'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.import-bulk');
Route::post('/member/businesses/{business}/features/import/{feature}', [FeatureController::class, 'import'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.import');
Route::put('/member/businesses/{business}/features/{feature}', [FeatureController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.update');
Route::delete('/member/businesses/{business}/features/{feature}', [FeatureController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.destroy');
Route::put('/member/businesses/{business}/feature-assignments', [FeatureController::class, 'updateAssignment'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.feature-assignments.update');
Route::delete('/member/businesses/{business}/feature-assignments/{assignment}', [FeatureController::class, 'removeAssignment'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.feature-assignments.remove');
Route::post('/member/businesses/{business}/features/reorder', [FeatureController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.businesses.features.reorder');

Route::get('/member/businesses/{business}/menu-categories', [MemberMenuCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.index');
Route::post('/member/businesses/{business}/menu-categories', [MemberMenuCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.store');
Route::put('/member/businesses/{business}/menu-categories/{category}', [MemberMenuCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.update');
Route::delete('/member/businesses/{business}/menu-categories/{category}', [MemberMenuCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.categories.destroy');

Route::get('/member/businesses/{business}/product-categories', [MemberProductCategoryController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.index');
Route::post('/member/businesses/{business}/product-categories', [MemberProductCategoryController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.store');
Route::put('/member/businesses/{business}/product-categories/{category}', [MemberProductCategoryController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.update');
Route::delete('/member/businesses/{business}/product-categories/{category}', [MemberProductCategoryController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.product.categories.destroy');

Route::get('/member/businesses/{business}/menu-products', [MemberMenuProductController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.index');
Route::get('/member/businesses/{business}/menu-products/create', [MemberMenuProductController::class, 'create'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.create');
Route::post('/member/businesses/{business}/menu-products', [MemberMenuProductController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.store');
Route::post('/member/businesses/{business}/menu-products/reorder', [MemberMenuProductController::class, 'reorder'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.reorder');
Route::post('/member/businesses/{business}/menu-products/bulk-delete', [MemberMenuProductController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.bulk-delete');
Route::get('/member/businesses/{business}/menu-products/{product}/edit', [MemberMenuProductController::class, 'edit'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.edit');
Route::put('/member/businesses/{business}/menu-products/{product}', [MemberMenuProductController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.update');
Route::delete('/member/businesses/{business}/menu-products/{product}', [MemberMenuProductController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.destroy');
Route::post('/member/businesses/{business}/menu-products/{product}/clone', [MemberMenuProductController::class, 'clone'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.clone');
Route::post('/member/businesses/{business}/menu-products/{product}/variants', [MemberMenuProductVariantController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.store');
Route::put('/member/businesses/{business}/menu-products/{product}/variants/{variant}', [MemberMenuProductVariantController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.update');
Route::delete('/member/businesses/{business}/menu-products/{product}/variants/{variant}', [MemberMenuProductVariantController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.variants.destroy');
Route::post('/member/businesses/{business}/menu-products/{product}/images', [MemberMenuProductImageController::class, 'store'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.store');
Route::put('/member/businesses/{business}/menu-products/{product}/images/{image}', [MemberMenuProductImageController::class, 'update'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.update');
Route::delete('/member/businesses/{business}/menu-products/{product}/images/{image}', [MemberMenuProductImageController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.menu.products.images.destroy');

Route::get('/member/businesses/{business}/minisite-theme', [MinisiteThemeController::class, 'index'])
    ->middleware(['auth', 'verified', 'active', 'role:member'])
    ->name('member.business.minisite-theme.index');
Route::put('/member/businesses/{business}/minisite-theme/{theme}', [MinisiteThemeController::class, 'update'])
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

    Route::get('/business-modules', [BusinessModuleController::class, 'index'])
        ->name('admin.business-modules.index');
    Route::get('/businesses/{business}/modules', [BusinessModuleController::class, 'edit'])
        ->name('admin.business-modules.edit');
    Route::put('/businesses/{business}/modules', [BusinessModuleController::class, 'update'])
        ->name('admin.business-modules.update');

    Route::get('/businesses', [BusinessController::class, 'index'])
        ->name('admin.businesses.index');
    Route::get('/businesses/create', [BusinessController::class, 'create'])
        ->name('admin.businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])
        ->name('admin.businesses.store');
    Route::get('/businesses/{business}/edit', [BusinessController::class, 'edit'])
        ->name('admin.businesses.edit');
    Route::put('/businesses/{business}', [BusinessController::class, 'update'])
        ->name('admin.businesses.update');
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy'])
        ->name('admin.businesses.destroy');

    Route::get('/businesses/{business}/hero', [BusinessHeroController::class, 'index'])
        ->name('admin.business.hero.index');
    Route::post('/businesses/{business}/hero', [BusinessHeroController::class, 'update'])
        ->name('admin.business.hero.update');

    Route::get('/businesses/{business}/social-networks', [BusinessSocialNetworkController::class, 'index'])
        ->name('admin.business.social-networks.index');
    Route::post('/businesses/{business}/social-networks', [BusinessSocialNetworkController::class, 'store'])
        ->name('admin.business.social-networks.store');
    Route::post('/businesses/{business}/social-networks/{socialNetwork}', [BusinessSocialNetworkController::class, 'update'])
        ->name('admin.business.social-networks.update');
    Route::delete('/businesses/{business}/social-networks/{socialNetwork}', [BusinessSocialNetworkController::class, 'destroy'])
        ->name('admin.business.social-networks.destroy');

    Route::get('/businesses/{business}/locations', [BusinessContentController::class, 'locationsIndex'])
        ->name('admin.business.locations.index');
    Route::get('/businesses/{business}/locations/create', [BusinessContentController::class, 'locationsCreate'])
        ->name('admin.business.locations.create');
    Route::post('/businesses/{business}/locations', [BusinessContentController::class, 'locationsStore'])
        ->name('admin.business.locations.store');
    Route::get('/businesses/{business}/locations/{location}/edit', [BusinessContentController::class, 'locationsEdit'])
        ->name('admin.business.locations.edit');
    Route::put('/businesses/{business}/locations/{location}', [BusinessContentController::class, 'locationsUpdate'])
        ->name('admin.business.locations.update');
    Route::delete('/businesses/{business}/locations/{location}', [BusinessContentController::class, 'locationsDestroy'])
        ->name('admin.business.locations.destroy');

    Route::get('/businesses/{business}/services', [BusinessContentController::class, 'servicesIndex'])
        ->name('admin.business.services.index');
    Route::get('/businesses/{business}/services/create', [BusinessContentController::class, 'servicesCreate'])
        ->name('admin.business.services.create');
    Route::post('/businesses/{business}/services', [BusinessContentController::class, 'servicesStore'])
        ->name('admin.business.services.store');
    Route::get('/businesses/{business}/services/{service}/edit', [BusinessContentController::class, 'servicesEdit'])
        ->name('admin.business.services.edit');
    Route::put('/businesses/{business}/services/{service}', [BusinessContentController::class, 'servicesUpdate'])
        ->name('admin.business.services.update');
    Route::delete('/businesses/{business}/services/{service}', [BusinessContentController::class, 'servicesDestroy'])
        ->name('admin.business.services.destroy');

    Route::get('/businesses/{business}/faqs', [BusinessContentController::class, 'faqsIndex'])
        ->name('admin.business.faqs.index');
    Route::get('/businesses/{business}/faqs/create', [BusinessContentController::class, 'faqsCreate'])
        ->name('admin.business.faqs.create');
    Route::post('/businesses/{business}/faqs', [BusinessContentController::class, 'faqsStore'])
        ->name('admin.business.faqs.store');
    Route::get('/businesses/{business}/faqs/{faq}/edit', [BusinessContentController::class, 'faqsEdit'])
        ->name('admin.business.faqs.edit');
    Route::put('/businesses/{business}/faqs/{faq}', [BusinessContentController::class, 'faqsUpdate'])
        ->name('admin.business.faqs.update');
    Route::delete('/businesses/{business}/faqs/{faq}', [BusinessContentController::class, 'faqsDestroy'])
        ->name('admin.business.faqs.destroy');

    Route::get('/businesses/{business}/faq-categories', [BusinessContentController::class, 'faqCategoriesIndex'])
        ->name('admin.business.faq-categories.index');
    Route::post('/businesses/{business}/faq-categories', [BusinessContentController::class, 'faqCategoriesStore'])
        ->name('admin.business.faq-categories.store');
    Route::put('/businesses/{business}/faq-categories/{category}', [BusinessContentController::class, 'faqCategoriesUpdate'])
        ->name('admin.business.faq-categories.update');
    Route::delete('/businesses/{business}/faq-categories/{category}', [BusinessContentController::class, 'faqCategoriesDestroy'])
        ->name('admin.business.faq-categories.destroy');

    Route::get('/businesses/{business}/products', [BusinessContentController::class, 'productsIndex'])
        ->name('admin.business.products.index');
    Route::get('/businesses/{business}/products/create', [BusinessContentController::class, 'productsCreate'])
        ->name('admin.business.products.create');
    Route::post('/businesses/{business}/products', [BusinessContentController::class, 'productsStore'])
        ->name('admin.business.products.store');
    Route::get('/businesses/{business}/products/{product}/edit', [BusinessContentController::class, 'productsEdit'])
        ->name('admin.business.products.edit');
    Route::put('/businesses/{business}/products/{product}', [BusinessContentController::class, 'productsUpdate'])
        ->name('admin.business.products.update');
    Route::delete('/businesses/{business}/products/{product}', [BusinessContentController::class, 'productsDestroy'])
        ->name('admin.business.products.destroy');

    Route::get('/businesses/{business}/galleries', [BusinessContentController::class, 'galleriesIndex'])
        ->name('admin.business.galleries.index');
    Route::get('/businesses/{business}/galleries/create', [BusinessContentController::class, 'galleriesCreate'])
        ->name('admin.business.galleries.create');
    Route::post('/businesses/{business}/galleries', [BusinessContentController::class, 'galleriesStore'])
        ->name('admin.business.galleries.store');
    Route::get('/businesses/{business}/galleries/{gallery}/edit', [BusinessContentController::class, 'galleriesEdit'])
        ->name('admin.business.galleries.edit');
    Route::put('/businesses/{business}/galleries/{gallery}', [BusinessContentController::class, 'galleriesUpdate'])
        ->name('admin.business.galleries.update');
    Route::delete('/businesses/{business}/galleries/{gallery}', [BusinessContentController::class, 'galleriesDestroy'])
        ->name('admin.business.galleries.destroy');
    Route::post('/businesses/{business}/galleries/{gallery}/set-primary', [BusinessContentController::class, 'galleriesSetPrimary'])
        ->name('admin.business.galleries.set-primary');

    Route::get('/businesses/{business}/gallery', [BusinessContentController::class, 'galleryIndex'])
        ->name('admin.business.gallery.index');
    Route::get('/businesses/{business}/gallery/{gallery}', [BusinessContentController::class, 'galleryIndex'])
        ->where('gallery', '[0-9]+')
        ->name('admin.business.gallery.show');
    Route::post('/businesses/{business}/gallery', [BusinessContentController::class, 'galleryStore'])
        ->name('admin.business.gallery.store');
    Route::put('/businesses/{business}/gallery/{image}', [BusinessContentController::class, 'galleryUpdate'])
        ->name('admin.business.gallery.update');
    Route::delete('/businesses/{business}/gallery/{image}', [BusinessContentController::class, 'galleryDestroy'])
        ->name('admin.business.gallery.destroy');

    Route::get('/businesses/{business}/appointments', [BusinessContentController::class, 'appointmentsIndex'])
        ->name('admin.business.appointments.index');
    Route::get('/businesses/{business}/appointments/create', [BusinessContentController::class, 'appointmentsCreate'])
        ->name('admin.business.appointments.create');
    Route::post('/businesses/{business}/appointments', [BusinessContentController::class, 'appointmentsStore'])
        ->name('admin.business.appointments.store');
    Route::get('/businesses/{business}/appointments/{appointment}', [BusinessContentController::class, 'appointmentsShow'])
        ->name('admin.business.appointments.show');
    Route::get('/businesses/{business}/appointments/{appointment}/edit', [BusinessContentController::class, 'appointmentsEdit'])
        ->name('admin.business.appointments.edit');
    Route::put('/businesses/{business}/appointments/{appointment}', [BusinessContentController::class, 'appointmentsUpdate'])
        ->name('admin.business.appointments.update');
    Route::delete('/businesses/{business}/appointments/{appointment}', [BusinessContentController::class, 'appointmentsDestroy'])
        ->name('admin.business.appointments.destroy');
    Route::post('/businesses/{business}/appointments/{appointment}/cancel', [BusinessContentController::class, 'appointmentsCancel'])
        ->name('admin.business.appointments.cancel');

    Route::get('/businesses/{business}/slots', [AdminSlotController::class, 'index'])
        ->name('admin.business.slots.index');
    Route::post('/businesses/{business}/slots', [AdminSlotController::class, 'store'])
        ->name('admin.business.slots.store');
    Route::put('/businesses/{business}/slots/{slot}', [AdminSlotController::class, 'update'])
        ->name('admin.business.slots.update');
    Route::delete('/businesses/{business}/slots/{slot}', [AdminSlotController::class, 'destroy'])
        ->name('admin.business.slots.destroy');

    Route::get('/businesses/{business}/leads', [BusinessLeadsController::class, 'index'])
        ->name('admin.business.leads.index');
    Route::get('/businesses/{business}/leads/create', [BusinessLeadsController::class, 'create'])
        ->name('admin.business.leads.create');
    Route::post('/businesses/{business}/leads', [BusinessLeadsController::class, 'store'])
        ->name('admin.business.leads.store');
    Route::get('/businesses/{business}/leads/{lead}', [BusinessLeadsController::class, 'show'])
        ->name('admin.business.leads.show');
    Route::get('/businesses/{business}/leads/{lead}/edit', [BusinessLeadsController::class, 'edit'])
        ->name('admin.business.leads.edit');
    Route::put('/businesses/{business}/leads/{lead}', [BusinessLeadsController::class, 'update'])
        ->name('admin.business.leads.update');
    Route::delete('/businesses/{business}/leads/{lead}', [BusinessLeadsController::class, 'destroy'])
        ->name('admin.business.leads.destroy');

    Route::get('/businesses/{business}/contact-form/submissions', [BusinessContactFormController::class, 'submissions'])
        ->name('admin.business.contact-form.submissions');

    Route::get('/businesses/{business}/ai-chatbot', [BusinessAiChatbotController::class, 'index'])
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

    Route::get('/businesses/{business}/reviews', [BusinessReviewController::class, 'index'])
        ->name('admin.business.reviews.index');
    Route::get('/businesses/{business}/reviews/create', [BusinessReviewController::class, 'create'])
        ->name('admin.business.reviews.create');
    Route::post('/businesses/{business}/reviews', [BusinessReviewController::class, 'store'])
        ->name('admin.business.reviews.store');
    Route::get('/businesses/{business}/reviews/{review}/edit', [BusinessReviewController::class, 'edit'])
        ->name('admin.business.reviews.edit');
    Route::put('/businesses/{business}/reviews/{review}', [BusinessReviewController::class, 'update'])
        ->name('admin.business.reviews.update');
    Route::delete('/businesses/{business}/reviews/{review}', [BusinessReviewController::class, 'destroy'])
        ->name('admin.business.reviews.destroy');

    Route::get('/businesses/{business}/promotions', [BusinessPromotionController::class, 'index'])
        ->name('admin.business.promotions.index');
    Route::get('/businesses/{business}/promotions/create', [BusinessPromotionController::class, 'create'])
        ->name('admin.business.promotions.create');
    Route::post('/businesses/{business}/promotions', [BusinessPromotionController::class, 'store'])
        ->name('admin.business.promotions.store');
    Route::get('/businesses/{business}/promotions/{promotion}/edit', [BusinessPromotionController::class, 'edit'])
        ->name('admin.business.promotions.edit');
    Route::put('/businesses/{business}/promotions/{promotion}', [BusinessPromotionController::class, 'update'])
        ->name('admin.business.promotions.update');
    Route::delete('/businesses/{business}/promotions/{promotion}', [BusinessPromotionController::class, 'destroy'])
        ->name('admin.business.promotions.destroy');

    Route::get('/businesses/{business}/menu-categories', [MenuCategoryController::class, 'index'])
        ->name('admin.menu.categories.index');
    Route::post('/businesses/{business}/menu-categories', [MenuCategoryController::class, 'store'])
        ->name('admin.menu.categories.store');
    Route::put('/businesses/{business}/menu-categories/{category}', [MenuCategoryController::class, 'update'])
        ->name('admin.menu.categories.update');
    Route::delete('/businesses/{business}/menu-categories/{category}', [MenuCategoryController::class, 'destroy'])
        ->name('admin.menu.categories.destroy');

    Route::get('/businesses/{business}/menu-products', [MenuProductController::class, 'index'])
        ->name('admin.menu.products.index');
    Route::post('/businesses/{business}/menu-products', [MenuProductController::class, 'store'])
        ->name('admin.menu.products.store');
    Route::put('/businesses/{business}/menu-products/{product}', [MenuProductController::class, 'update'])
        ->name('admin.menu.products.update');
    Route::delete('/businesses/{business}/menu-products/{product}', [MenuProductController::class, 'destroy'])
        ->name('admin.menu.products.destroy');
    Route::post('/businesses/{business}/menu-products/{product}/variants', [MenuProductVariantController::class, 'store'])
        ->name('admin.menu.products.variants.store');
    Route::put('/businesses/{business}/menu-products/{product}/variants/{variant}', [MenuProductVariantController::class, 'update'])
        ->name('admin.menu.products.variants.update');
    Route::delete('/businesses/{business}/menu-products/{product}/variants/{variant}', [MenuProductVariantController::class, 'destroy'])
        ->name('admin.menu.products.variants.destroy');
    Route::post('/businesses/{business}/menu-products/{product}/images', [MenuProductImageController::class, 'store'])
        ->name('admin.menu.products.images.store');
    Route::put('/businesses/{business}/menu-products/{product}/images/{image}', [MenuProductImageController::class, 'update'])
        ->name('admin.menu.products.images.update');
    Route::delete('/businesses/{business}/menu-products/{product}/images/{image}', [MenuProductImageController::class, 'destroy'])
        ->name('admin.menu.products.images.destroy');

    Route::get('/settings', [SettingController::class, 'index'])
        ->middleware('permission_or_user:settings.view,1')
        ->name('admin.settings.index');
    Route::put('/settings', [SettingController::class, 'update'])
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

    Route::get('/invitations', [InvitationController::class, 'index'])
        ->middleware(['permission_or_user:invitations.view,1', 'module:invitations'])
        ->name('admin.invitations.index');
    Route::get('/invitations/create', [InvitationController::class, 'create'])
        ->middleware(['permission_or_user:invitations.create,1', 'module:invitations'])
        ->name('admin.invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])
        ->middleware(['permission_or_user:invitations.create,1', 'module:invitations'])
        ->name('admin.invitations.store');
    Route::get('/invitations/{invitation}', [InvitationController::class, 'show'])
        ->middleware(['permission_or_user:invitations.view,1', 'module:invitations'])
        ->name('admin.invitations.show');
    Route::put('/invitations/{invitation}/revoke', [InvitationController::class, 'revoke'])
        ->middleware(['permission_or_user:invitations.revoke,1', 'module:invitations'])
        ->name('admin.invitations.revoke');
    Route::post('/invitations/{invitation}/resend', [InvitationController::class, 'resend'])
        ->middleware(['permission_or_user:invitations.update,1', 'module:invitations'])
        ->name('admin.invitations.resend');

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

    Route::get('/business-module-definitions', [BusinessModuleDefinitionController::class, 'index'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.index');
    Route::get('/business-module-definitions/create', [BusinessModuleDefinitionController::class, 'create'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.create');
    Route::post('/business-module-definitions', [BusinessModuleDefinitionController::class, 'store'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.store');
    Route::get('/business-module-definitions/{definition}/edit', [BusinessModuleDefinitionController::class, 'edit'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.edit');
    Route::put('/business-module-definitions/{definition}', [BusinessModuleDefinitionController::class, 'update'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.update');
    Route::delete('/business-module-definitions/{definition}', [BusinessModuleDefinitionController::class, 'destroy'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.business-module-definitions.destroy');

    Route::get('/businesses/{business}/minisite', [AdminMinisiteController::class, 'index'])
        ->name('admin.business.minisite.index');
    Route::post('/businesses/{business}/minisite', [AdminMinisiteController::class, 'update'])
        ->name('admin.business.minisite.update');

    Route::get('/industries', [IndustryController::class, 'index'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.index');
    Route::get('/industries/create', [IndustryController::class, 'create'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.create');
    Route::post('/industries', [IndustryController::class, 'store'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.store');
    Route::get('/industries/{industry}/edit', [IndustryController::class, 'edit'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.edit');
    Route::put('/industries/{industry}', [IndustryController::class, 'update'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.update');
    Route::delete('/industries/{industry}', [IndustryController::class, 'destroy'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.industries.destroy');

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

    Route::get('/minisite-themes', [MinisiteThemeController::class, 'index'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.index');
    Route::get('/minisite-themes/create', [MinisiteThemeController::class, 'create'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.create');
    Route::get('/minisite-themes/{theme}/edit', [MinisiteThemeController::class, 'edit'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.edit');
    Route::post('/minisite-themes', [MinisiteThemeController::class, 'store'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.store');
    Route::put('/minisite-themes/{theme}', [MinisiteThemeController::class, 'update'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.update');
    Route::delete('/minisite-themes/{theme}', [MinisiteThemeController::class, 'destroy'])
        ->middleware(['auth', 'admin_or_user:1'])
        ->name('admin.minisite-themes.destroy');

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
