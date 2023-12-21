<?php
/*****************************************************/
# Page/Class name   : ReviewsController
/*****************************************************/

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App;
use AdminHelper;
use Helper;
use Redirect;
use Validator;
use View;
use App\Models\OrderReview;
use App\Models\SiteSetting;

class ReviewsController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_review_list');
        $data['panel_title']= trans('custom_admin.lab_review_list');

        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);

            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            
            $reviewQuery  = OrderReview::orderBy('created_at','desc')->paginate(AdminHelper::ADMIN_LIST_LIMIT);

            $data['reviews'] = $reviewQuery;
            
            return view('admin.review.list', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.review.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_review_list');
        $data['panel_title']= trans('custom_admin.lab_review_list');

        try
        {
            $data['order_by']    = 'created_at';
            $data['order']       = 'desc';
            
            $reviewQuery  = OrderReview::orderBy('created_at','desc')->get();

            $data['reviews'] = $reviewQuery;
            
            return view('admin.review.show_all', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.review.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : details
    # Params        : Request $request
    /*****************************************************/
    public function details(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_review_details');
        $data['panel_title']= trans('custom_admin.lab_review_details');

        try
        {
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;
            
            $reviewId = $request->id;
            if ($reviewId != '') {
                $data['reviewDetails'] = $reviewDetails = OrderReview::where(['id' => $reviewId])->first();
                return view('admin.review.details', $data);
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            } 
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.review.details')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : delete
    # Params        : Request $request, $id
    /*****************************************************/
    public function delete(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.review.list');
            }
            $details = OrderReview::where('id', $id)->first();
            if ($details != null) {
                $delete = $details->delete();
                if ($delete) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }
                return redirect()->back();
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.review.list')->with('error', $e->getMessage());
        }
    }

}
