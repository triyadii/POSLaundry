<input type="hidden" id="edit_service_id" value="{{ $service->id }}" />

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Nama Layanan / Paket</label>
    <input type="text" class="form-control form-control-solid" value="{{ $service->name }}" name="name" />
    <span class="text-danger error-text name_error_edit mt-2"></span>
</div>

<div class="row g-9 mb-7">
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Kategori Laundry</label>
        <select class="form-select form-select-solid" name="category_id">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $service->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <span class="text-danger error-text category_id_error_edit mt-2"></span>
    </div>
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Satuan Unit</label>
        <select class="form-select form-select-solid" name="unit">
            <option value="kg" {{ $service->unit == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
            <option value="pcs" {{ $service->unit == 'pcs' ? 'selected' : '' }}>Pcs (Lembar)</option>
            <option value="meter" {{ $service->unit == 'meter' ? 'selected' : '' }}>Meter (m2)</option>
            <option value="pasang" {{ $service->unit == 'pasang' ? 'selected' : '' }}>Pasang (Sepatu)</option>
        </select>
        <span class="text-danger error-text unit_error_edit mt-2"></span>
    </div>
</div>

<div class="row g-9 mb-7">
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Harga Per Unit (Rp)</label>
        <input type="number" class="form-control form-control-solid" value="{{ intval($service->price_per_unit) }}" name="price_per_unit" min="0" />
        <span class="text-danger error-text price_per_unit_error_edit mt-2"></span>
    </div>
    <div class="col-md-6 fv-row">
        <label class="required fs-6 fw-semibold mb-2">Estimasi Durasi (Jam)</label>
        <input type="number" class="form-control form-control-solid" value="{{ $service->estimated_duration_hours }}" name="estimated_duration_hours" min="1" />
        <span class="text-danger error-text estimated_duration_hours_error_edit mt-2"></span>
    </div>
</div>

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Status Layanan</label>
    <select class="form-select form-select-solid" name="is_active">
        <option value="1" {{ $service->is_active ? 'selected' : '' }}>Aktif (Tersedia di POS)</option>
        <option value="0" {{ !$service->is_active ? 'selected' : '' }}>Non-Aktif (Diarsipkan)</option>
    </select>
    <span class="text-danger error-text is_active_error_edit mt-2"></span>
</div>
