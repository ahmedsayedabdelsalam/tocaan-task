<?php

namespace App\Http\Controllers\API;

use App\Data\OrderData;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreOrderRequest;
use App\Http\Requests\API\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Auth::guard('api')->user()->orders();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $orders = $query->latest()->paginate();

        return OrderResource::collection($orders)->response();
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $orderData = OrderData::from($request->validated());

        $order = Auth::guard('api')->user()->orders()->create([
            'details' => $orderData->items->toArray(),
            'total' => $orderData->getTotal(),
        ]);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        $order = Auth::guard('api')->user()->orders()->findOrFail($id);

        return (new OrderResource($order))->response();
    }

    public function update(UpdateOrderRequest $request, string $id): JsonResponse
    {
        $order = Auth::guard('api')->user()->orders()->findOrFail($id);

        if ($request->filled('items')) {
            $orderData = OrderData::from($request->validated());
            $order->details = $orderData->items->toArray();
            $order->total = $orderData->getTotal();
        }

        if ($request->filled('status')) {
            $order->status = $request->input('status');
        }

        $order->save();

        return (new OrderResource($order))->response();
    }

    public function destroy(string $id): JsonResponse
    {
        $order = Auth::guard('api')->user()->orders()->findOrFail($id);

        if ($order->payments()->exists()) {
            return response()->json(['message' => 'Order cannot be deleted because it has associated payments.'], 422);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
