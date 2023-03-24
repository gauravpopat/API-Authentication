<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CompanyController extends Controller
{
    //List of companies with their employees
    public function list()
    {
        $companies = Company::with('employees')->get();
        return ok('Company List', $companies);
    }


    //Create Company
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'email'     => 'required|email|max:255|unique:companies,email',
            'logo'      => 'required|mimes:jpeg,jpg,png,gif|dimensions:max_width=100,max_height=100',
            'website'   => 'required|unique:companies,website',
            'is_active' => 'in:true,false'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $image = $request->file('logo'); //store logo file in image
        $imageName = 'logo' . time() . $request->file('logo')->getClientOriginalName(); //generated new image name

        $company = Company::create($request->only(['name', 'email', 'website', 'is_active']) + [
            'logo'  => $imageName
        ]);

        //Move company logo into storage/app/public/company/logo/{company_id}/... 
        $path = storage_path('app/public/company/logo/') . $company->id . '/';
        $image->move($path, $imageName);

        return ok('Company Created Successfully.', $company);
    }

    //Update Company Details
    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'        => 'required|exists:companies,id',
            'name'      => 'max:255',
            'email'     => 'email|max:255|unique:companies,email',
            'logo'      => 'image',
            'website'   => 'max:255',
            'is_active' => 'in:0,1'
        ]);

        if ($validation->fails())
            return error('Validation Error', $validation->errors(), 'validation');

        $company = Company::find($request->id);

        if ($request->logo) {
            //Delete the old logo.
            $oldLogo = storage_path('app/public/company/logo/') . $company->id . '/' . $company->logo;
            File::delete($oldLogo);

            //store logo in image
            $image = $request->file('logo');
            //generate new image name
            $imageName = 'logo' . time() . $image->getClientOriginalName();

            //Update the logo.
            $company->update([
                'logo' => $imageName
            ]);

            //Move updated logo into storage/app/public/company/logo/{company_id}/... 
            $path = storage_path('app/public/company/logo/') . $company->id . '/';
            $image->move($path, $imageName);
        }

        $company->update($request->only(['name', 'email', 'website', 'is_active']));
        return ok('Data Updated Successfully.');
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
        $company = Company::find($id);

        //get the companyid folder
        $companyFile = storage_path('app/public/company/logo/') . $company->id;

        //delete that folder
        File::deleteDirectory($companyFile);

        //delete the company
        $company->delete();
        return ok('Company Deleted Successfully');
    }
}
