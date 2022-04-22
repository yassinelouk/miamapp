<?php

namespace App\Http\Controllers\Admin;

use App\Models\script;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Session;


class ScriptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.script.script');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validate = $req->validate([
        //     'csv_file' => 'nullable|image|mimes:jpeg,png,doc,docx|max:2048',
        // ]);

        $file = $request->file('csv_file');
        $name=$request->file('csv_file')->getClientOriginalName();

        $file->move(public_path().'/csv_files/', $name);

        Session::flash('success', 'File imported successfully!');
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\script  $script
     * @return \Illuminate\Http\Response
     */
    public function show(script $script)
    {
        $path = public_path('csv_files');
        $filesinFolder = File::allFiles($path);
        $file = File::get("csv_files/products.json");
        $products = json_decode($file);
        foreach($products as $product) {
            $prod = new Product();
            $prod->language_id = 179;
            $prod->title = $product->title;
            $prod->slug = $product->slug;
            $prod->category_id = $product->category_id;
            $prod->feature_image = $product->feature_image;
            $prod->summary = $product->summary;
            $prod->description = $product->description;
            $prod->variations = $product->variations;
            $prod->addons = $product->addons;
            $prod->current_price = $product->current_price;
            $prod->previous_price = $product->previous_price;
            $prod->rating = $product->rating;
            $prod->status = $product->status;
            $prod->is_feature = $product->is_feature;
            $prod->is_special = $product->is_special;
            $prod->fidelity_score = $product->fidelity_score;
            if($prod->save()) {
                Session::flash('success', 'Products imported successfully!');
            } else {
                echo 'erreur';
            }
        }
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\script  $script
     * @return \Illuminate\Http\Response
     */
    public function excelScript(script $script)
    {
        $products = fopen("csv_files/product.csv","r");
        $data = fgetcsv($products, 1000, ",");
        while (($data = fgetcsv($products, 1000, ";")) !== FALSE) {
            $prod = new Product();
            $prod->language_id = $data[1];
            $prod->title = "$data[2]";
            $prod->slug = "$data[3]";
            $prod->category_id = $data[4];
            $prod->feature_image = "$data[5]";
            $prod->summary = "$data[6]";
            $prod->description = "$data[7]";
            $prod->variations = "$data[8]";
            $prod->addons = "$data[9]";
            $prod->current_price = $data[10];
            $prod->previous_price = $data[11];
            $prod->rating = $data[12];
            $prod->status = $data[13];
            $prod->is_feature = $data[14];
            $prod->is_special = $data[17];
            $prod->fidelity_score = $data[18];
            if($prod->save()) {
                Session::flash('success', 'Products imported successfully!');
            } else {
                echo 'erreur';
            }
        }

        return back();
        Session::flash('success', 'Products imported successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\script  $script
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, script $script)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\script  $script
     * @return \Illuminate\Http\Response
     */
    public function destroy(script $script)
    {
        //
    }
}
