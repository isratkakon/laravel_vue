<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Skill;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    public function Index(){
        $skills = Skill::all();
        return Inertia::render('skill/Index', compact('skills'));
    }


    public function SkillCreate(){
        return Inertia::render('skill/Create');
    }



    public function SkillStore(Request $request){
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image'],
            'name' => ['required', 'min:3'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $uniqueName = time() . '-' . Str::random(10) . '.' . $image->
            getClientOriginalExtension();
            $image->move ('skill_images', $uniqueName);

            Skill::create([
                'name' => $request->name,
                'image' => 'skill_images/' .$uniqueName
            ]);

            return Redirect::route('skill.index')->with('success', 'Skill created successfully.');
        }

        return Redirect::back();
    }


    // skill edit

    public function SkillEdit( Skill $skill){
        
        return Inertia::render('skill/Edit', ['skill' => $skill, ]);  
    }

    // sill update 

    public function SkillUpdate(Request $request, Skill $skill){
        $request->validate([
            'name' => ['required', 'min:3'],
        ]);
    
        // Initialize image with the current skill's image
        $image = $skill->image;
    
        // Check if the request has a file and if it is valid
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($skill->image && file_exists(public_path($skill->image))) {
                unlink(public_path($skill->image));
            }
    
            // Store the new image
            $image = $request->file('image');
            $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('skill_images'), $uniqueName);
    
            // Update the image path
            $image = 'skill_images/' . $uniqueName;
        }
    
        // Update the skill
        $skill->update([
            'name' => $request->name,
            'image' => $image
        ]);
    
        // Redirect with success message
        return Redirect::route('skill.index');

    }

    // skill delete

    public function SkillDelete(Skill $skill){

            $image = $skill->image;
            if (File::exists($image)) {
                File::delete($image);
            }


            $skill->delete();

            return Redirect::route('skill.index');


    }

}
