<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jobs;
use App\Models\Offer;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserSkill;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends Controller
{
    public function profile(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->skills = UserSkill::where('user_id', $user->uuid)->get();;
            $user->education = UserEducation::where('user_id', $user->uuid)->get();
            $portfolio = Portfolio::where('user_id', $user->uuid)->first();
            if ($portfolio) {
                $portfolio->image = explode(',', $portfolio->image);

                $user->portfolio = $portfolio;
            }
            else{
                $obj = new stdClass();
                $user->portfolio = $obj;

            }
            $user->reviews = 4.0;
            return response()->json([
                'status' => true,
                'action' =>  'Profile',
                'data' => $user
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function applyJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => "required|exists:jobs,id",
            'description' => 'required',
            'time' => 'required',
            'budget' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $user = User::find($request->user()->uuid);
        $create = new Offer();
        $create->user_id = $user->uuid;
        $create->job_id = $request->job_id;
        $create->budget = $request->budget;
        $create->time = $request->time;
        $create->description = $request->description;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Offer Send',
        ]);
    }

    public function listJobs(Request $request, $status)
    {
        $user = User::find($request->user()->uuid);
        $jobIds  = Offer::where('user_id', $user->uuid)->where('status', $status)->orderBy('id', 'asc')->pluck('job_id');
        $jobs = Jobs::with(['user'])->whereIn('id', $jobIds)->latest()->paginate(12);
        return response()->json([
            'status' => true,
            'action' =>  'jobs',
            'data' => $jobs
        ]);
    }

    public function appliedJobDetail(Request $request, $job_id)
    {
        $obj = new stdClass();
        $job = Jobs::with(['user'])->where('id', $job_id)->first();
        $offer = Offer::where('user_id', $request->user()->uuid)->where('job_id', $job_id)->first();
        $obj->job = $job;
        $obj->offer = $offer;
        return response()->json([
            'status' => true,
            'action' =>  'Detail',
            'data' => $obj
        ]);
    }

    public function changeStatus(Request $request, $status, $offer_id)
    {


        $find = Offer::find($offer_id);
        if ($find) {
            $find->status = $status;
            if ($request->status == 1) {
                $find->accept_time =  strtotime(date('Y-m-d H:i:s'));
                $find->start_time =  strtotime(date('Y-m-d H:i:s'));
            }
            if ($request->status == 2) {
                $find->complete_time =  strtotime(date('Y-m-d H:i:s'));
            }
            $find->save();

            return response()->json([
                'status' => true,
                'action' =>  'Satus Change',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Offer not found',
        ]);
    }

    public function myJobs(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($type == 'my_task') {
            $jobs = Jobs::with(['user'])->where('user_id', $user->uuid)->latest()->paginate(12);
        }
        if ($type == 'assign_task') {
            $jobIds = Jobs::where('user_id', $user->uuid)->pluck('id');
            $ofersIds = Offer::whereIn('job_id', $jobIds)->where('status', 1)->pluck('job_id');
            $jobs = Jobs::whereIn('id', $ofersIds)->orderBy('id', 'desc')->paginate(12);
        }
        if ($type == 'complete_task') {
            $jobIds = Jobs::where('user_id', $user->uuid)->pluck('id');
            $ofersIds = Offer::whereIn('job_id', $jobIds)->where('status', 2)->pluck('job_id');
            $jobs = Jobs::whereIn('id', $ofersIds)->orderBy('id', 'desc')->paginate(12);
        }
        return response()->json([
            'status' => true,
            'action' =>  'Jobs',
            'data' => $jobs
        ]);
    }

    public function seeOffer(Request $request, $job_id)
    {
        $offers = Offer::with(['user'])->where('job_id', $job_id)->latest()->paginate(12);
        return response()->json([
            'status' => true,
            'action' =>  'Jobs Offers',
            'data' => $offers
        ]);
    }
}