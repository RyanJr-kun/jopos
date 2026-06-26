@extends('layouts/blankLayout')

@section('title', 'Point of Sales - JO Computer')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-sale.scss'])
@endsection

@section('content')

    @include('pos::penjualan.partials._navbar')

    {{-- ============================================================
     POS WRAPPER — Dua Panel Utama
     ============================================================ --}}
    <div class="pos-wrapper">

        {{-- ==========================================
         PANEL KIRI: Daftar & Filter Produk
         ========================================== --}}
        <div class="pos-products-panel" id="pos-products-panel">

            {{-- Header Panel: Greeting + Search --}}
            <div class="pos-products-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="pos-greeting mb-0">
                            Selamat datang, <span>{{ auth()->user()->username }}</span>
                        </p>
                        <p class="pos-date">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>

                    {{-- Search --}}
                    <div class="pos-search-wrap flex-grow-1 ms-3" style="max-width: 320px;">
                        <i class="pos-search-icon" aria-hidden="true"></i>
                        <input type="text" id="product-search" class="form-control form-control-sm"
                            placeholder="Cari produk, sku atau scan barcode…" aria-label="Cari atau scan produk"
                            autocomplete="off">
                    </div>
                </div>
            </div>

            {{-- Category Filter --}}
            <div class="pos-category-bar mt-2 d-flex align-items-center flex-wrap gap-1 py-2 bg-white border-top border-bottom mb-3"
                id="category-outer">

                {{-- Tombol Semua --}}
                <a href="{{ route('penjualan.create') }}"
                    class="btn btn-sm rounded-pill {{ !request()->filled('kategori') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="bx bx-category me-1"></i> Semua
                </a>

                <div class="vr text-light mx-1"></div>

                {{-- Loop kategori utama --}}
                @foreach ($kategoris as $kategori)
                    @php
                        $isActive =
                            request('kategori') == $kategori->id ||
                            $kategori->children->contains('id', request('kategori'));
                    @endphp

                    @if ($kategori->children->isNotEmpty())
                        {{-- Kategori dengan sub-kategori: pakai dropdown --}}
                        <div class="dropdown">
                            <button
                                class="btn btn-sm rounded-pill dropdown-toggle {{ $isActive ? 'btn-primary' : 'btn-outline-secondary' }}"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $kategori->name }}
                            </button>

                            <ul class="dropdown-menu shadow-sm border-0 mt-1"
                                style="min-width: 200px; border-radius: 8px; overflow: hidden;">
                                {{-- Pilih semua produk dalam kategori ini (termasuk sub) --}}
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2
                            {{ request('kategori') == $kategori->id ? 'active' : '' }}"
                                        href="{{ route('penjualan.create', ['kategori' => $kategori->id]) }}">
                                        <i class="bx bx-folder-open fs-6 text-primary"></i>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 13px;">Semua {{ $kategori->name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                {{ $kategori->children->count() }} sub-kategori
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                {{-- Sub-kategori --}}
                                @foreach ($kategori->children as $child)
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center py-2
                                {{ request('kategori') == $child->id ? 'active' : '' }}"
                                            href="{{ route('penjualan.create', ['kategori' => $child->id]) }}"
                                            style="font-size: 13px;">
                                            {{ $child->name }}
                                            <span class="badge bg-label-primary ms-2"
                                                style="font-size: 10px; font-weight: 400;">
                                                sub
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        {{-- Kategori tanpa sub-kategori: tombol biasa --}}
                        <a href="{{ route('penjualan.create', ['kategori' => $kategori->id]) }}"
                            class="btn btn-sm rounded-pill {{ $isActive ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $kategori->name }}
                        </a>
                    @endif
                @endforeach

            </div>
            @include('pos::penjualan.partials._list_produk')

        </div>{{-- /pos-products-panel --}}

        {{-- ==========================================
         PANEL KANAN: Keranjang & Transaksi
         ========================================== --}}
        <div class="pos-cart-panel" id="pos-cart-panel">
            <form action="{{ route('penjualan.store') }}" method="POST" id="penjualanForm"
                class="d-flex flex-column h-100 overflow-hidden" novalidate aria-label="Form Transaksi Penjualan">
                @csrf

                {{-- Cart Header --}}
                <div class="pos-cart-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size:.88rem;">Detail Pesanan</h6>
                            <p class="invoice-badge mb-0">
                                Invoice: <span>{{ $referensi }}</span>
                            </p>
                            <input type="hidden" name="referensi" value="{{ $referensi }}">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-md bg-label-success cart-count-badge" id="cart-item-count"
                                aria-live="polite" aria-atomic="true">
                                <i class="bx bx-shopping-bag me-1" aria-hidden="true"></i>
                                <span>0 Item</span>
                            </span>
                            <button type="button" class="btn btn-outline-danger btn-reset-cart mb-0" id="btn-reset-cart"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kosongkan Keranjang"
                                aria-label="Kosongkan Keranjang" disabled>
                                <i class="bx bx-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger text-white mx-3 mt-2 mb-0 py-2" role="alert">
                        <strong class="fw-bold d-block mb-1">Terjadi kesalahan:</strong>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Customer Select --}}
                <div class="pos-customer-section">
                    <label for="Customer">
                        Customer <span class="text-danger" aria-hidden="true">*</span>
                    </label>
                    <div class="customer-row">
                        <select class="form-select select2 @error('customer_id') is-invalid @enderror" name="customer_id"
                            id="Customer" required aria-required="true" data-placeholder="Pilih Customer">
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}" @selected(old('customer_id') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <button type="button" class="btn btn-outline-info px-2 mb-0" data-bs-toggle="modal"
                            data-bs-target="#createCustomerModal" aria-label="Tambah Pelanggan Baru">
                            <i class="bx bx-plus-circle" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                {{-- Cart Items — PerfectScrollbar vertical --}}
                <div class="pos-cart-scroll" id="pos-cart-scroll">
                    <table class="cart-table" aria-label="Keranjang Belanja">
                        <thead>
                            <tr>
                                <th width="38%">Produk</th>
                                <th class="text-center" width="22%">Qty</th>
                                <th width="15%">PPN</th>
                                <th width="20%">Subtotal</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items-container" aria-live="polite">
                            {{-- Dirender oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Rincian Biaya / Totals --}}
                <div class="pos-totals">
                    <div class="totals-row">
                        <span class="totals-label">Subtotal (DPP)</span>
                        <span class="totals-value" id="subtotal">Rp 0</span>
                    </div>
                    <div class="totals-row">
                        <span class="totals-label">PPN</span>
                        <span class="totals-value" id="pajak-total-display">Rp 0</span>
                    </div>
                    <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                        data-type="service" data-label="Service" role="button" tabindex="0"
                        aria-label="Edit biaya service">
                        <span class="totals-label">Service</span>
                        <span class="totals-value" id="service-display">Rp 0</span>
                        <input type="hidden" name="service" id="service-input" value="0">
                    </div>
                    <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                        data-type="ongkir" data-label="Ongkos Kirim" role="button" tabindex="0"
                        aria-label="Edit ongkos kirim">
                        <span class="totals-label">Ongkir</span>
                        <span class="totals-value" id="ongkir-display">Rp 0</span>
                        <input type="hidden" name="ongkir" id="ongkir-input" value="0">
                    </div>
                    <div class="totals-row clickable" data-bs-toggle="modal" data-bs-target="#editExtraCostModal"
                        data-type="diskon" data-label="Diskon" role="button" tabindex="0" aria-label="Edit diskon">
                        <span class="totals-label">Diskon (Rp)</span>
                        <span class="totals-value" id="diskon-display">Rp 0</span>
                        <input type="hidden" name="diskon" id="diskon-input" value="0">
                    </div>
                    <div class="totals-grand">
                        <span class="grand-label">Total</span>
                        <span class="grand-value" id="total-akhir" aria-live="polite">Rp 0</span>
                    </div>
                </div>

                {{-- Pembayaran (Di Panel Kanan Cart) --}}
                <div class="pos-payment p-3 border-top mt-auto">
                    {{-- Tombol Aksi Utama --}}
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary d-flex justify-content-between align-items-center"
                            id="btn-open-payment" data-bs-toggle="modal" data-bs-target="#paymentModal" disabled>
                            <span class="text-muted">Bayar Sekarang</span>
                            <span class="text-muted" id="cart-total-btn-display">Rp 0</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>{{-- /pos-cart-panel --}}

    </div>{{-- /pos-wrapper --}}

    {{-- ============================================================
     MOBILE: Cart FAB + Overlay
     ============================================================ --}}
    <button class="btn-cart-fab" id="btn-cart-fab" aria-label="Buka Keranjang">
        <i class="bx bx-cart-alt" aria-hidden="true"></i>
        <span class="fab-badge" id="fab-badge">0</span>
    </button>

    <div class="pos-cart-overlay" id="pos-cart-overlay" aria-hidden="true"></div>

    {{-- ============================================================
     MODALS
     ============================================================ --}}

    @include('pos::penjualan.partials._model')

