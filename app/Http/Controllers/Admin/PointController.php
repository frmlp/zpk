<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PointController extends Controller
{
    // logika endpointu PUT|PATCH api/admin/points/{point}
    public function update(Request $request, Point $point)
    {
        error_log("PointController::update()");
        error_log($point);
        $point->update(
            $request->validate([
                'code' => 'required|string',
                'description' => 'required|string',
                'easting' => 'required|numeric',
                'northing' => 'required|numeric'
            ])
        );

        return redirect('/admin/zpk')->with('success', 'Zaktualizowano');
    }

    // logika endpointu POST api/admin/points
    public function store(Request $request)
    {
        // Gate::authorize('create', Point::class);

        $point = Point::create([
            ...$request->validate([
                'code' => 'required|string',
                'description' => 'required|string',
                'easting' => 'required|numeric',
                'northing' => 'required|numeric'
            ]),
        ]);

        return redirect('/admin/zpk')->with('success', 'Dodano');
    }

    // do zaimplementowania logika endpointu DELETE api/admin/points/{point}
    public function destroy(Request $request)
    {
        
    }
}
