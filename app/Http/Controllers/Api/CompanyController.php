<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use App\Services\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = Company::where('name', '!=', 'Adma Digital Solusi')
            ->orderBy('created_at', 'DESC')
            ->when($request->q, function ($companies) {
                $companies = $companies->where('name', 'ILIKE', '%' . request()->q . '%');
            })->get();

        return response()->json([
            'data' => $companies
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

        $company = new Company([
            'name' => $request->name
        ]);
        $company->save();

        return response()->json([
            'message' => 'Company successfully created',
            'data' => $company
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::where('id', $id)->first();
        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        return response()->json([
            $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|min:3|unique:categories,name'
        ]);

        $company = Company::where('id', $id)->first();
        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $company->update($request->all());

        return response()->json([
            'message' => 'Company successfully updated',
            'data' => $company
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::where('id', $id)->first();
        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $company->delete();
        return response()->json([
            'message' => 'Company successfully deleted'
        ], 200);
    }
}
