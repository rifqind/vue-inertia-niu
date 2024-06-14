<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\ColumnGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get the resource
        $columns = Column::leftJoin('column_groups as cg', 'cg.id', '=', 'columns.id_column_groups')
            ->get([
                'columns.*',
                'cg.label as columnGroupsLabel'
            ]);
        $number = 1;
        foreach ($columns as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        $column_groups = ColumnGroup::orderBy('label')->get(['column_groups.label as label', 'column_groups.id as value']);
        return Inertia::render('Master/Column', [
            'columns' => $columns,
            'column_groups' => $column_groups
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
        $validatedData = $request->validate([
            'label' => ['required', 'array'],
            'label.*' => ['required', 'string'],
            'id_column_groups' => 'required',
        ]);
        // dd($validatedData['label']);
        if($request->id) {
            $updated = Column::where('id', $request->id)->update([
                'label' => $validatedData['label'][0],
                'id_column_groups' => $validatedData['id_column_groups'],
            ]);
            return redirect()->route('columns.index')->with('message', 'Berhasil mengedit kolom tersebut');
        }
        foreach ($validatedData['label'] as $key => $value) {
            # code...
            $insertedRow = Column::create([
                'label' => $value,
                'id_column_groups' => $validatedData['id_column_groups']
            ]);
        }
        return redirect()->route('columns.index')->with('message', 'Berhasil menambahkan kolom baru');
        //
    }

    public function fetchForUpdate(string $id)
    {
        $target = Column::find($id);
        return response()->json([
            'data' => $target
        ]);
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
        // $decryptedId = Crypt::decrypt($id);

        // Find the subject by ID
        $column = Column::findOrFail($request->id);
        try {
            //code...
            $column->delete();
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('columns.index')->with('error', 'Gagal menghapus kolom tersebut, Kolom sudah digunakan untuk suatu data');
        }
        // Delete the column

        // Respond with a JSON success message
        return redirect()->route('columns.index')->with('message', 'Berhasil menghapus kolom tersebut');
    }

    public function fetchForCreate(string $id)
    {
        $target = Column::where('id_column_groups', $id)
            ->leftJoin('column_groups as cg', 'cg.id', '=', 'columns.id_column_groups')
            ->get(['columns.id', 'columns.label as label', 'cg.label as tipe']);
        return response()->json([
            'data' => $target,
        ]);
    }
}