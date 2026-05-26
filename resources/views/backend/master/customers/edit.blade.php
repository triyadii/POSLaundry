<input type="hidden" id="edit_customer_id" value="{{ $customer->id }}" />

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Nama Lengkap</label>
    <input type="text" class="form-control form-control-solid" value="{{ $customer->name }}" name="name" />
    <span class="text-danger error-text name_error_edit mt-2"></span>
</div>

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Nomor HP / WhatsApp</label>
    <input type="text" class="form-control form-control-solid" value="{{ $customer->phone }}" name="phone" />
    <span class="text-danger error-text phone_error_edit mt-2"></span>
</div>

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Status Member</label>
    <select class="form-select form-select-solid" name="member_status">
        <option value="regular" {{ $customer->member_status == 'regular' ? 'selected' : '' }}>Regular</option>
        <option value="VIP" {{ $customer->member_status == 'VIP' ? 'selected' : '' }}>VIP (Diskon Member 10%)</option>
    </select>
    <span class="text-danger error-text member_status_error_edit mt-2"></span>
</div>

<div class="d-flex flex-column mb-7 fv-row">
    <label class="required fs-6 fw-semibold mb-2">Poin Loyalitas</label>
    <input type="number" class="form-control form-control-solid" value="{{ $customer->loyalty_points }}" name="loyalty_points" min="0" />
    <span class="text-danger error-text loyalty_points_error_edit mt-2"></span>
</div>

<div class="d-flex flex-column mb-7 fv-row">
    <label class="fs-6 fw-semibold mb-2">Alamat Pengantaran (Lengkap)</label>
    <textarea class="form-control form-control-solid" rows="3" name="address">{{ $customer->address }}</textarea>
    <span class="text-danger error-text address_error_edit mt-2"></span>
</div>

<div class="row g-9 mb-7">
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">Latitude (GPS)</label>
        <input type="text" class="form-control form-control-solid" value="{{ $customer->latitude }}" name="latitude" />
    </div>
    <div class="col-md-6 fv-row">
        <label class="fs-6 fw-semibold mb-2">Longitude (GPS)</label>
        <input type="text" class="form-control form-control-solid" value="{{ $customer->longitude }}" name="longitude" />
    </div>
</div>
