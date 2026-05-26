@extends('backend.layout.app')
@section('title', 'Dashboard Kasir Laundry')
@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-0">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Kasir POS</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-gray-700 fw-bold lh-1"><a href="{{ route('dashboard') }}"
                            class="text-white text-hover-primary"><i class="ki-outline ki-home text-gray-700 fs-6"></i></a>
                    </li>
                    <li class="breadcrumb-item"><i class="ki-outline ki-right fs-5 text-gray-700 mx-n1"></i></li>
                    <li class="breadcrumb-item text-gray-700 fw-bold lh-1">Kasir</li>
                    <li class="breadcrumb-item"><i class="ki-outline ki-right fs-5 text-gray-700 mx-n1"></i></li>
                    <li class="breadcrumb-item text-gray-900">Dashboard POS</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Statistics Cards -->
            <div class="row g-5 mb-10">
                <div class="col-md-4">
                    <div class="card card-flush bg-light-warning border-warning border-dashed py-5 px-6 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-timer fs-3x text-warning me-4"></i>
                            <div>
                                <span class="fs-6 text-warning fw-bold d-block">Laundry Sedang Diproses</span>
                                <span class="fs-2hx fw-bold text-gray-800">{{ $pendingCount }}</span>
                                <span class="fs-7 text-muted d-block">Nota belum selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-flush bg-light-success border-success border-dashed py-5 px-6 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-verify fs-3x text-success me-4"></i>
                            <div>
                                <span class="fs-6 text-success fw-bold d-block">Selesai Hari Ini</span>
                                <span class="fs-2hx fw-bold text-gray-800">{{ $completedTodayCount }}</span>
                                <span class="fs-7 text-muted d-block">Siap diambil pelanggan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-flush bg-light-primary border-primary border-dashed py-5 px-6 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-calculator fs-3x text-primary me-4"></i>
                            <div>
                                <span class="fs-6 text-primary fw-bold d-block">Output Kiloan Hari Ini</span>
                                <span class="fs-2hx fw-bold text-gray-800">{{ number_format($totalWeightToday, 1) }} <span class="fs-4 fw-semibold">kg</span></span>
                                <span class="fs-7 text-muted d-block">Total cuci kiloan hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction List -->
            <div class="card card-flush shadow-sm">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                            <input type="text" id="search-order" class="form-control w-250px ps-12"
                                placeholder="Cari invoice / pelanggan..." />
                        </div>
                    </div>
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <a href="{{ route('order.create') }}" class="btn btn-primary fw-bold">
                            <i class="ki-outline ki-plus fs-2"></i> Buat Order Laundry
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 table-orders" id="table-orders">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">No</th>
                                    <th class="min-w-100px">No Invoice</th>
                                    <th class="min-w-150px">Pelanggan</th>
                                    <th class="min-w-120px">Total Tagihan</th>
                                    <th class="min-w-120px">Sisa Tagihan</th>
                                    <th class="min-w-150px">Estimasi Selesai</th>
                                    <th class="min-w-100px">Status Laundry</th>
                                    <th class="text-end min-w-150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                @foreach ($orders as $index => $order)
                                    @php
                                        $sisa = $order->grand_total - $order->down_payment;
                                        
                                        // Tag status laundry
                                        $statusClass = 'badge-light-secondary';
                                        if($order->order_status == 'dicuci') $statusClass = 'badge-light-primary';
                                        elseif($order->order_status == 'dikeringkan') $statusClass = 'badge-light-warning';
                                        elseif($order->order_status == 'disetrika') $statusClass = 'badge-light-info';
                                        elseif($order->order_status == 'packing') $statusClass = 'badge-light-dark';
                                        elseif($order->order_status == 'selesai') $statusClass = 'badge-light-success';
                                        elseif($order->order_status == 'diambil') $statusClass = 'badge-light-success';
                                    @endphp
                                    <tr class="border-bottom border-gray-200">
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="fw-bold text-gray-800">{{ $order->invoice_no }}</span></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-gray-800">{{ $order->customer?->name ?? $order->customer_name }}</span>
                                                <span class="text-muted fs-8">{{ $order->customer?->phone ?? 'Walk-In / Non-Member' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-gray-800">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            @if ($sisa <= 0)
                                                <span class="badge badge-light-success fs-7 fw-bold">LUNAS</span>
                                            @else
                                                <span class="badge badge-light-danger fs-7 fw-bold">KURANG Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted fs-7">{{ $order->estimated_completed_at ? \Carbon\Carbon::parse($order->estimated_completed_at)->translatedFormat('d M Y H:i') . ' WIB' : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClass }} fs-7 fw-bold text-uppercase">{{ $order->order_status }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                @if ($order->order_status == 'diterima')
                                                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" onclick="changeStatus('{{ $order->id }}', 'dicuci')" title="Mulai Proses Cuci Laundry">
                                                        Proses
                                                    </button>
                                                @endif

                                                @if (in_array($order->order_status, ['diterima', 'dicuci', 'dikeringkan', 'disetrika', 'packing']))
                                                    <button type="button" class="btn btn-sm btn-light-success fw-bold" onclick="changeStatus('{{ $order->id }}', 'selesai')" title="Tandai Laundry Selesai & Kirim Email">
                                                        Selesai
                                                    </button>
                                                @endif

                                                @if ($sisa > 0)
                                                    <button type="button" class="btn btn-sm btn-light-danger fw-bold" onclick="openPaymentSusulan('{{ $order->id }}', {{ $sisa }})">
                                                        Pelunasan
                                                    </button>
                                                @endif

                                                @if ($order->order_status == 'selesai' && $sisa <= 0)
                                                    <button type="button" class="btn btn-sm btn-light-success fw-bold" onclick="deliverLaundry('{{ $order->id }}')">
                                                        Diserahkan
                                                    </button>
                                                @endif

                                                <a href="{{ route('order.print', $order->id) }}" target="_blank" class="btn btn-sm btn-icon btn-light-primary" title="Cetak Nota">
                                                    <i class="ki-outline ki-printer fs-4"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Pelunasan -->
    <div class="modal fade" id="Modal_Payment_Susulan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Pelunasan Sisa Tagihan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i
                            class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body mx-5 my-7">
                    <input type="hidden" id="susulan_order_id">
                    <input type="hidden" id="susulan_grand_total">

                    <div class="text-center mb-5">
                        <span class="fs-5 text-muted">Sisa Kekurangan</span>
                        <div class="fs-1 fw-bold text-danger" id="susulan-total-text">Rp 0</div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Metode Pembayaran</label>
                        <select id="susulan_payment_method" class="form-select form-select-solid">
                            <option value="cash">💵 Tunai (Cash)</option>
                            <option value="midtrans">📱 QRIS / Transfer (Midtrans)</option>
                        </select>
                    </div>

                    <div id="susulan_cash_area" class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold mb-2">Uang Diterima (Rp)</label>
                        <input type="number" class="form-control form-control-solid" id="susulan_pay_amount"
                            placeholder="0">
                        <div class="mt-3"><span class="fs-6 text-gray-700 fw-bold">Kembalian: <span
                                    id="susulan_change_amount" class="text-danger">Rp 0</span></span></div>
                    </div>

                    <div class="text-center pt-5 border-top mt-5">
                        <button type="button" class="btn btn-primary w-100" id="btn-process-susulan">Proses
                            Pembayaran</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('.table-orders').DataTable({
                    order: false,
                    searching: true,
                    paging: true,
                    info: true,
                    language: {
                        emptyTable: "Belum ada transaksi laundry aktif saat ini.",
                        zeroRecords: "Tidak ada transaksi yang cocok ditemukan.",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        lengthMenu: "Tampilkan _MENU_ entri",
                        search: "Cari:",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });

                // Custom search box bind
                $('#search-order').on('keyup', function() {
                    table.search($(this).val()).draw();
                });

                // Format Rupiah
                const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);

                // Open Pelunasan Modal
                window.openPaymentSusulan = function(orderId, grandTotal) {
                    $('#susulan_order_id').val(orderId);
                    $('#susulan_grand_total').val(grandTotal);
                    $('#susulan-total-text').text(formatRupiah(grandTotal));
                    $('#susulan_pay_amount').val('');
                    $('#susulan_change_amount').text('Rp 0');
                    $('#Modal_Payment_Susulan').modal('show');
                }

                // Calculator Kembalian
                $('#susulan_pay_amount').on('keyup', function() {
                    let pay = parseInt($(this).val()) || 0;
                    let total = parseInt($('#susulan_grand_total').val()) || 0;
                    let change = pay - total;
                    $('#susulan_change_amount').text(formatRupiah(change < 0 ? 0 : change));
                });

                // Toggle cash inputs based on payment method
                $('#susulan_payment_method').on('change', function() {
                    if ($(this).val() == 'midtrans') $('#susulan_cash_area').slideUp();
                    else $('#susulan_cash_area').slideDown();
                });

                // Submit Pelunasan
                $('#btn-process-susulan').on('click', function() {
                    let orderId = $('#susulan_order_id').val();
                    let payMethod = $('#susulan_payment_method').val();
                    let total = parseInt($('#susulan_grand_total').val());
                    let payAmt = parseInt($('#susulan_pay_amount').val()) || 0;

                    if (payMethod == 'cash' && payAmt < total) {
                        Swal.fire('Uang Kurang!', 'Nominal uang tidak cukup.', 'warning');
                        return;
                    }

                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');

                    $.ajax({
                        url: "{{ route('order.pay-existing') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            order_id: orderId,
                            payment_method: payMethod
                        },
                        success: function(res) {
                            $('#Modal_Payment_Susulan').modal('hide');

                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#009ef7',
                                cancelButtonColor: '#f1416c',
                                confirmButtonText: '<i class="ki-outline ki-printer fs-4 me-2 text-white"></i> Cetak Nota',
                                cancelButtonText: 'Selesai',
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open('{{ url('admin') }}/order/print/' + res.order_id, '_blank', 'width=400,height=600');
                                }
                                location.reload();
                            });
                        },
                        error: function(err) {
                            Swal.fire('Gagal!', err.responseJSON.error || 'Terjadi kesalahan sistem.', 'error');
                            $('#btn-process-susulan').prop('disabled', false).text('Proses Pembayaran');
                        }
                    });
                });

                // Deliver/Pickup Laundry
                window.deliverLaundry = function(orderId) {
                    Swal.fire({
                        title: 'Serahkan Pakaian?',
                        text: "Konfirmasi bahwa pakaian bersih telah diserahkan ke tangan pelanggan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#50cd89',
                        cancelButtonColor: '#f1416c',
                        confirmButtonText: 'Ya, Serahkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('admin') }}/order/clear-table/" + orderId,
                                method: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(err) {
                                    Swal.fire('Gagal!', err.responseJSON.error || 'Gagal menyerahkan pakaian.', 'error');
                                }
                            });
                        }
                    });
                }

                // Change Laundry Order Status (Proses & Selesai)
                window.changeStatus = function(orderId, status) {
                    let titleText = status === 'selesai' ? 'Tandai Laundry Selesai?' : 'Mulai Proses Laundry?';
                    let descText = status === 'selesai' 
                        ? 'Pakaian akan ditandai selesai dan sistem otomatis mengirimkan email notifikasi ke customer.' 
                        : 'Laundry akan dimasukkan ke antrean proses pencucian.';
                    let confirmBtnColor = status === 'selesai' ? '#50cd89' : '#009ef7';

                    Swal.fire({
                        title: titleText,
                        text: descText,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: confirmBtnColor,
                        cancelButtonColor: '#f1416c',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Harap tunggu sebentar...',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });

                            $.ajax({
                                url: "{{ route('order.update-status') }}",
                                method: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    order_id: orderId,
                                    status: status
                                },
                                success: function(res) {
                                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(err) {
                                    Swal.fire('Gagal!', err.responseJSON.error || 'Gagal memperbarui status laundry.', 'error');
                                }
                            });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
