<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialPurchaseTransactionRequest;
use App\Models\MaterialPurchase;
use Illuminate\Http\RedirectResponse;

class MaterialPurchaseTransactionController extends Controller
{
    public function store(MaterialPurchaseTransactionRequest $request, MaterialPurchase $materialPurchase): RedirectResponse
    {
        $materialPurchase->transactions()->create(
            $request->safe()->only(['transaction_date', 'payment_method', 'amount', 'notes'])
        );

        return redirect()->route('admin.material-purchases.index')->with('success', 'Transaksi supplier tercatat.');
    }
}
