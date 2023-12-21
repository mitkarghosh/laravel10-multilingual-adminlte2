<?php
/*****************************************************/
# Page/Class name   : FaqsController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use AdminHelper;
use App\Models\Faq;
use App\Models\FaqLocal;

class FaqsController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_faq');
        $data['panel_title']= trans('custom_admin.lab_faq');
        
        try
        {
            $data['order_by']   = 'sort';
            $data['order']      = 'desc';

            $query = Faq::whereNull('deleted_at');

            $data['searchText'] = $key = $request->searchText;

            if ($key) {
                // if the search key is provided, proceed to build query for search
                $query->where(function ($q) use ($key) {
                    $q->where('title', 'LIKE', '%' . $key . '%');
                    
                });
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->paginate(AdminHelper::ADMIN_LIST_LIMIT);
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.faq.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_faq');
        $data['panel_title']= trans('custom_admin.lab_faq');
        
        try
        {
            $data['order_by']   = 'sort';
            $data['order']      = 'desc';

            $query = Faq::whereNull('deleted_at');

            $data['searchText'] = $key = $request->searchText;

            if ($key) {
                // if the search key is provided, proceed to build query for search
                $query->where(function ($q) use ($key) {
                    $q->where('title', 'LIKE', '%' . $key . '%');
                    
                });
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.faq.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.faq.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_faq');
        $data['panel_title']    = trans('custom_admin.lab_add_faq');
    
        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'title' => 'required|min:2|max:255|unique:'.(new Faq)->getTable().',title',
                    'title_de'  => 'required|min:2|max:255|unique:'.(new FaqLocal)->getTable().',local_title',
                    'description_en'	=> 'required|min:10',
                    'description_de' 	=> 'required|min:10',
				);
				$validationMessages = array(
					'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    'description_en.required'	=> trans('custom_admin.error_description'),
					'description_en.min'        => trans('custom_admin.error_description_min'),
					'description_de.required'	=> trans('custom_admin.error_description_de'),
                    'description_de.min'     	=> trans('custom_admin.error_description_de_min'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.faq.add')->withErrors($Validator)->withInput();
				} else {
                    $newSlug = Helper::generateUniqueSlug(new Faq(), $request->title);

                    $gettingLastSortedCount = Faq::select('sort')->whereNull('deleted_at')->orderBy('sort','desc')->first();
                    $newSort = isset($gettingLastSortedCount->sort) ? ($gettingLastSortedCount->sort + 1) : 0;

                    $new = new Faq;
                    $new->title = trim($request->title, ' ');
                    $new->slug  = $newSlug;
                    $new->sort  = $newSort;
                    $save = $new->save();                
					if ($save) {
                        $insertedId = $new->id;

                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal = new FaqLocal;
                            $newLocal->faq_id       = $insertedId;
                            $newLocal->lang_code    = $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                                $newLocal->local_description = trim($request->description_en, ' ');
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                                $newLocal->local_description = trim($request->description_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

                        $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
						return redirect()->route('admin.'.\App::getLocale().'.faq.list');
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			return view('admin.faq.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function edit(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_edit_faq');
        $data['panel_title']= trans('custom_admin.lab_edit_faq');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = Faq::find($id);
            $data['id'] = $id;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.faq.list');
                }
                $validationCondition = array(
                    'title'             => 'required|min:2|max:255|unique:' .(new Faq)->getTable().',title,' .$id,
                    'title_de'          => 'required|min:2|max:255|unique:'.(new FaqLocal)->getTable().',local_title,' .$id.',faq_id',
                    'description_en'	=> 'required|min:10',
                    'description_de' 	=> 'required|min:10',
                );
                $validationMessages = array(
                    'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    'description_en.required'	=> trans('custom_admin.error_description'),
					'description_en.min'        => trans('custom_admin.error_description_min'),
					'description_de.required'	=> trans('custom_admin.error_description_de'),
                    'description_de.min'     	=> trans('custom_admin.error_description_de_min'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $newSlug = Helper::generateUniqueSlug(new Faq(), $request->title, $id);

                    $update['title'] = trim($request->title, ' ');
                    $update['slug']  = $newSlug;

                    $save = Faq::where('id', $id)->update($update);                        
                    if ($save) {
                        // Faq local
                        FaqLocal::where('faq_id', $id)->delete();
                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal           = new FaqLocal;
                            $newLocal->faq_id = $id;
                            $newLocal->lang_code= $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                                $newLocal->local_description = trim($request->description_en, ' ');
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                                $newLocal->local_description = trim($request->description_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.faq.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.faq.list', ['page' => $pageNo]);
                    }
                }
            }
            return view('admin.faq.edit')->with(['details' => $details, 'data' => $data]);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.faq.list');
            }
            $details = Faq::where('id', $id)->first();
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
                return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.faq.list');
            }

            $details = Faq::where('id', $id)->first();
            if ($details != null) {
                $delete = $details->delete();
                if ($delete) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }

                // Checking this subject is already assigned to build package
                // $teacherSubjectCount = TeacherSubject::where('subject_id', $id)->count();
                // if ($teacherSubjectCount > 0) {
                //     $request->session()->flash('alert-warning', 'This subject is already assigned to teacher');
                //     return redirect()->back();
                // }
                
                // Checking this subject is already assigned to build package
                // $packageDurationCount = PackageSubject::where('subject_id', $id)->count();
                // if ($packageDurationCount > 0) {
                //     $request->session()->flash('alert-warning', 'This subject is already assigned to build package');
                // } else {
                //     $delete = $details->delete();
                //     if ($delete) {
                //         $request->session()->flash('alert-danger', 'Subject has been deleted successfully');
                //     } else {
                //         $request->session()->flash('alert-danger', 'An error occurred while deleting the subject');
                //     }
                // }
                return redirect()->back();

            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.faq.list')->with('error', $e->getMessage());
        }
    }
    
}