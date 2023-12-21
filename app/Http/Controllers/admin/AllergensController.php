<?php
/*****************************************************/
# Page/Class name   : AllergensController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use AdminHelper;
use App\Models\Allergen;
use App\Models\AllergenLocal;
use Image;

class AllergensController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_allergen_list');
        $data['panel_title']= trans('custom_admin.lab_allergen_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);
            
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';
            
            $query = Allergen::whereNull('deleted_at');

            $data['searchText'] = $key = $request->searchText;

            if ($key) {
                // if the search key is provided, proceed to build query for search
                $query->where(function ($q) use ($key) {
                    $q->where('title', 'LIKE', '%' . $key . '%');
                    
                });
            }
            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])
                                            ->paginate(AdminHelper::ADMIN_LIST_LIMIT);
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }       
            return view('admin.allergen.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_allergen_list');
        $data['panel_title']= trans('custom_admin.lab_allergen_list');
        
        try
        {
            $data['order_by']   = 'created_at';
            $data['order']      = 'desc';
            
            $query = Allergen::whereNull('deleted_at');

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
            return view('admin.allergen.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.allergen.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_allergen');
        $data['panel_title']    = trans('custom_admin.lab_add_allergen');
    
        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'title'     => 'required|min:2|max:255|unique:'.(new Allergen)->getTable().',title',
                    'title_de'  => 'required|min:2|max:255|unique:'.(new AllergenLocal)->getTable().',local_title',
                    'image'     => 'required',
                    'image'     => 'dimensions:min_width='.AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_WIDTH.', min_height='.AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_HEIGHT,
                    'image'     => 'mimes:jpeg,jpg,png,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE,

				);
				$validationMessages = array(
					'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    'image.required'            => trans('custom_admin.error_image'),
                    'image.dimensions'          => trans('custom_admin.error_image_dimension'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.allergen.add')->withErrors($Validator)->withInput();
				} else {
                    $newSlug = Helper::generateUniqueSlug(new Allergen(), $request->title);

                    $gettingLastSortedCount = Allergen::select('sort')->whereNull('deleted_at')->orderBy('sort','desc')->first();
                    $newSort = isset($gettingLastSortedCount->sort) ? ($gettingLastSortedCount->sort + 1) : 0;

                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat =  $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename  = 'allergen_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                        
                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/allergen/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/allergen/thumbs/' . $filename));
                    }

                    $new = new Allergen;
                    $new->title = trim($request->title, ' ');
                    $new->sort  = $newSort;
                    $new->slug  = $newSlug;
                    $new->image = $filename;
                    $save = $new->save();
                
					if ($save) {	
                        $insertedId = $new->id;

                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal = new AllergenLocal;
                            $newLocal->allergen_id   = $insertedId;
                            $newLocal->lang_code= $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

						$request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
						return redirect()->route('admin.'.\App::getLocale().'.allergen.list');
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			return view('admin.allergen.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function edit(Request $request, $id = null) {
        $data['page_title']     = trans('custom_admin.lab_edit_allergen');
        $data['panel_title']    = trans('custom_admin.lab_edit_allergen');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = Allergen::find($id);
            $data['id'] = $id;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.allergen.list');
                }
                $validationCondition = array(
                    'title'     => 'required|min:2|max:255|unique:' .(new Allergen)->getTable().',title,' .$id,
                    'title_de'  => 'required|min:2|max:255|unique:'.(new AllergenLocal)->getTable().',local_title,' .$id.',allergen_id',
                    'image'     => 'dimensions:min_width='.AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_WIDTH.', min_height='.AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_HEIGHT,
                    'image'     => 'mimes:jpeg,jpg,png,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE,
                );
                $validationMessages = array(
                    'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    'image.dimensions'          => trans('custom_admin.error_image_dimension'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $newSlug = Helper::generateUniqueSlug(new Allergen(), $request->title, $id);

                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat = $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'allergen_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/allergen/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_ALLERGEN_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/allergen/thumbs/' . $filename));
                        
                        $largeImage = public_path().'/uploads/allergen/'.$details->image;
                        @unlink($largeImage);
                        $thumbImage = public_path().'/uploads/allergen/thumbs/'.$details->image;
                        @unlink($thumbImage);
                        $update['image'] = $filename;
                    }

                    $update['title'] = trim($request->title, ' ');
                    $update['slug']  = $newSlug;
                    $save = Allergen::where('id', $id)->update($update);                        
                    if ($save) {
                        // Allergen local
                        AllergenLocal::where('allergen_id', $id)->delete();
                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal           = new AllergenLocal;
                            $newLocal->allergen_id = $id;
                            $newLocal->lang_code= $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.allergen.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.allergen.list', ['page' => $pageNo]);
                    }
                }
            }
            
            $details = Allergen::find($id);
            $data['id'] = $id;
            return view('admin.allergen.edit')->with(['details' => $details, 'data' => $data]);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.allergen.list');
            }
            $details = Allergen::where('id', $id)->first();
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
                return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.allergen.list');
            }

            $details = Allergen::where('id', $id)->first();
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
            return redirect()->route('admin.'.\App::getLocale().'.allergen.list')->with('error', $e->getMessage());
        }
    }
    
}