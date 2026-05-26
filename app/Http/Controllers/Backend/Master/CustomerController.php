<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class CustomerController extends Controller
{
    public function index()
    {
        return view('backend.master.customers.index');
    }

    public function getDataCustomers(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::orderBy('created_at', 'desc')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<span class="fw-bold text-gray-800">' . e($row->name) . '</span>';
                })
                ->addColumn('phone', function ($row) {
                    return '<span class="text-muted">' . e($row->phone) . '</span>';
                })
                ->addColumn('member_status', function ($row) {
                    $badge = $row->member_status == 'VIP' ? 'badge-light-danger' : 'badge-light-primary';
                    return '<span class="badge ' . $badge . ' fs-7 fw-bold">' . e(strtoupper($row->member_status)) . '</span>';
                })
                ->addColumn('loyalty_points', function ($row) {
                    return '<span class="fw-semibold text-success">' . e($row->loyalty_points) . ' Pts</span>';
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
                ->rawColumns(['name', 'phone', 'member_status', 'loyalty_points', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'member_status' => 'required|in:regular,VIP',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'member_status' => $request->member_status,
                'loyalty_points' => 0,
            ]);

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('tambah pelanggan')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip'    => $request->ip(),
                    'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()],
                    'new'   => $customer->toArray(),
                ])->log('Menambah pelanggan baru: ' . $customer->name);

            \DB::commit();
            return response()->json(['success' => 'Pelanggan berhasil ditambahkan.', 'judul' => 'Berhasil'], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage(), 'judul' => 'Gagal'], 500);
        }
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $html = view('backend.master.customers.show', compact('customer'))->render();
        return response()->json(['html' => $html]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $html = view('backend.master.customers.edit', compact('customer'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'member_status' => 'required|in:regular,VIP',
            'loyalty_points' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            \DB::beginTransaction();

            $customer = Customer::findOrFail($id);
            $oldData = $customer->toArray();

            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'member_status' => $request->member_status,
                'loyalty_points' => $request->loyalty_points,
            ]);

            // === ACTIVITY LOG ===
            $agent = new Agent;
            activity()
                ->useLog('edit pelanggan')
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip'  => $request->ip(),
                    'old' => $oldData,
                    'new' => $customer->toArray(),
                ])->log('Mengubah data pelanggan: ' . $customer->name);

            \DB::commit();
            return response()->json(['success' => 'Data pelanggan berhasil diperbarui.', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan sistem.', 'judul' => 'Gagal'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $customer = Customer::findOrFail($id);
            $oldData = $customer->toArray();

            $customer->delete();

            activity()->useLog('hapus pelanggan')->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip(), 'old' => $oldData])
                ->log('Menghapus data pelanggan: ' . $oldData['name']);

            \DB::commit();
            return response()->json(['success' => 'Pelanggan berhasil dihapus.', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Pelanggan gagal dihapus karena memiliki riwayat transaksi.', 'judul' => 'Gagal']);
        }
    }
}
