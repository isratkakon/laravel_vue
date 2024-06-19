<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Skill;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
   
    

    public function Index(){
        $projects = Project::with('skill')->get();
        return Inertia::render('project/Index', compact('projects'));
    }

    // public function Index(){
    //     return Inertia::render('project/Index');
    // }


    //create project
     
    public function ProjectCreate(){
        $skills = Skill::all();
        return Inertia::render('project/Create', compact('skills'));
    }



    //store project

    public function ProjectStore(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image'],
            'name' => ['required', 'min:3'],
            'skill_id' => ['required', 'min:1'],
            'project_url' => ['required', 'min:3'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);        
        }

        if ($request->hasFile('image')) {
            $project_image = $request->file('image');
            $uniqueName = time(). '-' . Str::random(10). '.' . $project_image->getClientOriginalExtension();
            $project_image->move('project_image', $uniqueName);

            Project::create([
                'name'=> $request->name,
                'skill_id'=> $request->skill_id,
                'project_url'=> $request->project_url,
                'image'=> 'project_images/' . $uniqueName,
            ]);
            return Redirect::route('project.index')->with('message', 'Project created successfully.');
        }

        return Redirect::back();
    }



    //edit project


    public function ProjectEdit(Project $project){
        $skills = Skill::all();
        return Inertia::render('project/Edit', compact('skills', 'project'));

    }
}
