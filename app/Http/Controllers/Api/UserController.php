<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReportRequest;
use App\Models\Category;
use App\Models\Jobs;
use App\Models\JobView;
use App\Models\Offer;
use App\Models\Portfolio;
use App\Models\Report;
use App\Models\Review;
use App\Models\SaveJob;
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
            $user->reviews = Review::where('user_id', $user->uuid)->avg('rating');
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
        $job = Jobs::find($request->job_id);
        $user = User::find($request->user()->uuid);
        $create = new Offer();
        $create->user_id = $user->uuid;
        $create->to_id = $job->user_id;
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
        $offerIds = Offer::where('user_id', $user->uuid)->where('status', $status)->where('job_id', '!=', 0)->pluck('id');
        $offerIds1 = Offer::where('user_id', $user->uuid)->where('status', $status)->where('job_id', 0)->where('status', '!=', 0)->pluck('id');
        $mergedOfferIds = $offerIds->merge($offerIds1);
        $offers = Offer::whereIn('id', $mergedOfferIds)->orderBy('id', 'desc')->paginate(12);
        foreach ($offers as $offer) {
            $job = Jobs::find($offer->job_id);
            if ($job) {
                $category = Category::find($job->category_id);
                if ($category) {
                    $job->category_image = $category->image;
                } else {
                    $job->category_image = '';
                }
                $offer->job = $job;
            } else {
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

    public function appliedJobDetail(Request $request, $offer_id)
    {
        $user = User::find($request->user()->uuid);
        $offer = Offer::find($offer_id);
        if ($offer) {
            $other_user = User::find($offer->to_id);
            $job = Jobs::with(['user'])->where('id', $offer->job_id)->first();
            if ($job) {

                $category = Category::find($job->category_id);
                if ($category) {
                    $job->category_image = $category->image;
                } else {
                    $job->category_image = '';
                }
                $offer->job = $job;
            } else {
                $offer->job = new stdClass();
            }
            $offer->other_user = $other_user;
            $review  = Review::where('offer_id', $offer->id)->first();
            if ($review) {
                $offer->is_review_added = true;
            } else {
                $offer->is_review_added = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Detail',
            'data' => $offer
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
            return response()->json([
                'status' => true,
                'action' =>  'Jobs',
                'data' => $jobs
            ]);
        }
        if ($type == 'assign_task') {
            $offers = Offer::where('to_id', $user->uuid)->where('status', 1)->latest()->paginate(12);
            foreach ($offers as $offer) {
                $job = Jobs::find($offer->job_id);
                if ($job) {
                    $category = Category::find($job->category_id);
                    if ($category) {
                        $job->category_image = $category->image;
                    } else {
                        $job->category_image = '';
                    }
                    $offer->job = $job;
                } else {
                    $offer->job = new stdClass();
                }
            }
            // $jobIds = Jobs::where('user_id', $user->uuid)->pluck('id');
            // $ofersIds = Offer::whereIn('job_id', $jobIds)->where('status', 1)->pluck('job_id');
            // $jobs = Jobs::whereIn('id', $ofersIds)->orderBy('id', 'desc')->paginate(12);
        }
        if ($type == 'complete_task') {
            $offers = Offer::where('to_id', $user->uuid)->where('status', 2)->latest()->paginate(12);
            foreach ($offers as $offer) {
                $job = Jobs::find($offer->job_id);
                if ($job) {
                    $category = Category::find($job->category_id);
                    if ($category) {
                        $job->category_image = $category->image;
                    } else {
                        $job->category_image = '';
                    }
                    $offer->job = $job;
                } else {
                    $offer->job = new stdClass();
                }
            }
            // $jobIds = Jobs::where('user_id', $user->uuid)->pluck('id');
            // $ofersIds = Offer::whereIn('job_id', $jobIds)->where('status', 2)->pluck('job_id');
            // $jobs = Jobs::whereIn('id', $ofersIds)->orderBy('id', 'desc')->paginate(12);
        }

        // foreach ($jobs as $item) {
        //     $category = Category::find($item->category_id);
        //     if ($category) {
        //         $item->category_image = $category->image;
        //     } else {
        //         $item->category_image = '';
        //     }
        // }
        return response()->json([
            'status' => true,
            'action' =>  'Jobs',
            'data' => $offers
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
