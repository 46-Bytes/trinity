<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Subscription;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;

class User extends Authenticatable {
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Billable;

    public bool $subscriptionIsActive = false;
    public bool $diagnosticStatus = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getActiveDiagnostic() {
        if ($this->diagnosticCount() === 0) {
            return null;
        } else {
            if ($this->hasIncompleteDiagnostic()) {
                return Diagnostic::getActiveDiagnostic($this->id);
            }
        }
        return null;
    }

    public function diagnosticCount(): int {
        return Diagnostic::where('user_id', $this->id)->count();
    }

    public function hasIncompleteDiagnostic(): bool {
        $diagnostic = Diagnostic::with(['conversation.messages'])
            ->where('user_id', $this->id)
            ->where('status', '!=', Diagnostic::STATUS_COMPLETED)
            ->first();

        return $diagnostic !== null;
    }

    public function getRoles(): array {
        return $this->getRoleNames()->toArray();
    }

    public function checkStatus() {
        $this->subscriptionIsActive = $this->subscriptionIsActive();
        $this->diagnosticStatus = $this->getDiagnosticStatus();
    }

    /**
     * Determine if the user has an active subscription.
     *
     * @return bool
     */
    public function subscriptionIsActive(): bool {
        $subscription = $this->getActiveSubscription();
        return $subscription !== null;
    }

    /**
     * Get the user's active subscription.
     *
     * @return Subscription|null
     */
    public function getActiveSubscription(): ?Subscription {
        return $this->subscriptions()->active()->first();
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }

    public function getDiagnosticStatus(): bool {
        $diagnostic = Diagnostic::with(['conversation.messages'])
            ->where('user_id', $this->id)
            ->where('status', Diagnostic::STATUS_COMPLETED)
            ->first();
        return !($diagnostic === null);
    }


    // Relationships

    public function getNameAttribute(): string {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function formEntries() {
        return $this->hasMany(FormEntry::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }

    public function activities() {
        return $this->hasMany(UserActivity::class);
    }

    public function reminders() {
        return $this->hasMany(Reminder::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function media() {
        return $this->hasMany(Media::class);
    }

    public function full_name(): string {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if the user has admin privileges.
     *
     * @return bool
     */
    public function isAdmin(): bool {
        // Assuming you have a 'role' field or 'is_admin' boolean field to differentiate admins
        return auth()->user()->hasRole('admin'); // Or if you're using a boolean 'is_admin' field, return $this->is_admin;
    }

    public function org(): HasOne {
        return $this->hasOne(Org::class, 'user_id');
    }

    // Define the relationship with the Org model

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
