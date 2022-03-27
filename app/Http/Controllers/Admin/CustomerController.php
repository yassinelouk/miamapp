<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Models\FidelityTime;
use App\Models\BasicExtended;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Validator;

class CustomerController extends Controller
{
    public function index(Request $request) {
        $term = $request->term;
        $data['customers'] = Customer::when($term, function ($query, $term) {
            return $query->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('phone', 'LIKE', '%' . $term . '%');
        })
        ->orderBy('id', 'DESC')->paginate(10);
        return view('admin.customers.index', $data);
    }
    public function fidelityIndex(Request $request) {
        $data['fidelitytimes'] = FidelityTime::all();
        return view('admin.customers.fidelity', $data);
    }
    public function updateFidelitytime(Request $request) {
        $start = $request->start_time;
        $end = $request->end_time;
        $fts = FidelityTime::all();

        for ($i=0; $i < count($fts); $i++) {
            $fts[$i]->start_time = $start[$i];
            $fts[$i]->end_time = $end[$i];
            $fts[$i]->save();
        }

        session()->flash('success', __('Fidelity times updated successfully'));
        return back();
    }
    public function updateFidelityStatus(Request $request) {
        $request->validate([
            'is_fidelity' => 'required',
            'base_fidelity_rate' => 'required|numeric',
        ]);
        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->is_fidelity = $request->is_fidelity;
            $be->base_fidelity_rate = $request->base_fidelity_rate;
            $be->save();
        }
        Session::flash('success', 'updated successfully!');
        return back();
    }
    public function store(Request $request)
    {
        $rules = [
            'phone' => 'required|unique:customers',
            'name' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $customer = new Customer;
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->save();

        Session::flash('success', 'Customer added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail($request->customer_id);

        $rules = [
            'phone' => [
                'required',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'name' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->save();

        Session::flash('success', 'Customer updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {

        $customer = Customer::findOrFail($request->customer_id);
        $customer->delete();

        Session::flash('success', 'Customer deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $customer = Customer::findOrFail($id);
            $customer->delete();
        }

        Session::flash('success', 'Customers deleted successfully!');
        return "success";
    }
}
