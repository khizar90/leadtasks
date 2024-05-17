<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class SettingController extends Controller
{

    public function splash($user_id = null)
    {
        $obj = new stdClass();
        $obj1 = new stdClass();

        $post = Category::select('id', 'name', 'image')->where('type', 'post')->get();
        $obj->post_categories = $post;
        

        if ($user_id != null) {
            $user = User::where('uuid',$user_id)->first();
            if ($user) {
                $obj->user = $user;
                
                $is_delete = false;
            } else {
                $is_delete = true;
                $obj->user = $obj1;
            }
        } else {
            $obj->user = $obj1;
            $is_delete = false;
        }

        return response()->json([
            'status' => true,
            'action' => "Splash",
            'is_delete' => $is_delete,
            'data' => $obj,
        ]);
    }

    public function faqs(){
        $list = Faq::all();
        return response()->json([
            'status' => true,
            'action' => "Faq List",
            'data' => $list
        ]);
    }
}
