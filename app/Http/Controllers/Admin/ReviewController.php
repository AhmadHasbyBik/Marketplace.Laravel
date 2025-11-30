<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $reviews = Review::with(['product', 'user'])
            ->when($status === 'pending', fn($q) => $q->where('is_approved', false)->where('is_rejected', false))
            ->when($status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($status === 'rejected', fn($q) => $q->where('is_rejected', true))
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews', 'status'));
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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        $action = $request->input('action');

        $review->update([
            'is_approved' => $action === 'approve',
            'is_rejected' => $action === 'reject',
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Status ulasan diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan dihapus');
    }
}
