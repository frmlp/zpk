<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointTag;
use Illuminate\Http\Request;

class PointTagController extends Controller
{
    private function validatePointTagData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Point $point)
    {
        $validatedData = $this->validatePointTagData($request);

        // Tworzenie nowego tagu i przypisanie go do punktu
        $point->tags()->create($validatedData);

        return redirect()->route('admin.zpk')->with('success', 'Dodano nowy tag do punktu');
    }

    /**
     * Display the specified resource.
     */
    public function show(PointTag $pointTag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Point $point, PointTag $tag)
    {
        $validatedData = $this->validatePointTagData($request);
        $tag->update($validatedData);

        return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano tag');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Point $point, PointTag $tag)
    {
        $point->tags()->detach($tag);

        return redirect()->route("admin.zpk")->with('success', 'Usunięto tag z punktu');
    }
    
}
