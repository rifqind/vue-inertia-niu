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
    public function index()
    {
        $number = 1;
        $RowGroups = RowGroup::orderBy('label')->get();
        foreach ($RowGroups as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        return Inertia::render('Master/RowGroup', [
            'RowGroup' => $RowGroups
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
        $request->validate([
            'label' => 'required',
        ]);
        if ($request->id) {
            $updated = RowGroup::where('id', $request->id)->update(['label' => $request->label]);
            return redirect()->route('row_group.index')->with('message', 'Berhasil mengedit kelompok kolom');
        }
        $insertedRow = RowGroup::create($request->validate([
            'label' => 'required',
        ]));
        return redirect()->route('row_group.index')->with('message', 'Berhasil menambah kelompok kolom baru');
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

        // Delete the subject
        $column_gorup->delete();

        // Respond with a JSON success message
        return redirect()->route('row_group.index')->with('message', 'Berhasil menghapus kelompok kolom tersebut');
    }
}
