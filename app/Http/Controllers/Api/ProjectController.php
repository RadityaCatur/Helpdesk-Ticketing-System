<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use App\Models\Company;
use Illuminate\Support\Facades\Facade as FacadesAuth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('pic')) {
            $projects = Project::where('company_id', auth()->user()->company_id)
                ->orderBy('status', 'DESC')
                ->orderBy('id', 'DESC')
                ->when($request->q, function ($projects) {
                    $projects = $projects->where('name', 'ILIKE', '%' . request()->q . '%')
                        ->orWhere('id', 'ILIKE', '%' . request()->q . '%');
                })->get();

            foreach ($projects as $project) {
                $project->category;
            }

            return response()->json([
                'data' => $projects
            ], 200);
        } else {
            $projects = Project::orderBy('status', 'DESC')
                ->orderBy('id', 'DESC')
                ->when($request->q, function ($projects) {
                    $projects = $projects->where('name', 'ILIKE', '%' . request()->q . '%')
                        ->orWhere('id', 'ILIKE', '%' . request()->q . '%')
                        ->orWhere('status', 'ILIKE', '%' . request()->q . '%')
                        ->orWhere('created_at', 'ILIKE', '%' . request()->q . '%');
                })->get();

            return response()->json([
                'data' => $projects
            ]);
        }
    }

    public function getAll()
    {
        $projects = auth()->user()->projects;

        foreach ($projects as $project) {
            $project->category;
        }

        return response()->json([
            'data' => $projects
        ]);
    }

    /**
     * Get all admin
     */
    public function admin(Request $request)
    {
        return response()->json([
            'data' => User::role('admin')->get()
        ]);
    }

    /**
     * Get all users
     */
    public function users()
    {
        return response()->json([
            'data' => User::role('pic')->get()
        ]);
    }

    /**
     * Get all client
     */
    public function client()
    {
        return response()->json([
            'data' => Company::all()
        ]);
    }

    /**
     * Get all employee
     */
    public function employees()
    {
        return response()->json([
            'data' => User::role('employee')->get()
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
            'category_id' => 'required',
            'leader_id' => 'required',
            'name' => 'string|required',
            'contract' => 'required',
            'contract_started_at' => 'required',
            'contract_ended_at' => 'required',
            'service_level_agreement' => 'nullable',
            'jumlah_hari' => 'nullable',
            'logo' => 'nullable|mimes:jpeg,jpg,png,gif|max:10000'
        ]);

        $user = User::where('company_id', '=', $request->company_id)->first();
        if ($user != null) {
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                // $logo->store('uploads','public');
                $filename = time() . '-' . preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logo->storeAs('uploads', $filename, 'public');
                $project = new Project([
                    'user_id' => $user->id,
                    'category_id' => $request->category_id,
                    'priority_id' => $request->priority_id,
                    'leader_id' => $request->leader_id,
                    'company_id' => $request->company_id,
                    'name' => $request->name,
                    'contract' => $request->contract,
                    'contract_started_at' => $request->contract_started_at,
                    'contract_ended_at' => $request->contract_ended_at,
                    'service_level_agreement' => $request->service_level_agreement,
                    'jumlah_hari' => $request->jumlah_hari,
                    'logo' => $filename
                ]);
            } else {
                $project = new Project([
                    'user_id' => $user->id,
                    'category_id' => $request->category_id,
                    'priority_id' => $request->priority_id,
                    'leader_id' => $request->leader_id,
                    'company_id' => $request->company_id,
                    'name' => $request->name,
                    'contract' => $request->contract,
                    'contract_started_at' => $request->contract_started_at,
                    'contract_ended_at' => $request->contract_ended_at,
                    'service_level_agreement' => $request->service_level_agreement,
                    'jumlah_hari' => $request->jumlah_hari,
                    'logo' => null
                ]);
            }

            $project->save();

            return response()->json([
                'status'  => true,
                'message' => 'Project successfully created',
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pic clint perlu ditambahkan terlebih dahulu',
                'data' => []
            ], 404);
        }


        // $programmers = explode(',', $request->programmers);
        // $project->programmers()->sync($programmers);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::with(['user'])->where('id', $id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }
        $project->company;
        $project->user;
        $project->leader;
        $project->category;
        $project->user_company;
        // $project->programmers;
        if ($project->user) {
            $project->user->company;
        } else {
            $project->user = null;
        }

        return response()->json([
            'data' => (new ProjectResource($project))
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
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }

        $user = User::where('company_id', $request->company_id)->first();

        if ($user != null) {
            # code...
            $project->update([
                'user_id' =>  $user->id ?? $project->user_id,
                'category_id' => $request->category_id ?? $project->category_id,
                'priority_id' => $request->priority_id ?? $project->priority_id,
                'leader_id' => $request->leader_id ?? $project->leader_id,
                'company_id' => $request->company_id ?? $project->company_id,
                'name' => $request->name ?? $project->name,
                'status' => $request->status ?? $project->status,
            ]);

            // $programmers = explode(',', $request->programmers);
            // $project->programmers()->sync($programmers);

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $filename = time() . '-' . preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logo->storeAs('uploads', $filename, 'public');

                $project->update([
                    'logo' => $filename
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Project successfully updated',
                'data' => $project
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pic client belum ditambahkan',
                'data' => []
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project successfully deleted'
        ], 200);
    }

    // protected function syncProgrammer(Project $project, array $users)
    // {        
    //     $project->programmers()->sync($users);
    // }
}
