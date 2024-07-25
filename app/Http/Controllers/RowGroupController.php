<?php

namespace App\Http\Controllers;

use App\Models\RowGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RowGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        $query = RowGroup::query();

        $number = 1;
        $dataToCounted = $query;

        if ($request->orderAttribute) {
            $order = $request->orderAttribute;
            if (sizeof($order) > 2) $query->orderBy($order['label'], $order['value']);
            else $query->orderBy('label');
        } else $query->orderBy('label');

        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['label'])) $query->where('label', 'like', '%' . $filter['label'] . '%');
        }
        $countData = $dataToCounted->count();
        $RowGroups = $query->paginate($paginated, ['*'], 'page', $currentPage);
        foreach ($RowGroups as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        if ($request->paginated) {
            return response()->json([
                'RowGroup' => $RowGroups,
                'countData' => $countData
            ]);
        }
        return Inertia::render('Master/RowGroup', [
            'RowGroup' => $RowGroups,
            'countData' => $countData
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function fetch(string $id)
    {
        $target = RowGroup::find($id);
        return response()->json([
            'data' => $target
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->fileUpload) {
            $fileData = $request->fileUpload;
            // dd($fileData);
            if ($fileData[0][0] != 'label') {
                return redirect()->route('row_group.index')->with('message', 'Gagal Upload, tidak sesuai template');
            };
            foreach ($fileData as $key => $value) {
                # code...
                if ($key > 0) {
                    // Check if the value is empty
                    if (empty($value[0])) {
                        return redirect()->route('row_group.index')->with('error', 'Gagal Upload, kolom label tidak boleh kosong');
                    }

                    // Check for uniqueness in a case-insensitive manner
                    $exists = RowGroup::whereRaw('LOWER(label) = ?', [strtolower($value[0])])->exists();
                    if ($exists) {
                        return redirect()->route('row_group.index')->with('error', 'Gagal Upload, label ' . $value[0] . ' sudah ada');
                    }

                    $insertedRow = RowGroup::create([
                        'label' => $value[0]
                    ]);
                }
            }
            return redirect()->route('row_group.index')->with('message', 'Berhasil menambah kelompok baris baru');
        } else {
            $request->validate([
                'label' => ['required'],
            ]);
            $exists = RowGroup::whereRaw('LOWER(label) = ?', [strtolower($request->label)])->exists();
            if ($exists) {
                return redirect()->route('row_group.index')->with('error', 'Gagal Upload, label ' . $request->label . ' sudah ada');
            }
            if ($request->id) {
                $updated = RowGroup::where('id', $request->id)->update(['label' => $request->label]);
                return redirect()->route('row_group.index')->with('message', 'Berhasil mengedit kelompok kolom');
            }
            $insertedRow = RowGroup::create($request->validate([
                'label' => 'required',
            ]));
            return redirect()->route('row_group.index')->with('message', 'Berhasil menambah kelompok kolom baru');
        }
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // Find the subject by ID
        $column_gorup = RowGroup::findOrFail($request->id);
        try {
            //code...
            $column_gorup->delete();
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('row_group.index')->with('error', 'Gagal menghapus kelompok baris tersebut, Periksa apakah masih ada baris di bawah kelompok baris ini');
        }
        // Delete the subject

        // Respond with a JSON success message
        return redirect()->route('row_group.index')->with('message', 'Berhasil menghapus kelompok baris tersebut');
    }
}
