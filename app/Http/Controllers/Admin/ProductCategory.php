<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Pcategory;
use App\Models\Language;
use Validator;
use Session;

class ProductCategory extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['pcategories'] = Pcategory::where('language_id', $lang_id)->orderBy('position')->paginate(10);

        $data['lang_id'] = $lang_id;
        return view('admin.product.category.index',$data);
    }


    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'type' => 'required',
            'name' => 'required|max:255',
            'status' => 'required',
            'tax' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $data = new Pcategory;
        $input = $request->all();

        if($request->hasFile('image')){
          $image = $request->image;
          $name =  uniqid() . '.'. $image->getClientOriginalExtension();
          $image->move('assets/front/img/category/', $name);
          $input['image'] = $name;
        }

        $input['slug'] =  make_slug($request->name);
        $input['tax'] =  empty($request->tax) ? 0.00 : $request->tax;
        $input['position'] = Pcategory::max('position') + 1;
    //    dd($input);
        $data->create($input);

        Session::flash('success', 'Category added successfully!');
        return "success";
    }


    public function edit($id)
    {
        $data = Pcategory::findOrFail($id);
        return view('admin.product.category.edit',compact('data'));
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'tax' => 'nullable|numeric',
            'type' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        $data = Pcategory::findOrFail($request->category_id);
        $input = $request->all();
        if($request->hasFile('image')){
@unlink('assets/front/img/category/' . $data->image);
            $image = $request->image;
            $name =  uniqid() . '.'. $image->getClientOriginalExtension();
            $image->move('assets/front/img/category/', $name);
            $input['image'] = $name;
          }

        $input['slug'] =  make_slug($request->name);
        $input['tax'] =  empty($request->tax) ? 0.00 : $request->tax;
        $data->update($input);

        Session::flash('success', 'Category Update successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $category = Pcategory::findOrFail($request->category_id);
        if ($category->products()->count() > 0) {
            Session::flash('warning', 'First, delete all the product under the selected categories!');
            return back();
        }
@unlink('assets/front/img/category/' . $category->image);
        $category->delete();

        Session::flash('success', 'Category deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $pcategory = Pcategory::findOrFail($id);
            if ($pcategory->products()->count() > 0) {
                Session::flash('warning', 'First, delete all the product under the selected categories!');
                return "success";
            }
        }

        foreach ($ids as $id) {
            $pcategory = Pcategory::findOrFail($id);
@unlink('assets/front/img/category/' . $pcategory->image);
            $pcategory->delete();
        }

        Session::flash('success', 'product categories deleted successfully!');
        return "success";
    }

    public function FeatureCheck(Request $request)
    {
        $id = $request->pcategory_id;
        $value = $request->feature;

        $pcategory = Pcategory::findOrFail($id);
        $pcategory->is_feature = $value;
        $pcategory->save();

        Session::flash('success', 'Product category updated successfully!');
        return back();
    }

    public function removeImage(Request $request) {
        $type = $request->type;
        $pcatid = $request->pcategory_id;

        $pcategory = Pcategory::findOrFail($pcatid);

        if ($type == "pcategory") {
            @unlink("assets/front/img/category/" . $pcategory->image);
            $pcategory->image = NULL;
            $pcategory->save();
        }

        $request->session()->flash('success', 'Image removed successfully!');
        return "success";
    }

    public function updatePositions(Request $request){
        foreach ($request->position_logs as $position_log) {
            $category = Pcategory::find($position_log[0]);
            $category->position = $position_log[1];
            $category->save();
        }
        $json = array(
            "success" => "Database successfully updated."
        );
        return json_encode($json);
    }

}
