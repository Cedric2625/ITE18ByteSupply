<?php

namespace App\Http\Controllers;

use App\Models\HardwareComponent;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\SelectedComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Reuse filtering similar to admin index
        $query = HardwareComponent::with(['category','supplier'])->latest();
        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('supplier')) $query->where('supplier_id', $request->supplier);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('component_name','like',"%$s%")
                  ->orWhere('brand','like',"%$s%")
                  ->orWhere('model','like',"%$s%")
                  ->orWhere('component_reference_number','like',"%$s%");
            });
        }
        $components = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('shop.index', compact('components','categories','suppliers'));
    }

    public function cart()
    {
        $cart = session('cart', []);
        return view('shop.cart', compact('cart'));
    }

    public function addToCart(Request $request, HardwareComponent $hardwareComponent)
    {
        $validated = $request->validate(['quantity' => ['required','integer','min:1']]);
        $cart = session('cart', []);
        $newQty = ($cart[$hardwareComponent->id]['quantity'] ?? 0) + $validated['quantity'];
        $cart[$hardwareComponent->id] = [
            'id' => $hardwareComponent->id,
            'name' => $hardwareComponent->component_name,
            'price' => (float) $hardwareComponent->retail_price,
            'quantity' => min($newQty, max(1, $hardwareComponent->stock_quantity)),
        ];
        session(['cart' => $cart]);
        return back()->with('success', 'Added to cart.');
    }

    public function removeFromCart(HardwareComponent $hardwareComponent)
    {
        $cart = session('cart', []);
        unset($cart[$hardwareComponent->id]);
        session(['cart' => $cart]);
        return back()->with('success', 'Removed from cart.');
    }

    public function checkout()
    {
        $cart = session('cart', []);
        abort_if(empty($cart), 404);
        return view('shop.checkout', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => ['required','in:cash,credit_card,bank_transfer,online_payment'],
        ]);
        $cart = session('cart', []);
        abort_if(empty($cart), 404);

        return DB::transaction(function () use ($request, $cart) {
            $buyer = Auth::guard('buyer')->user();
            $order = Order::create([
                'order_date' => now(),
                'total_amount' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']),
                'shipping_status' => 'pending',
                'tracking_number' => null,
                'estimated_delivery' => now()->addDays(7),
                'order_reference_number' => strtoupper(str()->random(12)),
                'buyer_id' => $buyer->id,
                'admin_id' => 1, // assign default; in real flow choose on admin side
                'payment_method' => $request->payment_method,
            ]);

            foreach ($cart as $item) {
                SelectedComponent::create([
                    'order_id' => $order->id,
                    'component_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
                // reduce stock
                HardwareComponent::where('id', $item['id'])->decrement('stock_quantity', $item['quantity']);
            }

			session()->forget('cart');
			return redirect()->route('shop.orders.show', $order)->with('success', 'Order placed successfully.');
        });
    }

    public function myOrders()
    {
        $buyer = Auth::guard('buyer')->user();
        $orders = Order::where('buyer_id', $buyer->id)
            ->latest('order_date')
            ->paginate(10);
        return view('shop.orders.index', compact('orders'));
    }

    public function myOrderShow(Order $order)
    {
        $buyer = Auth::guard('buyer')->user();
        abort_unless($order->buyer_id === $buyer->id, 403);
        $order->load(['selectedComponents.hardwareComponent', 'buyer']);
        return view('shop.orders.show', compact('order'));
    }

	public function settings()
	{
		$buyer = Auth::guard('buyer')->user();
		return view('shop.settings', compact('buyer'));
	}
}


