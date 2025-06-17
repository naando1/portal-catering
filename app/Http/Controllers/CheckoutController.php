<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with(['cartItems.menu.cateringPartner'])
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong, silakan tambahkan menu terlebih dahulu.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string',
            'payment_proof' => 'required|image|max:2048',
            'note' => 'nullable|string'
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->with(['cartItems.menu'])
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong, silakan tambahkan menu terlebih dahulu.');
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'total_amount' => $cart->total,
            'delivery_address' => $request->delivery_address,
            'note' => $request->note
        ]);

        // Create order items
        foreach ($cart->cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item->menu_id,
                'quantity' => $item->quantity,
                'price' => $item->menu->price,
                'subtotal' => $item->menu->price * $item->quantity
            ]);
        }

        // Upload payment proof
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Create payment
        Payment::create([
            'order_id' => $order->id,
            'amount' => $cart->total,
            'payment_proof' => $paymentProofPath,
            'status' => 'pending'
        ]);

        // Clear cart
        $cart->cartItems()->delete();

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function success(Request $request)
    {
        $order = Order::where('id', $request->order)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->route('home');
        }

        return view('checkout.success', compact('order'));
    }
}
