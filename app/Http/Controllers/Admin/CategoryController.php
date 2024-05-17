<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function list($type)
    {
        $categories = Category::where('type', $type)->get();

        if ($type == 'post') {
            return view('category.post', compact('categories'));
        }
        
    }
    public function add(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/admin/categories/', $filename))
                $path =  '/uploads/admin/categories/' . $filename;
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        // $image = public_path($category->image);
        // if(file_exists($image)){
        //     unlink($image);
        // }

        $category->name = $request->name;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/admin/categories/', $filename))
                $path =  '/uploads/admin/categories/' . $filename;
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->back();
    }
}
