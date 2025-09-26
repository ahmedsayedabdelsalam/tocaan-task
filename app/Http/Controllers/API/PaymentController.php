<?php

namespace App\Http\Controllers\API;

use App\Data\ProcessPaymentData;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Auth::guard('api')->user()->payments();

        $payments = $query->latest()->paginate();

        return PaymentResource::collection($payments)->response();
    }

    public function process(string $orderId, ProcessPaymentRequest $request): JsonResponse
    {
        $order = Auth::guard('api')->user()->orders()->findOrFail($orderId);

        if ($order->status !== OrderStatus::Confirmed) {
            return response()->json(['message' => 'Payments can only be processed for confirmed orders.'], 422);
        }

        $dto = ProcessPaymentData::from($request->validated());
        $method = $dto->method;
        $payload = $dto->payload;

        $gateway = PaymentGatewayFactory::make($method);
        $payment = $gateway->process($order, $payload);

        return (new PaymentResource($payment))->response()->setStatusCode(201);
    }
}
