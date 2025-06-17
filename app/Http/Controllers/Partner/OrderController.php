<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $partner = auth()->user()->cateringPartner;
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        $query = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            ->distinct();
        
        if ($request->has('status') && $request->status) {
            $query->where('orders.status', $request->status);
        }
        
        $orders = $query->orderBy('orders.created_at', 'desc')
            ->paginate(10);
        
        return view('partner.orders.index', compact('orders'));
    }

    public function show($orderId)
    {
        $partner = auth()->user()->cateringPartner;
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        // Check if the order contains any menu from this partner
        $hasPartnerMenu = OrderItem::where('order_id', $orderId)
            ->whereIn('menu_id', $partnerMenus)
            ->exists();
        
        if (!$hasPartnerMenu) {
            abort(403);
        }
        
        $order = Order::with(['user', 'orderItems.menu', 'payment'])
            ->findOrFail($orderId);
        
        // Filter order items to only show this partner's items
        $partnerOrderItems = $order->orderItems->filter(function ($item) use ($partnerMenus) {
            return in_array($item->menu_id, $partnerMenus->toArray());
        });
        
        return view('partner.orders.show', compact('order', 'partnerOrderItems'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,delivered,cancelled'
        ]);
        
        $partner = auth()->user()->cateringPartner;
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        // Check if the order contains any menu from this partner
        $hasPartnerMenu = OrderItem::where('order_id', $orderId)
            ->whereIn('menu_id', $partnerMenus)
            ->exists();
        
        if (!$hasPartnerMenu) {
            abort(403);
        }
        
        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();
        
        return redirect()->route('partner.orders.show', $order->id)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }
}