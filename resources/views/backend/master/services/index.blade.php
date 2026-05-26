@extends('backend.layout.app')
@section('title', 'Master Layanan Laundry')
@section('content')
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-0">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Layanan Laundry</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-gray-700 fw-bold lh-1"><a href="{{ route('dashboard') }}"
                            class="text-white text-hover-primary"><i class="ki-outline ki-home text-gray-700 fs-6"></i></a>
                    </li>
                    <li class="breadcrumb-item"><i class="ki-outline ki-right fs-5 text-gray-700 mx-n1"></i></li>
                    <li class="breadcrumb-item text-gray-700 fw-bold lh-1">Master Data</li>
                    <li class="breadcrumb-item"><i class="ki-outline ki-right fs-5 text-gray-700 mx-n1"></i></li>
                    <li class="breadcrumb-item text-gray-900">Layanan Laundry</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card card-flush">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                            <input type="text" id="search" class="form-control w-250px ps-12"
                                placeholder="Cari layanan..." />
                        </div>
                    </div>
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <button type="button" class="btn btn-sm btn-primary" id="btn_tambah_data">
                            <i class="ki-outline ki-plus fs-2"></i> Tambah Layanan
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5 table-services" id="table-services">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">No</th>
                                <th class="min-w-150px">Nama Layanan</th>
                                <th class="min-w-100px">Kategori</th>
                                <th class="min-w-120px">Harga Per Unit</th>
                                <th class="min-w-100px">Estimasi Durasi</th>
                                <th class="min-w-80px">Status</th>
                                <th class="text-end min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Modal_Tambah_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Tambah Layanan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i
                            class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="FormTambahModalID" class="form">
                        @csrf
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Nama Layanan / Paket</label>
                            <input type="text" class="form-control form-control-solid" placeholder="Contoh: Cuci Kiloan Setrika Express 6 Jam" name="name" />
                            <span class="text-danger error-text name_error_add mt-2"></span>
                        </div>
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Kategori Laundry</label>
                                <select class="form-select form-select-solid" name="category_id">
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text category_id_error_add mt-2"></span>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Satuan Unit</label>
                                <select class="form-select form-select-solid" name="unit">
                                    <option value="kg" selected>Kilogram (kg)</option>
                                    <option value="pcs">Pcs (Lembar)</option>
                                    <option value="meter">Meter (m2)</option>
                                    <option value="pasang">Pasang (Sepatu)</option>
                                </select>
                                <span class="text-danger error-text unit_error_add mt-2"></span>
                            </div>
                        </div>
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Harga Per Unit (Rp)</label>
                                <input type="number" class="form-control form-control-solid" placeholder="Contoh: 15000" name="price_per_unit" min="0" />
                                <span class="text-danger error-text price_per_unit_error_add mt-2"></span>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Estimasi Durasi (Jam)</label>
                                <input type="number" class="form-control form-control-solid" placeholder="Contoh: 6" name="estimated_duration_hours" min="1" value="48" />
                                <span class="text-danger error-text estimated_duration_hours_error_add mt-2"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Status Layanan</label>
                            <select class="form-select form-select-solid" name="is_active">
                                <option value="1" selected>Aktif (Tersedia di POS)</option>
                                <option value="0">Non-Aktif (Diarsipkan)</option>
                            </select>
                            <span class="text-danger error-text is_active_error_add mt-2"></span>
                        </div>
                        <div class="text-center pt-15">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btn-add-data">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress" style="display: none;">Harap tunggu... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Modal_Edit_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content" id="edit-modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Edit Layanan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal"><i
                            class="ki-outline ki-cross fs-1"></i></div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="FormEditModalID" class="form">
                        @csrf @method('PUT')
                        <div id="EditRowModalBody"></div>
                        <div class="text-center pt-15">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btn-edit-data">
                                <span class="indicator-label">Simpan Perubahan</span>
                                <span class="indicator-progress" style="display: none;">Harap tunggu... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Modal_Detail_Data" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Detail Layanan</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7" id="DetailRowModalBody">
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('stylesheets')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ URL::to('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Init DataTable
                var table = $('.table-services').DataTable({
                    processing: true,
                    serverSide: true,
                    order: false,
                    ajax: "{{ route('get-dataservices') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'category',
                            name: 'category.name'
                        },
                        {
                            data: 'price',
                            name: 'price_per_unit'
                        },
                        {
                            data: 'duration',
                            name: 'estimated_duration_hours'
                        },
                        {
                            data: 'status',
                            name: 'is_active'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-end'
                        }
                    ]
                });

                // Search dengan Debounce
                let timeout;
                $('#search').on('keyup', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        table.search($(this).val()).draw();
                    }, 500);
                });

                // Form Tambah Submit
                $('#btn_tambah_data').click(function() {
                    $('#FormTambahModalID')[0].reset();
                    $('.error-text').text('');
                    $('#Modal_Tambah_Data').modal('show');
                });

                $('#FormTambahModalID').on('submit', function(e) {
                    e.preventDefault();
                    $('#btn-add-data .indicator-label').hide();
                    $('#btn-add-data .indicator-progress').show();
                    $('#btn-add-data').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('services.store') }}",
                        method: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            if (res.errors) {
                                $.each(res.errors, function(prefix, val) {
                                    $('span.' + prefix + '_error_add').text(val[0]);
                                });
                            } else {
                                $('#Modal_Tambah_Data').modal('hide');
                                table.ajax.reload();
                                Swal.fire("Berhasil!", res.success, "success");
                            }
                            resetButton('#btn-add-data');
                        },
                        error: function() {
                            Swal.fire("Error", "Gagal menyimpan data", "error");
                            resetButton('#btn-add-data');
                        }
                    });
                });

                // Tampil Modal Edit
                $('body').on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    $.get("{{ url('admin') }}/services/" + id + "/edit", function(res) {
                        $('#EditRowModalBody').html(res.html);
                        $('#Modal_Edit_Data').modal('show');
                    });
                });

                // Form Edit Submit
                $('#FormEditModalID').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#edit_service_id').val();
                    $('#btn-edit-data .indicator-label').hide();
                    $('#btn-edit-data .indicator-progress').show();
                    $('#btn-edit-data').prop('disabled', true);

                    $.ajax({
                        url: "{{ url('admin') }}/services/" + id,
                        method: 'POST',
                        data: new FormData(this),
                        contentType: false,
                        processData: false,
                        success: function(res) {
                            if (res.errors) {
                                $.each(res.errors, function(prefix, val) {
                                    $('span.' + prefix + '_error_edit').text(val[0]);
                                });
                            } else {
                                $('#Modal_Edit_Data').modal('hide');
                                table.ajax.reload();
                                Swal.fire("Berhasil!", res.success, "success");
                            }
                            resetButton('#btn-edit-data');
                        },
                        error: function() {
                            Swal.fire("Error", "Gagal mengupdate data", "error");
                            resetButton('#btn-edit-data');
                        }
                    });
                });

                // Hapus Data
                $('body').on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    let name = $(this).data('name');
                    Swal.fire({
                        title: "Hapus Layanan?",
                        text: "Layanan '" + name + "' akan dihapus permanen!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal",
                        customClass: {
                            confirmButton: "btn btn-danger",
                            cancelButton: "btn btn-secondary"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('admin') }}/services/" + id,
                                type: "DELETE",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(res) {
                                    if (res.success) {
                                        table.ajax.reload();
                                        Swal.fire("Terhapus!", res.success, "success");
                                    } else {
                                        Swal.fire("Gagal!", res.error, "error");
                                    }
                                }
                            });
                        }
                    });
                });

                // Tampil Modal Detail
                $('body').on('click', '.btn-detail', function() {
                    let id = $(this).data('id');
                    $('#DetailRowModalBody').html(
                        '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Memuat data...</div></div>'
                        );
                    $('#Modal_Detail_Data').modal('show');

                    $.get("{{ url('admin') }}/services/" + id, function(res) {
                        $('#DetailRowModalBody').html(res.html);
                    }).fail(function() {
                        $('#DetailRowModalBody').html(
                            '<div class="alert alert-danger">Gagal memuat data detail.</div>');
                    });
                });

                function resetButton(selector) {
                    $(selector + ' .indicator-label').show();
                    $(selector + ' .indicator-progress').hide();
                    $(selector).prop('disabled', false);
                }
            });
        </script>
    @endpush
@endsection
