<?php


namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product', 'shippingMethod'])
            ->latest()
            ->paginate(10);

        return view('front.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load(['items.product', 'history', 'address', 'user']);
        $reviewedProductIds = Review::where('user_id', auth()->id())
            ->where('order_reference', $order->order_number)
            ->pluck('product_id')
            ->toArray();

        return view('front.orders.show', compact('order', 'reviewedProductIds'));
    }

    /**
     * Store the payment proof uploaded by the customer.
     */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'max:5120'],
        ]);

        if ($order->payment_proof_path) {
            Storage::disk('public')->delete($order->payment_proof_path);
        }

        $path = Storage::disk('public')->putFile('payment-proofs', $validated['payment_proof']);
        $order->update(['payment_proof_path' => $path]);

        return redirect()->route('front.orders.show', $order)
            ->with('success', 'Bukti pembayaran telah dikirim. Admin akan melakukan verifikasi.');
    }

    /**
     * Download a PDF invoice for the paid order.
     */
    public function invoice(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->is_paid, 403);

        $order->load(['items.product', 'shippingMethod', 'address', 'user']);

        $pdf = Pdf::loadView('front.orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$order->order_number}.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
