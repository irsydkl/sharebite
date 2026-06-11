<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * Handle Midtrans payment notification webhook.
     * POST /webhook/midtrans
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Configure Midtrans for verification
            MidtransConfig::$serverKey    = config('midtrans.server_key');
            MidtransConfig::$isProduction = config('midtrans.is_production');

            // Parse & verify the notification
            $notification = new Notification();

            $orderId           = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus       = $notification->fraud_status ?? 'accept';
            $signatureKey      = $notification->signature_key ?? '';

            // Verify signature
            $expectedSignature = hash('sha512',
                $orderId
                . $notification->status_code
                . $notification->gross_amount
                . config('midtrans.server_key')
            );

            if ($signatureKey && $signatureKey !== $expectedSignature) {
                Log::warning("Midtrans webhook: invalid signature for order_id={$orderId}");
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            Log::info("Midtrans webhook received: order={$orderId} status={$transactionStatus} fraud={$fraudStatus}");

            // Build payload for service
            $payload = [
                'order_id'           => $orderId,
                'transaction_status' => $this->resolveStatus($transactionStatus, $fraudStatus),
                'transaction_id'     => $notification->transaction_id ?? null,
                'payment_type'       => $notification->payment_type ?? null,
            ];

            $this->paymentService->handleWebhook($payload);

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Internal error'], 500);
        }
    }

    /**
     * Map Midtrans transaction + fraud status to our internal status.
     */
    private function resolveStatus(string $transactionStatus, string $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'accept' ? 'capture' : 'deny';
        }

        return $transactionStatus; // settlement, cancel, deny, expire, pending
    }
}
