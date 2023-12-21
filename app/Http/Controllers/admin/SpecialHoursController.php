<?php
/*****************************************************/
# Page/Class name   : SpecialHoursController
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
use App\Models\SpecialHour;

class SpecialHoursController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_special_hour_list');
        $data['panel_title']= trans('custom_admin.lab_special_hour_list');
        $data['order_by']   = 'id';
        $data['order']      = 'desc';

        $specialHourList = SpecialHour::orderBy($data['order_by'], $data['order'])->paginate(AdminHelper::ADMIN_LIST_LIMIT);
        $data['allSpecialHour'] = $specialHourList;
        return view('admin.specialHour.list', $data);
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request)
    {
        $data['page_title']  = trans('custom_admin.lab_special_hour_list');
        $data['panel_title'] = trans('custom_admin.lab_special_hour_list');
        $data['order_by']    = 'id';
        $data['order']       = 'desc';

        $specialHourQuery        = SpecialHour::whereNull('deleted_at');

        $specialHourList = SpecialHour::orderBy($data['order_by'], $data['order'])->get();
        $data['allSpecialHour'] = $specialHourList;
        return view('admin.specialHour.show_all', $data);
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request)
    {
        $data['page_title']     = trans('custom_admin.lab_add_special_hour');
        $data['panel_title']    = trans('custom_admin.lab_add_special_hour');

        try
        {
            if ($request->isMethod('POST')) {
                // Checking validation
                $validationCondition = array(
                    'special_date'     => 'required',
                ); // validation condition
                $validationMessages = array(
                    'special_date.required'            => trans('custom_admin.error_date'),
                );
                $validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($validator->fails()) {
                    return \Redirect::route('admin.'.\App::getLocale().'.specialHour.add')->withErrors($validator)->withInput();
                } else {
                    $newSpecialHour                 = new SpecialHour;
                    $newSpecialHour->special_date   = $request->special_date ? date('Y-m-d', strtotime($request->special_date)) : date('Y-m-d');
                    $keySlot = $errorStatus = $overlapping = 0;
                    $newSpecialHour->holiday        = isset($request->delivery['holiday'][$keySlot]) ? $request->delivery['holiday'][$keySlot] : '0';

                    if (count($request->delivery['slot'][$keySlot]['start_time']) > 1) {
                        $betweenTime = 0;

                        foreach ($request->delivery['slot'][$keySlot]['start_time'] as $keySlotTime => $valSlotTime) {
                            if ($keySlotTime == 0) {
                                $startTime1  = strtotime($valSlotTime);
                                $endTime1    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);

                                $actualStartTime1  = $valSlotTime;
                                $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
                            } else {
                                $startTime2  = strtotime($valSlotTime);
                                $endTime2    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);

                                $actualStartTime2  = $valSlotTime;
                                $actualEndTime2    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
                            }
                        }

                        if ( ($startTime1 < $endTime1) && ($startTime2 < $endTime2) ) {
                            // checking overlapping time
                            if ($startTime2 >= $startTime1 && $startTime2 <= $endTime1) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($endTime2 >= $startTime1 && $endTime2 <= $endTime1) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($startTime1 >= $startTime2  && $startTime1 <= $endTime2) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($endTime1 >= $startTime2 && $endTime1 <= $endTime2) {
                                $betweenTime = 1;
                                $overlapping++;
                            }

                            // If all are different time and not with the 2 times
                            if ($betweenTime == 0) {
                                $newSpecialHour->start_time     = $actualStartTime1;
                                $newSpecialHour->end_time       = $actualEndTime1;
                                $newSpecialHour->start_time2    = $actualStartTime2;
                                $newSpecialHour->end_time2      = $actualEndTime2;
                                $newSpecialHour->save();
                            }
                        } else {
                            $errorStatus = 1;
                        }
                    } else {
                        $startTime  = strtotime($request->delivery['slot'][$keySlot]['start_time'][0]);
                        $endTime    = strtotime($request->delivery['slot'][$keySlot]['end_time'][0]);

                        $actualStartTime1  = $request->delivery['slot'][$keySlot]['start_time'][0];
                        $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][0];

                        if ($startTime < $endTime) {
                            $newSpecialHour->start_time     = $actualStartTime1;
                            $newSpecialHour->end_time       = $actualEndTime1;
                            $newSpecialHour->save();
                        } else {
                            $errorStatus = 1;
                        }
                    }

                    if ($errorStatus == 0 && $overlapping == 0) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.specialHour.list');
                    } else if ($overlapping > 0) {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_overlap_some_records'));
                        return redirect()->back();
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_some_records'));
                        return redirect()->back();
                    }
                }
            }
            return view('admin.specialHour.add', $data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.specialHour.list')->with('error', $e->getMessage());
        }

    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id = null
    /*****************************************************/
    public function edit(Request $request, $id = null)
    {
        $data['page_title'] = trans('custom_admin.lab_edit_special_hour');
        $data['panel_title']= trans('custom_admin.lab_edit_special_hour');
        
        try
        {
            $data['id']= $id;
            $specialHourDetail = SpecialHour::where('id',$id)->first();

            if ($request->isMethod('POST')) {
                // Checking validation
                $validationCondition = array(
                    'special_date'     => 'required',
                ); // validation condition
                $validationMessages = array(
                    'special_date.required'            => trans('custom_admin.error_date'),
                );
                $validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($validator->fails()) {
                    return \Redirect::route('admin.'.\App::getLocale().'.specialHour.edit', $id)->withErrors($validator)->withInput();
                } else {
                    $specialHourDetail->special_date   = $request->special_date ? date('Y-m-d', strtotime($request->special_date)) : date('Y-m-d');
                    $keySlot = $errorStatus = $overlapping = 0;
                    $specialHourDetail->holiday        = isset($request->delivery['holiday'][$keySlot]) ? $request->delivery['holiday'][$keySlot] : '0';

                    if (count($request->delivery['slot'][$keySlot]['start_time']) > 1) {
                        $betweenTime = 0;

                        foreach ($request->delivery['slot'][$keySlot]['start_time'] as $keySlotTime => $valSlotTime) {
                            if ($keySlotTime == 0) {
                                $startTime1  = strtotime($valSlotTime);
                                $endTime1    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);

                                $actualStartTime1  = $valSlotTime;
                                $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
                            } else {
                                $startTime2  = strtotime($valSlotTime);
                                $endTime2    = strtotime($request->delivery['slot'][$keySlot]['end_time'][$keySlotTime]);

                                $actualStartTime2  = $valSlotTime;
                                $actualEndTime2    = $request->delivery['slot'][$keySlot]['end_time'][$keySlotTime];
                            }
                        }

                        if ( ($startTime1 < $endTime1) && ($startTime2 < $endTime2) ) {
                            // checking overlapping time
                            if ($startTime2 >= $startTime1 && $startTime2 <= $endTime1) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($endTime2 >= $startTime1 && $endTime2 <= $endTime1) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($startTime1 >= $startTime2  && $startTime1 <= $endTime2) {
                                $betweenTime = 1;
                                $overlapping++;
                            }
                            else if ($endTime1 >= $startTime2 && $endTime1 <= $endTime2) {
                                $betweenTime = 1;
                                $overlapping++;
                            }

                            // If all are different time and not with the 2 times
                            if ($betweenTime == 0) {
                                $specialHourDetail->start_time     = $actualStartTime1;
                                $specialHourDetail->end_time       = $actualEndTime1;
                                $specialHourDetail->start_time2    = $actualStartTime2;
                                $specialHourDetail->end_time2      = $actualEndTime2;
                                $specialHourDetail->save();
                            }
                        } else {
                            $errorStatus = 1;
                        }
                    } else {
                        $startTime  = strtotime($request->delivery['slot'][$keySlot]['start_time'][0]);
                        $endTime    = strtotime($request->delivery['slot'][$keySlot]['end_time'][0]);

                        $actualStartTime1  = $request->delivery['slot'][$keySlot]['start_time'][0];
                        $actualEndTime1    = $request->delivery['slot'][$keySlot]['end_time'][0];

                        if ($startTime < $endTime) {
                            $specialHourDetail->start_time     = $actualStartTime1;
                            $specialHourDetail->end_time       = $actualEndTime1;
                            $specialHourDetail->save();
                        } else {
                            $errorStatus = 1;
                        }
                    }

                    if ($errorStatus == 0 && $overlapping == 0) {
                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.specialHour.list');
                    } else if ($overlapping > 0) {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_overlap_some_records'));
                        return redirect()->back();
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_some_records'));
                        return redirect()->back();
                    }
                }
            }
            return view('admin.specialHour.edit')->with(['details' => $specialHourDetail, 'data' => $data]);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.specialHour.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.specialHour.list');
            }

            $specialHourDetails = SpecialHour::where('id', $id)->first();
            if ($specialHourDetails != null) {
                $deleteSpecialHour = SpecialHour::find($id)->delete();
                if ($deleteSpecialHour) {
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

    /*****************************************************/
    # Function name : slotDelete
    # Params        : Request $request, $id
    /*****************************************************/
    public function slotDelete(Request $request, $id = null)
    {
        try
        {
            if ($id == null) {
                return redirect()->route('admin.'.\App::getLocale().'.specialHour.edit', $id);
            }
            $details = SpecialHour::where('id', $id)->first();
            if ($details != null) {
                $details->start_time2   = null;
                $details->end_time2     = null;
                $details->save();
                
                $request->session()->flash('alert-success', trans('custom_admin.success_data_deleted_successfully'));
                return redirect()->back();                
            } else {
                return redirect()->route('admin.'.\App::getLocale().'.specialHour.edit', $id)->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.specialHour.edit', $id)->with('error', $e->getMessage());
        }
    }
}