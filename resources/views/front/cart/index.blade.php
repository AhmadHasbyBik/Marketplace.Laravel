@extends('layouts.front')

@section('title', 'Keranjang - UMKM Dapoer Cupid')

@section('content')
    <div class="space-y-6">
        <h1 class="text-3xl font-semibold">Keranjang Belanja</h1>
        @if(empty($cart['items']))
            <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center text-slate-500">
                Keranjang kosong. <a href="{{ route('front.products.index') }}" class="text-rose-500 underline">Lanjut belanja</a>
            </div>
        @else
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl space-y-4">
                @foreach($cart['items'] as $item)
                    <div
                        class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4 last:border-b-0"
                        data-cart-item
                        data-product-id="{{ $item['product_id'] }}"
                        data-backorder="{{ $item['backorder'] ?? 0 }}"
                        data-production-ready="{{ ($item['production_ready'] ?? false) ? '1' : '0' }}"
                    >
                        <div class="flex flex-col gap-2">
                            <div class="font-semibold">{{ $item['name'] }}</div>
                            <div class="flex items-center gap-1 text-sm text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2m0 0h13.6l1.5 7H7.4"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 13h12l1 5H5l1-5z"/>
                                </svg>
                                Harga: Rp{{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="flex flex-col gap-1" data-quantity-controls>
                            <div class="flex items-center gap-2">
                                <button type="button" class="quantity-btn flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-600 focus:outline-none" data-action="decrement">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                    </svg>
                                </button>
                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    value="{{ $item['quantity'] }}"
                                    aria-label="Kuantitas {{ $item['name'] }}"
                                    data-quantity-input
                                    data-stock="{{ $item['stock'] ?? 0 }}"
                                    @if(($item['backorder'] ?? 0) === 0)
                                        max="{{ max($item['stock'] ?? 1, 1) }}"
                                    @endif
                                    class="w-16 rounded-2xl border border-slate-200 py-1 text-center text-sm font-semibold text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-200"
                                />
                                <button type="button" class="quantity-btn flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-600 focus:outline-none" data-action="increment">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-slate-500" data-stock-tip>Stok tersedia: {{ $item['stock'] ?? 0 }}</p>
                            <p class="text-xs text-rose-500" data-backorder-tip>
                                @if(($item['backorder'] ?? 0) > 0)
                                    @if($item['production_ready'] ?? false)
                                        Stok terbatas: sisanya akan diproduksi dari bahan baku.
                                    @else
                                        Bahan baku tidak cukup untuk kuantitas ini.
                                    @endif
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('front.cart.destroy', $item['product_id']) }}" class="self-end">
                            @csrf
                            @method('DELETE')
                            <button
                                class="inline-flex items-center gap-2 rounded-full border border-rose-100 bg-white px-4 py-1.5 text-xs font-semibold tracking-wide text-rose-600 shadow-[0_20px_40px_rgba(236,72,153,0.25)] transition hover:bg-rose-50 hover:shadow-[0_20px_50px_rgba(236,72,153,0.35)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Hapus barang
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl flex flex-col gap-4">
                <div class="flex items-center justify-between text-sm text-slate-500">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex items-center justify-between text-lg font-semibold text-rose-600">
                    <span>Total</span>
                    <span id="cart-total">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <form action="{{ route('front.cart.clear') }}" method="POST">
                        @csrf
                        <button
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-rose-600 px-5 py-2 text-sm font-semibold text-white shadow-[0_20px_30px_rgba(236,72,153,0.35)] transition hover:from-rose-600 hover:to-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 6v12M15 6v12M5 18h14a2 2 0 002-2V6H3v10a2 2 0 002 2z"/>
                            </svg>
                            Kosongkan keranjang
                        </button>
                    </form>
                    <a href="{{ route('front.checkout.index') }}" data-cart-checkout-link class="rounded-full bg-rose-500 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-rose-600">Checkout</a>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const subtotalEl = document.getElementById('cart-subtotal');
            const totalEl = document.getElementById('cart-total');
            const updateUrlTemplate = "{{ route('front.cart.update', '__ID__', false) }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const pendingCartUpdates = new Map();
            const checkoutLink = document.querySelector('[data-cart-checkout-link]');
            let isAwaitingCartSync = false;

            const updateCheckoutLinkState = (disable) => {
                if (!checkoutLink) {
                    return;
                }
                checkoutLink.classList.toggle('opacity-60', disable);
                checkoutLink.classList.toggle('pointer-events-none', disable);
                checkoutLink.setAttribute('aria-disabled', disable ? 'true' : 'false');
            };

            const waitForPendingCartUpdates = async () => {
                if (pendingCartUpdates.size === 0) {
                    return;
                }
                const results = await Promise.allSettled([...pendingCartUpdates.values()]);
                const hasFailure = results.some((result) => result.status === 'rejected');
                if (hasFailure) {
                    throw new Error('Beberapa pembaruan keranjang gagal. Periksa kembali sebelum checkout.');
                }
            };

            const handleCheckoutClick = async (event) => {
                if (!checkoutLink || pendingCartUpdates.size === 0 || isAwaitingCartSync) {
                    return;
                }
                event.preventDefault();
                isAwaitingCartSync = true;
                updateCheckoutLinkState(true);
                try {
                    await waitForPendingCartUpdates();
                    window.location.assign(checkoutLink.href);
                } catch (error) {
                    console.error(error);
                    alert(error.message);
                } finally {
                    isAwaitingCartSync = false;
                    updateCheckoutLinkState(false);
                }
            };

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value).replace('Rp', 'Rp');
            }

            async function updateCartQuantity(productId, quantity) {
                if (!csrfToken) {
                    throw new Error('CSRF token tidak ditemukan');
                }

                const url = updateUrlTemplate.replace('__ID__', productId);
                const updatePromise = (async () => {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ quantity }),
                    });

                    const payload = await response.json().catch(() => null);

                    if (!response.ok) {
                        const error = new Error(payload?.message || 'Gagal memperbarui keranjang');
                        if (payload?.available_stock !== undefined) {
                            error.availableStock = payload.available_stock;
                        }
                        if (payload?.quantity !== undefined) {
                            error.quantity = payload.quantity;
                        }
                        throw error;
                    }

                    return payload;
                })();

                pendingCartUpdates.set(productId, updatePromise);
                let payload;
                try {
                    payload = await updatePromise;
                } finally {
                    pendingCartUpdates.delete(productId);
                }

                const subtotal = Number(payload?.subtotal ?? 0);
                subtotalEl.textContent = formatCurrency(subtotal);
                totalEl.textContent = formatCurrency(subtotal);

                return payload;
            }

            function updateControlState(container, quantity, availableStock, payload = null) {
                const decrementButton = container.querySelector('[data-action="decrement"]');
                const incrementButton = container.querySelector('[data-action="increment"]');
                const quantityInput = container.querySelector('[data-quantity-input]');
                const stockTip = container.querySelector('[data-stock-tip]');
                const parsedStock = Number(availableStock ?? quantityInput?.dataset.stock ?? 0);
                const normalizedStock = Number.isFinite(parsedStock) ? Math.max(0, parsedStock) : 0;
                const allowBackorder = Boolean(payload?.production_ready) && Number(payload?.backorder ?? 0) > 0;
                const disableIncrement = normalizedStock > 0 ? quantity >= normalizedStock : !allowBackorder;
                const disableDecrement = quantity <= 1;

                const toggleButtonState = (button, disabled) => {
                    if (!button) {
                        return;
                    }
                    button.disabled = disabled;
                    button.classList.toggle('opacity-40', disabled);
                    button.classList.toggle('pointer-events-none', disabled);
                };

                toggleButtonState(incrementButton, disableIncrement);
                toggleButtonState(decrementButton, disableDecrement);

                if (quantityInput) {
                    quantityInput.dataset.stock = normalizedStock;
                    quantityInput.dataset.lastSynced = quantity;
                    if (normalizedStock > 0) {
                        quantityInput.setAttribute('max', Math.max(1, normalizedStock));
                    } else {
                        quantityInput.removeAttribute('max');
                    }
                }

                if (stockTip) {
                    stockTip.textContent = normalizedStock > 0 ? `Stok tersedia: ${normalizedStock}` : 'Stok tidak tersedia';
                }
            }

            function updateBackorderTip(container, payload) {
                const backorderTip = container.querySelector('[data-backorder-tip]');
                if (!backorderTip) {
                    return;
                }
                const backorderAmount = Number(payload?.backorder ?? 0);
                const ready = Boolean(payload?.production_ready);
                if (backorderAmount > 0) {
                    backorderTip.textContent = ready
                        ? 'Stok terbatas: sisanya akan diproduksi dari bahan baku.'
                        : 'Bahan baku tidak cukup untuk kuantitas ini.';
                } else {
                    backorderTip.textContent = '';
                }
            }

            function initializeQuantityControl(container) {
                const quantityInput = container.querySelector('[data-quantity-input]');
                const productId = container.dataset.productId;
                const initialPayload = {
                    backorder: Number(container.dataset.backorder ?? 0),
                    production_ready: container.dataset.productionReady === '1',
                };
                if (!quantityInput || !productId) {
                    return;
                }

                const handleChange = async (rawValue) => {
                    const parsed = Number(rawValue);
                    const sanitized = Number.isFinite(parsed) ? Math.max(1, Math.trunc(parsed)) : 1;
                    const currentValue = Number(quantityInput.value) || 1;
                    if (sanitized === currentValue) {
                        return;
                    }

                    quantityInput.value = sanitized;
                    quantityInput.setAttribute('aria-busy', 'true');

                    try {
                        const payload = await updateCartQuantity(productId, sanitized);
                        const appliedQuantity = Number(payload?.quantity ?? sanitized);
                        const stock = Number(payload?.available_stock ?? quantityInput.dataset.stock ?? 0);
                        quantityInput.value = appliedQuantity;
                        updateControlState(container, appliedQuantity, stock, payload);
                        updateBackorderTip(container, payload);
                        container.dataset.backorder = Number(payload?.backorder ?? 0);
                        container.dataset.productionReady = payload?.production_ready ? '1' : '0';
                    } catch (error) {
                        const fallbackQuantity = Number(error?.quantity ?? currentValue);
                        const fallbackStock = Number(error?.availableStock ?? quantityInput.dataset.stock ?? 0);
                        quantityInput.value = fallbackQuantity;
                        const fallbackPayload = {
                            backorder: Number(error?.backorder ?? 0),
                            production_ready: Boolean(error?.production_ready),
                        };
                        updateControlState(container, fallbackQuantity, fallbackStock, fallbackPayload);
                        updateBackorderTip(container, fallbackPayload);
                        container.dataset.backorder = Number(fallbackPayload.backorder ?? 0);
                        container.dataset.productionReady = fallbackPayload.production_ready ? '1' : '0';
                        console.error(error);
                        if (error instanceof Error && error.message) {
                            alert(error.message);
                        }
                    } finally {
                        quantityInput.removeAttribute('aria-busy');
                    }
                };

                container.querySelectorAll('[data-action]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const action = button.dataset.action;
                        const current = Number(quantityInput.value) || 1;
                        const next = action === 'increment' ? current + 1 : Math.max(1, current - 1);
                        void handleChange(next);
                    });
                });

                quantityInput.addEventListener('change', () => {
                    const value = Number(quantityInput.value);
                    if (!Number.isFinite(value)) {
                        quantityInput.value = 1;
                        return;
                    }
                    void handleChange(value);
                });

                updateControlState(container, Number(quantityInput.value) || 1, Number(quantityInput.dataset.stock ?? 0), initialPayload);
                updateBackorderTip(container, initialPayload);
            }

            document.querySelectorAll('[data-cart-item]').forEach(initializeQuantityControl);
            checkoutLink?.addEventListener('click', handleCheckoutClick);
        });
    </script>
@endsection
