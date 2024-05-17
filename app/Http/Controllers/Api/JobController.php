<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\CreateJobRequest;
use App\Models\Jobs;
use App\Models\JobView;
use App\Models\Offer;
use App\Models\SaveJob;
use App\Models\User;
use Illuminate\Http\Request;

class JobController extends Controller
{

    public function index(Request $request)
    {
        $active = Jobs::latest()->limit(12)->get();
        $user = User::find($request->user()->uuid);
        $jobIds = JobView::where('user_id', $user->uuid)->limit(12)->pluck('job_id');
        $recents = Jobs::whereIn('id', $jobIds)->latest()->limit(12)->get();
        // foreach($jobIds as $item){
        //     $job = Jobs::find($item);
        //     $recents[] = $job;
        // }

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

        $saved  = SaveJob::where('job_id', $job_id)->where('user_id', $request->user()->uuid)->first();
        if ($saved) {
            $job->is_saved = true;
        } else {
            $job->is_saved = false;
        }

        $is_apply = Offer::where('user_id', $request->user()->uuid)->where('job_id', $job_id)->firs();
        if($is_apply){
            $job->is_apply = true;
        }
        else{
            $job->is_apply = false;
        }
        $find = JobView::where('job_id', $job_id)->where('user_id', $request->user()->uuid)->first();
        if (!$find) {
            $create = new JobView();
            $create->user_id = $request->user()->uuid;
            $create->job_id = $job_id;
            $create->save();
        }

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


}
