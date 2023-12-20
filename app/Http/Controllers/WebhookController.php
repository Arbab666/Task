<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
   
    public function __invoke(Request $request): JsonResponse
{
   
      // TODO: Complete this method
    $data = $request->only([
        'order_id',
        'subtotal_price',
        'merchant_domain',
        'discount_code',
        'customer_email',
        'customer_name',
    ]);


    $this->orderService->processOrder($data);

    // Return a JSON response indicating successful processing
    return response()->json(['message' => 'Order processed successfully'], 200);
}
}
