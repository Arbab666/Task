<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
{
    // TODO: Complete this method
    // Check if the affiliate with the same email already exists for the merchant
    $existingAffiliate = $merchant->affiliates()->where('email', $email)->first();

    if ($existingAffiliate) {
        throw new AffiliateCreateException("Affiliate with email '{$email}' already exists for this merchant.");
    }

    // Create a new affiliate
    $affiliate = Affiliate::create([
        'merchant_id' => $merchant->id,
        'email' => $email,
        'name' => $name,
        'commission_rate' => $commissionRate,
    ]);

    // Send an email to the newly created affiliate
    Mail::to($email)->send(new AffiliateCreated($affiliate));

    return $affiliate;
}
}
