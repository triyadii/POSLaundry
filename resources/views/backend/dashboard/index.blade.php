@extends('backend.layout.app')
@section('title', 'Dashboard Analytics Laundry')
@section('content')

    <div id="kt_app_content" class="app-content flex-column-fluid mt-5">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Summary Widgets -->
            <div class="row g-5 g-xl-10 mb-xl-10">
                <div class="col-md-6">
                    <div class="card bg-light-primary border-0 shadow-sm h-100">
                        <div class="card-body p-6">
                            <div class="fs-6 fw-semibold text-primary mb-2">Total Omzet (Bulan Ini)</div>
                            <div class="fs-2hx fw-bold text-gray-800">Rp
                                {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
                            <span class="fs-8 text-muted d-block mt-1">Pendapatan kotor laundry terbayar</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light-info border-0 shadow-sm h-100">
                        <div class="card-body p-6">
                            <div class="fs-6 fw-semibold text-info mb-2">Volume Cucian Diproses</div>
                            <div class="fs-2hx fw-bold text-gray-800">
                                {{ number_format($summary['items_sold'], 1, ',', '.') }} <span
                                    class="fs-4 text-muted">kg/pcs</span>
                            </div>
                            <span class="fs-8 text-muted d-block mt-1">Berat volume total cucian masuk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Selling items -->
            <div class="row g-5 g-xl-10 mb-xl-10 mt-5">
                <div class="col-xl-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header pt-5 border-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Performa Outlet Laundry Harian</span>
                                <span class="text-muted fw-semibold fs-7">Grafik Omzet Aktual Bulan Ini</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2 pb-0 ps-0">
                            <div id="kt_sales_chart" style="height: 350px"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header pt-5 border-0">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Paket Laundry Terlaris</span>
                                <span class="text-muted fw-semibold fs-7">Top 5 Penjualan Terbanyak Bulan Ini</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            @forelse($topProducts as $top)
                                <div class="d-flex flex-stack mb-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol py-2 symbol-40px me-4">
                                            <span
                                                class="symbol-label bg-light-primary text-primary fw-bold">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <a href="#"
                                                class="fs-6 text-gray-800 text-hover-primary fw-bold mb-1">{{ $top->service->name ?? 'Layanan Dihapus' }}</a>
                                            <div class="fw-semibold text-gray-400 fs-8">
                                                {{ $top->service->category->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <div class="fs-5 fw-bolder text-gray-800">{{ number_format($top->total_qty, 1) }} <span
                                                class="fs-8 fw-normal text-muted">{{ $top->service->unit ?? 'kg' }}</span></div>
                                        <div class="fs-7 fw-bold text-success">Rp
                                            {{ number_format($top->total_revenue, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-10">Belum ada data laundry diproses bulan ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Laundry Services Alert Table -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1 text-warning"><i
                                        class="ki-outline ki-warning-2 fs-2 text-warning me-2"></i> Paket Laundry Non-Aktif</span>
                                <span class="text-muted fw-semibold fs-7">Layanan laundry yang saat ini sedang tidak ditawarkan di kasir</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('services.index') }}" class="btn btn-sm btn-light-primary">Kelola Layanan</a>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4 rounded-start">Kategori</th>
                                            <th>Nama Paket Layanan</th>
                                            <th>Harga / Satuan</th>
                                            <th class="text-end pe-4 rounded-end">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inactiveServices as $service)
                                            <tr>
                                                <td class="ps-4">
                                                    <span
                                                        class="badge badge-light-dark">{{ $service->category->name ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-gray-800">{{ $service->name }}</div>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-success">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit }}</span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <span class="text-warning fw-bold"><i
                                                            class="ki-outline ki-cross-circle fs-5 text-warning"></i>
                                                         Non-Aktif</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">Semua paket layanan aktif dan tersedia di kasir! 🎉</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var chartData = @json($chartData);
            var element = document.getElementById('kt_sales_chart');

            if (!element) return;

            var options = {
                series: [{
                    name: 'Omzet Laundry Aktual',
                    type: 'column',
                    data: chartData.sales
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                dataLabels: {
                    enabled: false
                },
                labels: chartData.categories,
                xaxis: {
                    type: 'category',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#a1a5b7',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return "Rp " + (value || 0).toLocaleString('id-ID');
                        },
                        style: {
                            colors: '#a1a5b7',
                            fontSize: '12px'
                        }
                    }
                },
                colors: ['#009ef7'],
                fill: {
                    opacity: 0.85
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                grid: {
                    borderColor: '#eff2f5',
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        });
    </script>
@endpush
