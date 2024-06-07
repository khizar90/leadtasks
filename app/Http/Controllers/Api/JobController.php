<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\CreateJobRequest;
use App\Models\Category;
use App\Models\Jobs;
use App\Models\JobView;
use App\Models\Message;
use App\Models\Offer;
use App\Models\Review;
use App\Models\SaveJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{

    public function index(Request $request)
    {
        $active = Jobs::latest()->limit(12)->get();
        $user = User::find($request->user()->uuid);
        $jobIds = JobView::where('user_id', $user->uuid)->limit(12)->pluck('job_id');
        $recents = Jobs::whereIn('id', $jobIds)->latest()->limit(12)->get();
        foreach ($recents as $item) {
            $category = Category::find($item->category_id);
            if ($category) {
                $item->category_image = $category->image;
            } else {
                $item->category_image = '';
            }

            $saved  = SaveJob::where('job_id', $item->id)->where('user_id', $user->uuid)->first();
            if ($saved) {
                $item->is_saved = true;
            } else {
                $item->is_saved = false;
            }
        }
        foreach ($active as $item1) {
            $category = Category::find($item1->category_id);
            if ($category) {
                $item1->category_image = $category->image;
            } else {
                $item1->category_image = '';
            }
            $saved  = SaveJob::where('job_id', $item1->id)->where('user_id', $user->uuid)->first();
            if ($saved) {
                $item1->is_saved = true;
            } else {
                $item1->is_saved = false;
            }
        }

        return response()->json([
            'status' => true,
            'action' => "Home",
            'data' => array(
                'active' => $active,
                'recent' => $recents
            )
        ]);
    }

    public function list(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);

        if ($type == 'active') {
            $jobs = Jobs::latest()->paginate(12);
        }
        if ($type == 'recent') {
            $jobIds = JobView::where('user_id', $user->uuid)->pluck('job_id');
            $jobs = Jobs::whereIn('id', $jobIds)->latest()->paginate(12);
        }

        foreach ($jobs as $job) {
            $saved  = SaveJob::where('job_id', $job->id)->where('user_id', $user->uuid)->first();
            if ($saved) {
                $job->is_saved = true;
            } else {
                $job->is_saved = false;
            }
            $category = Category::find($job->category_id);
            if ($category) {
                $job->category_image = $category->image;
            } else {
                $job->category_image = '';
            }
        }
        return response()->json([
            'status' => true,
            'action' => "Jobs",
            'data' => $jobs
        ]);
    }
    public function create(CreateJobRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Jobs();
        $create->user_id = $user->uuid;
        $create->category_id = $request->category_id;
        $create->category_name = $request->category_name;
        $create->requirement = $request->requirement ?: '';
        $create->description = $request->description ?: '';
        $create->title = $request->title;
        $create->is_flexible = $request->is_flexible;
        $create->date = $request->date ?: '';
        $create->budget_type = $request->budget_type;
        $create->budget = $request->budget;
        $create->task_time = $request->task_time;
        $create->location = $request->location;
        $create->lat = $request->lat;
        $create->lng = $request->lng;
        $create->is_remote = $request->is_remote;
        $create->time = strtotime(date('Y-m-d H:i:s'));
        $create->save();

        return response()->json([
            'status' => true,
            'action' => "Job Added",
            'data' => $create
        ]);
    }

    public function detail(Request $request, $job_id)
    {
        $job = Jobs::with(['user'])->where('id', $job_id)->first();

        $category = Category::find($job->category_id);
        if ($category) {
            $job->category_image = $category->image;
        } else {
            $job->category_image = '';
        }

        $saved  = SaveJob::where('job_id', $job_id)->where('user_id', $request->user()->uuid)->first();
        if ($saved) {
            $job->is_saved = true;
        } else {
            $job->is_saved = false;
        }

        $is_apply = Offer::where('user_id', $request->user()->uuid)->where('job_id', $job_id)->first();
        if ($is_apply) {
            $job->is_apply = true;
        } else {
            $job->is_apply = false;
        }
        $find = JobView::where('job_id', $job_id)->where('user_id', $request->user()->uuid)->first();
        if (!$find) {
            $create = new JobView();
            $create->user_id = $request->user()->uuid;
            $create->job_id = $job_id;
            $create->save();
        }

        $total_offer = Offer::where('job_id', $job_id)->count();
        $job->total_offer = $total_offer;

        return response()->json([
            'status' => true,
            'action' => "Job Detail",
            'data' => $job
        ]);
    }

    public function save(Request $request, $job_id)
    {

        $find = SaveJob::where('job_id', $job_id)->where('user_id', $request->user()->uuid)->first();
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Job Un Saved",
            ]);
        } else {
            $create = new SaveJob();
            $create->user_id = $request->user()->uuid;
            $create->job_id = $job_id;
            $create->save();
        }

        return response()->json([
            'status' => true,
            'action' => "Job Saved",
        ]);
    }

    public function delete($job_id)
    {
        $find = Jobs::find($job_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Job Deleted!",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "Job not found",
            ]);
        }
    }

    public function listSavedJob(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $job_ids = SaveJob::where('user_id', $user->uuid)->orderBy('id', 'desc')->pluck('job_id');
        $jobs = [];
        foreach ($job_ids as $id) {
            $job = Jobs::find($id);
            if ($job) {
                $job->is_saved = true;
                $category = Category::find($job->category_id);
                if ($category) {
                    $job->category_image = $category->image;
                } else {
                    $job->category_image = '';
                }
            }
            $jobs[] = $job;
        }

        $count  = count($jobs);
        $jobs = collect($jobs);
        $jobs = $jobs->forPage($request->page, 12)->values();


        return response()->json([
            'status' => true,
            'action' => "Saved Jobs",
            'data' => array(
                'data' => $jobs,
                'total' => $count
            )
        ]);
    }
    public function listMessages(Request $request,$offer_id){
        $offer = Offer::find($offer_id);
        // $user = User::find($offer->user_id);

        if ($offer) {
            $messages = Message::where('offer_id',$offer_id)->get();
            foreach($messages as $messages){
                $messages->user = User::find($messages->from);
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'data' => $messages,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "No Job found",
        ]);
    }

    public function addReview(Request $request){
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'user_id' => "required|exists:users,uuid",
            'job_id' => "required|exists:jobs,id",
            'rating' => 'required'
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new Review();
        $create->user_id = $request->user_id;
        $create->person_id = $user->uuid;
        $create->job_id = $request->job_id;
        $create->rating = $request->rating;
        $create->feedback = $request->feedback ? : '';
        $create->time = time();
        $create->save();
        return response()->json([
            'status' => true,
            'action' => "Rating Added",
            'data' => $create
        ]);
    }
}
