<?php
/*****************************************************/
# Page/Class name   : CouponsController
/*****************************************************/

namespace App\Http\Controllers\admin;

use App;
use App\Http\Controllers\Controller;
use AdminHelper;
use Illuminate\Http\Request;
use Helper;
use Redirect;
use Validator;
use View;
use App\Models\Coupon;

class CouponsController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title']  = trans('custom_admin.lab_coupon_list');
        $data['panel_title'] = trans('custom_admin.lab_coupon_list');
        $data['order_by']    = 'created_at';
        $data['order']       = 'desc';

        $couponQuery        = Coupon::whereNull('deleted_at');

        $couponDuration = '';
        $discountType   = '';
        $data['searchText']     = $key = $request->searchText;
        $data['couponDuration'] = $couponDuration = isset($request->coupon_duration)?$request->coupon_duration:'';
        $data['discountType']   = $discountType = isset($request->discount_type)?$request->discount_type:'';

        if ($key) {
            // if the search key is provided, proceed to build query for search
            $couponQuery->where(function ($q) use ($key) {
                $q->where('code', 'LIKE', '%' . $key . '%');
            });
        } else {
            //For filter section
                if($couponDuration != ''){
                    if ($couponDuration != '') {
                        if (strpos($couponDuration, ' - ') !== false) {
                            $explodedCouponDuration = explode(" - ",$couponDuration);
                            $couponQuery = $couponQuery->where('start_time', '>=', strtotime($explodedCouponDuration[0]))->where('end_time', '<=', strtotime($explodedCouponDuration[1]));
                            
                        }
                    }
                }
            
            if ($discountType != '') {
                $couponQuery = $couponQuery->where('discount_type', $discountType);
            }
        }

        $couponExists = $couponQuery->count();
        if ($couponExists > 0) {
            $couponList = $couponQuery->orderBy($data['order_by'], $data['order'])
                                        //->where('end_time', '>=', strtotime(now()))    
                                        ->paginate(AdminHelper::ADMIN_LIST_LIMIT);
            $data['allCoupon'] = $couponList;
        } else {
            $data['allCoupon'] = array();
        }
        // $couponTypes = AdminHelper::COUPON_TYPES;        
        return view('admin.coupon.list', $data);
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title']  = trans('custom_admin.lab_coupon_list');
        $data['panel_title'] = trans('custom_admin.lab_coupon_list');
        $data['order_by']    = 'created_at';
        $data['order']       = 'desc';

        $couponQuery        = Coupon::whereNull('deleted_at');

        $couponDuration = '';
        $discountType   = '';
        $data['searchText']     = $key = $request->searchText;
        $data['couponDuration'] = $couponDuration = isset($request->coupon_duration)?$request->coupon_duration:'';
        $data['discountType']   = $discountType = isset($request->discount_type)?$request->discount_type:'';

        if ($key) {
            // if the search key is provided, proceed to build query for search
            $couponQuery->where(function ($q) use ($key) {
                $q->where('code', 'LIKE', '%' . $key . '%');
            });
        } else {
            //For filter section
                if($couponDuration != ''){
                    if ($couponDuration != '') {
                        if (strpos($couponDuration, ' - ') !== false) {
                            $explodedCouponDuration = explode(" - ",$couponDuration);
                            $couponQuery = $couponQuery->where('start_time', '>=', strtotime($explodedCouponDuration[0]))->where('end_time', '<=', strtotime($explodedCouponDuration[1]));
                            
                        }
                    }
                }
            
            if ($discountType != '') {
                $couponQuery = $couponQuery->where('discount_type', $discountType);
            }
        }

        $couponExists = $couponQuery->count();
        if ($couponExists > 0) {
            $couponList = $couponQuery->orderBy($data['order_by'], $data['order'])->get();
            $data['allCoupon'] = $couponList;
        } else {
            $data['allCoupon'] = array();
        }
        // $couponTypes = AdminHelper::COUPON_TYPES;        
        return view('admin.coupon.show_all', $data);
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request)
    {
        $data['page_title']     = trans('custom_admin.lab_add_coupon');
        $data['panel_title']    = trans('custom_admin.lab_add_coupon');

        try
        {
            if ($request->isMethod('POST'))
            {
                // Checking validation
                $validationCondition = array(
                    'code'           => 'required|regex:/^[a-zA-Z0-9]+$/|unique:'.(new Coupon)->getTable().',code,NULL,id,deleted_at,NULL',
                    'discount_type'  => 'required',
                    'amount'         => 'required|regex:/^[1-9]\d*(\.\d+)?$/',
                    'start_time'     => 'required',
                ); // validation condition
                $validationMessages = array(
                    'code.required'            => trans('custom_admin.error_coupon_code'),
                    'code.regex'               => trans('custom_admin.error_coupon_code_valid'),
                    'code.unique'              => trans('custom_admin.error_coupon_code_unique'),                    
                    'discount_type.required'   => trans('custom_admin.error_discount_type'),
                    'amount.required'          => trans('custom_admin.error_amount'),
                    'amount.regex'             => trans('custom_admin.error_valid_amount'),
                    'start_time.required'      => trans('custom_admin.error_coupon_duration')
                );

                $validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($validator->fails()) {
                    return \Redirect::route('admin.'.\App::getLocale().'.coupon.add')->withErrors($validator)->withInput();
                } else {
                    $newCoupon                  = new Coupon;
                    $newCoupon->code            = trim($request->code, ' ');
                    $newCoupon->has_minimum_cart_amount = $request->has_minimum_cart_amount ? $request->has_minimum_cart_amount : 'N';
                    $newCoupon->cart_amount     = $request->cart_amount ? $request->cart_amount : null;
                    $newCoupon->discount_type   = $request->discount_type;
                    $newCoupon->amount          = $request->amount;
                    $startTime                  = date('Y-m-d H:i', strtotime($request->start_time));
                    $newCoupon->start_time      = strtotime($startTime);
                    $endTime                    = date('Y-m-d H:i', strtotime($request->end_time));
                    $newCoupon->end_time        = strtotime($endTime);
                    $newCoupon->is_one_time_use = $request->is_one_time_use ? 'Y' : 'N';
                    $newCoupon->is_one_time_use_per_user = $request->is_one_time_use_per_user ? 'Y' : 'N';
                    $saveCoupon                 = $newCoupon->save();
                    if ($saveCoupon) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.coupon.list');
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
                        return redirect()->back();
                    }
                }
            }
            return view('admin.coupon.add', $data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.coupon.list')->with('error', $e->getMessage());
        }

    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id = null
    /*****************************************************/
    public function edit(Request $request, $id = null)
    {
        $data['page_title'] = trans('custom_admin.lab_edit_coupon');
        $data['panel_title']= trans('custom_admin.lab_edit_coupon');
        
        try
        {
            $couponDetail = Coupon::where('id',$id)->first();

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.coupon.list');
                }
                // Checking validation
                $validationCondition = array(
                    'code'           => 'required|regex:/^[a-zA-Z0-9]+$/|unique:'.(new Coupon)->getTable().',code,' .$id,
                    'discount_type'  => 'required',
                    'amount'         => 'required|regex:/^[1-9]\d*(\.\d+)?$/',
                    'start_time'     => 'required'
                );
                $validationMessages = array(
                    'code.required'            => trans('custom_admin.error_coupon_code'),
                    'code.regex'               => trans('custom_admin.error_coupon_code_valid'),
                    'code.unique'              => trans('custom_admin.error_coupon_code_unique'),                    
                    'discount_type.required'   => trans('custom_admin.error_discount_type'),
                    'amount.required'          => trans('custom_admin.error_amount'),
                    'amount.regex'             => trans('custom_admin.error_valid_amount'),
                    'start_time.required'      => trans('custom_admin.error_coupon_duration')
                );

                $validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($validator->fails()) {
                    return \Redirect::back()->withErrors($validator)->withInput();
                } else {
                    $process = true;
                    if ($request->end_time) {
                        if (strtotime($request->end_time) < strtotime($request->start_time)) {
                            $process = false;
                        }
                    }

                    if (!$process) {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_start_end_date_time'));
                        return \Redirect::back()->withInput();
                    } else {
                        $startTime  = date('Y-m-d H:i', strtotime($request->start_time));
                        $endTime    = date('Y-m-d H:i', strtotime($request->end_time));
                        
                        $updateCouponData = array(
                            'code'                      => trim($request->code, ' '),
                            'has_minimum_cart_amount'   => $request->has_minimum_cart_amount ? $request->has_minimum_cart_amount : 'N',
                            'cart_amount'               => $request->cart_amount ? $request->cart_amount : null,
                            'discount_type'             => $request->discount_type,
                            'amount'                    => $request->amount,
                            'start_time'                => strtotime($startTime),
                            'end_time'                  => strtotime($endTime),
                            'is_one_time_use'           => $request->is_one_time_use ? 'Y' : 'N',
                            'is_one_time_use_per_user'  => $request->is_one_time_use_per_user ? 'Y' : 'N',
                        );
                        $saveCouponData = Coupon::where('id', $id)->update($updateCouponData);
                        if ($saveCouponData) {
                            $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                            return redirect()->route('admin.'.\App::getLocale().'.coupon.list');
                        } else {
                            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                            return redirect()->route('admin.'.\App::getLocale().'.coupon.list');
                        }
                    }
                }
            }
            return view('admin.coupon.edit')->with(['details' => $couponDetail, 'data' => $data]);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.coupon.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : status
    # Params        : Request $request, $id = null
    /*****************************************************/
    public function status(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.coupon.list');
            }

            $couponExists = Coupon::where('id', $id)->count();
            if ($couponExists > 0) {
                $couponDetails = Coupon::where('id', $id)->first();
                if ($couponDetails->status == 1) {
                    $updateCoupon = Coupon::where('id', $id)->update(['status' => '0']);
                    if ($updateCoupon) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                        return redirect()->back();
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->back();
                    }
                } else if ($couponDetails->status == 0) {
                    $updateCoupon = Coupon::where('id', $id)->update(['status' => '1']);
                    if ($updateCoupon) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                        return redirect()->back();
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->back();
                    }
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : delete
    # Params        : Request $request, $id = null
    /*****************************************************/
    public function delete(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.coupon.list');
            }

            $couponDetails = Coupon::where('id', $id)->first();
            if ($couponDetails != null) {
                $deleteCoupon = Coupon::find($id)->delete();
                if ($deleteCoupon) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}