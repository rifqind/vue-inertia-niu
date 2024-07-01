<?php

namespace App\Http\Controllers;

use App\Models\Row;
use App\Models\RowGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        $query = Row::query();

        $number = 1;
        $dataToCounted = $query->join('row_groups as rg', 'rg.id', '=', 'rows.id_row_groups')
            ->select([
                'rows.*', 'rg.label as rowGroupsLabel'
            ]);
        if ($request->orderAttribute) {
            $order = $request->orderAttribute;
            if (sizeof($order) > 2) $query->orderBy($order['label'], $order['value']);
        }
        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['label'])) $query->where('rows.label', 'like', '%' . $filter['label'] . '%');
            if (!empty($filter['rowGroupsLabel'])) $query->where('rg.label', 'like', '%' . $filter['rowGroupsLabel'] . '%');
        }

        $countData = $dataToCounted->count();
        $rows = $query->paginate($paginated, ['*'], 'page', $currentPage);

        foreach ($rows as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        $row_groups = RowGroup::orderBy('label')->get(['row_groups.label as label', 'row_groups.id as value']);
        if ($request->paginated) {
            return response()->json([
                'rows' => $rows,
                'countData' => $countData
            ]);
        }
        return Inertia::render('Master/Row', [
            'rows' => $rows,
            'row_groups' => $row_groups,
            'countData' => $countData
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'label' => ['required', 'array'],
            'label.*' => ['required', 'string'],
            'id_row_groups' => 'required',
        ]);
        if ($request->id) {
            $updated = Row::where('id', $request->id)->update([
                'label' => $validatedData['label'][0],
                'id_row_groups' => $validatedData['id_row_groups'],
            ]);
            return redirect()->route('rows.index')->with('message', 'Berhasil mengedit baris');
        }
        // $inserted = Row::create($validatedData);
        foreach ($validatedData['label'] as $key => $value) {
            # code...
            $insertedRow = Row::create([
                'label' => $value,
                'id_row_groups' => $validatedData['id_row_groups']
            ]);
        }
        return redirect()->route('rows.index')->with('message', 'Berhasil menambah baris baru');
    }

    public function fetchForUpdate(string $id)
    {
        $target = Row::find($id);
        return response()->json([
            'data' => $target
        ]);
    }
    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        // $decryptedId = Crypt::decrypt($id);
        // Find the subject by ID
        $rows = Row::findOrFail($request->id);
        try {
            //code...
            $rows->delete();
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('rows.index')->with('error', 'Gagal menghapus baris, Baris sudah digunakan untuk suatu data');
        }
        // Delete the subject

        // Respond with a JSON success message
        return redirect()->route('rows.index')->with('message', 'Berhasil menghapus baris');
    }

    public function fetchForCreate(string $id)
    {
        $target = Row::where('id_row_groups', $id)
            ->leftJoin('row_groups as rg', 'rg.id', '=', 'rows.id_row_groups')
            ->get(['rows.id', 'rows.label as label', 'rg.label as tipe']);
        return response()->json([
            'data' => $target,
        ]);
    }
}