@endsection

@section('page-script')
    <script type="module">
        const initSelect2 = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                const formatBankLogo = (state) => {
                    if (!state.id) {
                        return state.text;
                    }
                    const logoUrl = $(state.element).data('logo');
                    if (!logoUrl) {
                        return state.text;
                    }
                    const $state = $(
                        '<span class="d-flex align-items-center">' +
                        '<img src="' + logoUrl +
                        '" style="width: 24px; height: 24px; object-fit: contain; margin-right: 8px;" alt="logo" />' +
                        '<span>' + state.text + '</span>' +
                        '</span>'
                    );

                    return $state;
                };

                $('.select2').each(function() {
                    const $this = $(this);
                    const isBankSelect = $this.hasClass('select2-bank');
                    let select2Options = {
                        placeholder: $this.data('placeholder') || "Pilih...",
                        allowClear: $this.find('option[value=""]').length > 0,
                        width: '100%',
                        minimumResultsForSearch: 10
                    };

                    if (isBankSelect) {
                        select2Options.templateResult = formatBankLogo;
                        select2Options.templateSelection = formatBankLogo;
                    }

                    $this.select2(select2Options);
                });
            } else {
                setTimeout(initSelect2, 100);
            }
        };

        initSelect2();
    </script>
    <script>
        function toggleFullScreen(event) {
            if (event) event.preventDefault();
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(console.error);
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }

        // ============================================================
        //  MOBILE CART TOGGLE (Bottom Sheet)
        // ============================================================
        (function() {
            const fab = document.getElementById('btn-cart-fab');
            const overlay = document.getElementById('pos-cart-overlay');
            const cartPanel = document.getElementById('pos-cart-panel');

            function openCart() {
                cartPanel.classList.add('pos-cart-open');
                overlay.classList.add('visible');
                overlay.setAttribute('aria-hidden', 'false');
            }

            function closeCart() {
                cartPanel.classList.remove('pos-cart-open');
                overlay.classList.remove('visible');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            if (fab) fab.addEventListener('click', openCart);
            if (overlay) overlay.addEventListener('click', closeCart);

            // Tutup cart saat modal terbuka (UX mobile)
            document.addEventListener('show.bs.modal', closeCart);

            // Expose untuk update badge dari JS lain
            window.updateFabBadge = function(count) {
                const badge = document.getElementById('fab-badge');
                if (badge) badge.textContent = count;
            };
        })();

        // ============================================================
        //  PERFECT SCROLLBAR — inisialisasi setelah DOM ready
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {

            // ── Produk (vertical) ───────────────────────────────────
            const productScrollEl = document.getElementById('pos-product-scroll');
            if (productScrollEl && typeof PerfectScrollbar !== 'undefined') {
                window._psProduct = new PerfectScrollbar(productScrollEl, {
                    wheelSpeed: 1.2,
                    wheelPropagation: false,
                    minScrollbarLength: 30,
                });
            }

            // ── Kategori (horizontal) ───────────────────────────────
            const categoryEl = document.getElementById('category-container');
            if (categoryEl && typeof PerfectScrollbar !== 'undefined') {
                window._psCategory = new PerfectScrollbar(categoryEl, {
                    suppressScrollY: true,
                    wheelSpeed: 0.8,
                    wheelPropagation: false,
                });
            }

            // ── Cart (vertical) ─────────────────────────────────────
            const cartScrollEl = document.getElementById('pos-cart-scroll');
            if (cartScrollEl && typeof PerfectScrollbar !== 'undefined') {
                window._psCart = new PerfectScrollbar(cartScrollEl, {
                    wheelSpeed: 1,
                    wheelPropagation: false,
                    minScrollbarLength: 30,
                });
            }

            // Update PS setiap cart berubah (dipanggil dari renderCart)
            window.refreshCartPS = function() {
                if (window._psCart) window._psCart.update();
            };
        });

        // ============================================================
        //  REALTIME CLOCK
        // ============================================================
        (function() {
            const clockElement = document.getElementById('realtime-clock');

            function updateClock() {
                if (!clockElement) return;
                clockElement.textContent = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).format(new Date()).replace(/\./g, ':');
            }
            setInterval(updateClock, 1000);
            updateClock();
        })();

        // ============================================================
        //  MAIN POS LOGIC
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {

            // ── Utilities ────────────────────────────────────────────
            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);

            const formatNumberInput = (e) => {
                const raw = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            };

            const calcDppAndTax = (hargaJualTotal, pajakRate) => {
                const dpp = hargaJualTotal / (1 + (pajakRate / 100));
                const pajak = hargaJualTotal - dpp;
                return {
                    dpp,
                    pajak
                };
            };

            const debounce = (fn, delay = 250) => {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn(...args), delay);
                };
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const defaultHeaders = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            };

            // ── State ─────────────────────────────────────────────────
            const cart = new Map();
            let selectedCategoryId = 'all';
            let tempProductDataForSN = {};

            const pajakRates = new Map();
            @foreach ($taxes as $pajak)
                pajakRates.set({{ $pajak->id }}, {{ $pajak->rate }});
            @endforeach

            // ── DOM Elements ─────────────────────────────────────────
            const productList = document.getElementById('product-list');
            const allProductCards = document.querySelectorAll('.product-card-pos');
            const cartContainer = document.getElementById('cart-items-container');
            const cartItemCount = document.getElementById('cart-item-count');
            const subtotalEl = document.getElementById('subtotal');
            const serviceInput = document.getElementById('service-input');
            const ongkirInput = document.getElementById('ongkir-input');
            const diskonInput = document.getElementById('diskon-input');
            const changeDisplay = document.getElementById('change-display');
            const paymentInputEl = document.getElementById('jumlah-dibayar-input');
            const payExactButton = document.getElementById('btn-pay-exact');
            const totalAkhirEl = document.getElementById('total-akhir');
            const productSearchInput = document.getElementById('product-search');
            const mainForm = document.getElementById('penjualanForm');
            const saveButton = document.getElementById('btn-save-transaction');
            const resetButton = document.getElementById('btn-reset-cart');
            const editItemModal = new bootstrap.Modal(document.getElementById('editCartItemModal'));
            const editItemHargaInput = document.getElementById('edit-item-harga');
            const editItemDiskonInput = document.getElementById('edit-item-diskon');
            const serialNumberModalEl = document.getElementById('serialNumberModal');
            const serialNumberModal = new bootstrap.Modal(serialNumberModalEl);
            const variantModalEl = document.getElementById('variantModal');
            const variantModal = variantModalEl ? new bootstrap.Modal(variantModalEl) : null;
            const snListContainer = document.getElementById('sn-list-container');
            const snNamaProduct = document.getElementById('sn-name-produk');
            const snRequiredCount = document.getElementById('sn-required-count');
            const snConfirmBtn = document.getElementById('btn-confirm-sn');
            const snErrorMessage = document.getElementById('sn-error-message');
            const btnOpenPayment = document.getElementById('btn-open-payment');
            const cartTotalBtnDisplay = document.getElementById('cart-total-btn-display');

            // ── CART: Fungsi Inti ─────────────────────────────────────
            const updateCartAndTotals = () => {
                renderCart();
                calculateTotals();
                toggleSaveButton();
                // Update PerfectScrollbar cart setelah render
                if (window.refreshCartPS) window.refreshCartPS();
            };

            const addProductToCart = (productData, serialNumbers = []) => {
                // Gunakan kombinasi product_id dan variant_id sebagai UNIQUE KEY di keranjang
                const cartKey = productData.variant_id ? `${productData.id}-${productData.variant_id}` :
                    productData.id.toString();

                if (cart.has(cartKey)) {
                    const item = cart.get(cartKey);
                    if (item.wajib_seri && serialNumbers.length > 0) {
                        item.jumlah = serialNumbers.length;
                        item.serial_numbers = serialNumbers;
                    } else {
                        // Logika produk reguler
                        if (item.jumlah < item.stok) {
                            item.jumlah++;
                        } else {
                            showToast(`Stok untuk "${productData.name}" tidak mencukupi.`, 'warning');
                        }
                    }
                } else {
                    if (parseInt(productData.stok) > 0) {
                        const initialQuantity = serialNumbers.length > 0 ? serialNumbers.length : 1;
                        const displayName = productData.variant_name ?
                            `${productData.name} - ${productData.variant_name}` : productData.name;

                        cart.set(cartKey, {
                            cart_key: cartKey,
                            id: parseInt(productData.id),
                            variant_id: productData.variant_id || null, // Tangkap ID Varian
                            variant_name: productData.variant_name || null,
                            name: displayName, // Nama yang ditampilkan di keranjang
                            harga: parseFloat(productData.hargaAsli || productData.harga),
                            stok: parseInt(productData.stok),
                            img: productData.image_url || productData.img, // Dukung image_url
                            jumlah: initialQuantity,
                            harga_jual: parseFloat(productData.harga),
                            diskon: 0,
                            taxe_id: productData.pajakId ? parseInt(productData.pajakId) : null,
                            pajak_rate: productData.pajakRate ? parseFloat(productData.pajakRate) : 0,
                            serial_numbers: serialNumbers,
                            wajib_seri: productData.wajibSeri === 'true' || productData.wajibSeri ===
                                true
                        });
                    } else {
                        showToast(`"${productData.name}" kehabisan stok.`, 'warning');
                    }
                }
                updateCartAndTotals();
            };

            const updateQuantity = (id, newQuantity) => {
                if (!cart.has(id)) return;
                const item = cart.get(id);
                newQuantity = parseInt(newQuantity);

                if (item.serial_numbers && item.serial_numbers.length > 0) {
                    if (newQuantity > 0 && newQuantity < item.jumlah) {
                        item.serial_numbers.pop();
                        item.jumlah = newQuantity;
                    } else if (newQuantity <= 0) {
                        cart.delete(id);
                    }
                } else {
                    if (newQuantity > 0 && newQuantity <= item.stok) {
                        item.jumlah = newQuantity;
                    } else if (newQuantity > item.stok) {
                        item.jumlah = item.stok;
                        showToast(`Stok maksimum untuk "${item.name}" adalah ${item.stok}.`, 'warning');
                    } else {
                        cart.delete(id);
                    }
                }
                updateCartAndTotals();
            };

            const removeFromCart = (id) => {
                cart.delete(id);
                updateCartAndTotals();
            };

            // ── CART: Render ─────────────────────────────────────────
            const renderCart = () => {
                cartContainer.innerHTML = '';

                if (cart.size === 0) {
                    cartContainer.innerHTML = `
                <tr id="cart-empty-message">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <div class="d-flex flex-column align-items-center justify-content-center py-5">    
                            <i class="bx bx-shop fs-1 text-muted d-block mb-2" aria-hidden="true"></i>
                            <p class="mb-0">Keranjang masih kosong</p>
                        </div>
                    </td>
                </tr>`;
                } else {
                    let formIndex = 0;
                    cart.forEach(item => {
                        const hargaJualTotal = (item.harga_jual * item.jumlah) - item.diskon;
                        const {
                            dpp: dppItem,
                            pajak: pajakAmountItem
                        } = calcDppAndTax(hargaJualTotal, item.pajak_rate);

                        let serialNumberInputs = '';
                        (item.serial_numbers || []).forEach(sn => {
                            serialNumberInputs +=
                                `<input type="hidden" name="items[${formIndex}][serial_numbers][]" value="${sn}">`;
                        });

                        const serialNumberDisplay = (item.serial_numbers?.length > 0) ?
                            `<small class="text-xs text-muted d-flex">SN: ${item.serial_numbers.join(', ')}</small>` :
                            '';

                        let hargaDisplay =
                            `<span class="text-xs">${formatCurrency(item.harga_jual)}</span>`;
                        if (item.harga_jual < item.harga) {
                            hargaDisplay = `
                        <span class="text-xs text-danger">${formatCurrency(item.harga_jual)}</span><br>
                        <small class="text-muted text-decoration-line-through">${formatCurrency(item.harga)}</small>`;
                        }

                        const editButtonHtml = item.wajib_seri ? '' : `
                    <button class="btn btn-link text-dark p-0 edit-item" data-id="${item.id}"
                            title="Edit Item" type="button" aria-label="Edit item ${item.name}">
                        <i class="bx bx-edit" aria-hidden="true"></i>
                    </button>`;

                        let namaTampilan = item.variant_name ? `${item.name}` : item.name;

                        cartContainer.insertAdjacentHTML('beforeend', `
                            <tr class="cart-item-row">
                                <input type="hidden" name="items[${formIndex}][product_id]" value="${item.id}">
                                <input type="hidden" name="items[${formIndex}][product_variant_id]" value="${item.variant_id || ''}"> 
                                <input type="hidden" name="items[${formIndex}][jumlah]" value="${item.jumlah}">
                                <input type="hidden" name="items[${formIndex}][harga_jual]" value="${item.harga_jual}">
                                <input type="hidden" name="items[${formIndex}][diskon]" value="${item.diskon}">
                                <input type="hidden" name="items[${formIndex}][taxe_id]" value="${item.taxe_id || ''}">
                                ${serialNumberInputs}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="${item.img}" class="avatar avatar-md rounded me-2" alt="${item.name}">
                                        <div class="d-flex flex-column" style="min-width:0;">
                                            <p class="mb-0 fw-bold text-xs text-wrap" title="${item.name}">${namaTampilan}</p>
                                            <small class="d-flex">${serialNumberDisplay}</small>
                                            <small class="text-muted">${hargaDisplay}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-primary btn-sm rounded-circle p-0 qty-decrease" data-cart-key="${item.cart_key}" type="button" style="width:22px;height:22px;line-height:1;">−</button>
                                    <span class="fw-bold px-1 text-sm">${item.jumlah}</span>
                                    <button class="btn btn-outline-primary btn-sm rounded-circle p-0 qty-increase" data-cart-key="${item.cart_key}" type="button" style="width:22px;height:22px;line-height:1;">+</button>
                                </td>
                                <td><span class="text-xs fw-bold">${formatCurrency(pajakAmountItem)}</span></td>
                                <td><span class="text-xs fw-bold">${formatCurrency(dppItem)}</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center">
                                        ${editButtonHtml} <button class="btn btn-link text-danger p-0 ms-1 remove-item" data-cart-key="${item.cart_key}" type="button">
                                            <i class="bx bx-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`);
                        formIndex++;
                    });
                }

                // Update cart count badge + FAB badge
                const totalItems = Array.from(cart.values()).reduce((sum, item) => sum + item.jumlah, 0);
                cartItemCount.innerHTML =
                    `<i class="bx bx-shopping-bag me-1" aria-hidden="true"></i> ${totalItems} Item`;
                if (window.updateFabBadge) window.updateFabBadge(totalItems);

                // Tandai kartu produk aktif
                allProductCards.forEach(card => {
                    const cardId = parseInt(card.dataset.id);
                    card.classList.toggle('active', cart.has(cardId));
                });
            };

            // ── CART: Kalkulasi ────────────────────────────────────────
            const calculateTotals = () => {
                let subtotal = 0,
                    totalTaxe = 0;
                cart.forEach(item => {
                    const hargaJualTotal = (item.harga_jual * item.jumlah) - item.diskon;
                    const {
                        dpp,
                        pajak
                    } = calcDppAndTax(hargaJualTotal, item.pajak_rate);
                    subtotal += dpp;
                    totalTaxe += pajak;
                });
                const service = parseFloat(serviceInput.value) || 0;
                const ongkir = parseFloat(ongkirInput.value) || 0;
                const diskon = parseFloat(diskonInput.value) || 0;
                const total = (subtotal + totalTaxe + service + ongkir) - diskon;

                subtotalEl.textContent = formatCurrency(subtotal);
                document.getElementById('pajak-total-display').textContent = formatCurrency(totalTaxe);
                totalAkhirEl.textContent = formatCurrency(Math.max(0, total));

                // TAMBAHKAN BARIS INI:
                if (cartTotalBtnDisplay) cartTotalBtnDisplay.textContent = formatCurrency(Math.max(0, total));

                calculateChange();
                return total;
            };

            const calculateChange = () => {
                const rawText = totalAkhirEl.textContent.replace(/[^\d,]/g, '').replace(',', '.');
                const total = parseFloat(rawText) || 0;
                const paymentAmount = parseFloat(paymentInputEl.value.replace(/[^0-9]/g, '')) || 0;
                const change = paymentAmount - total;

                changeDisplay.textContent = formatCurrency(change);
                changeDisplay.classList.toggle('negative', change < 0);
                changeDisplay.classList.toggle('text-danger', change < 0);
                changeDisplay.classList.toggle('text-success', change >= 0);

                // ── LOGIKA TAMBAHAN UNTUK TANGGAL JATUH TEMPO ────────────────
                const jatuhTempoWrapper = document.getElementById('jatuh-tempo-wrapper');
                const jatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');

                if (jatuhTempoWrapper && jatuhTempoInput) {
                    if (paymentAmount >= total && total > 0) {
                        // JIKA LUNAS (Uang Pas atau Lebih): Sembunyikan & Disable input, lalu kosongkan nilainya
                        jatuhTempoWrapper.classList.add('d-none');
                        jatuhTempoInput.disabled = true;
                        jatuhTempoInput.value = '';
                    } else {
                        // JIKA KURANG (Piutang): Munculkan & Aktifkan kembali input
                        jatuhTempoWrapper.classList.remove('d-none');
                        jatuhTempoInput.disabled = false;
                    }
                }
            };

            const toggleSaveButton = () => {
                const isCartEmpty = cart.size === 0;
                saveButton.disabled = isCartEmpty;
                resetButton.disabled = isCartEmpty;

                // TAMBAHKAN BARIS INI:
                if (btnOpenPayment) btnOpenPayment.disabled = isCartEmpty;
            };

            // ── Filter Produk ─────────────────────────────────────────
            const filterProducts = () => {
                const searchTerm = productSearchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                allProductCards.forEach(card => {
                    const wrapper = card.parentElement; // .product-card-wrap
                    const productName = card.dataset.name.toLowerCase();
                    const categoryId = wrapper.dataset.productCategoryId;

                    const categoryMatch = selectedCategoryId === 'all' || selectedCategoryId ==
                        categoryId;
                    const searchMatch = productName.includes(searchTerm);

                    wrapper.style.display = (categoryMatch && searchMatch) ? '' : 'none';
                    if (categoryMatch && searchMatch) visibleCount++;
                });

                let emptyMsg = document.getElementById('filter-empty-message');
                if (visibleCount === 0) {
                    if (!emptyMsg) {
                        emptyMsg = document.createElement('div');
                        emptyMsg.id = 'filter-empty-message';
                        emptyMsg.style.cssText =
                            'grid-column:1/-1;text-align:center;padding:40px 0;color:#a0aab4;';
                        emptyMsg.innerHTML = '<p>Tidak ada produk yang cocok.</p>';
                        productList.appendChild(emptyMsg);
                    }
                } else if (emptyMsg) {
                    emptyMsg.remove();
                }
            };

            // ── Barcode Scanner ───────────────────────────────────────
            const handleBarcodeScan = async (barcode) => {
                if (!barcode) return;
                try {
                    const url =
                        "{{ route('get-data.produk.by-barcode', ['barcode' => 'BARCODE_PLACEHOLDER']) }}"
                        .replace('BARCODE_PLACEHOLDER', encodeURIComponent(barcode));
                    const response = await fetch(url, {
                        headers: defaultHeaders
                    });
                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Produk tidak ditemukan.');

                    const productData = {
                        id: data.id,
                        name: data.name_product,
                        harga: data.harga_diskon ?? data.harga_jual,
                        hargaAsli: data.harga_jual,
                        stok: data.qty,
                        img: data.image_url,
                        wajibSeri: data.wajib_seri ? 'true' : 'false',
                        pajakId: data.taxe_id,
                        pajakRate: data.pajak ? data.pajak.rate : 0
                    };

                    const itemInCart = cart.get(productData.id);
                    if (productData.wajibSeri === 'true') {
                        const requiredQty = itemInCart ? itemInCart.jumlah + 1 : 1;
                        const existingSerials = itemInCart ? itemInCart.serial_numbers : [];
                        tempProductDataForSN = productData;
                        openSerialNumberModal(productData.id, null, productData.name, requiredQty,
                            existingSerials);
                    } else {
                        if (itemInCart) {
                            updateQuantity(productData.id, itemInCart.jumlah + 1);
                        } else {
                            addProductToCart(productData);
                        }
                    }
                    productSearchInput.value = '';
                } catch (error) {
                    showToast(error.message, 'danger');
                }
            };

            // ── Toast ─────────────────────────────────────────────────
            const showToast = (message, type = 'info') => {
                let container = document.querySelector('.toast-container-pos');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-container-pos';
                    document.body.appendChild(container);
                }
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-white bg-${type} border-0 show`;
                toastEl.setAttribute('role', 'alert');
                toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>`;
                container.appendChild(toastEl);
                const bsToast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                bsToast.show();
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            };

            // ── Serial Number Modal ───────────────────────────────────
            // Tambah parameter variantId
            const openSerialNumberModal = (productId, variantId, productName, requiredQty,
                existingSerials = []) => {

                document.getElementById('sn-produk-id').value = productId;
                document.getElementById('sn-variant-id').value = variantId || ''; // ← hidden input baru

                document.getElementById('sn-name-produk').textContent = productName;
                document.getElementById('sn-required-count').textContent = requiredQty;
                document.getElementById('sn-image-produk').src = tempProductDataForSN.img ||
                    '/assets/img/produk.png';
                document.getElementById('sn-stok-produk').textContent = tempProductDataForSN.stok;

                snErrorMessage.textContent = '';
                snListContainer.innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                    </div>`;

                serialNumberModal.show();

                // Bangun URL dengan variant_id jika ada
                const url = new URL(`{{ route('serialNumber.getProduct') }}`);
                url.searchParams.set('product_id', productId);
                if (variantId) url.searchParams.set('variant_id', variantId); // ← kirim ke controller

                fetch(url.toString(), {
                        headers: defaultHeaders
                    })
                    .then(r => r.json())
                    .then(data => {
                        snListContainer.innerHTML = '';
                        if (!data.serial_numbers || data.serial_numbers.length === 0) {
                            snListContainer.innerHTML =
                                '<p class="text-center text-muted py-3">Tidak ada nomor seri tersedia.</p>';
                            return;
                        }
                        data.serial_numbers.forEach(sn => {
                            const isChecked = existingSerials.includes(sn.serial_number);
                            const isDisabled = sn.status !== 'Tersedia' && !isChecked;
                            snListContainer.insertAdjacentHTML('beforeend', `
                                <label class="list-group-item list-group-item-action d-flex gap-2 align-items-center
                                            ${isDisabled ? 'text-muted disabled' : ''}">
                                    <input class="form-check-input flex-shrink-0 sn-checkbox" type="checkbox"
                                        value="${sn.serial_number}"
                                        ${isChecked  ? 'checked'  : ''}
                                        ${isDisabled ? 'disabled' : ''}>
                                    <span class="d-flex justify-content-between align-items-center w-100">
                                        <span>${sn.serial_number}</span>
                                        <small class="badge bg-label-${sn.status === 'Tersedia' ? 'success' : 'secondary'}">
                                            ${sn.status}
                                        </small>
                                    </span>
                                </label>`);
                        });
                    })
                    .catch(() => {
                        snListContainer.innerHTML =
                            '<p class="text-center text-danger py-3">Gagal memuat nomor seri.</p>';
                    });
            };

            // ── Confirm Serial Number ──────────────────────────────────
            if (snConfirmBtn) {
                snConfirmBtn.addEventListener('click', () => {
                    const productId = parseInt(document.getElementById('sn-produk-id').value);
                    const requiredQty = parseInt(snRequiredCount.textContent);
                    const selected = [...document.querySelectorAll('.sn-checkbox:checked')].map(cb => cb
                        .value);

                    if (selected.length !== requiredQty) {
                        snErrorMessage.textContent =
                            `Pilih tepat ${requiredQty} nomor seri (dipilih: ${selected.length}).`;
                        return;
                    }
                    if (tempProductDataForSN && parseInt(tempProductDataForSN.id) !== productId) {
                        snErrorMessage.textContent =
                            'Terjadi kesalahan konteks produk. Tutup modal dan coba lagi.';
                        return;
                    }
                    snErrorMessage.textContent = '';
                    addProductToCart(tempProductDataForSN, selected);
                    serialNumberModal.hide();

                });
            }

            // ── Edit Modal ─────────────────────────────────────────────
            const openEditModal = (id) => {
                const item = cart.get(id);
                if (!item) return;
                document.getElementById('edit-item-id').value = id;
                document.getElementById('edit-item-name').value = item.name;
                editItemHargaInput.value = new Intl.NumberFormat('id-ID').format(item.harga_jual);
                editItemDiskonInput.value = new Intl.NumberFormat('id-ID').format(item.diskon);

                const pajakSelect = document.getElementById('edit-item-pajak-id');
                pajakSelect.value = item.taxe_id || '';
                editItemModal.show();
            };

            document.getElementById('saveItemChangesBtn')?.addEventListener('click', () => {
                const id = parseInt(document.getElementById('edit-item-id').value);
                const item = cart.get(id);
                if (!item) return;

                const newHarga = parseFloat(editItemHargaInput.value.replace(/[^0-9]/g, '')) || item
                    .harga_jual;
                const newDiskon = parseFloat(editItemDiskonInput.value.replace(/[^0-9]/g, '')) || 0;
                const pajakSel = document.getElementById('edit-item-pajak-id');
                const newPajakId = pajakSel.value ? parseInt(pajakSel.value) : null;
                const newPajakRate = newPajakId ? (parseFloat(pajakSel.selectedOptions[0].dataset.rate) ||
                    0) : 0;

                item.harga_jual = newHarga;
                item.diskon = newDiskon;
                item.taxe_id = newPajakId;
                item.pajak_rate = newPajakRate;

                updateCartAndTotals();
                editItemModal.hide();
            });

            // ── Extra Cost (Service/Ongkir/Diskon) ────────────────────
            const editExtraCostModal = new bootstrap.Modal(document.getElementById('editExtraCostModal'));
            let currentExtraCostType = null;

            document.querySelectorAll('[data-bs-target="#editExtraCostModal"]').forEach(el => {
                el.addEventListener('click', () => {
                    const type = el.dataset.type;
                    const label = el.dataset.label;
                    currentExtraCostType = type;
                    document.getElementById('extra-cost-label').textContent = label;
                    document.getElementById('extra-cost-type').value = type;

                    const promoSection = document.getElementById('promo-code-section');
                    promoSection.style.display = type === 'diskon' ? '' : 'none';
                    promoSection.setAttribute('aria-hidden', type === 'diskon' ? 'false' : 'true');

                    const inputEl = document.getElementById('extra-cost-value');
                    const maps = {
                        service: serviceInput,
                        ongkir: ongkirInput,
                        diskon: diskonInput
                    };
                    inputEl.value = new Intl.NumberFormat('id-ID').format(maps[type]?.value || 0);
                });
            });

            document.getElementById('saveExtraCostBtn')?.addEventListener('click', () => {
                const val = parseFloat(document.getElementById('extra-cost-value').value.replace(/[^0-9]/g,
                    '')) || 0;
                const maps = {
                    service: {
                        input: serviceInput,
                        display: document.getElementById('service-display')
                    },
                    ongkir: {
                        input: ongkirInput,
                        display: document.getElementById('ongkir-display')
                    },
                    diskon: {
                        input: diskonInput,
                        display: document.getElementById('diskon-display')
                    },
                };
                if (currentExtraCostType && maps[currentExtraCostType]) {
                    maps[currentExtraCostType].input.value = val;
                    maps[currentExtraCostType].display.textContent = formatCurrency(val);
                }
                editExtraCostModal.hide();
                calculateTotals();
            });

            // ── Promo Code ─────────────────────────────────────────────
            document.getElementById('apply-promo-btn')?.addEventListener('click', async () => {
                const code = document.getElementById('promo-code-input').value.trim();
                const feedback = document.getElementById('promo-feedback');
                if (!code) {
                    feedback.textContent = 'Masukkan kode promo.';
                    return;
                }
                try {
                    const r = await fetch(`{{ url('get-data/promo') }}/${encodeURIComponent(code)}`, {
                        headers: defaultHeaders
                    });
                    const data = await r.json();
                    if (!r.ok) throw new Error(data.message || 'Kode tidak valid.');

                    const diskonVal = data.type === 'percentage' ?
                        Math.round((parseFloat(totalAkhirEl.textContent.replace(/[^0-9]/g, '')) * data
                            .nilai_diskon) / 100) :
                        data.nilai_diskon;

                    document.getElementById('extra-cost-value').value = new Intl.NumberFormat('id-ID')
                        .format(diskonVal);
                    feedback.innerHTML =
                        `<span class="text-success">Promo diterapkan: ${formatCurrency(diskonVal)}</span>`;
                } catch (err) {
                    feedback.innerHTML = `<span class="text-danger">${err.message}</span>`;
                }
            });

            // ── Sales History ─────────────────────────────────────────
            document.getElementById('salesHistoryModal')?.addEventListener('show.bs.modal', async () => {
                const body = document.getElementById('salesHistoryBody');
                body.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>`;
                try {
                    const r = await fetch('{{ route('penjualan.history.today') }}', {
                        headers: defaultHeaders
                    });

                    if (!r.ok) throw new Error('Server error: ' + r.status);

                    const sales = await r.json();

                    if (sales.length === 0) {
                        body.innerHTML =
                            '<p class="text-center text-muted py-4">Belum ada transaksi hari ini.</p>';
                        return;
                    }

                    // Base URL untuk detail — referensi di-append di tiap baris
                    const showBaseUrl = '{{ url('penjualan') }}';

                    const rows = sales.map(s => `
                    <tr class="align-middle" style="cursor:pointer;"
                        onclick="window.open('${showBaseUrl}/${encodeURIComponent(s.referensi)}', '_blank')">
                        <td><span class="badge bg-label-primary">${s.referensi}</span></td>
                        <td>${s.name}</td>
                        <td class="text-end fw-semibold">Rp ${Number(s.total_akhir).toLocaleString('id-ID')}</td>
                        <td><span class="badge bg-label-success">${s.status}</span></td>
                        <td class="text-muted">${s.waktu}</td>
                        <td>
                            <a href="${showBaseUrl}/${encodeURIComponent(s.referensi)}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary px-2"
                            onclick="event.stopPropagation()">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>`).join('');

                    body.innerHTML = `
                    <div class="table-responsive text-nowrap border rounded-3">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-label-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>`;
                } catch (err) {
                    body.innerHTML =
                        `<p class="text-center text-danger py-4">Gagal memuat riwayat: ${err.message}</p>`;
                }
            });

            // ── Event Listeners ───────────────────────────────────────

            // Klik produk
            productList.addEventListener('click', (e) => {
                const card = e.target.closest('.product-card-pos');
                if (!card) return;

                // Ambil data dari atribut HTML
                const {
                    id,
                    name,
                    harga,
                    hargaAsli,
                    stok,
                    disabled,
                    img,
                    wajibSeri,
                    pajakId,
                    pajakRate,
                    hasVariant,
                    variants
                } = card.dataset;

                // Jangan izinkan klik jika stok habis (dan tidak punya varian)
                if (disabled === 'true' && hasVariant === 'false') return;

                // Kumpulkan data dasar produk
                const baseProductData = {
                    id,
                    name,
                    harga,
                    hargaAsli,
                    stok,
                    img,
                    image_url: img,
                    wajibSeri: wajibSeri,
                    pajakId,
                    pajakRate,
                    variant_id: null,
                    variant_name: null
                };

                if (hasVariant === 'true') {
                    // Jika punya varian, parsing JSON variannya dan buka modal
                    const parsedVariants = JSON.parse(variants || '[]');
                    openVariantModal(baseProductData, parsedVariants);
                } else {
                    // Jika produk tunggal biasa, langsung teruskan
                    handleProductSelection(baseProductData);
                }
            });

            // modal varian
            const openVariantModal = (baseProductData, variants) => {
                document.getElementById('variant-product-name').textContent = baseProductData.name;
                document.getElementById('variant-product-img').src = baseProductData.image_url ||
                    baseProductData.img;
                document.getElementById('variant-product-price').textContent = formatCurrency(baseProductData
                    .harga);

                const container = document.getElementById('variant-list-container');
                container.innerHTML = '';

                variants.forEach(v => {
                    const isOutOfStock = parseInt(v.stok) <= 0;
                    const btn = document.createElement('button');

                    // Desain tombol varian
                    btn.className =
                        `btn d-flex justify-content-between align-items-center mb-2 ${isOutOfStock ? 'btn-outline-secondary disabled' : 'btn-outline-primary'}`;
                    btn.innerHTML =
                        `<span>${v.name}</span> <span class="badge bg-label-${isOutOfStock ? 'secondary' : 'primary'}">Stok: ${v.stok}</span>`;

                    if (!isOutOfStock) {
                        btn.addEventListener('click', () => {
                            variantModal.hide(); // Tutup modal varian

                            // Copy data produk utama, lalu timpa dengan data variannya
                            const selectedProductData = {
                                ...baseProductData
                            };
                            selectedProductData.stok = v.stok;
                            selectedProductData.variant_id = v.id;
                            selectedProductData.variant_name = v.name;
                            // Jika varian punya harga/gambar beda, bisa ditambahkan di sini: selectedProductData.harga = v.harga;

                            handleProductSelection(selectedProductData);
                        });
                    }
                    container.appendChild(btn);
                });

                variantModal.show();
            };

            // Fungsi Universal untuk mengecek Wajib SN sebelum masuk keranjang
            const handleProductSelection = (productData) => {
                tempProductDataForSN = productData; // Simpan untuk dipakai Modal SN

                if (productData.wajibSeri === 'true' || productData.wajibSeri === true) {
                    const cartKey = productData.variant_id ? `${productData.id}-${productData.variant_id}` :
                        productData.id.toString();
                    const itemInCart = cart.get(cartKey);

                    const existingSerials = itemInCart ? itemInCart.serial_numbers : [];
                    const requiredQty = itemInCart ? itemInCart.jumlah + 1 : 1;

                    const displayName = productData.variant_name ?
                        `${productData.name} - ${productData.variant_name}` : productData.name;

                    // Buka modal SN
                    openSerialNumberModal(productData.id, productData.variant_id || null, displayName,
                        requiredQty, existingSerials);
                } else {
                    // Jika tidak wajib SN, langsung tembak ke keranjang
                    addProductToCart(productData);
                }
            };

            // Aksi dalam tabel keranjang
            cartContainer.addEventListener('click', (e) => {
                const target = e.target.closest('[data-cart-key]');
                if (!target) return;

                const cartKey = target.dataset.cartKey; // Sekarang string (contoh: "15-3")
                const item = cart.get(cartKey);
                if (!item) return;

                if (e.target.closest('.qty-increase')) {
                    if (item.serial_numbers?.length > 0) {
                        tempProductDataForSN = {
                            ...item,
                            wajibSeri: true
                        };
                        const displayName = item.variant_name ? `${item.name} (${item.variant_name})` : item
                            .name;
                        openSerialNumberModal(item.id, item.variant_id || null, displayName, item.jumlah +
                            1, item.serial_numbers);
                    } else {
                        updateQuantity(cartKey, item.jumlah + 1);
                    }
                } else if (e.target.closest('.qty-decrease')) {
                    if (item.serial_numbers?.length > 0) {
                        if (item.jumlah - 1 === 0) {
                            removeFromCart(cartKey);
                        } else {
                            tempProductDataForSN = {
                                ...item,
                                wajibSeri: true
                            };
                            const displayName = item.variant_name ? `${item.name} (${item.variant_name})` :
                                item.name;
                            openSerialNumberModal(item.id, item.variant_id || null, displayName, item
                                .jumlah - 1, item.serial_numbers);
                        }
                    } else {
                        // Produk reguler tanpa SN
                        updateQuantity(cartKey, item.jumlah - 1);
                    }
                } else if (e.target.closest('.remove-item')) {
                    removeFromCart(cartKey);
                }
            });

            // Reset keranjang
            if (resetButton) {
                resetButton.addEventListener('click', () => {
                    if (cart.size > 0 && confirm('Anda yakin ingin mengosongkan keranjang?')) {
                        cart.clear();
                        updateCartAndTotals();
                    }
                });
            }

            // Pencarian produk
            productSearchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const val = e.target.value.trim();
                    if (val) handleBarcodeScan(val);
                }
            });
            productSearchInput.addEventListener('input', debounce(filterProducts, 200));

            // Submit form
            mainForm.addEventListener('submit', function(e) {
                if (cart.size === 0) {
                    e.preventDefault();
                    showToast('Keranjang belanja kosong. Tambahkan produk terlebih dahulu.', 'warning');
                }
            });

            // Input jumlah bayar
            paymentInputEl.addEventListener('input', calculateChange);
            paymentInputEl.addEventListener('blur', formatNumberInput);

            // Bayar pas
            payExactButton.addEventListener('click', () => {
                const total = parseFloat(totalAkhirEl.textContent.replace(/[^0-9]/g, '')) || 0;
                paymentInputEl.value = new Intl.NumberFormat('id-ID').format(total);
                calculateChange();
            });

            document.querySelectorAll('.quick-pay-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const amount = parseInt(btn.dataset.amount) || 0;
                    const currentVal = parseFloat(paymentInputEl.value.replace(/[^0-9]/g, '')) || 0;
                    const newVal = currentVal + amount;
                    paymentInputEl.value = new Intl.NumberFormat('id-ID').format(newVal);
                    calculateChange();
                });
            });

            // Filter kategori
            const categoryFilterContainer = document.getElementById('category-container');
            if (categoryFilterContainer) {
                categoryFilterContainer.addEventListener('click', function(e) {
                    const clicked = e.target.closest('.category-btn');
                    if (!clicked || clicked.classList.contains('category-active')) return;

                    const current = categoryFilterContainer.querySelector('.category-active');
                    if (current) {
                        current.classList.remove('category-active');
                        current.setAttribute('aria-selected', 'false');
                    }
                    clicked.classList.add('category-active');
                    clicked.setAttribute('aria-selected', 'true');
                    selectedCategoryId = clicked.dataset.categoryId;
                    filterProducts();
                });
            }

            // ── Inisialisasi ─────────────────────────────────────────
            document.getElementById('paymentModal').addEventListener('show.bs.modal', function() {
                const total = parseFloat(document.getElementById('total-akhir').textContent.replace(
                    /[^0-9]/g, '')) || 0;
                document.getElementById('payment-modal-total').textContent = formatCurrency(total);
                paymentInputEl.value = '';
                calculateChange();

                setTimeout(() => {
                    document.getElementById('jumlah-dibayar-input').focus();
                }, 500);
            });

            // ── Handle Detail Transfer ─────────────────────────────────────────
            const paymentRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
            const transferDetails = document.getElementById('transfer-details');
            const bankTujuanSelect = document.getElementById('bank_id');

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === 'TRANSFER') {
                        // Tampilkan dropdown dan jadikan wajib diisi
                        transferDetails.classList.remove('d-none');
                        bankTujuanSelect.setAttribute('required', 'required');
                    } else {
                        // Sembunyikan, hapus wajib isi, dan reset nilainya
                        transferDetails.classList.add('d-none');
                        bankTujuanSelect.removeAttribute('required');
                        bankTujuanSelect.value = '';
                    }
                });
            });

            // Pastikan state awalnya benar saat modal dibuka
            document.getElementById('paymentModal').addEventListener('show.bs.modal', function() {
                const isTransfer = document.getElementById('pay-transfer').checked;
                if (!isTransfer) {
                    transferDetails.classList.add('d-none');
                    bankTujuanSelect.removeAttribute('required');
                    bankTujuanSelect.value = '';
                }
            });

            updateCartAndTotals();
        });
    </script>
@endsection
