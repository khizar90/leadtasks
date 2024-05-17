<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AddEducationRequest;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    public function addEducation(AddEducationRequest $request)
    {
        $create = new UserEducation();
        $create->user_id = $request->user()->uuid;
        $create->institute_name = $request->institute_name;
        $create->degree_name = $request->degree_name;
        $create->start_year = $request->start_year;
        $create->end_year = $request->end_year;
        $create->save();

        $newData =  UserEducation::find($create->id);
        return response()->json([
            'status' => true,
            'action' => "Education Added",
            'data' => $newData
        ]);
    }

    public function listEducation(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $find = UserEducation::where('user_id',$user->uuid)->latest()->get();
        return response()->json([
            'status' => true,
            'action' => "Education list",
            'data' => $find
        ]);
    }

    public function deleteEducation(Request $request, $id)
    {
        $find = UserEducation::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Education Deleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Education not Found",
        ]);
    }
    public function listSkill(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $skills = UserSkill::where('user_id', $user->uuid)->latest()->get();
        return response()->json([
            'status' => true,
            'action' => "Skill list",
            'data' => $skills
        ]);
    }

    public function addSkill(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new UserSkill();
        $create->user_id = $user->uuid;
        $create->name = $request->name;
        $create->save();

        $newData =  UserSkill::find($create->id);

        return response()->json([
            'status' => true,
            'action' => "Skill Added",
            'data' => $newData
        ]);
    }

    public function deleteSkill(Request $request, $id)
    {
        $find = UserSkill::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Skill Deleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Skill not Found",
        ]);
    }


    public function addPortfolio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media' => "required",
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $user = User::find($request->user()->uuid);
        if ($user) {
            $images = [];
            $create = new Portfolio();
            $create->user_id = $user->uuid;
            if ($request->hasFile('media')) {
                $files = $request->file('media');
                foreach ($files as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $mime = explode('/', $file->getClientMimeType());
                    $filename = time() . '-' . uniqid() . '.' . $extension;
                    if ($file->move('uploads/user/' . $user->uuid . '/portfolio/', $filename)) {
                        $path = '/uploads/user/' . $user->uuid . '/portfolio/' . $filename;
                        $images[] = $path;
                    }
                }
                $imageString = implode(',', $images); // Convert array to comma-separated string
                $create->image = $imageString;


                // $create->image = $images;
            }
            $create->save();


            return response()->json([
                'status' => true,
                'action' => "Portfolio Added",
            ]);
        }
    }

    public function deletePortfolio($id){
        $find = Portfolio::find($id);
        if($find){
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Portfolio Dleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Portfolio not found",
        ]);
    }
}
