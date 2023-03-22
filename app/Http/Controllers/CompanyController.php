<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function list()
    {
        $companies = Company::all();
        return ok('Company List', $companies);
    }


    //Create Company
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'email'     => 'required|email|max:255|unique:companies,email',
            'logo'      => 'required|image',
            'website'   => 'required'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $image = $request->file('logo'); //store logo file in image
        $imageName = 'logo' . now() . $request->file('logo')->getClientOriginalName(); //generated new image name

        //move image into storage/app/public
        if ($image->move(storage_path('app/public/'), $imageName)) {
            $company = Company::create($request->only(['name', 'email', 'website']) + [
                'logo'  => 'app/public/' . $imageName
            ]);
            return ok('Company Created Successfully.', $company);
        } else {
            return error('Logo upload problem!! Try again.');
        }
    }

    //Update Company Details
    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'        => 'required|exists:companies,id',
            'name'      => 'max:255',
            'email'     => 'email|max:255|unique:companies,email',
            'logo'      => 'image',
            'website'   => 'max:255'
        ]);
        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        if (count($request->all()) > 1) {
            $company = Company::find($request->id);

            if ($request->logo) {
                $image = $request->file('logo');
                $imageName = 'logo' . now() . $image->getClientOriginalName();
                $image->move(storage_path('app/public/'), $imageName);
            }
            $company->update($request->only(['name', 'email', 'website']) + [
                'logo' => 'app/public/' . $imageName
            ]);
            return ok('Data Updated Successfully.');
        } else {
            return error('No Data Passed for Update');
        }
    }

    //Show Company
    public function show($id)
    {
        $company = Company::find($id);
        return ok('Company Detail', $company);
    }

    //Delete Company
    public function delete($id)
    {
        Company::find($id)->delete();
        return ok('Company Deleted Successfully');
    }
}
