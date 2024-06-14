<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Resources\PositionResource;
use App\Services\JsonResponse;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                
        $positions = Position::orderBy('id', 'DESC')
            ->when($request->q, function($positions) {
                $positions = $positions->where('name', 'ILIKE', '%' . request()->q . '%')                        
                    ->orWhere('id', 'ILIKE', '%' . request()->q . '%')                    
                    ->orWhere('created_at', 'ILIKE', '%' . request()->q . '%');
            })->get();

        return response()->json([
            'data' => $positions
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|unique:categories,name'
        ]);

        $position = new Position([
            'name' => $request->name
        ]);
        $position->save();

        return response()->json([
            'message' => 'Position successfully created',
            'data' => $position
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $position = Position::where('id', $id)->first();
        if(!$position) {
            return response()->json([
                'message' => 'position not found'
            ], 404);
        }

        return response()->json([
            $position
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|min:3|unique:categories,name'
        ]);
        
        $position = Position::where('id', $id)->first();
        if(!$position) {
            return response()->json([
                'message' => 'position not found'
            ], 404);
        }

        $position->update($request->all());

        return response()->json([
            'message' => 'position successfully updated',
            'data' => $position
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $position = Position::where('id', $id)->first();
        if(!$position) {
            return response()->json([
                'message' => 'Position not found'
            ], 404);
        }

        $position->delete();
        return response()->json([
            'message' => 'Position successfully deleted'
        ], 200);
    }
}
