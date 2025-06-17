<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['orderItems.menu', 'payment']);

        return view('customer.orders.show', compact('order'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $payment = $order->payment;

        if (!$payment) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan.');
        }

        // Delete old payment proof if exists
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        // Upload new payment proof
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Update payment
        $payment->update([
            'payment_proof' => $paymentProofPath,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Pembayaran Anda sedang diverifikasi.');
    }
}