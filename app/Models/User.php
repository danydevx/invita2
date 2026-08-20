<?php

namespace App\Models;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasRoles, MustVerifyEmail, Notifiable, SoftDeletes;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new EmailVerificationNotification(
            $this->id,
            $this->email,
            $this->created_at?->timestamp
        ));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'onboarding_completed_at' => 'datetime',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(UserNotificationPreference::class);
    }

    public function announcementStates(): HasMany
    {
        return $this->hasMany(UserAnnouncementState::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function webhookEndpoints(): HasMany
    {
        return $this->hasMany(WebhookEndpoint::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(UserFeatureFlag::class);
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function listings()
    {
        return $this->hasMany(\Modules\Listings\Models\Listing::class);
    }

    public function primaryListing()
    {
        return $this->hasOne(\Modules\Listings\Models\Listing::class)->latestOfMany();
    }

    public function forceDeleteWithRelations(): void
    {
        $userId = $this->id;

        Activity::where('user_id', $userId)->delete();
        Activity::where('actor_id', $userId)->delete();

        UserNotification::where('user_id', $userId)->delete();
        UserNotificationPreference::where('user_id', $userId)->delete();
        UserAnnouncementState::where('user_id', $userId)->delete();
        UserFeatureFlag::where('user_id', $userId)->delete();

        ApiKey::where('user_id', $userId)->delete();
        WebhookEndpoint::where('user_id', $userId)->delete();

        Payment::where('user_id', $userId)->delete();
        Invoice::where('user_id', $userId)->delete();
        Subscription::where('user_id', $userId)->delete();
        SupportTicket::where('user_id', $userId)->delete();

        LegalAcceptance::where('user_id', $userId)->delete();

        SocialAccount::where('user_id', $userId)->delete();

        UserProfile::where('user_id', $userId)->delete();

        foreach ($this->mediaFiles as $file) {
            if ($file->path && file_exists(storage_path('app/'.$file->path))) {
                unlink(storage_path('app/'.$file->path));
            }
            $file->delete();
        }

        foreach ($this->listings as $listing) {
            $listing->forceDeleteWithRelations();
        }

        $this->roles()->detach();

        $this->forceDelete();
    }
}
