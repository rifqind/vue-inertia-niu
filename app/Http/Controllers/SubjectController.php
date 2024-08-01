<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
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

        $number = 1;
        $query = Subject::query();
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
        $subjects = $query->paginate($paginated, ['*'], 'page', $currentPage);
        foreach ($subjects as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        if ($request->paginated) {
            return response()->json([
                'subjects' => $subjects,
                'countData' => $countData
            ]);
        }
        return Inertia::render('Master/Subject', [
            'subjects' => $subjects,
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
        // dd($request);
        if ($request->id) {
            $request->validate([
                'label' => 'required',
            ]);
            $insertedRow = Subject::where('id', $request->id)->update(['label' => $request->label]);
            return redirect()->route('subject.index')->with('message', 'Berhasil mengedit subjek');
        }
        $validatedData = $request->validate([
            'label' => 'required',
        ]);
        $insertedRow = Subject::create($validatedData);
        // return redirect(route('subject.index'))->with(['success' => 'Successfully inserted with id' . $insertedRow->id]);
        return redirect()->route('subject.index')->with('message', 'Berhasil menambahkan subjek baru');
    }

    public function fetch(string $id)
    {
        $subjects = Subject::where('id', $id)->first();
        return response()->json([
            'data' => $subjects
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
        $subject = Subject::findOrFail($request->id);
        // Delete the subject
        $subject->delete();
        // Respond with a JSON success message
        // return response()->json(['message' => 'Subject deleted successfully']);
        return redirect()->route('subject.index')->with('message', 'Berhasil menghapus subjek');
        //
    }
}
