@extends('layouts.front')

@section('title', 'Checkout - UMKM Dapoer Cupid')

@section('content')
    <form action="{{ route('front.checkout.store') }}" method="POST" class="space-y-6">
        @csrf
        <h1 class="text-3xl font-semibold">Checkout</h1>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-5">
                <div class="space-y-1">
                    <h2 class="font-semibold text-lg">Detail Pengiriman</h2>
                    <p class="text-sm text-slate-500">Berat paket dihitung otomatis dari produk, cukup pilih provinsi, kota/kabupaten, dan kecamatan/desa dari Raja Ongkir.</p>
                </div>
                <div class="space-y-3 text-sm text-slate-500">
                    <label class="block space-y-1">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Provinsi</span>
                        <select
                            id="shipping-province-select"
                            name="destination_province"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-rose-500 focus:outline-none"
                            disabled
                        >
                            <option value="">Memuat provinsi...</option>
                        </select>
                    </label>
                    <label class="block space-y-1">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Kota / Kabupaten</span>
                        <select
                            id="shipping-city-select"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-rose-500 focus:outline-none disabled:bg-slate-50"
                            required
                            disabled
                        >
                            <option value="">Pilih provinsi</option>
                        </select>
                        <input type="hidden" name="destination_city_id" id="shipping-city-id-input" />
                        <input type="hidden" name="destination_city_name" id="shipping-city-name-input" />
                    </label>
                    <label class="block space-y-1">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Kecamatan / Desa</span>
                        <select
                            id="shipping-district-select"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-rose-500 focus:outline-none disabled:bg-slate-50"
                            disabled
                        >
                            <option value="">Pilih kota / kabupaten</option>
                        </select>
                        <input type="hidden" name="destination_district_id" id="shipping-district-id-input" />
                        <input type="hidden" name="destination_district_name" id="shipping-district-name-input" />
                    </label>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Berat paket (gram)</span>
                        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-900">
                            <span id="shipping-weight-display">{{ number_format(max($totalWeight, 1), 0, ',', '.') }} g</span>
                        </div>
                        <p class="text-xs text-slate-400">Berat otomatis diambil dari nilai berat tiap produk yang dikelola di dashboard admin.</p>
                    </div>
                    <button
                        type="button"
                        id="shipping-refresh-button"
                        class="w-full rounded-2xl bg-rose-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-600 disabled:cursor-not-allowed disabled:opacity-60"
                        disabled
                    >
                        Hitung ulang ongkir
                    </button>
                    <p id="shipping-location-hint" class="text-xs text-slate-500">Lengkapi provinsi, kota/kabupaten, dan kecamatan/desa untuk membuka opsi ongkir.</p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-4">
                <div class="space-y-2">
                    <h2 class="font-semibold text-lg">Metode Pengiriman</h2>
                    <p class="text-sm text-slate-500">Tarif dihitung real-time dari Raja Ongkir. Pilih layanan setelah berat dan tujuan siap.</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span>Tujuan</span>
                        <span id="shipping-destination-display">-</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <span>Total berat paket</span>
                        <span id="shipping-weight-summary">{{ number_format(max($totalWeight, 1), 0, ',', '.') }} g</span>
                    </div>
                    <div id="shipping-loading" class="rounded-2xl border border-slate-100 bg-white/80 px-4 py-3 text-sm text-slate-500">
                        Lengkapi provinsi, kota/kabupaten, dan kecamatan/desa untuk melihat tarif ongkir.
                    </div>
                    <div id="shipping-notice" class="hidden rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-500"></div>
                    <div id="shipping-error" class="hidden rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-600"></div>
                    <label class="block text-sm text-slate-500">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pilih kurir & layanan</span>
                        <select
                            id="shipping-options-select"
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 focus:border-rose-500 focus:outline-none"
                            disabled
                        >
                            <option value="">Pilih layanan pengiriman</option>
                        </select>
                    </label>
                    <div id="shipping-summary" class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                        <p class="text-slate-900 font-semibold mb-1">Belum ada pilihan pengiriman</p>
                        <p>Setelah tarif dimuat, pilih layanan untuk melihat detail biaya.</p>
                    </div>
                </div>
                <input type="hidden" name="shipping_method_id" id="shipping-method-input" />
                <input type="hidden" name="shipping_cost" id="shipping-cost-input" />
                <input type="hidden" name="shipping_service" id="shipping-service-input" />
                <input type="hidden" name="shipping_courier" id="shipping-courier-input" />
                <input type="hidden" name="shipping_etd" id="shipping-etd-input" />
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div id="order-summary" data-subtotal="{{ $subTotal }}">
                <h2 class="text-lg font-semibold mb-3">Ringkasan Pesanan</h2>
                <div class="space-y-3 text-sm text-slate-500">
                    @if($isQuickCheckout)
                        <p class="text-xs text-slate-500">Sesuaikan kuantitas di bawah sebelum menghitung ulang ongkir.</p>
                    @endif
                    @foreach($cart as $item)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-slate-900">{{ $item['name'] }}</div>
                                @if($isQuickCheckout)
                                    <div
                                        class="mt-2 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 px-3 py-1 text-xs text-slate-500"
                                        data-quick-quantity
                                        data-update-url="{{ route('front.checkout.quickUpdate', $item['product_id']) }}"
                                        data-max-stock="{{ max($item['stock'] ?? 1, 1) }}"
                                    >
                                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:border-rose-300" data-action="decrement">
                                            <span class="sr-only">Kurangi kuantitas</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                            </svg>
                                        </button>
                                        <input
                                            type="number"
                                            min="1"
                                            max="{{ max($item['stock'] ?? 1, 1) }}"
                                            value="{{ $item['quantity'] }}"
                                            aria-label="Kuantitas {{ $item['name'] }}"
                                            data-quantity-input
                                            class="w-16 rounded-2xl border border-slate-200 py-1 text-center text-sm font-semibold text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-200"
                                        />
                                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:border-rose-300" data-action="increment">
                                            <span class="sr-only">Tambah kuantitas</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                            </svg>
                                        </button>
                                        <span class="text-xs text-slate-400">Stok: {{ $item['stock'] ?? 0 }}</span>
                                    </div>
                                @else
                                    <div class="text-xs text-slate-500 mt-1">
                                        Kuantitas: {{ $item['quantity'] }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-sm font-semibold text-slate-900">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-dashed pt-4 text-sm text-slate-500">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span id="summary-subtotal">Rp{{ number_format($subTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir</span>
                        <span id="summary-shipping-cost">Rp0</span>
                    </div>
                    <div class="mt-2 flex justify-between text-base font-semibold text-slate-900">
                        <span>Total</span>
                        <span id="summary-total">Rp{{ number_format($subTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $paymentOptions = config('payment.options', []);
        @endphp
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-4">
            <div class="space-y-2">
                <h2 class="text-lg font-semibold">Metode Pembayaran</h2>
                <p class="text-sm text-slate-500">Pilih metode pembayaran yang paling nyaman dan ikuti petunjuk resmi di bawah ini.</p>
            </div>
            <div class="grid gap-3 md:grid-cols-2" id="payment-options">
                @forelse($paymentOptions as $value => $option)
                    <label
                        class="payment-option flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 transition hover:border-rose-300"
                        data-payment-title="{{ $option['label'] }}"
                        data-payment-details="{{ $option['details'] }}"
                        data-payment-value="{{ $value }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        <div class="flex flex-col gap-1 text-sm">
                            <span class="font-semibold text-slate-900">{{ $option['label'] }}</span>
                            <span class="text-xs text-slate-500">{{ $option['tagline'] }}</span>
                        </div>
                        <input type="radio" name="payment_method" value="{{ $value }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} />
                    </label>
                @empty
                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">Manual / Transfer</span>
                        <input type="radio" name="payment_method" value="manual" class="sr-only" checked />
                    </label>
                @endforelse
            </div>

            <div id="payment-instructions" class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                <p class="font-semibold text-slate-900" id="payment-instruction-title">Pilih metode pembayaran</p>
                <p class="text-slate-500" id="payment-instruction-body">Kami akan menampilkan instruksi terkini setelah Anda memilih opsi di atas.</p>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold">Catatan</label>
                <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm"></textarea>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-rose-500 px-4 py-3 text-white font-semibold">Konfirmasi Pesanan</button>
        </div>
    </form>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const shippingConfig = @json($shippingConfig);
            const shippingProvinceSelect = document.getElementById('shipping-province-select');
            const shippingCitySelect = document.getElementById('shipping-city-select');
            const shippingCityIdInput = document.getElementById('shipping-city-id-input');
            const shippingCityNameInput = document.getElementById('shipping-city-name-input');
            const shippingDistrictSelect = document.getElementById('shipping-district-select');
            const shippingDistrictIdInput = document.getElementById('shipping-district-id-input');
            const shippingDistrictNameInput = document.getElementById('shipping-district-name-input');
            const shippingDestinationDisplay = document.getElementById('shipping-destination-display');
            const shippingWeightDisplay = document.getElementById('shipping-weight-display');
            const shippingWeightSummary = document.getElementById('shipping-weight-summary');
            let shippingWeightValue = Math.max(Number(@json(max($totalWeight ?? 0, 1))), 1);
            const shippingRefreshButton = document.getElementById('shipping-refresh-button');
            const shippingOptionsSelect = document.getElementById('shipping-options-select');
            const shippingLoadingEl = document.getElementById('shipping-loading');
            const shippingNoticeEl = document.getElementById('shipping-notice');
            const shippingErrorEl = document.getElementById('shipping-error');
            const shippingSummaryEl = document.getElementById('shipping-summary');
            const shippingCostInput = document.getElementById('shipping-cost-input');
            const shippingMethodInput = document.getElementById('shipping-method-input');
            const shippingServiceInput = document.getElementById('shipping-service-input');
            const shippingCourierInput = document.getElementById('shipping-courier-input');
            const shippingEtdInput = document.getElementById('shipping-etd-input');
            const shippingLocationHint = document.getElementById('shipping-location-hint');
            const checkoutButton = document.querySelector('button[type="submit"]');
            const orderSummaryEl = document.getElementById('order-summary');
            const paymentOptionCards = document.querySelectorAll('[data-payment-value]');
            const paymentInstructionTitle = document.getElementById('payment-instruction-title');
            const paymentInstructionBody = document.getElementById('payment-instruction-body');
            const summaryShippingCostEl = document.getElementById('summary-shipping-cost');
            const summaryTotalEl = document.getElementById('summary-total');
            const orderSubtotal = Number(orderSummaryEl?.dataset.subtotal ?? 0);
            const defaultShippingSummary = shippingSummaryEl?.innerHTML ?? '';
            let shippingFetchTimer;

            const setCheckoutButtonState = (enabled) => {
                if (!checkoutButton) {
                    return;
                }
                checkoutButton.disabled = !enabled;
                checkoutButton.classList.toggle('opacity-60', !enabled);
                checkoutButton.classList.toggle('cursor-not-allowed', !enabled);
            };

            const formatCurrency = (value) => {
                const num = Number(value) || 0;
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
            };

            const updateSummaryTotals = (cost) => {
                const numericCost = Number(cost) || 0;
                if (summaryShippingCostEl) {
                    summaryShippingCostEl.textContent = formatCurrency(numericCost);
                }
                if (summaryTotalEl) {
                    summaryTotalEl.textContent = formatCurrency(orderSubtotal + numericCost);
                }
            };

            const resetShippingSelection = () => {
                if (shippingOptionsSelect) {
                    shippingOptionsSelect.innerHTML = '<option value="">Pilih layanan pengiriman</option>';
                    shippingOptionsSelect.disabled = true;
                }
                if (shippingCostInput) {
                    shippingCostInput.value = '';
                }
                if (shippingMethodInput) {
                    shippingMethodInput.value = '';
                }
                if (shippingServiceInput) {
                    shippingServiceInput.value = '';
                }
                if (shippingCourierInput) {
                    shippingCourierInput.value = '';
                }
                if (shippingEtdInput) {
                    shippingEtdInput.value = '';
                }
                if (shippingSummaryEl) {
                    shippingSummaryEl.innerHTML = defaultShippingSummary;
                }
                if (shippingNoticeEl) {
                    shippingNoticeEl.classList.add('hidden');
                    shippingNoticeEl.textContent = '';
                }
                if (shippingErrorEl) {
                    shippingErrorEl.classList.add('hidden');
                    shippingErrorEl.textContent = '';
                }
                updateSummaryTotals(0);
                setCheckoutButtonState(false);
            };

            const selectShippingOption = (option) => {
                if (!option || !option.value) {
                    return;
                }
                const costValue = Number(option.dataset.cost) || 0;
                if (shippingCostInput) {
                    shippingCostInput.value = costValue;
                }
                if (shippingMethodInput) {
                    shippingMethodInput.value = option.dataset.methodId || '';
                }
                if (shippingServiceInput) {
                    shippingServiceInput.value = option.dataset.serviceName || '';
                }
                if (shippingCourierInput) {
                    shippingCourierInput.value = option.dataset.courier || '';
                }
                if (shippingEtdInput) {
                    shippingEtdInput.value = option.dataset.etd || '';
                }
                updateSummaryTotals(costValue);
                if (shippingSummaryEl) {
                    const courierLabel = option.dataset.courier ? option.dataset.courier.toUpperCase() : '';
                    const serviceLabel = option.dataset.serviceName || 'Layanan';
                    const description = option.dataset.description || 'Estimasi tiba tersedia setelah pemilihan.';
                    shippingSummaryEl.innerHTML = `
                        <p class="text-slate-900 font-semibold text-sm">${courierLabel} · ${serviceLabel}</p>
                        <p class="text-xs text-slate-500">${description}</p>
                        <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                            <span>Estimasi tiba</span>
                            <span>${option.dataset.etd || '-'}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-sm font-semibold text-rose-600">
                            <span>Tarif ongkir</span>
                            <span>${formatCurrency(costValue)}</span>
                        </div>
                    `;
                }
                setCheckoutButtonState(Boolean(costValue && option.dataset.methodId));
            };

            const renderShippingOptions = (rates) => {
                if (!shippingOptionsSelect) {
                    return;
                }
                const services = [];
                (rates || []).forEach((rate) => {
                    (rate.services || []).forEach((service) => services.push(service));
                });
                shippingOptionsSelect.innerHTML = '<option value="">Pilih layanan pengiriman</option>';
                if (services.length === 0) {
                    shippingOptionsSelect.disabled = true;
                    if (shippingSummaryEl) {
                        shippingSummaryEl.innerHTML = `
                            <p class="text-slate-900 font-semibold mb-1">Tidak ada opsi pengiriman</p>
                            <p class="text-sm text-slate-500">Silakan ubah berat paket atau hubungi admin.</p>
                        `;
                    }
                    setCheckoutButtonState(false);
                    return;
                }
                services.forEach((service) => {
                    const option = document.createElement('option');
                    option.value = `${service.courier ?? ''}-${service.service ?? ''}-${service.cost ?? 0}`;
                    option.dataset.methodId = service.method_id ?? '';
                    option.dataset.courier = service.courier ?? '';
                    option.dataset.serviceName = service.service ?? '';
                    option.dataset.description = service.description ?? '';
                    option.dataset.cost = service.cost ?? 0;
                    option.dataset.etd = service.etd ?? '';
                    option.textContent = `${(service.courier ?? 'Kurir').toUpperCase()} · ${(service.service ?? 'Layanan')} (${service.etd ?? '-'}) — ${formatCurrency(Number(service.cost) || 0)}`;
                    shippingOptionsSelect.appendChild(option);
                });
                shippingOptionsSelect.disabled = false;
                const firstOption = shippingOptionsSelect.querySelector('option[value]:not([value=""])');
                if (firstOption) {
                    shippingOptionsSelect.value = firstOption.value;
                    selectShippingOption(firstOption);
                }
                if (shippingLoadingEl) {
                    shippingLoadingEl.textContent = 'Tarif berhasil dimuat. Pilih layanan pengiriman untuk melihat detail biaya.';
                }
            };

            const getWeightValue = () => Math.max(shippingWeightValue, 1);
            const canRequestRates = () => Boolean(
                shippingCityIdInput?.value &&
                shippingProvinceSelect?.value &&
                shippingDistrictIdInput?.value &&
                getWeightValue() > 0
            );

            const updateShippingActionState = () => {
                const canFetch = canRequestRates();
                if (shippingRefreshButton) {
                    shippingRefreshButton.disabled = !canFetch;
                }
                if (shippingLoadingEl) {
                    shippingLoadingEl.textContent = canFetch
                        ? 'Tekan tombol "Hitung ulang ongkir" untuk memperbarui tarif.'
                        : 'Lengkapi provinsi, kota/kabupaten, dan kecamatan/desa untuk melihat tarif ongkir.';
                }
            };

            const fetchShippingRates = async () => {
                if (!canRequestRates() || !shippingConfig.costs_url) {
                    return;
                }
                resetShippingSelection();
                if (shippingLoadingEl) {
                    shippingLoadingEl.textContent = 'Memuat tarif ongkir...';
                }
                try {
                    const params = new URLSearchParams();
                    params.set('destination_city_id', shippingCityIdInput?.value ?? '');
                    params.set('destination_city_name', shippingCityNameInput?.value ?? '');
                    params.set('destination_district_id', shippingDistrictIdInput?.value ?? '');
                    params.set('destination_district_name', shippingDistrictNameInput?.value ?? '');
                    params.set('destination_province', shippingProvinceSelect?.value ?? '');
                    params.set('weight', getWeightValue().toString());
                    params.set('price', 'lowest');
                    const response = await fetch(`${shippingConfig.costs_url}?${params.toString()}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal memuat tarif ongkir');
                    }
                    const destinationText = [
                        payload.destination?.province ?? shippingProvinceSelect?.value,
                        payload.destination?.city ?? shippingCityNameInput?.value,
                        payload.destination?.district ?? shippingDistrictNameInput?.value,
                    ].filter(Boolean).join(' · ');
                    if (shippingDestinationDisplay) {
                        shippingDestinationDisplay.textContent = destinationText || '-';
                    }
                    const shownWeight = Math.max(Number(payload.total_weight ?? getWeightValue()), 1);
                    shippingWeightValue = shownWeight;
                    if (shippingWeightDisplay) {
                        shippingWeightDisplay.textContent = `${shownWeight.toLocaleString('id-ID')} g`;
                    }
                    if (shippingWeightSummary) {
                        shippingWeightSummary.textContent = `${shownWeight.toLocaleString('id-ID')} g`;
                    }
                    if (shippingNoticeEl) {
                        if (payload.fallback_message) {
                            shippingNoticeEl.textContent = payload.fallback_message;
                            shippingNoticeEl.classList.remove('hidden');
                        } else {
                            shippingNoticeEl.classList.add('hidden');
                            shippingNoticeEl.textContent = '';
                        }
                    }
                    renderShippingOptions(payload.data || []);
                    updateShippingActionState();
                } catch (error) {
                    if (shippingLoadingEl) {
                        shippingLoadingEl.textContent = 'Gagal memuat tarif ongkir';
                    }
                    if (shippingErrorEl) {
                        shippingErrorEl.textContent = error.message;
                        shippingErrorEl.classList.remove('hidden');
                    }
                    if (shippingSummaryEl) {
                        shippingSummaryEl.innerHTML = `
                            <p class="text-slate-900 font-semibold mb-1">Kesalahan pemuatan ongkir</p>
                            <p class="text-sm text-slate-500">${error.message}</p>
                        `;
                    }
                    setCheckoutButtonState(false);
                }
            };

            const scheduleShippingFetch = () => {
                clearTimeout(shippingFetchTimer);
                if (!canRequestRates()) {
                    updateShippingActionState();
                    return;
                }
                shippingFetchTimer = window.setTimeout(fetchShippingRates, 400);
            };

            const loadProvinces = async () => {
                if (!shippingProvinceSelect || !shippingConfig.provinces_url) {
                    return;
                }
                if (shippingErrorEl) {
                    shippingErrorEl.classList.add('hidden');
                    shippingErrorEl.textContent = '';
                }
                shippingProvinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';
                shippingProvinceSelect.disabled = true;
                try {
                    const response = await fetch(shippingConfig.provinces_url, {
                        headers: { Accept: 'application/json' },
                    });
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal memuat provinsi');
                    }
                    const provinces = payload.data || [];
                    if (provinces.length === 0) {
                        throw new Error('Tidak ada provinsi tersedia');
                    }
                    shippingProvinceSelect.innerHTML = '<option value="">Pilih provinsi</option>';
                    provinces.forEach((province) => {
                        const option = document.createElement('option');
                        const label = province.name ?? province.province ?? '';
                        option.value = label;
                        option.textContent = label || 'Provinsi';
                        option.dataset.id = province.id ?? province.province_id ?? '';
                        shippingProvinceSelect.appendChild(option);
                    });
                    shippingProvinceSelect.disabled = false;
                } catch (error) {
                    shippingProvinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
                    shippingProvinceSelect.disabled = true;
                    if (shippingErrorEl) {
                        shippingErrorEl.textContent = error.message;
                        shippingErrorEl.classList.remove('hidden');
                    }
                    if (shippingLocationHint) {
                        shippingLocationHint.textContent = 'Gagal memuat data provinsi.';
                        shippingLocationHint.classList.add('text-rose-600');
                    }
                }
            };

            const loadCities = async (provinceId) => {
                if (!shippingCitySelect || !shippingConfig.cities_url) {
                    return;
                }
                shippingCitySelect.innerHTML = '<option value="">Memuat kota/kabupaten...</option>';
                shippingCitySelect.disabled = true;
                try {
                    const params = new URLSearchParams();
                    if (provinceId) {
                        params.set('province_id', provinceId);
                    }
                    const response = await fetch(`${shippingConfig.cities_url}?${params.toString()}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal memuat kota/kabupaten');
                    }
                    const cities = payload.data || [];
                    if (cities.length === 0) {
                        throw new Error('Tidak ada kota/kabupaten tersedia untuk provinsi ini');
                    }
                    shippingCitySelect.innerHTML = '<option value="">Pilih kota / kabupaten</option>';
                    cities.forEach((city) => {
                        const option = document.createElement('option');
                        const label = city.name ?? city.city ?? city.city_name ?? '';
                        option.value = city.id ?? city.city_id ?? '';
                        option.textContent = label || 'Kota / Kabupaten';
                        option.dataset.name = option.textContent;
                        shippingCitySelect.appendChild(option);
                    });
                    shippingCitySelect.disabled = false;
                } catch (error) {
                    shippingCitySelect.innerHTML = '<option value="">Gagal memuat kota/kabupaten</option>';
                    shippingCitySelect.disabled = true;
                    if (shippingErrorEl) {
                        shippingErrorEl.textContent = error.message;
                        shippingErrorEl.classList.remove('hidden');
                    }
                }
            };

            const loadDistricts = async (cityId) => {
                if (!shippingDistrictSelect || !shippingConfig.districts_url) {
                    return;
                }
                if (shippingErrorEl) {
                    shippingErrorEl.classList.add('hidden');
                    shippingErrorEl.textContent = '';
                }
                shippingDistrictSelect.innerHTML = '<option value="">Memuat kecamatan/desa...</option>';
                shippingDistrictSelect.disabled = true;
                if (shippingDistrictIdInput) {
                    shippingDistrictIdInput.value = '';
                }
                if (shippingDistrictNameInput) {
                    shippingDistrictNameInput.value = '';
                }
                if (!cityId) {
                    shippingDistrictSelect.innerHTML = '<option value="">Pilih kota / kabupaten terlebih dahulu</option>';
                    shippingDistrictSelect.disabled = true;
                    updateShippingActionState();
                    return;
                }
                try {
                    const params = new URLSearchParams();
                    params.set('city_id', cityId);
                    const response = await fetch(`${shippingConfig.districts_url}?${params.toString()}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal memuat kecamatan/desa');
                    }
                    const districts = payload.data || [];
                    if (districts.length === 0) {
                        throw new Error('Tidak ada kecamatan/desa untuk kota/kabupaten ini');
                    }
                    shippingDistrictSelect.innerHTML = '<option value="">Pilih kecamatan / desa</option>';
                    districts.forEach((district) => {
                        const option = document.createElement('option');
                        const label = district.name ?? district.district ?? '';
                        option.value = district.id ?? '';
                        option.textContent = label || 'Kecamatan / Desa';
                        option.dataset.name = option.textContent;
                        shippingDistrictSelect.appendChild(option);
                    });
                    shippingDistrictSelect.disabled = false;
                } catch (error) {
                    shippingDistrictSelect.innerHTML = '<option value="">Gagal memuat kecamatan/desa</option>';
                    shippingDistrictSelect.disabled = true;
                    if (shippingErrorEl) {
                        shippingErrorEl.textContent = error.message;
                        shippingErrorEl.classList.remove('hidden');
                    }
                }
                finally {
                    updateShippingActionState();
                }
            };

            const updateDestinationDisplay = () => {
                if (!shippingDestinationDisplay) {
                    return;
                }
                const provinceLabel = shippingProvinceSelect?.selectedOptions?.[0]?.textContent?.trim() || '';
                const cityLabel = shippingCitySelect?.selectedOptions?.[0]?.textContent?.trim() || '';
                const districtLabel = shippingDistrictSelect?.selectedOptions?.[0]?.textContent?.trim() || '';
                const display = [provinceLabel, cityLabel, districtLabel].filter(Boolean).join(' · ');
                shippingDestinationDisplay.textContent = display || '-';
            };

            shippingProvinceSelect?.addEventListener('change', () => {
                const selected = shippingProvinceSelect.selectedOptions?.[0];
                const provinceId = selected?.dataset?.id || '';
                if (shippingCitySelect) {
                    shippingCitySelect.value = '';
                    shippingCitySelect.disabled = true;
                }
                if (shippingDistrictSelect) {
                    shippingDistrictSelect.innerHTML = '<option value="">Pilih kota / kabupaten terlebih dahulu</option>';
                    shippingDistrictSelect.disabled = true;
                }
                shippingCityIdInput.value = '';
                shippingCityNameInput.value = '';
                if (shippingDistrictIdInput) {
                    shippingDistrictIdInput.value = '';
                }
                if (shippingDistrictNameInput) {
                    shippingDistrictNameInput.value = '';
                }
                updateDestinationDisplay();
                resetShippingSelection();
                loadCities(provinceId);
                updateShippingActionState();
            });

            shippingCitySelect?.addEventListener('change', () => {
                const selected = shippingCitySelect.selectedOptions?.[0];
                if (selected) {
                    shippingCityIdInput.value = selected.value;
                    shippingCityNameInput.value = selected.dataset.name || selected.textContent.trim();
                } else {
                    shippingCityIdInput.value = '';
                    shippingCityNameInput.value = '';
                }
                if (shippingDistrictIdInput) {
                    shippingDistrictIdInput.value = '';
                }
                if (shippingDistrictNameInput) {
                    shippingDistrictNameInput.value = '';
                }
                updateDestinationDisplay();
                resetShippingSelection();
                const cityId = selected?.value || '';
                loadDistricts(cityId);
                scheduleShippingFetch();
            });

            shippingDistrictSelect?.addEventListener('change', () => {
                const selected = shippingDistrictSelect.selectedOptions?.[0];
                if (selected && selected.value) {
                    shippingDistrictIdInput.value = selected.value;
                    shippingDistrictNameInput.value = selected.dataset.name || selected.textContent.trim();
                } else {
                    shippingDistrictIdInput.value = '';
                    shippingDistrictNameInput.value = '';
                }
                updateDestinationDisplay();
                resetShippingSelection();
                scheduleShippingFetch();
            });

            shippingRefreshButton?.addEventListener('click', fetchShippingRates);

            shippingOptionsSelect?.addEventListener('change', (event) => {
                selectShippingOption(event.target.selectedOptions?.[0]);
            });

            resetShippingSelection();
            updateDestinationDisplay();
            updateShippingActionState();
            loadProvinces();

            const updatePaymentInstruction = (value) => {
                const selectedCard = Array.from(paymentOptionCards).find((card) => card.dataset.paymentValue === value);
                if (!selectedCard) {
                    return;
                }
                paymentInstructionTitle.textContent = selectedCard.dataset.paymentTitle || 'Pilih metode pembayaran';
                paymentInstructionBody.textContent = selectedCard.dataset.paymentDetails || 'Ikuti instruksi di atas untuk menyelesaikan pembayaran.';
                paymentOptionCards.forEach((card) => {
                    card.classList.remove('border-rose-300', 'shadow-lg');
                    card.setAttribute('aria-selected', 'false');
                });
                selectedCard.classList.add('border-rose-300', 'shadow-lg');
                selectedCard.setAttribute('aria-selected', 'true');
            };

            paymentOptionCards.forEach((card) => {
                card.addEventListener('click', () => {
                    const input = card.querySelector('input[name="payment_method"]');
                    if (input) {
                        input.checked = true;
                    }
                    updatePaymentInstruction(card.dataset.paymentValue);
                });
            });

            const quickQuantityControl = document.querySelector('[data-quick-quantity]');
            if (quickQuantityControl) {
                const quickInput = quickQuantityControl.querySelector('[data-quantity-input]');
                const quickUrl = quickQuantityControl.dataset.updateUrl || '';
                const decrementButton = quickQuantityControl.querySelector('[data-action="decrement"]');
                const incrementButton = quickQuantityControl.querySelector('[data-action="increment"]');
                const maxStock = Math.max(Number(quickQuantityControl.dataset.maxStock) || 1, 1);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const clampQuantity = (value) => {
                    const parsed = Number(value);
                    if (Number.isNaN(parsed) || parsed < 1) {
                        return 1;
                    }
                    return Math.min(parsed, maxStock);
                };

                const persistQuantity = async (quantity) => {
                    if (!quickUrl) {
                        return;
                    }
                    const response = await fetch(quickUrl, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                        },
                        body: JSON.stringify({ quantity }),
                    });
                    if (!response.ok) {
                        const payload = await response.json().catch(() => null);
                        throw new Error(payload?.message || 'Gagal memperbarui kuantitas');
                    }
                    await response.json().catch(() => null);
                };

                const updateQuickQuantity = (value) => {
                    const nextQuantity = clampQuantity(value);
                    if (quickInput) {
                        quickInput.value = nextQuantity;
                    }
                    persistQuantity(nextQuantity)
                        .then(() => window.location.reload())
                        .catch((error) => {
                            console.error(error);
                            alert(error.message);
                        });
                };

                decrementButton?.addEventListener('click', () => {
                    const current = Number(quickInput?.value ?? 1);
                    updateQuickQuantity(current - 1);
                });
                incrementButton?.addEventListener('click', () => {
                    const current = Number(quickInput?.value ?? 1);
                    updateQuickQuantity(current + 1);
                });
                quickInput?.addEventListener('change', () => {
                    updateQuickQuantity(quickInput.value);
                });
            }

            const initialPayment = document.querySelector('[name="payment_method"]:checked');
            if (initialPayment) {
                updatePaymentInstruction(initialPayment.value);
            }
        });
    </script>


@endsection
