<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingMethodRequest;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $methods = ShippingMethod::orderBy('name')->paginate(15);

        return view('admin.shipping.index', compact('methods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingMethodRequest $request): RedirectResponse
    {
        ShippingMethod::create($request->validated());

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Metode pengiriman dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingMethod $method): View
    {
        return view('admin.shipping.edit', compact('method'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShippingMethodRequest $request, ShippingMethod $method): RedirectResponse
    {
        $method->update($request->validated());

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Metode pengiriman diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingMethod $method): RedirectResponse
    {
        $method->delete();

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Metode pengiriman dihapus');
    }
}
