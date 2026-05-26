<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th class="w-30 fw-bold">Nama Lengkap</th>
                <td>{{ $customer->name }}</td>
            </tr>
            <tr>
                <th class="fw-bold">Nomor HP / WA</th>
                <td>
                    <span class="text-primary">{{ $customer->phone }}</span>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="btn btn-icon btn-sm btn-light-success ms-2" title="Hubungi via WhatsApp">
                        <i class="lab la-whatsapp fs-3"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <th class="fw-bold">Status Member</th>
                <td>
                    @if($customer->member_status == 'VIP')
                        <span class="badge badge-light-danger fw-bold fs-7">VIP (DISKON 10%)</span>
                    @else
                        <span class="badge badge-light-primary fw-bold fs-7">REGULAR</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="fw-bold">Poin Loyalitas</th>
                <td>
                    <span class="fw-bold text-success">{{ $customer->loyalty_points }} Pts</span>
                </td>
            </tr>
            <tr>
                <th class="fw-bold">Alamat Rumah</th>
                <td>{{ $customer->address ?? '-' }}</td>
            </tr>
            <tr>
                <th class="fw-bold">Koordinat GPS</th>
                <td>
                    @if($customer->latitude && $customer->longitude)
                        <span class="badge badge-light-secondary fs-7">Lat: {{ $customer->latitude }}</span>
                        <span class="badge badge-light-secondary fs-7">Lng: {{ $customer->longitude }}</span>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $customer->latitude }},{{ $customer->longitude }}" target="_blank" class="btn btn-icon btn-sm btn-light-primary ms-2" title="Buka di Google Maps">
                            <i class="ki-outline ki-geolocation fs-3"></i>
                        </a>
                    @else
                        <span class="text-muted">Tidak diset</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="fw-bold">Terdaftar Pada</th>
                <td>{{ $customer->created_at->translatedFormat('d F Y H:i') }} WIB</td>
            </tr>
        </tbody>
    </table>
</div>
