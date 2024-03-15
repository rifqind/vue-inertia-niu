<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Crypt;


use App\Models\Subject;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $number = 1;
        $subjects = Subject::orderBy('label')->get();
        foreach ($subjects as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        return Inertia::render('Master/Subject', [
            'subjects' => $subjects
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        // dd($request);
        if ($request->id) {
            $request->validate([
                'label' => 'required',
            ]);
            $insertedRow = Subject::where('id', $request->id)->update(['label' => $request->label]);
            return redirect()->route('subject.index')->with('message','Berhasil mengedit subjek');
        }
        $validatedData = $request->validate($request->rules());
        $insertedRow = Subject::create($validatedData);
        // return redirect(route('subject.index'))->with(['success' => 'Successfully inserted with id' . $insertedRow->id]);
        return redirect()->route('subject.index')->with('message','Berhasil menambahkan subjek baru');
    }

    public function fetch(string $id) {
        $subjects= Subject::where('id', $id)->first();
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
