<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Priority;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priorities = Priority::all();
        
        return response()->json([
            'data' => $priorities
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
            'name' => 'required|unique:priorities,name'
        ]);

        $priority = Priority::create($request->all());

        return response()->json([
            'message' => 'Priority successfully created',
            'data' => $priority
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $priority = Priority::where('id', $id)->first();
        if(!$priority) {
            return response()->json([
                'message' => 'Priority not found'
            ], 404);
        }

        return response()->json([
            'data' => $priority
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $priority = Priority::where('id', $id)->first();
        if(!$priority) {
            return response()->json([
                'message' => 'Priority not found'
            ], 404);
        }

        $priority->update($request->only('name'));

        return response()->json([
            'message' => 'Priority successfully updated',
            'data' => $priority
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $priority = Priority::where('id', $id)->first();
        if(!$priority) {
            return response()->json([
                'message' => 'Priority not found'
            ], 404);
        }

        $priority->delete();

        return response()->json([
            'message' => 'Priority successfully deleted'
        ], 200);
    }
}
