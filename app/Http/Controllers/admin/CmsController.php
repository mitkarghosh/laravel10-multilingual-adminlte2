<?php
/*****************************************************/
# Page/Class name   : CmsController
# Purpose           : CMS content Management
/*****************************************************/
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Cms;
use App\Models\CmsLocal;
use Helper;
use AdminHelper;

class CmsController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_cms_list');
        $data['panel_title']= trans('custom_admin.lab_cms_list');

        $pageNo = $request->input('page');
        Session::put('pageNo',$pageNo);

        $data['listData']   = Cms::orderBy('id','asc');
        $data['searchText'] = $key = $request->searchText; //search text for searching facility
        if ($key) {
            // if the search key is provided, proceed to build query for search
            $data['listData']->where(function ($q) use ($key) {
                $q->where('name', 'LIKE', '%' . $key . '%');
            });

        }
        $data['listData'] = $data['listData']->paginate(AdminHelper::ADMIN_LIST_LIMIT);
        return view('admin.cms.list', $data);
    }

    /*****************************************************/
    # Function name : edit
    # Params        : $id, Request $request
    /*****************************************************/
    public function edit($id, Request $request)
    {
        $data['page_title']     = trans('custom_admin.lab_edit_cms');
        $data['panel_title']    = trans('custom_admin.lab_edit_cms');

        $data['details']     = Cms::find($id);

        if ($request->isMethod('POST')) {
            // Checking validation
            $validationCondition = array(
                'title_en'          => 'required|min:2|max:255',
                'title_de'          => 'required|min:2|max:255',
                // 'description_en'    => 'required',
                // 'description_de'    => 'required'
            ); // validation condition
            $validationMessages = array(
                'title_en.required' => 'Please enter Title (English)',
                'title_en.min'      => 'Title (English) should be should be at least 2 characters',
                'title_en.max'      => 'Title (English) should not be more than 255 characters',
                'title_de.required' => 'Please enter Title (Dutch)',
                'title_de.min'      => 'Title (Dutch) should be should be at least 2 characters',
                'title_de.max'      => 'Title (Dutch) should not be more than 255 characters',
                // 'description_en.required'          => 'Description(English) is required',
                // 'description_de.required'          => 'Description(Dutch) is required'
            );
            $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
            if ($Validator->fails()) {
                return \Redirect::back()->withErrors($Validator);
            }
            
            $newSlug = Helper::generateUniqueSlug(new Cms(), $request->title_en, $id);
            Cms::where('id', '=', $id)->update([
                'name' => $request->title_en,
                'updated_by' => \Auth::guard('admin')->user()->id,
                'meta_keyword' => $request->meta_keyword,
                'meta_description' => $request->meta_description
            ]);
            CmsLocal::where('page_id', '=', $id)->where('lang_code','=', 'EN')->update([
                'title' => $request->title_en,
                'description' => isset($request->description_en) ? $request->description_en : null
            ]);
            CmsLocal::where('page_id', '=', $id)->where('lang_code','=', 'DE')->update([
                'title' => $request->title_de,
                'description' => isset($request->description_de) ? $request->description_de : null
            ]);
            return \Redirect::route('admin.'.\App::getLocale().'.CMS.list')->with('alert-success', trans('custom_admin.success_data_updated_successfully'));
            
        } else {
            return view('admin.cms.edit', $data);
        }
    }
}