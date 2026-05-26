<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th class="w-30 fw-bold">Nama Layanan</th>
                <td><span class="fw-bold text-gray-800">{{ $service->name }}</span></td>
            </tr>
            <tr>
                <th class="fw-bold">Kategori</th>
                <td><span class="badge badge-light-info fs-7">{{ $service->category->name }}</span></td>
            </tr>
            <tr>
                <th class="fw-bold">Satuan Unit</th>
                <td><span class="badge badge-light-secondary fs-7 text-uppercase">{{ $service->unit }}</span></td>
            </tr>
            <tr>
                <th class="fw-bold">Harga per Unit</th>
                <td><span class="fw-bold text-success">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }}</span></td>
            </tr>
            <tr>
                <th class="fw-bold">Estimasi Durasi</th>
                <td><span class="text-muted">{{ $service->estimated_duration_hours }} Jam ({{ round($service->estimated_duration_hours / 24, 1) }} Hari)</span></td>
            </tr>
            <tr>
                <th class="fw-bold">Status</th>
                <td>
                    @if($service->is_active)
                        <span class="badge badge-light-success fw-bold fs-7">AKTIF (Tampil di POS Kasir)</span>
                    @else
                        <span class="badge badge-light-danger fw-bold fs-7">NON-AKTIF (Diarsipkan)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="fw-bold">Dibuat Pada</th>
                <td>{{ $service->created_at->translatedFormat('d F Y H:i') }} WIB</td>
            </tr>
        </tbody>
    </table>
</div>
