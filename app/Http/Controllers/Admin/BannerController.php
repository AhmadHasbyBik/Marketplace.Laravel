<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $banners = Banner::orderBy('order')->paginate(10);

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerRequest $request): RedirectResponse
    {
        Banner::create($request->validated());

        return redirect()->route('admin.banners.index')->with('success', 'Banner tersimpan');
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
    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $banner->update($request->validated());

        return redirect()->route('admin.banners.index')->with('success', 'Banner diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner dihapus');
    }
}
