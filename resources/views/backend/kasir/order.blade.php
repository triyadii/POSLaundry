@extends('backend.layout.app')
@section('title', 'Transaksi Laundry Baru')
@section('content')

    <!-- Flatpickr CSS for estimated completion date -->
    @push('stylesheets')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            .service-card {
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }
            .service-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
            }
            .service-card.selected {
                border-color: #009ef7 !important;
                background-color: #f1faff !important;
            }
            /* Hide HTML5 number input spinners */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
    @endpush

    <div id="kt_app_content" class="app-content flex-column-fluid mt-5">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Top bar -->
            <div class="d-flex align-items-center justify-content-between bg-white rounded p-5 mb-7 shadow-sm">
                <div class="d-flex align-items-center">
                    <a href="{{ route('order.index') }}" class="btn btn-icon btn-light me-4">
                        <i class="ki-outline ki-arrow-left fs-2"></i>
                    </a>
                    <div>
                        <h2 class="mb-1">Kasir Point of Sale (POS)</h2>
                        <span class="text-muted fw-bold">Pembuatan nota laundry terpadu</span>
                    </div>
                </div>
            </div>

            <!-- Dual-Column Layout -->
            <div class="row g-7">
                
                <!-- COLUMN LEFT: Service selection, stains diagnosis, and completion time -->
                <div class="col-xl-7">
                    <div class="card card-flush bg-transparent border-0 mb-6">
                        <div class="card-body p-0">
                            
                            <!-- Search & Quick Navigation -->
                            <div class="d-flex align-items-center mb-6 gap-3">
                                <div class="position-relative flex-grow-1">
                                    <i class="ki-outline ki-magnifier fs-3 position-absolute translate-middle-y top-50 ms-4"></i>
                                    <input type="text" id="search-service" class="form-control ps-12 form-control-solid" placeholder="Cari layanan laundry..." />
                                </div>
                            </div>

                            <!-- Services Grid -->
                            <div class="row g-4" id="services-grid">
                                @foreach ($services as $service)
                                    <div class="col-md-6 service-item" data-name="{{ strtolower($service->name) }}">
                                        <div class="card card-flush h-100 service-card border border-2 border-gray-200 shadow-xs" 
                                             onclick="addServiceToCart('{{ $service->id }}', '{{ e($service->name) }}', {{ $service->price_per_unit }}, '{{ $service->unit }}', {{ $service->estimated_duration_hours }})">
                                            <div class="card-body p-5">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <span class="badge badge-light-primary fw-bold fs-7">{{ $service->category->name }}</span>
                                                    <span class="text-muted fs-8 fw-semibold" title="Estimasi penyelesaian laundry">
                                                        <i class="ki-outline ki-timer fs-7 text-primary"></i> 
                                                        {{ $service->estimated_duration_hours >= 24 ? round($service->estimated_duration_hours / 24) . ' Hari' : $service->estimated_duration_hours . ' Jam' }}
                                                    </span>
                                                </div>
                                                <h3 class="fs-4 fw-bold text-gray-800 mb-2">{{ $service->name }}</h3>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-5 fw-bolder text-success">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}<span class="fs-7 text-muted fw-normal"> / {{ $service->unit }}</span></span>
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-primary rounded-circle">
                                                        <i class="ki-outline ki-plus fs-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <!-- Diagnosis, Instructions, & Estimated Completion Date -->
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-5">
                            <h3 class="card-title fw-bold text-gray-800 fs-4">Diagnosis & Petunjuk Produksi</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column mb-5">
                                <label class="fs-6 fw-bold mb-2">Petunjuk Khusus Cucian (Global)</label>
                                <textarea id="special_instructions" class="form-control form-control-solid" rows="2" placeholder="Contoh: Pisahkan pakaian luntur, gantung gaun pesta, lipat rapi tanpa parfum..."></textarea>
                            </div>
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <div class="d-flex flex-column">
                                        <label class="required fs-6 fw-bold mb-2">Estimasi Selesai (Target)</label>
                                        <div class="position-relative">
                                            <i class="ki-outline ki-calendar fs-3 position-absolute translate-middle-y top-50 ms-4"></i>
                                            <input type="text" id="estimated_completed_at" class="form-control ps-12 form-control-solid" placeholder="Pilih waktu penyelesaian..." />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column h-100 justify-content-end pb-1">
                                        <span class="text-muted fs-7">Sistem menghitung estimasi secara otomatis berdasarkan paket paling lama yang dipilih, tetapi Anda dapat mengubahnya secara manual jika diperlukan.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- COLUMN RIGHT: Cart Items, Customer Selector, Promos, & Payment options -->
                <div class="col-xl-5">
                    <div class="card card-flush shadow-sm bg-body">
                        
                        <div class="card-header pt-5">
                            <h3 class="card-title fw-bold text-gray-800 fs-3">Rincian Transaksi</h3>
                        </div>

                        <div class="card-body">
                            
                            <!-- Customer Selector with Inline Quick Register -->
                            <div class="fv-row mb-6">
                                <label class="required fs-6 fw-bold mb-2 text-gray-800">
                                    <i class="ki-outline ki-profile-user fs-3 text-primary me-1"></i> Pilih / Input Pelanggan
                                </label>
                                <div class="d-flex gap-3 mb-4">
                                    <select id="customer_select" class="form-select form-select-solid" onchange="handleCustomerChange()">
                                        <option value="" disabled selected>-- Pilih Pelanggan --</option>
                                        <option value="manual" data-name="" data-vip="0" data-points="0" data-phone="" data-email="" data-address="">➕ -- Ketik Nama Manual (Non-Member) --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    data-name="{{ $customer->name }}"
                                                    data-vip="{{ $customer->member_status == 'VIP' ? '1' : '0' }}" 
                                                    data-points="{{ $customer->loyalty_points }}"
                                                    data-phone="{{ $customer->phone }}"
                                                    data-email="{{ $customer->email }}"
                                                    data-address="{{ $customer->address }}">
                                                {{ $customer->name }} ({{ strtoupper($customer->member_status) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#Modal_Tambah_Customer" title="Tambah Pelanggan Baru">
                                        <i class="ki-outline ki-plus fs-2"></i>
                                    </button>
                                </div>

                                <!-- Dynamic Customer Info Panel -->
                                <div id="customer-details-card" class="border border-dashed border-gray-300 rounded p-4 mb-4 bg-light-light">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="required fs-8 fw-bold text-gray-700 mb-1">Nama Pelanggan</label>
                                            <input type="text" id="customer_name_manual" class="form-control form-control-solid form-control-sm" placeholder="Nama Pelanggan" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fs-8 fw-bold text-gray-700 mb-1">Nomor WA / HP</label>
                                            <input type="text" id="customer_phone_manual" class="form-control form-control-solid form-control-sm" placeholder="08123xxx" />
                                        </div>
                                        <div class="col-md-12">
                                            <label class="fs-8 fw-bold text-gray-700 mb-1">Email Pelanggan (Notifikasi Selesai)</label>
                                            <input type="email" id="customer_email_manual" class="form-control form-control-solid form-control-sm" placeholder="pelanggan@gmail.com" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Method & Fees Selection -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="required fs-8 fw-bold text-gray-700 mb-1">Metode Pengiriman</label>
                                        <select id="order_type" class="form-select form-select-solid form-select-sm" onchange="handleOrderTypeChange()">
                                            <option value="self_pickup">🧺 Ambil Sendiri (Self-Pickup)</option>
                                            <option value="delivery">🚚 Antar ke Rumah (Delivery)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-none" id="delivery-fee-area">
                                        <label class="required fs-8 fw-bold text-gray-700 mb-1">Biaya Ongkir (Rp)</label>
                                        <input type="number" id="delivery_fee" class="form-control form-control-solid form-control-sm" value="0" min="0" oninput="calculateCartTotals()" />
                                    </div>
                                </div>

                                <!-- Delivery Address input -->
                                <div class="mb-4">
                                    <label class="fs-8 fw-bold text-gray-700 mb-1">Alamat Lengkap Pelanggan</label>
                                    <textarea id="delivery_address" class="form-control form-control-solid form-control-sm" rows="2" placeholder="Masukkan alamat lengkap pengiriman/pengambilan laundry..."></textarea>
                                </div>

                                <div id="vip-badge-area" class="mt-2 d-none">
                                    <span class="badge badge-light-danger fs-7 fw-bold"><i class="ki-outline ki-verify fs-7 text-danger me-1"></i> VIP Member: Diskon 10% otomatis ditambahkan!</span>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            <div class="table-responsive border border-dashed rounded-3 p-4 mb-6" style="max-height: 280px; overflow-y: auto;">
                                <table class="table align-middle gs-0 gy-3 my-0">
                                    <thead>
                                        <tr class="fw-bold text-gray-400 border-bottom border-gray-200 fs-8">
                                            <th class="ps-2">Layanan</th>
                                            <th class="w-100px text-center">Jumlah (Qty)</th>
                                            <th class="text-end pe-2">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-tbody">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted fst-italic py-8">Keranjang laundry kosong.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Promo Selection -->
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold mb-2 text-gray-800">
                                    <i class="ki-outline ki-discount fs-3 text-danger me-1"></i> Gunakan Promo / Voucher
                                </label>
                                <select id="promo_select" class="form-select form-select-solid" onchange="calculateCartTotals()">
                                    <option value="">-- Tidak Ada Promo --</option>
                                    @foreach ($promos as $promo)
                                        <option value="{{ $promo->id }}" data-type="{{ $promo->discount_type }}" data-value="{{ $promo->discount_value }}">
                                            {{ $promo->name }} ({{ $promo->discount_type == 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Cost Breakdown -->
                            <div class="d-flex flex-stack bg-light-primary rounded-3 p-4 mb-3">
                                <div class="fs-7 fw-bold text-primary">Subtotal</div>
                                <div class="fs-6 fw-bolder text-primary" id="summary-subtotal">Rp 0</div>
                             </div>

                             <div class="d-flex flex-stack bg-light-info rounded-3 p-4 mb-3 d-none" id="summary-delivery-row">
                                <div class="fs-7 fw-bold text-info">Biaya Pengiriman (Ongkir)</div>
                                <div class="fs-6 fw-bolder text-info" id="summary-delivery">Rp 0</div>
                             </div>

                             <div class="d-flex flex-stack bg-light-danger rounded-3 p-4 mb-3 d-none" id="summary-discount-row">
                                <div class="fs-7 fw-bold text-danger">Total Diskon</div>
                                <div class="fs-6 fw-bolder text-danger" id="summary-discount">- Rp 0</div>
                             </div>

                             <div class="d-flex flex-stack bg-light-warning rounded-3 p-4 mb-4">
                                <div class="fs-7 fw-bold text-warning">Pajak ({{ $setting->tax_rate ?? 0 }}%)</div>
                                <div class="fs-6 fw-bolder text-warning" id="summary-tax">Rp 0</div>
                             </div>

                             <div class="d-flex flex-stack bg-success rounded-3 p-5 mb-6 shadow-sm">
                                <div class="fs-6 fw-bold text-white">Grand Total</div>
                                <div class="fs-4 fw-bolder text-white" id="summary-grandtotal">Rp 0</div>
                             </div>

                            <!-- Checkout Trigger Button -->
                            <button type="button" class="btn btn-primary btn-lg w-100 py-4 fs-4 fw-bold" id="btn-open-payment" disabled onclick="triggerPaymentModal()">
                                <i class="ki-outline ki-wallet fs-3 me-2"></i> Pilih Metode Bayar
                            </button>

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- MODAL METODE PEMBAYARAN & DP SPLIT -->
    <div class="modal fade" id="Modal_Payment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Metode Pembayaran Laundry</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body mx-5 my-7">
                    
                    <div class="text-center mb-6">
                        <span class="fs-5 text-muted d-block">Total Tagihan Laundry</span>
                        <div class="fs-1 fw-bold text-success" id="payment-modal-grandtotal">Rp 0</div>
                    </div>

                    <!-- Payment Mode -->
                    <div class="fv-row mb-6">
                        <label class="required fs-6 fw-semibold mb-2">Tipe Pembayaran</label>
                        <select id="payment_method" class="form-select form-select-solid" onchange="togglePaymentInputs()">
                            <option value="cash">💵 Tunai Penuh (Cash Lunas)</option>
                            <!-- <option value="midtrans">📱 QRIS / Bank Transfer (Midtrans Lunas)</option> -->
                            <!-- <option value="down_payment">💸 Uang Muka / Down Payment (DP)</option> -->
                        </select>
                    </div>

                    <!-- Down Payment Input Area -->
                    <div id="dp_input_area" class="fv-row mb-6 d-none">
                        <label class="required fs-6 fw-semibold mb-2">Nominal DP Diterima (Rp)</label>
                        <input type="number" class="form-control form-control-solid" id="down_payment_amount" placeholder="Contoh: 15000" min="0" onkeyup="calculateRemainingBalance()">
                        <div class="mt-3">
                            <span class="fs-6 text-gray-700 fw-bold">Sisa Tagihan Nanti: <span id="dp_remaining_amount" class="text-danger">Rp 0</span></span>
                        </div>
                    </div>

                    <!-- Cash Input Area -->
                    <div id="cash_input_area" class="fv-row mb-6">
                        <label class="required fs-6 fw-semibold mb-2">Uang Diterima (Rp)</label>
                        <input type="number" class="form-control form-control-solid" id="pay_amount" placeholder="Contoh: 50000" onkeyup="calculateCashChange()">
                        <div class="mt-3">
                            <span class="fs-6 text-gray-700 fw-bold">Uang Kembalian: <span id="pay_change_amount" class="text-success">Rp 0</span></span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center pt-5 border-top mt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-confirm-submit" onclick="submitLaundryOrder()">Proses Transaksi</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REGISTER NEW CUSTOMER INLINE -->
    <div class="modal fade" id="Modal_Tambah_Customer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Registrasi Pelanggan Baru</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body scroll-y mx-5 my-3">
                    <form id="FormQuickRegisterCustomer" class="form">
                        @csrf
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-solid" name="name" placeholder="Nama Pelanggan" required />
                        </div>
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nomor HP / WhatsApp</label>
                            <input type="text" class="form-control form-control-solid" name="phone" placeholder="Contoh: 0812345678" required />
                        </div>
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Status Member</label>
                            <select class="form-select form-select-solid" name="member_status">
                                <option value="regular">Regular</option>
                                <option value="VIP">VIP (Diskon 10% Otomatis)</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column mb-5 fv-row">
                            <label class="fs-6 fw-semibold mb-2">Alamat Pengantaran (Lengkap)</label>
                            <textarea class="form-control form-control-solid" rows="2" name="address" placeholder="Jalan, Blok, Kecamatan..."></textarea>
                        </div>
                        <div class="text-center pt-8 mb-5">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btn-save-quick-customer">
                                <span class="indicator-label">Simpan Pelanggan</span>
                                <span class="indicator-progress" style="display: none;">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        
        <script>
            // Flatpickr Init for DateTime
            flatpickr("#estimated_completed_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                defaultDate: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000), // Default +48 hours
                time_24hr: true
            });

            var cart = [];
            var subtotal = 0;
            var discount = 0;
            var taxRate = {{ $setting->tax_rate ?? 0 }} / 100;
            var tax = 0;
            var grandTotal = 0;

            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number);

            // Search filter for service grid
            $('#search-service').on('keyup', function() {
                let query = $(this).val().toLowerCase();
                $('.service-item').each(function() {
                    let name = $(this).data('name');
                    if (name.includes(query)) $(this).removeClass('d-none');
                    else $(this).addClass('d-none');
                });
            });

            // Add item to cart
            window.addServiceToCart = function(id, name, price, unit, durationHours) {
                let item = cart.find(i => i.service_id === id);
                if (item) {
                    item.qty = parseFloat((item.qty + 1.0).toFixed(2));
                } else {
                    cart.push({
                        service_id: id,
                        name: name,
                        price: price,
                        unit: unit,
                        qty: 1.0,
                        duration_hours: durationHours,
                        item_condition: '',
                        note: ''
                    });
                }
                renderCart();
            }

            // Update item quantity
            window.updateCartQty = function(id, action) {
                let item = cart.find(i => i.service_id === id);
                if (item) {
                    let step = 1.0;
                    if (action === 'increase') {
                        item.qty = parseFloat((item.qty + step).toFixed(2));
                    } else if (action === 'decrease') {
                        item.qty = parseFloat((item.qty - step).toFixed(2));
                        if (item.qty <= 0) {
                            cart = cart.filter(i => i.service_id !== id);
                        }
                    }
                    renderCart();
                }
            }

            // Direct input quantity keyup
            window.updateCartQtyDirect = function(id, val) {
                let item = cart.find(i => i.service_id === id);
                if (item) {
                    let qty = parseFloat(val) || 0;
                    if (qty > 0) {
                        item.qty = qty;
                    }
                    // Recalculate totals directly to keep in sync without full render to avoid focus loss
                    calculateCartTotals();
                }
            }

            // Update individual item condition / notes
            window.updateItemMetadata = function(id, type, value) {
                let item = cart.find(i => i.service_id === id);
                if (item) {
                    if (type === 'condition') item.item_condition = value;
                    else if (type === 'note') item.note = value;
                }
            }

            // Render Cart HTML
            window.renderCart = function() {
                let html = '';
                if (cart.length === 0) {
                    html = '<tr><td colspan="3" class="text-center text-muted fst-italic py-8">Keranjang laundry kosong.</td></tr>';
                    $('#btn-open-payment').prop('disabled', true);
                } else {
                    $('#btn-open-payment').prop('disabled', false);

                    cart.forEach(item => {
                        let step = 1.0;
                        let lineTotal = item.qty * item.price;
                        html += `
                        <tr class="border-bottom border-gray-100">
                            <td class="pt-3 pb-2">
                                <div class="fw-bold text-gray-800 fs-6">${item.name}</div>
                                <span class="text-success fs-7 fw-semibold">Rp ${numberFormat(item.price)} / ${item.unit}</span>
                                
                                <!-- Stains Diagnosis input -->
                                <div class="mt-2">
                                    <input type="text" class="form-control form-control-solid fs-8 py-1 px-2 mb-1" 
                                           placeholder="Diagnosis noda/kerusakan item..." 
                                           value="${item.item_condition}" 
                                           onchange="updateItemMetadata('${item.service_id}', 'condition', this.value)">
                                    <input type="text" class="form-control form-control-solid fs-8 py-1 px-2" 
                                           placeholder="Petunjuk khusus item ini..." 
                                           value="${item.note}" 
                                           onchange="updateItemMetadata('${item.service_id}', 'note', this.value)">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center border border-gray-300 rounded p-1">
                                    <button type="button" class="btn btn-icon btn-sm btn-light w-20px h-20px rounded-circle" onclick="updateCartQty('${item.service_id}', 'decrease')">
                                        <i class="ki-outline ki-minus fs-6"></i>
                                    </button>
                                    <input type="number" class="form-control border-0 text-center px-0 fs-6 fw-bold w-55px h-25px" 
                                           value="${item.qty}" step="${step}" min="0.01" 
                                           onkeyup="updateCartQtyDirect('${item.service_id}', this.value)">
                                    <button type="button" class="btn btn-icon btn-sm btn-light w-20px h-20px rounded-circle" onclick="updateCartQty('${item.service_id}', 'increase')">
                                        <i class="ki-outline ki-plus fs-6"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-end pe-2 fw-bolder text-gray-900 fs-6">
                                Rp ${numberFormat(lineTotal)}
                            </td>
                        </tr>
                        `;
                    });

                    // Set Estimated completion auto-date based on maximum duration package selected
                    let maxHours = 0;
                    cart.forEach(item => {
                        if (item.duration_hours > maxHours) maxHours = item.duration_hours;
                    });
                    
                    if (maxHours > 0) {
                        let autoDate = new Date(Date.now() + maxHours * 60 * 60 * 1000);
                        document.getElementById('estimated_completed_at')._flatpickr.setDate(autoDate);
                    }
                }
                
                $('#cart-tbody').html(html);
                calculateCartTotals();
            }

            // Helper format number
            function numberFormat(val) {
                return new Intl.NumberFormat('id-ID').format(val);
            }

            // Calculate totals (Subtotal, Discount, Tax, Grand Total)
            window.calculateCartTotals = function() {
                subtotal = 0;
                cart.forEach(item => {
                    subtotal += item.qty * item.price;
                });

                discount = 0;
                
                // VIP Customer discount (10%)
                let customerSelected = $('#customer_select').find(':selected');
                if (customerSelected.val()) {
                    let isVip = customerSelected.data('vip') == '1';
                    if (isVip) {
                        $('#vip-badge-area').removeClass('d-none');
                        discount += Math.round(subtotal * 0.10);
                    } else {
                        $('#vip-badge-area').addClass('d-none');
                    }
                } else {
                    $('#vip-badge-area').addClass('d-none');
                }

                // Promo discount selector
                let promoSelected = $('#promo_select').find(':selected');
                if (promoSelected.val()) {
                    let type = promoSelected.data('type');
                    let val = parseFloat(promoSelected.data('value'));
                    if (type === 'percentage') {
                        discount += Math.round((subtotal - discount) * (val / 100));
                    } else {
                        discount += val;
                    }
                }

                let netSubtotal = subtotal - discount;
                if (netSubtotal < 0) netSubtotal = 0;

                tax = Math.round(netSubtotal * taxRate);
                
                // Add delivery fee
                let deliveryFee = 0;
                if ($('#order_type').val() === 'delivery') {
                    deliveryFee = parseInt($('#delivery_fee').val()) || 0;
                }
                
                grandTotal = netSubtotal + tax + deliveryFee;

                // Update DOM elements
                $('#summary-subtotal').text(formatRupiah(subtotal));
                if (discount > 0) {
                    $('#summary-discount-row').removeClass('d-none');
                    $('#summary-discount').text('- ' + formatRupiah(discount));
                } else {
                    $('#summary-discount-row').addClass('d-none');
                }
                
                if ($('#order_type').val() === 'delivery') {
                    $('#summary-delivery-row').removeClass('d-none');
                    $('#summary-delivery').text(formatRupiah(deliveryFee));
                } else {
                    $('#summary-delivery-row').addClass('d-none');
                }
                
                $('#summary-tax').text(formatRupiah(tax));
                $('#summary-grandtotal').text(formatRupiah(grandTotal));
            }

            // Handle Customer selection change
            window.handleCustomerChange = function() {
                let opt = $('#customer_select').find(':selected');
                let val = opt.val();
                
                if (val === 'manual') {
                    $('#customer_name_manual').val('').prop('readonly', false);
                    $('#customer_phone_manual').val('').prop('readonly', false);
                    $('#customer_email_manual').val('').prop('readonly', false);
                    $('#delivery_address').val('').prop('readonly', false);
                } else if (val) {
                    $('#customer_name_manual').val(opt.data('name') || '').prop('readonly', false);
                    $('#customer_phone_manual').val(opt.data('phone') || '').prop('readonly', false);
                    $('#customer_email_manual').val(opt.data('email') || '').prop('readonly', false);
                    $('#delivery_address').val(opt.data('address') || '').prop('readonly', false);
                }
                calculateCartTotals();
            }

            // Handle Order Type (Pickup/Delivery) Change
            window.handleOrderTypeChange = function() {
                let type = $('#order_type').val();
                if (type === 'delivery') {
                    $('#delivery-fee-area').removeClass('d-none');
                } else {
                    $('#delivery-fee-area').addClass('d-none');
                    $('#delivery_fee').val(0);
                }
                calculateCartTotals();
            }

            // Trigger payment modal
            window.triggerPaymentModal = function() {
                let customerId = $('#customer_select').val();
                if (!customerId) {
                    Swal.fire('Pelanggan Belum Dipilih!', 'Harap pilih pelanggan terlebih dahulu.', 'warning');
                    return;
                }

                if (customerId === 'manual' && !$('#customer_name_manual').val().trim()) {
                    Swal.fire('Nama Pelanggan Kosong!', 'Harap ketik nama pelanggan manual terlebih dahulu.', 'warning');
                    return;
                }

                $('#payment-modal-grandtotal').text(formatRupiah(grandTotal));
                
                // Reset inputs inside modal
                $('#pay_amount').val('');
                $('#pay_change_amount').text('Rp 0');
                $('#down_payment_amount').val('');
                $('#dp_remaining_amount').text('Rp 0');
                
                $('#payment_method').val('cash');
                togglePaymentInputs();

                $('#Modal_Payment').modal('show');
            }

            // Toggle Cash vs DP splits inputs
            window.togglePaymentInputs = function() {
                let method = $('#payment_method').val();
                if (method === 'down_payment') {
                    $('#dp_input_area').removeClass('d-none');
                    $('#cash_input_area').addClass('d-none'); // DP uses unique split checkout confirmation
                } else if (method === 'midtrans') {
                    $('#dp_input_area').addClass('d-none');
                    $('#cash_input_area').addClass('d-none'); // Midtrans uses popup snap
                } else {
                    $('#dp_input_area').addClass('d-none');
                    $('#cash_input_area').removeClass('d-none'); // Cash lunas
                }
            }

            // Cash change calculator
            window.calculateCashChange = function() {
                let pay = parseInt($('#pay_amount').val()) || 0;
                let change = pay - grandTotal;
                $('#pay_change_amount').text(formatRupiah(change < 0 ? 0 : change));
            }

            // DP balance splits calculator
            window.calculateRemainingBalance = function() {
                let dp = parseInt($('#down_payment_amount').val()) || 0;
                let remaining = grandTotal - dp;
                $('#dp_remaining_amount').text(formatRupiah(remaining < 0 ? 0 : remaining));
            }

            // Submit laundry order via AJAX
            window.submitLaundryOrder = function() {
                let customerId = $('#customer_select').val();
                let method = $('#payment_method').val();
                let dpAmt = parseInt($('#down_payment_amount').val()) || 0;
                let payAmt = parseInt($('#pay_amount').val()) || 0;

                // Validations
                if (method === 'cash' && payAmt < grandTotal) {
                    Swal.fire('Uang Kurang!', 'Uang diterima kurang dari grand total.', 'warning');
                    return;
                }
                if (method === 'down_payment' && dpAmt <= 0) {
                    Swal.fire('DP Wajib Diisi!', 'Uang muka (DP) wajib lebih besar dari Rp 0.', 'warning');
                    return;
                }
                if (method === 'down_payment' && dpAmt >= grandTotal) {
                    Swal.fire('Gagal!', 'Uang muka melebihi atau menyamai total tagihan. Silakan gunakan metode Bayar Tunai Lunas.', 'warning');
                    return;
                }

                $('#btn-confirm-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

                let payload = {
                    _token: '{{ csrf_token() }}',
                    customer_id: customerId,
                    customer_name_manual: $('#customer_name_manual').val(),
                    customer_phone_manual: $('#customer_phone_manual').val(),
                    customer_email: $('#customer_email_manual').val(),
                    delivery_address: $('#delivery_address').val(),
                    order_type: $('#order_type').val(),
                    delivery_fee: parseInt($('#delivery_fee').val()) || 0,
                    promo_id: $('#promo_select').val(),
                    payment_method: method,
                    down_payment_amount: dpAmt,
                    cash_received: parseInt($('#pay_amount').val()) || 0,
                    special_instructions: $('#special_instructions').val(),
                    estimated_completed_at: $('#estimated_completed_at').val(),
                    cart: cart
                };

                $.ajax({
                    url: "{{ route('order.store') }}",
                    method: "POST",
                    data: payload,
                    success: function(res) {
                        $('#Modal_Payment').modal('hide');

                        const showPrintDialog = (orderId) => {
                            Swal.fire({
                                title: 'Transaksi Berhasil!',
                                text: 'Apakah Anda ingin mencetak nota laundry pelanggan?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#009ef7',
                                cancelButtonColor: '#f1416c',
                                confirmButtonText: '<i class="ki-outline ki-printer fs-4 me-2 text-white"></i> Cetak Nota',
                                cancelButtonText: 'Kembali ke POS',
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open('{{ url('admin') }}/order/print/' + orderId, '_blank', 'width=400,height=600');
                                }
                                window.location.href = "{{ route('order.index') }}";
                            });
                        };

                        if (res.type === 'midtrans') {
                            document.body.style.overflow = 'hidden';
                            snap.pay(res.snap_token, {
                                onSuccess: function(result) {
                                    document.body.style.overflow = 'auto';
                                    Swal.fire({
                                        title: 'Memverifikasi Pembayaran...',
                                        allowOutsideClick: false,
                                        didOpen: () => { Swal.showLoading(); }
                                    });
                                    setTimeout(() => {
                                        showPrintDialog(res.order_id);
                                    }, 2500);
                                },
                                onPending: function() {
                                    document.body.style.overflow = 'auto';
                                    Swal.fire('Pending', 'Segera selesaikan pembayaran QRIS Anda.', 'info').then(() => {
                                        window.location.href = "{{ route('order.index') }}";
                                    });
                                },
                                onError: function() {
                                    document.body.style.overflow = 'auto';
                                    Swal.fire('Gagal', 'Pembayaran gagal diproses.', 'error');
                                    $('#btn-confirm-submit').prop('disabled', false).text('Proses Transaksi');
                                }
                            });
                        } else {
                            showPrintDialog(res.order_id);
                        }
                    },
                    error: function(err) {
                        Swal.fire('Gagal!', err.responseJSON.error || 'Terjadi kesalahan internal.', 'error');
                        $('#btn-confirm-submit').prop('disabled', false).text('Proses Transaksi');
                    }
                });
            }

            // Quick Register Customer inline
            $('#FormQuickRegisterCustomer').on('submit', function(e) {
                e.preventDefault();
                $('#btn-save-quick-customer').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('customers.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.errors) {
                            Swal.fire('Gagal!', 'Registrasi gagal. Cek inputan.', 'error');
                            $('#btn-save-quick-customer').prop('disabled', false).text('Simpan Pelanggan');
                        } else {
                            Swal.fire('Berhasil!', 'Pelanggan baru berhasil didaftarkan!', 'success').then(() => {
                                $('#Modal_Tambah_Customer').modal('hide');
                                $('#FormQuickRegisterCustomer')[0].reset();
                                $('#btn-save-quick-customer').prop('disabled', false).text('Simpan Pelanggan');
                                
                                // Dynamic reload page to fetch newly registered customer immediately in list
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                        $('#btn-save-quick-customer').prop('disabled', false).text('Simpan Pelanggan');
                    }
                });
            });
        </script>
    @endpush
@endsection
