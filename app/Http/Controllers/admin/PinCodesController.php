<?php
/*****************************************************/
# Page/Class name   : PinCodesController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use AdminHelper;
use App\Models\PinCode;

class PinCodesController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_pin_code_list');
        $data['panel_title']= trans('custom_admin.lab_pin_code_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);
            
            $data['order_by']   = 'code';
            $data['order']      = 'asc';
            
            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                $query = PinCode::where('code', $key);
            } else {
                $query = new PinCode;
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])
                                            ->paginate(AdminHelper::ADMIN_LIST_LIMIT);
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.pinCode.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_pin_code_list');
        $data['panel_title']= trans('custom_admin.lab_pin_code_list');
        
        try
        {
            $data['order_by']   = 'code';
            $data['order']      = 'asc';
            
            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                $query = PinCode::where('code', $key);
            } else {
                $query = new PinCode;
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.pinCode.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.pinCode.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_pin_code');
        $data['panel_title']    = trans('custom_admin.lab_add_pin_code');
    
        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'code'  => 'required|unique:'.(new PinCode)->getTable().',code',
                    'area'  => 'required',
                    'minimum_order_amount'  => 'required|regex:/^[0-9]\d*(\.\d+)?$/',
                    // 'delivery_charge'  => 'regex:/^[0-9]\d*(\.\d+)?$/',
				);
				$validationMessages = array(
                    'code.required' => trans('custom_admin.error_pin_code'),
                    'code.unique'   => trans('custom_admin.error_pin_code_unique'),
                    'area.required' => trans('custom_admin.error_area'),
                    'minimum_order_amount.required' => trans('custom_admin.error_minimum_order_amount'),
                    'minimum_order_amount.regex'    => trans('custom_admin.error_valid_amount'),
                    // 'delivery_charge.required' => trans('custom_admin.error_delivery_charge'),
                    // 'delivery_charge.regex'    => trans('custom_admin.error_valid_amount'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.pinCode.add')->withErrors($Validator)->withInput();
				} else {
                    $new = new PinCode;
                    $new->code  = trim($request->code, ' ');
                    $new->area  = trim($request->area, ' ');
                    $new->minimum_order_amount = $request->minimum_order_amount;
                    $new->delivery_charge = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                    $save = $new->save();                
					if ($save) {	
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
						return redirect()->route('admin.'.\App::getLocale().'.pinCode.list');
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			return view('admin.pinCode.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function edit(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_edit_pin_code');
        $data['panel_title']= trans('custom_admin.lab_edit_pin_code');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = PinCode::find($id);
            $data['id'] = $id;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.pinCode.list');
                }
                $validationCondition = array(
                    'code'  => 'required|unique:' .(new PinCode)->getTable().',code,' .$id,
                    'area'  => 'required',
                    'minimum_order_amount'  => 'required|regex:/^[0-9]\d*(\.\d+)?$/',
                    // 'delivery_charge'  => 'regex:/^[0-9]\d*(\.\d+)?$/',
                );
                $validationMessages = array(
                    'code.required' => trans('custom_admin.error_pin_code'),
                    'code.unique'   => trans('custom_admin.error_pin_code_unique'),
                    'area.required' => trans('custom_admin.error_area'),
                    'minimum_order_amount.required' => trans('custom_admin.error_minimum_order_amount'),
                    'minimum_order_amount.regex'    => trans('custom_admin.error_valid_amount'),
                    // 'delivery_charge.required' => trans('custom_admin.error_delivery_charge'),
                    // 'delivery_charge.regex'    => trans('custom_admin.error_valid_amount'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $update['code'] = trim($request->code, ' ');
                    $update['area'] = trim($request->area, ' ');
                    $update['minimum_order_amount'] = $request->minimum_order_amount;
                    $update['delivery_charge'] = isset($request->delivery_charge) ? $request->delivery_charge : 0;
                    $save = PinCode::where('id', $id)->update($update);                        
                    if ($save) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.pinCode.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.pinCode.list', ['page' => $pageNo]);
                    }
                }
            }
            
            return view('admin.pinCode.edit')->with(['details' => $details, 'data' => $data]);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : status
    # Params        : Request $request, $id
    /*****************************************************/
    public function status(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.pinCode.list');
            }
            $details = PinCode::where('id', $id)->first();
            if ($details != null) {
                if ($details->status == 1) {
                    $details->status = '0';
                    $details->save();
                    
                    $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                    return redirect()->back();

                } else if ($details->status == 0) {
                    $details->status = '1';
                    $details->save();

                    $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_something_went_wrong'));
                    return redirect()->back();
                }
            } else {
                return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.pinCode.list');
            }

            $details = PinCode::where('id', $id)->first();
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
            return redirect()->route('admin.'.\App::getLocale().'.pinCode.list')->with('error', $e->getMessage());
        }
    }
    
}