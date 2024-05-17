<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {

        // $verify = User::where('verify', 1)->count();


        $total = User::count();
        $todayActive = 0;

        $todayNew = User::whereDate('created_at', date('Y-m-d'))->count();
        $mainUsers = User::pluck('uuid');
        $loggedIn = UserDevice::whereIn('user_id', $mainUsers)->where('token', '!=', '')->distinct('user_id')->count();

        $iosTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'ios')->count();
        $androidTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'android')->count();

        return view('index', compact('todayActive', 'total', 'todayNew', 'mainUsers', 'loggedIn', 'iosTraffic', 'androidTraffic'));
    }

    public function users(Request $request)
    {

        $users = User::latest()->paginate(20);

        if ($request->ajax()) {
            $query = $request->input('query');
            $users = User::query();
            if ($query) {
                $users = $users->where('email', 'like', '%' . $query . '%');
            }
            $users = $users->latest()->Paginate(20);

            return view('user.user-ajax', compact('users'));
        }

        return view('user.index', compact('users'));
    }

    public function exportCSV()
    {


        $users = User::select('name', 'email', 'country', 'country_code', 'phone_number', 'address')->get();
        $columns = ['name', 'email', 'country', 'country_code', 'phone_number', 'address'];

        $handle = fopen(storage_path('users.csv'), 'w');

        fputcsv($handle, $columns);

        foreach ($users->chunk(2000) as $chunk) {
            foreach ($chunk as $user) {
                fputcsv($handle, $user->toArray());
            }
        }

        fclose($handle);

        return response()->download(storage_path('users.csv'))->deleteFileAfterSend(true);
    }

    public function faqs()
    {
        $faqs = Faq::all();

        return view('faq', compact('faqs'));
    }

    public function deleteFaq($id)
    {
        $faq  = Faq::find($id);
        $faq->delete();
        return redirect()->back();
    }

    public function addFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }

    public function editFaq(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = Faq::find($id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }
}
