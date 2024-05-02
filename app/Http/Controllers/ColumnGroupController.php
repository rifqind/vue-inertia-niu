<?php

namespace App\Http\Controllers;

use App\Models\ColumnGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColumnGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $number = 1;
        $columnGroups = ColumnGroup::orderBy('label')->get();
        foreach ($columnGroups as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        return Inertia::render('Master/ColumnGroup', [
            'columnGroup' => $columnGroups
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function fetch(string $id)
    {
        $target = ColumnGroup::find($id);
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
            $updated = ColumnGroup::where('id', $request->id)->update(['label' => $request->label]);
            return redirect()->route('column_group.index')->with('message', 'Berhasil mengedit kelompok kolom');
        }
        $insertedRow = ColumnGroup::create($request->validate([
            'label' => 'required',
        ]));
        return redirect()->route('column_group.index')->with('message', 'Berhasil menambah kelompok kolom baru');
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
        $column_gorup = ColumnGroup::findOrFail($request->id);

        // Delete the subject
        $column_gorup->delete();

        // Respond with a JSON success message
        return redirect()->route('column_group.index')->with('message', 'Berhasil menghapus kelompok kolom tersebut');
    }
}
