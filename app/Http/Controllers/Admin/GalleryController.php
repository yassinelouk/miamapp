<?php

namespace App\Http\Controllers\Admin;

use Session;
use Validator;
use App\Models\Gallery;
use App\Models\Language;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();

        $lang_id = 1;
        $data['galleries'] = Gallery::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

        $data['lang_id'] = $lang_id;

        return view('admin.gallery.index', $data);
    }

    public function edit($id)
    {
        $data['gallery'] = Gallery::findOrFail($id);
        return view('admin.gallery.edit', $data);
    }



    public function store(Request $request)
    {


        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $messages = [
            'language_id.required' => 'The language field is required',
        ];

        $rules = [
            'language_id' => 'required',
            'title' => 'required|max:255',
            'serial_number' => 'required|integer',
            'image' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $gallery = new Gallery;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('image')->move('assets/front/img/gallery/', $filename);
            $gallery->image = $filename;
        }

        $gallery->language_id = $request->language_id;
        $gallery->title = $request->title;
        $gallery->serial_number = $request->serial_number;
        $gallery->save();

        Session::flash('success', 'Image added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg');
        $gallery = Gallery::findOrFail($request->gallery_id);

        $rules = [
            'title' => 'required|max:255',
            'serial_number' => 'required|integer',

            'image' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $gallery = Gallery::findOrFail($request->gallery_id);

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $img->getClientOriginalExtension();
            $request->file('image')->move('assets/front/img/gallery/', $filename);
            @unlink('assets/front/img/gallery/' . $gallery->image);
            $gallery->image = $filename;
        }

        $gallery->title = $request->title;
        $gallery->serial_number = $request->serial_number;
        $gallery->save();

        Session::flash('success', 'Gallery updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {

        $gallery = Gallery::findOrFail($request->gallery_id);
        @unlink('assets/front/img/gallery/' . $gallery->image);
        $gallery->delete();

        Session::flash('success', 'Image deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $gallery = Gallery::findOrFail($id);
            @unlink('assets/front/img/gallery/' . $gallery->image);
            $gallery->delete();
        }

        Session::flash('success', 'Image deleted successfully!');
        return "success";
    }

    public function addPictures() {
        $path = public_path('assets/front/img/product/featured/gallery');
        $filesInFolder = File::allFiles($path);
        $pictures = array();
        foreach($filesInFolder as $fileInFolder){
            $file = pathinfo($fileInFolder);
            $pictures[] = $file['basename'];
        }
        return view('admin.gallery.add', compact('pictures'));
    }

    public function storePictures(Request $request) {
        $allowedExts = array('jpg', 'png', 'jpeg');
        if ($request->hasFile('pictures')) {
            foreach ($request->file('pictures') as $img) {
                $ext = $img->getClientOriginalExtension();
                // if (!in_array($ext, $allowedExts)) {
                //     return Session::flash('danger', 'Only png, jpg, jpeg image is allowed');
                // }
            }
            foreach ($request->file('pictures') as $img) {
                $filename = Str::random(5) . '.' . $img->getClientOriginalExtension();
                $img->move('assets/front/img/product/featured/gallery/', $filename);
            }
        }
        Session::flash('success', 'Images uploaded successfully!');
        return redirect()->route('admin.gallery');
    }
}
