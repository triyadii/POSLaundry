<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class ServiceController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('backend.master.services.index', compact('categories'));
    }

    public function getDataServices(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::with('category')->orderBy('created_at', 'desc')->select('services.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<span class="fw-bold text-gray-800">' . e($row->name) . '</span>';
                })
                ->addColumn('category', function ($row) {
                    return '<span class="badge badge-light-info fs-7">' . e($row->category->name) . '</span>';
                })
                ->addColumn('price', function ($row) {
                    return '<span class="fw-semibold text-gray-700">Rp ' . number_format($row->price_per_unit, 0, ',', '.') . ' / ' . e($row->unit) . '</span>';
                })
                ->addColumn('duration', function ($row) {
                    return '<span class="text-muted">' . e($row->estimated_duration_hours) . ' Jam</span>';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->is_active ? 'Aktif' : 'Non-Aktif';
                    $badge = $row->is_active ? 'badge-light-success' : 'badge-light-danger';
                    return '<span class="badge ' . $badge . ' fs-7">' . $status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown text-end">
                                <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions <i class="ki-outline ki-down fs-5 ms-1"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark fs-6">
                                    <li><a class="dropdown-item btn px-3 btn-detail" href="javascript:void(0)" data-id="' . $row->id . '">Detail</a></li>
                                    <li><a class="dropdown-item btn px-3 btn-edit" href="javascript:void(0)" data-id="' . $row->id . '">Edit</a></li>
                                    <li><a class="dropdown-item btn px-3 btn-delete" href="javascript:void(0)" data-id="' . $row->id . '" data-name="' . e($row->name) . '">Hapus</a></li>
                                </ul>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['name', 'category', 'price', 'duration', 'status', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|in:kg,pcs,meter,pasang',
            'price_per_unit' => 'required|numeric|min:0',
            'estimated_duration_hours' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama layanan wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'price_per_unit.required' => 'Harga per unit wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $service = Service::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit' => $request->unit,
                'price_per_unit' => $request->price_per_unit,
                'estimated_duration_hours' => $request->estimated_duration_hours,
                'is_active' => $request->is_active,
            ]);

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('tambah layanan')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip'    => $request->ip(),
                    'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()],
                    'new'   => $service->toArray(),
                ])->log('Menambah layanan laundry baru: ' . $service->name);

            \DB::commit();
            return response()->json(['success' => 'Layanan berhasil ditambahkan.', 'judul' => 'Berhasil'], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage(), 'judul' => 'Gagal'], 500);
        }
    }

    public function show($id)
    {
        $service = Service::with('category')->findOrFail($id);
        $html = view('backend.master.services.show', compact('service'))->render();
        return response()->json(['html' => $html]);
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $categories = Category::orderBy('name', 'asc')->get();
        $html = view('backend.master.services.edit', compact('service', 'categories'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|in:kg,pcs,meter,pasang',
            'price_per_unit' => 'required|numeric|min:0',
            'estimated_duration_hours' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $service = Service::findOrFail($id);
            $oldData = $service->toArray();

            $service->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'unit' => $request->unit,
                'price_per_unit' => $request->price_per_unit,
                'estimated_duration_hours' => $request->estimated_duration_hours,
                'is_active' => $request->is_active,
            ]);

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('edit layanan')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip'  => $request->ip(),
                    'old' => $oldData,
                    'new' => $service->toArray(),
                ])->log('Mengubah layanan laundry: ' . $service->name);

            \DB::commit();
            return response()->json(['success' => 'Layanan berhasil diperbarui.', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan sistem.', 'judul' => 'Gagal'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $service = Service::findOrFail($id);
            $oldData = $service->toArray();

            $service->delete();

            activity()->useLog('hapus layanan')->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip(), 'old' => $oldData])
                ->log('Menghapus layanan laundry: ' . $oldData['name']);

            \DB::commit();
            return response()->json(['success' => 'Layanan berhasil dihapus.', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Layanan gagal dihapus karena sedang digunakan dalam transaksi.', 'judul' => 'Gagal']);
        }
    }
}
