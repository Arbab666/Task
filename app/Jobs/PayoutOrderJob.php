<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PayoutOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Use the API service to send a payout of the correct amount.
     * Note: The order status must be paid if the payout is successful, or remain unpaid in the event of an exception.
     *
     * @return void
     */
  
    public function handle(ApiService $apiService)
{
   // TODO: Complete this method
    DB::beginTransaction();

    try {
       
        $payoutResult = $apiService->sendPayout($this->order->subtotal_price);

        
        if ($payoutResult['success']) {
 
            $this->order->update(['status' => Order::STATUS_PAID]);
            DB::commit();
        } else {
            // If the payout fails, throw an exception to trigger a rollback
            throw new \Exception("Payout failed: {$payoutResult['message']}");
        }
    } catch (\Exception $exception) {
        // If an exception occurs during the payout process, catch it, log it, and rollback the transaction
        \Log::error("Payout failed: {$exception->getMessage()}");
        DB::rollBack();
    }
}
}
