<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Modules\Listings\Models\Listing;
use Modules\VCards\Http\Controllers\Member\VCardController;
use Modules\VCards\Http\Controllers\Member\VCardImageController;
use Modules\VCards\Http\Controllers\Member\VCardPackageController;
use Modules\VCards\Http\Controllers\Member\VCardSeoController;
use Modules\VCards\Http\Controllers\Member\VCardTeamController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])->group(function () {
    Route::get('/member/listings/{listing}/vcards', [VCardController::class, 'index'])
        ->name('member.listings.vcards.index');
    Route::get('/member/listings/{listing}/vcards/create', [VCardController::class, 'create'])
        ->name('member.listings.vcards.create');
    Route::post('/member/listings/{listing}/vcards', [VCardController::class, 'store'])
        ->name('member.listings.vcards.store');
    Route::get('/member/listings/{listing}/vcards/{vcard}/edit', [VCardController::class, 'edit'])
        ->name('member.listings.vcards.edit');
    Route::put('/member/listings/{listing}/vcards/{vcard}', [VCardController::class, 'update'])
        ->name('member.listings.vcards.update');
    Route::delete('/member/listings/{listing}/vcards/{vcard}', [VCardController::class, 'destroy'])
        ->name('member.listings.vcards.destroy');
    Route::post('/member/listings/{listing}/vcards/{vcard}/duplicate', [VCardController::class, 'duplicate'])
        ->name('member.listings.vcards.duplicate');
    Route::post('/member/listings/{listing}/vcards/{vcard}/toggle-active', [VCardController::class, 'toggleActive'])
        ->name('member.listings.vcards.toggle-active');
    Route::get('/member/listings/{listing}/vcards/{vcard}/download', [VCardController::class, 'download'])
        ->name('member.listings.vcards.download');

    Route::get('/member/listings/{listing}/vcards-seo', [VCardSeoController::class, 'index'])
        ->name('member.listings.vcards.seo.index');
    Route::get('/member/listings/{listing}/vcards/{vcard}/seo', [VCardSeoController::class, 'edit'])
        ->name('member.listings.vcards.seo.edit');
    Route::post('/member/listings/{listing}/vcards/{vcard}/seo', [VCardSeoController::class, 'update'])
        ->name('member.listings.vcards.seo.update');

    Route::post('/member/listings/{listing}/vcards/{vcard}/logo', [VCardImageController::class, 'uploadLogo'])
        ->name('member.listings.vcards.logo.upload');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/logo', [VCardImageController::class, 'deleteLogo'])
        ->name('member.listings.vcards.logo.delete');
    Route::post('/member/listings/{listing}/vcards/{vcard}/badge', [VCardImageController::class, 'uploadBadge'])
        ->name('member.listings.vcards.badge.upload');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/badge', [VCardImageController::class, 'deleteBadge'])
        ->name('member.listings.vcards.badge.delete');
    Route::post('/member/listings/{listing}/vcards/{vcard}/profile-photo', [VCardImageController::class, 'uploadProfilePhoto'])
        ->name('member.listings.vcards.profile-photo.upload');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/profile-photo', [VCardImageController::class, 'deleteProfilePhoto'])
        ->name('member.listings.vcards.profile-photo.delete');
    Route::post('/member/listings/{listing}/vcards/{vcard}/hero-background-image', [VCardImageController::class, 'uploadHeroBackgroundImage'])
        ->name('member.listings.vcards.hero-background-image.upload');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/hero-background-image', [VCardImageController::class, 'deleteHeroBackgroundImage'])
        ->name('member.listings.vcards.hero-background-image.delete');

    Route::post('/member/listings/{listing}/vcards/reorder', [VCardController::class, 'reorder'])
        ->name('member.listings.vcards.reorder');
    Route::post('/member/listings/{listing}/vcards/bulk-delete', [VCardController::class, 'bulkDelete'])
        ->name('member.listings.vcards.bulk-delete');

    Route::post('/member/listings/{listing}/vcards/{vcard}/contacts', [VCardController::class, 'storeContact'])
        ->name('member.listings.vcards.contacts.store');
    Route::put('/member/listings/{listing}/vcards/{vcard}/contacts/{contact}', [VCardController::class, 'updateContact'])
        ->name('member.listings.vcards.contacts.update');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/contacts/{contact}', [VCardController::class, 'destroyContact'])
        ->name('member.listings.vcards.contacts.destroy');
    Route::post('/member/listings/{listing}/vcards/{vcard}/contacts/reorder', [VCardController::class, 'reorderContacts'])
        ->name('member.listings.vcards.contacts.reorder');

    Route::post('/member/listings/{listing}/vcards/{vcard}/fields', [VCardController::class, 'storeField'])
        ->name('member.listings.vcards.fields.store');
    Route::put('/member/listings/{listing}/vcards/{vcard}/fields/{field}', [VCardController::class, 'updateField'])
        ->name('member.listings.vcards.fields.update');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/fields/{field}', [VCardController::class, 'destroyField'])
        ->name('member.listings.vcards.fields.destroy');
    Route::post('/member/listings/{listing}/vcards/{vcard}/fields/reorder', [VCardController::class, 'reorderFields'])
        ->name('member.listings.vcards.fields.reorder');
    Route::post('/member/listings/{listing}/vcards/{vcard}/sections', [VCardController::class, 'updateSections'])
        ->name('member.listings.vcards.sections.update');

    Route::get('/member/listings/{listing}/vcard-teams', [VCardTeamController::class, 'index'])
        ->name('member.listings.vcard-teams.index');
    Route::get('/member/listings/{listing}/vcards/teams', [VCardTeamController::class, 'teamsIndex'])
        ->name('member.listings.vcards.teams.index');
    Route::get('/member/listings/{listing}/vcard-teams/data', [VCardTeamController::class, 'data'])
        ->name('member.listings.vcard-teams.data');
    Route::post('/member/listings/{listing}/vcard-teams', [VCardTeamController::class, 'store'])
        ->name('member.listings.vcard-teams.store');
    Route::put('/member/listings/{listing}/vcard-teams/{team}', [VCardTeamController::class, 'update'])
        ->name('member.listings.vcard-teams.update');
    Route::delete('/member/listings/{listing}/vcard-teams/{team}', [VCardTeamController::class, 'destroy'])
        ->name('member.listings.vcard-teams.destroy');
    Route::post('/member/listings/{listing}/vcard-teams/reorder', [VCardTeamController::class, 'reorder'])
        ->name('member.listings.vcard-teams.reorder');

    Route::get('/member/listings/{listing}/vcards/{vcard}/packages', [VCardPackageController::class, 'index'])
        ->name('member.listings.vcards.packages.index');
    Route::post('/member/listings/{listing}/vcards/{vcard}/packages', [VCardPackageController::class, 'store'])
        ->name('member.listings.vcards.packages.store');
    Route::put('/member/listings/{listing}/vcards/{vcard}/packages/{package}', [VCardPackageController::class, 'update'])
        ->name('member.listings.vcards.packages.update');
    Route::delete('/member/listings/{listing}/vcards/{vcard}/packages/{package}', [VCardPackageController::class, 'destroy'])
        ->name('member.listings.vcards.packages.destroy');
    Route::post('/member/listings/{listing}/vcards/{vcard}/packages/reorder', [VCardPackageController::class, 'reorder'])
        ->name('member.listings.vcards.packages.reorder');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-services', [VCardController::class, 'updateSelectedServices'])
        ->name('member.listings.vcards.selected-services.update');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-packages', [VCardController::class, 'updateSelectedPackages'])
        ->name('member.listings.vcards.selected-packages.update');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-gallery', [VCardController::class, 'updateSelectedGallery'])
        ->name('member.listings.vcards.selected-gallery.update');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-products', [VCardController::class, 'updateSelectedProducts'])
        ->name('member.listings.vcards.selected-products.update');

    Route::get('/member/listings/{listing}/vcard-services', [VCardController::class, 'getListingServices'])
        ->name('member.listings.vcard-services');

    Route::get('/member/listings/{listing}/vcard-packages', [VCardController::class, 'getListingPackages'])
        ->name('member.listings.vcard-packages');

    Route::get('/member/listings/{listing}/vcard-galleries', [VCardController::class, 'getListingGalleries'])
        ->name('member.listings.vcard-galleries');

    Route::get('/member/listings/{listing}/vcard-products', [VCardController::class, 'getListingProducts'])
        ->name('member.listings.vcard-products');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-testimonials', [VCardController::class, 'updateSelectedTestimonials'])
        ->name('member.listings.vcards.selected-testimonials.update');

    Route::get('/member/listings/{listing}/vcard-testimonials', [VCardController::class, 'getListingTestimonials'])
        ->name('member.listings.vcard-testimonials');

    Route::post('/member/listings/{listing}/vcards/{vcard}/business-hours', [VCardController::class, 'updateBusinessHours'])
        ->name('member.listings.vcards.business-hours.update');

    Route::get('/member/listings/{listing}/vcards/{vcard}/business-hours', [VCardController::class, 'getBusinessHours'])
        ->name('member.listings.vcards.business-hours');

    Route::post('/member/listings/{listing}/vcards/{vcard}/menu-categories', [VCardController::class, 'updateMenuCategories'])
        ->name('member.listings.vcards.menu-categories.update');

    Route::get('/member/listings/{listing}/vcard-menus', [VCardController::class, 'getListingMenus'])
        ->name('member.listings.vcard-menus');

    Route::post('/member/listings/{listing}/vcards/{vcard}/location', [VCardController::class, 'updateLocation'])
        ->name('member.listings.vcards.location.update');

    Route::get('/member/listings/{listing}/vcard-locations', [VCardController::class, 'getListingLocations'])
        ->name('member.listings.vcard-locations');

    Route::post('/member/listings/{listing}/vcards/{vcard}/selected-features', [VCardController::class, 'updateSelectedFeatures'])
        ->name('member.listings.vcards.selected-features.update');

    Route::get('/member/listings/{listing}/vcard-features', [VCardController::class, 'getListingFeatures'])
        ->name('member.listings.vcard-features');
});
