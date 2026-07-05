<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\DtfSize;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Store cart items and prepare for checkout
     */
    public function checkout(Request $request)
    {

    // Log::info('Checkout request received', [
    //         'user_id' => auth()->id(),
    //         'session_id' => session()->getId(),
    //         'request_data' => $request->all(),
    //     ]);
        try {
            // Get items from either 'items' or 'dtf_items' parameter name
            $items = $request->input('items', []);
            if (empty($items)) {
                $items = $request->input('dtf_items', []);
            }
            
            $regularItems = $request->input('regular_items', []);

            $savedItems = [];
            $userId = auth()->id();
            $sessionId = session()->getId();

            // \Log::info('Checkout request', [
            //     'items_count' => count($items),
            //     'has_files' => $request->hasFile('items.0.image'),
            //     'user_id' => $userId,
            //     'session_id' => $sessionId,
            // ]);

            // Process DTF Transfer items
            foreach ($items as $index => $item) {
                $imagePath = null;

                // Store image if uploaded
                if ($request->hasFile("items.{$index}.image")) {
                    $image = $request->file("items.{$index}.image");
                    $imagePath = $image->store('cart-images', 'public');
                    // \Log::info('Image stored', ['path' => $imagePath, 'size' => $image->getSize()]);
                } else {
                    \Log::warning('No image found for item', ['index' => $index]);
                }

                if (!isset($item['dtf_size_id'])) {
                    throw new \Exception('Missing dtf_size_id for item ' . $index);
                }

                $dtfSize = DtfSize::findOrFail($item['dtf_size_id']);

                $cartItem = CartItem::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'dtf_size_id' => $item['dtf_size_id'],
                    'quantity' => (int)($item['quantity'] ?? 1),
                    'image_path' => $imagePath,
                    'unit_price' => $dtfSize->price,
                    'total_price' => $dtfSize->price * (int)($item['quantity'] ?? 1),
                ]);

                $savedItems[] = $cartItem;
                // \Log::info('Cart item created', ['id' => $cartItem->id, 'size_id' => $item['dtf_size_id']]);
            }

            // Calculate totals
            $subtotal = collect($savedItems)->sum('total_price');
            $shipping = $subtotal > 50 ? 0 : 10;
            $total = $subtotal + $shipping;

            // Prepare checkout data
            $checkoutData = [
                'items' => $savedItems,
                'regular_items' => $regularItems,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
            ];

            // Create checkout session (integrate with Stripe/Payment provider)
            session()->put('checkout_data', $checkoutData);

            // \Log::info('Checkout successful', ['items_count' => count($savedItems), 'total' => $total]);

            return response()->json([
                'success' => true,
                'checkout_url' => route('checkout.show'),
                'data' => $checkoutData,
            ]);

        } catch (\Exception $e) {
            \Log::error('Checkout error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get user's cart items
     */
    public function index()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $items = CartItem::where(function ($query) use ($userId, $sessionId) {
            $query->where('user_id', $userId)
                  ->orWhere('session_id', $sessionId);
        })
        ->with('dtfSize')
        ->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $items->sum('total_price'),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);

        // Delete image if exists
        if ($cartItem->image_path && Storage::disk('public')->exists($cartItem->image_path)) {
            Storage::disk('public')->delete($cartItem->image_path);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);
        $quantity = (int)$request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->destroy($id);
        }

        $cartItem->update([
            'quantity' => $quantity,
        ]);

        $cartItem->updateTotalPrice();

        return response()->json([
            'success' => true,
            'item' => $cartItem,
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        CartItem::where(function ($query) use ($userId, $sessionId) {
            $query->where('user_id', $userId)
                  ->orWhere('session_id', $sessionId);
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }
}
