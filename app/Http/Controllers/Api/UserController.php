<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReportRequest;
use App\Models\Category;
use App\Models\Jobs;
use App\Models\Offer;
use App\Models\Portfolio;
use App\Models\Report;
use App\Models\Review;
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
            $portfolio = Portfolio::select('id', 'image')->where('user_id', $user->uuid)->get();
            if ($portfolio) {
                $user->portfolio = $portfolio;
            } else {
                $obj = new stdClass();
                $user->portfolio = $obj;
            }
            $user->reviews = Review::where('user_id',$user->uuid)->avg('rating');
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
        $offers  = Offer::where('user_id', $user->uuid)->where('status', $status)->latest()->paginate(12);
        foreach($offers as $offer){
            $job = Jobs::find($offer->job_id);
            if($job){
                $category = Category::find($job->category_id);
                if ($category) {
                    $job->category_image = $category->image;
                } else {
                    $job->category_image = '';
                }
                $offer->job = $job;
            }
            else{
                $offer->job = new stdClass();

            }
        }
       
        return response()->json([
            'status' => true,
            'action' =>  'jobs',
            'data' => $offers
        ]);

        // $user = User::find($request->user()->uuid);
        // $jobIds  = Offer::where('user_id', $user->uuid)->where('status', $status)->orderBy('id', 'asc')->pluck('job_id');
        // $jobs = Jobs::with(['user'])->whereIn('id', $jobIds)->latest()->paginate(12);
        // foreach ($jobs as $item) {
        //     $category = Category::find($item->category_id);
        //     if ($category) {
        //         $item->category_image = $category->image;
        //     } else {
        //         $item->category_image = '';
        //     }
        // }
        // return response()->json([
        //     'status' => true,
        //     'action' =>  'jobs',
        //     'data' => $jobs
        // ]);
    }

    public function appliedJobDetail(Request $request, $job_id)
    {
        $user = User::find($request->user()->uuid);
        $obj = new stdClass();
        $job = Jobs::with(['user'])->where('id', $job_id)->first();
        $offer = Offer::where('user_id', $user->uuid)->where('job_id', $job_id)->first();
        $category = Category::find($job->category_id);
        if ($category) {
            $job->category_image = $category->image;
        } else {
            $job->category_image = '';
        }

        $obj->job = $job;
        $check = Review::where('user_id', $offer->user_id)->where('person_id', $user->uuid)->first();
        if ($check) {
            $offer->is_review_added = true;
            $offer->review_count = 1;
        }
        else{
            $offer->is_review_added = false;
            $offer->review_count = 0;
        }
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

        foreach ($jobs as $item) {
            $category = Category::find($item->category_id);
            if ($category) {
                $item->category_image = $category->image;
            } else {
                $item->category_image = '';
            }
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

    public function report(ReportRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Report();
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->reported_id = $request->reported_id;
        $create->message = $request->message;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Report Added',
        ]);
    }

}
