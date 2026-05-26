<?php

namespace App\Http\Controllers\Backend\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $budget = null;
        $target = null;

        return view('backend.finance.expenses.index', compact('budget', 'target'));
    }

    // Fungsi khusus untuk mengatur Budget & Target Harian (Top Card)
    public function setBudget(Request $request)
    {
        return response()->json(['success' => 'Fitur dinonaktifkan.', 'judul' => 'Info']);
    }

    // === CRUD PENGELUARAN (EXPENSES) ===

    public function getDataExpenses(Request $request)
    {
        if ($request->ajax()) {
            // UBAH: expense_date jadi date
            $data = Expense::with('user')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->select('expenses.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($row) { // UBAH: expense_date jadi date
                    return '<span class="badge badge-light-primary fs-7">' . Carbon::parse($row->date)->translatedFormat('d M Y') . '</span>';
                })
                ->addColumn('title', function ($row) {
                    return '<span class="fw-bold text-gray-800">' . $row->category . '</span><br>' .
                        '<span class="text-muted fs-7">' . \Illuminate\Support\Str::limit($row->notes, 40) . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="fw-bold text-danger">Rp ' . number_format($row->amount, 0, ',', '.') . '</span>';
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? 'Sistem';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown text-end">
                                <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions <i class="ki-outline ki-down fs-5 ms-1"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark fs-6">
                                    <li><a class="dropdown-item btn px-3 btn-detail" href="javascript:void(0)" data-id="' . $row->id . '">Detail</a></li>
                                    <li><a class="dropdown-item btn px-3 btn-edit" href="javascript:void(0)" data-id="' . $row->id . '">Edit</a></li>
                                    <li><a class="dropdown-item btn px-3 btn-delete" href="javascript:void(0)" data-id="' . $row->id . '" data-name="' . $row->title . '">Hapus</a></li>
                                </ul>
                            </div>';
                })
                ->rawColumns(['date', 'title', 'amount', 'action']) // UBAH: expense_date jadi date
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'     => 'required|date',
            'category' => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'notes'    => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::create([
                'date'     => $request->date,
                'category' => $request->category,
                'amount'   => $request->amount,
                'notes'    => $request->notes,
                'user_id'  => Auth::id(),
            ]);

            activity()->useLog('tambah pengeluaran')->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip(), 'new' => $expense->toArray()])
                ->log('Mencatat pengeluaran: ' . $expense->category);

            DB::commit();
            return response()->json(['success' => 'Pengeluaran berhasil dicatat!', 'judul' => 'Berhasil'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menyimpan pengeluaran: ' . $e->getMessage(), 'judul' => 'Gagal'], 500);
        }
    }

    public function show($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        $html = view('backend.finance.expenses.show', compact('expense'))->render();
        return response()->json(['html' => $html]);
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $html = view('backend.finance.expenses.edit', compact('expense'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date'     => 'required|date',
            'category' => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'notes'    => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);
            $oldData = $expense->toArray();

            $expense->update([
                'date'     => $request->date,
                'category' => $request->category,
                'amount'   => $request->amount,
                'notes'    => $request->notes,
                'user_id'  => Auth::id(),
            ]);

            activity()->useLog('edit pengeluaran')->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip(), 'old' => $oldData, 'new' => $expense->fresh()->toArray()])
                ->log('Mengubah pengeluaran: ' . $expense->category);

            DB::commit();
            return response()->json(['success' => 'Pengeluaran berhasil diubah!', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage(), 'judul' => 'Gagal'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $expense = Expense::findOrFail($id);
            $oldData = $expense->toArray();

            $expense->delete();

            activity()->useLog('hapus pengeluaran')->causedBy(Auth::user())
                ->withProperties(['ip' => $request->ip(), 'old' => $oldData])
                ->log('Menghapus pengeluaran: ' . $oldData['category']);

            DB::commit();
            return response()->json(['success' => 'Pengeluaran berhasil dihapus.', 'judul' => 'Berhasil']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Gagal menghapus data.', 'judul' => 'Gagal'], 500);
        }
    }

    // === HISTORY BUDGET & TARGET ===
    public function getDataBudgets(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(collect())->make(true);
        }
    }
}
