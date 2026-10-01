<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    /** Sentinel for "no practical limit"; the pricing pages show it as "Unlimited". */
    public const UNLIMITED = 999;

    /**
     * What each plan includes. The feature gates enforce these numbers and the
     * pricing pages render them, so the plan we advertise and the plan we
     * enforce can't drift apart (they had: "3 projects" was sold, no such
     * feature existed, and Lifetime said "500" on one page, "Unlimited" on another).
     */
    public const TIERS = [
        'free' => ['upload_limit' => 2, 'doc_limit' => 10, 'steering_collections' => 1],
        'pro' => ['upload_limit' => self::UNLIMITED, 'doc_limit' => 100, 'steering_collections' => 10],
        'lifetime' => ['upload_limit' => self::UNLIMITED, 'doc_limit' => self::UNLIMITED, 'steering_collections' => self::UNLIMITED],
    ];

    /**
     * Put the user on a plan: the tier plus the limits the gates read.
     * Idempotent, so the Stripe webhook and the checkout success page can
     * both call it and whichever runs second is a no-op.
     */
    public function applyTier(string $tier): void
    {
        $limits = self::TIERS[$tier] ?? throw new \InvalidArgumentException("Unknown tier: {$tier}");

        $this->forceFill([
            'subscription_tier' => $tier,
            'upload_limit' => $limits['upload_limit'],
            'doc_limit' => $limits['doc_limit'],
        ])->save();
    }

    /**
     * Lifetime purchase. A buyer upgrading from Pro would otherwise keep being
     * billed monthly, so stop their subscription at the end of the period
     * they've already paid for. The customer.subscription.deleted event that
     * follows leaves them alone (the webhook only downgrades 'pro').
     */
    public function grantLifetime(): void
    {
        $this->applyTier('lifetime');

        $subscription = $this->subscription('default');

        if ($subscription && $subscription->valid() && ! $subscription->canceled()) {
            $subscription->cancel();
        }
    }

    public function steeringCollectionLimit(): int
    {
        return (self::TIERS[$this->subscription_tier] ?? self::TIERS['free'])['steering_collections'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_tier',
    ];

    public function steeringCollections()
    {
        return $this->hasMany(SteeringCollection::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the user's packages.
     */
    public function packages()
    {
        return $this->hasMany(UserPackage::class);
    }
}
