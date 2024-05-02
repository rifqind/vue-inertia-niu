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
    public function index()
    {
        //
        $rows = Row::leftJoin('row_groups as rg', 'rg.id', '=', 'rows.id_row_groups')
            ->get(['rows.*', 'rg.label as rowGroupsLabel']);

        foreach ($rows as $key => $value) {
            # code...
            $value->number = $key + 1;
        }
        $row_groups = RowGroup::orderBy('label')->get(['row_groups.label as label', 'row_groups.id as value']);
        return Inertia::render('Master/Row', [
            'rows' => $rows,
            'row_groups' => $row_groups
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
            'label' => ['required', 'string'],
            'id_row_groups' => 'required',
        ]);
        if ($request->id) {
            $updated = Row::where('id', $request->id)->update($validatedData);
            return redirect()->route('rows.index')->with('message', 'Berhasil mengedit baris');
        }
        $inserted = Row::create($validatedData);
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

        // Delete the subject
        $rows->delete();

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
