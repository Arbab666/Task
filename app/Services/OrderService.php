<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */

    public function processOrder(array $data)
{
    // Check same order_id already exists
    $existingOrder = Order::where('order_id', $data['order_id'])->first();

   
    if ($existingOrder) {
        return;
    }

    // Find or create the merchant based on the provided domain
    $merchant = Merchant::firstOrCreate(['domain' => $data['merchant_domain']]);

    // Find or create the affiliate based on the customer_email
    $affiliate = Affiliate::firstOrCreate(
        ['email' => $data['customer_email']],
        ['name' => $data['customer_name']]
    );

    // Create a new order
    $order = Order::create([
        'order_id' => $data['order_id'],
        'subtotal_price' => $data['subtotal_price'],
        'discount_code' => $data['discount_code'],
        'merchant_id' => $merchant->id,
        'affiliate_id' => $affiliate->id,
    ]);

    // Log any commissions for the affiliate
    $this->affiliateService->logCommission($affiliate, $order->subtotal_price);
}
}
