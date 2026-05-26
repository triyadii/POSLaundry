<?php

namespace App\Http\Controllers\Backend\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Mail;

class KasirController extends Controller
{
    public function index()
    {
        // Ambil transaksi laundry aktif
        $orders = Order::with(['customer', 'promo'])
            ->whereIn('order_status', ['diterima', 'dicuci', 'dikeringkan', 'disetrika', 'packing', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung Counter Statistik Laundry
        $pendingCount = Order::whereIn('order_status', ['diterima', 'dicuci', 'dikeringkan', 'disetrika', 'packing'])->count();
        $completedTodayCount = Order::where('order_status', 'selesai')
            ->whereDate('updated_at', today())
            ->count();
        $totalWeightToday = OrderDetail::whereHas('order', function($q) {
                $q->whereDate('created_at', today());
            })
            ->whereHas('service', function($q) {
                $q->where('unit', 'kg');
            })
            ->sum('qty');

        return view('backend.kasir.index', compact('orders', 'pendingCount', 'completedTodayCount', 'totalWeightToday'));
    }

    public function createOrder(Request $request)
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        $services = Service::with('category')->where('is_active', true)->get();
        $promos = Promo::where('is_active', true)->get();
        $setting = Setting::first();

        return view('backend.kasir.order', compact('customers', 'services', 'promos', 'setting'));
    }

    public function storeOrder(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'customer_id' => 'nullable',
            'customer_name_manual' => 'nullable|string|max:255',
            'customer_phone_manual' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'nullable|string',
            'order_type' => 'nullable|in:self_pickup,delivery',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,midtrans,down_payment',
            'down_payment_amount' => 'nullable|numeric|min:0',
            'cart' => 'required|array|min:1',
            'cart.*.service_id' => 'required|exists:services,id',
            'cart.*.qty' => 'required|numeric|min:0.01',
            'cart.*.item_condition' => 'nullable|string',
            'cart.*.note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        try {
            DB::beginTransaction();

            $customer_id = null;
            $customer_name = $request->customer_name_manual;
            $customer_email = $request->customer_email;
            $delivery_address = $request->delivery_address;
            $customer = null;

            if ($request->customer_id && $request->customer_id !== 'manual') {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer_id = $customer->id;
                    $customer_name = $customer->name;
                    if (!$customer_email) {
                        $customer_email = $customer->email;
                    }
                    if (!$delivery_address) {
                        $delivery_address = $customer->address;
                    }
                }
            }

            if (!$customer_name) {
                $customer_name = 'Walk-in Customer';
            }

            // 1. Hitung Subtotal
            $subtotal = 0;
            foreach ($request->cart as $item) {
                $service = Service::findOrFail($item['service_id']);
                $subtotal += $item['qty'] * $service->price_per_unit;
            }

            // 2. Hitung Diskon Promo / Member
            $discount_amount = 0;
            if ($request->promo_id) {
                $promo = Promo::find($request->promo_id);
                if ($promo && $promo->is_active) {
                    if ($promo->discount_type == 'percentage') {
                        $discount_amount = round($subtotal * ($promo->discount_value / 100));
                    } else {
                        $discount_amount = $promo->discount_value;
                    }
                }
            }

            // Tambahan Diskon VIP
            if ($customer && $customer->member_status == 'VIP') {
                $vipDiscount = round(($subtotal - $discount_amount) * 0.10); // Tambahan diskon 10% untuk VIP
                $discount_amount += $vipDiscount;
            }

            $net_subtotal = $subtotal - $discount_amount;
            if ($net_subtotal < 0) $net_subtotal = 0;

            // 3. Pajak
            $setting = Setting::first();
            $tax_rate = $setting ? $setting->tax_rate : 0;
            $tax = round($net_subtotal * ($tax_rate / 100));
            
            // Biaya Ongkir / Delivery
            $delivery_fee = 0;
            if ($request->order_type === 'delivery') {
                $delivery_fee = $request->delivery_fee ?? 0;
            }
            
            $grand_total = $net_subtotal + $tax + $delivery_fee;

            // 4. Hitung DP vs Pembayaran Penuh
            $down_payment = 0;
            $payment_status = 'unpaid';

            if ($request->payment_method == 'down_payment') {
                $down_payment = $request->down_payment_amount ?? 0;
                if ($down_payment >= $grand_total) {
                    $down_payment = $grand_total;
                    $payment_status = 'paid';
                }
            } else {
                // Full Payment
                $down_payment = $grand_total;
                $payment_status = 'paid';
            }

            // Calculate cash received and change for cash payments
            $cash_received = null;
            $cash_change = null;
            if ($request->payment_method === 'cash') {
                $cash_received = $request->cash_received ?? $grand_total;
                $cash_change = max(0, $cash_received - $grand_total);
            }

            $invoice_no = 'LND-' . date('YmdHis') . rand(10, 99);
            
            // 5. Simpan Order Utama
            $order = Order::create([
                'invoice_no' => $invoice_no,
                'customer_id' => $customer_id,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'delivery_address' => $delivery_address,
                'order_type' => $request->order_type ?? 'self_pickup',
                'promo_id' => $request->promo_id,
                'subtotal' => $subtotal,
                'discount_amount' => $discount_amount,
                'tax' => $tax,
                'delivery_fee' => $delivery_fee,
                'grand_total' => $grand_total,
                'down_payment' => $down_payment,
                'cash_received' => $cash_received,
                'cash_change' => $cash_change,
                'payment_method' => $request->payment_method,
                'payment_status' => $payment_status,
                'order_status' => 'diterima',
                'special_instructions' => $request->special_instructions,
                'estimated_completed_at' => $request->estimated_completed_at,
            ]);

            // 6. Simpan Detail Pakaian / Cucian
            foreach ($request->cart as $item) {
                $service = Service::findOrFail($item['service_id']);
                $itemSubtotal = $item['qty'] * $service->price_per_unit;

                $order->details()->create([
                    'service_id' => $service->id,
                    'qty' => $item['qty'],
                    'price' => $service->price_per_unit,
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['note'] ?? null,
                    'item_condition' => $item['item_condition'] ?? null,
                    'status' => 'entry',
                ]);
            }

            // 7. Simpan Order Status Log
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'diterima',
                'changed_by' => Auth::id(),
                'notes' => 'Pakaian diterima di POS kasir dan mulai diproses.',
            ]);

            // 8. Berikan Poin Loyalitas Pelanggan (1 poin per kelipatan Rp 10.000)
            if ($customer && $grand_total >= 10000) {
                $addedPoints = floor($grand_total / 10000);
                $customer->increment('loyalty_points', $addedPoints);
            }

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('tambah transaksi')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()],
                    'new' => $order->toArray(),
                ])->log('Membuat invoice laundry: ' . $order->invoice_no);

            // 9. Kirim Email Notifikasi (Hooks/Event Placeholder)
            // Kami telah membuat fungsi log/trace agar email terkirim nanti secara dinamis
            try {
                // \Mail::to($customer->email)->send(new \App\Mail\LaundryCreatedNotification($order));
                \Log::info("Email Notification Hook: Laundry order {$order->invoice_no} created successfully. Estimated completion: {$order->estimated_completed_at}. Sent to customer {$customer->name}.");
            } catch (\Exception $e) {
                \Log::error("Email Notification Error: " . $e->getMessage());
            }

            DB::commit();

            // Jika Midtrans dipilih
            if ($request->payment_method == 'midtrans') {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;
                \Midtrans\Config::$overrideNotifUrl = 'https://omnificent-reena-intermeasurable.ngrok-free.dev/api/midtrans-webhook';

                $params = [
                    'transaction_details' => ['order_id' => $invoice_no, 'gross_amount' => (int) $down_payment],
                    'customer_details' => [
                        'first_name' => $customer->name,
                        'phone' => $customer->phone,
                    ]
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);

                return response()->json([
                    'success' => true,
                    'type' => 'midtrans',
                    'snap_token' => $snapToken,
                    'order_id' => $order->id,
                    'message' => 'Layanan Midtrans diinisiasi.'
                ]);
            }

            return response()->json([
                'success' => true,
                'type' => $request->payment_method,
                'order_id' => $order->id,
                'message' => 'Order laundry berhasil dibuat!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
        }
    }

    public function payExistingOrder(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,midtrans',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            $remaining_amount = $order->grand_total - $order->down_payment;

            if ($remaining_amount <= 0) {
                return response()->json(['success' => false, 'error' => 'Invoice ini sudah lunas!'], 400);
            }

            $order->update([
                'down_payment' => $order->grand_total, // Set DP to match grand total
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method, // Update final payment method
            ]);

            // Tambahkan catatan status lunas
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $order->order_status,
                'changed_by' => Auth::id(),
                'notes' => 'Pelunasan sisa tagihan Rp ' . number_format($remaining_amount, 0, ',', '.') . ' berhasil diterima kasir.',
            ]);

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('pelunasan laundry')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'order_id' => $order->id,
                    'paid_amount' => $remaining_amount,
                ])->log('Melakukan pelunasan invoice laundry: ' . $order->invoice_no);

            // Email Notification Trigger
            try {
                \Log::info("Email Notification Hook: Laundry order {$order->invoice_no} has been FULLY PAID.");
            } catch (\Exception $e) {
                // Silently bypass
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'type' => 'cash',
                'order_id' => $order->id,
                'message' => 'Sisa tagihan lunas!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => 'Gagal melunasi tagihan: ' . $e->getMessage()], 500);
        }
    }

    public function clearTable($id)
    {
        // Untuk laundry, clearTable dirubah menjadi "Serahkan Laundry ke Pelanggan (Diambil)"
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            if ($order->payment_status != 'paid') {
                return response()->json(['success' => false, 'error' => 'Gagal! Tagihan laundry wajib dilunasi terlebih dahulu.'], 400);
            }

            $order->update(['order_status' => 'diambil']);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'diambil',
                'changed_by' => Auth::id(),
                'notes' => 'Pakaian telah diambil oleh pelanggan. Transaksi selesai.',
            ]);

            // === ACTIVITY LOG ===
            activity()
                ->useLog('pengambilan laundry')
                ->causedBy(Auth::user())
                ->log('Pakaian untuk invoice ' . $order->invoice_no . ' telah diambil oleh pelanggan.');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Laundry berhasil diserahkan ke pelanggan!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:dicuci,selesai',
        ]);

        try {
            DB::beginTransaction();
            $order = Order::findOrFail($request->order_id);
            $oldStatus = $order->order_status;
            $newStatus = $request->status;

            $order->update(['order_status' => $newStatus]);

            // Sync order details status transitions
            if ($newStatus === 'dicuci') {
                $order->details()->update(['status' => 'process']);
            } elseif ($newStatus === 'selesai') {
                $order->details()->update(['status' => 'done']);
            }

            // Save status log
            $note = "Status laundry diperbarui dari " . strtoupper($oldStatus) . " menjadi " . strtoupper($newStatus) . " oleh Kasir.";
            if ($newStatus === 'selesai') {
                $note = "Pakaian selesai diproses dan siap diambil pelanggan.";
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'changed_by' => Auth::id(),
                'notes' => $note,
            ]);

            // Trigger Email Notification when laundry is finished ('selesai')
            if ($newStatus === 'selesai') {
                $email = $order->customer_email ?? ($order->customer->email ?? null);
                if ($email) {
                    try {
                        $deliveryLabel = ($order->order_type === 'delivery') ? '🚚 Antar ke Rumah (Delivery)' : '🧺 Ambil Sendiri (Self-Pickup)';
                        $paymentStatusLabel = ($order->payment_status === 'paid') ? 'Paid / Lunas' : 'Belum Lunas';
                        $paymentColor = ($order->payment_status === 'paid') ? '#16a34a' : '#ef4444';

                        Mail::send([], [], function ($message) use ($order, $email, $deliveryLabel, $paymentStatusLabel, $paymentColor) {
                            $message->to($email)
                                ->subject("Laundry Selesai Di-proses! 🎉 - Invoice #{$order->invoice_no}")
                                ->html("
                                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
                                        <div style='text-align: center; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;'>
                                            <h2 style='color: #0284c7; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;'>LaundrySync POS</h2>
                                            <p style='color: #64748b; font-size: 14px; margin: 5px 0 0 0;'>Cepat, Bersih, dan Terpercaya</p>
                                        </div>
                                        <div style='margin-bottom: 25px;'>
                                            <p style='font-size: 16px; color: #334155; line-height: 1.6; margin-top: 0;'>
                                                Halo <strong>{$order->customer_name}</strong>,
                                            </p>
                                            <p style='font-size: 15px; color: #475569; line-height: 1.6;'>
                                                Kabar baik! Pakaian laundry Anda saat ini <strong>sudah selesai diproses secara wangi & rapi</strong>. Pakaian Anda telah siap untuk diambil atau diantarkan.
                                            </p>
                                        </div>
                                        <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #f1f5f9; margin-bottom: 25px;'>
                                            <h3 style='margin: 0 0 12px 0; color: #1e293b; font-size: 15px; font-weight: 600;'>📋 DETAIL NOTA LAUNDRY:</h3>
                                            <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                                                <tr>
                                                    <td style='padding: 6px 0; color: #64748b;'>No. Invoice:</td>
                                                    <td style='padding: 6px 0; font-weight: bold; text-align: right; color: #0f172a;'>#{$order->invoice_no}</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding: 6px 0; color: #64748b;'>Nama Pelanggan:</td>
                                                    <td style='padding: 6px 0; font-weight: bold; text-align: right; color: #0f172a;'>{$order->customer_name}</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding: 6px 0; color: #64748b;'>Layanan:</td>
                                                    <td style='padding: 6px 0; font-weight: bold; text-align: right; color: #0f172a;'>{$deliveryLabel}</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding: 6px 0; color: #64748b;'>Grand Total:</td>
                                                    <td style='padding: 6px 0; font-weight: 800; color: #16a34a; text-align: right; font-size: 16px;'>Rp " . number_format($order->grand_total, 0, ',', '.') . "</td>
                                                </tr>
                                                <tr>
                                                    <td style='padding: 6px 0; color: #64748b;'>Status Tagihan:</td>
                                                    <td style='padding: 6px 0; font-weight: bold; text-align: right; color: {$paymentColor}; text-transform: uppercase;'>
                                                        {$paymentStatusLabel}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style='text-align: center; margin-bottom: 25px;'>
                                            <p style='font-size: 13px; color: #64748b; margin: 0 0 10px 0;'>Silakan bawa struk fisik atau tunjukkan email ini saat pengambilan.</p>
                                        </div>
                                        <div style='text-align: center; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8; line-height: 1.5;'>
                                            Terima kasih atas kepercayaan Anda menggunakan jasa kami! <br>
                                            <strong>LaundrySync POS System</strong>
                                        </div>
                                    </div>
                                ");
                        });
                        \Log::info("Email Notification Sent: Laundry order {$order->invoice_no} status changed to 'selesai'. Email sent to {$email}.");
                    } catch (\Exception $mailErr) {
                        \Log::error("Email Notification Failed for order {$order->invoice_no}: " . $mailErr->getMessage());
                    }
                } else {
                    \Log::info("Email Notification Ignored: Laundry order {$order->invoice_no} completed but no email address is registered.");
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Status laundry berhasil diperbarui menjadi ' . strtoupper($newStatus) . '!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengubah status laundry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt($id)
    {
        $order = Order::with(['customer', 'details.service'])->findOrFail($id);
        $setting = Setting::first();
        return view('backend.kasir.print', compact('order', 'setting'));
    }
}
