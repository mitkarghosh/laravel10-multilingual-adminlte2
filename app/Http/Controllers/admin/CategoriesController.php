<?php
/*****************************************************/
# Page/Class name   : CategoriesController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use AdminHelper;
use App\Models\Category;
use App\Models\CategoryLocal;
use App\Models\Product;
use Image;

class CategoriesController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_category_list');
        $data['panel_title']= trans('custom_admin.lab_category_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);
            
            $data['order_by']   = 'sort';
            $data['order']      = 'asc';

            $query = Category::whereNull('deleted_at');

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
            return view('admin.category.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_category_list');
        $data['panel_title']= trans('custom_admin.lab_category_list');
        
        try
        {
            $data['order_by']   = 'sort';
            $data['order']      = 'asc';

            $query = Category::whereNull('deleted_at');

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
            return view('admin.category.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.show-all')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_category');
        $data['panel_title']    = trans('custom_admin.lab_add_category');
    
        try
        {
        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    'title'     => 'required|min:2|max:255|unique:'.(new Category)->getTable().',title',
                    'title_de'  => 'required|min:2|max:255|unique:'.(new CategoryLocal)->getTable().',local_title',
                    // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE.'|dimensions:min_width='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT,
                    // 'image' => 'image|dimensions:min_width='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT,
				);
				$validationMessages = array(
					'title.required'    => trans('custom_admin.error_title'),
					'title.min'         => trans('custom_admin.error_title_min'),
                    'title.max'         => trans('custom_admin.error_title_max'),
                    'title_de.required' => trans('custom_admin.error_title_dutch'),
                    'title_de.min'      => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'      => trans('custom_admin.error_title_dutch_max'),
                    // 'image.dimensions'  => trans('custom_admin.error_image_dimension'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.category.add')->withErrors($Validator)->withInput();
				} else {
                    $newSlug = Helper::generateUniqueSlug(new Category(), $request->title);

                    $gettingLastSortedCount = Category::select('sort')->whereNull('deleted_at')->orderBy('sort','desc')->first();
                    $newSort = isset($gettingLastSortedCount->sort) ? ($gettingLastSortedCount->sort + 1) : 0;

                    $new = new Category;

                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat =  $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename  = 'category_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                        
                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/category/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/category/thumbs/' . $filename));

                        $new->image = $filename;
                    }

                    $new->title = trim($request->title, ' ');
                    $new->sort = $newSort;
                    $new->slug  = $newSlug;
                    $save = $new->save();
					if ($save) {
                        $insertedId = $new->id;

                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal               = new CategoryLocal;
                            $newLocal->category_id  = $insertedId;
                            $newLocal->lang_code    = $language;
                            if ($language == 'EN') {
                                $newLocal->local_title   = trim($request->title, ' ');
                            } else {
                                $newLocal->local_title   = trim($request->title_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

						$request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
						return redirect()->route('admin.'.\App::getLocale().'.category.list');
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			return view('admin.category.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function edit(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_edit_category');
        $data['panel_title']= trans('custom_admin.lab_edit_category');

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = Category::find($id);
            $data['id'] = $id;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.category.list');
                }
                $validationCondition = array(
                    'title'  => 'required|min:2|max:255|unique:' .(new Category)->getTable().',title,' .$id,
                    'title_de'  => 'required|min:2|max:255|unique:'.(new CategoryLocal)->getTable().',local_title,' .$id.',category_id',
                    // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE.'|dimensions:min_width='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT,
                    // 'image' => 'dimensions:min_width='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT,
                );
                $validationMessages = array(
                    'title.required'    => trans('custom_admin.error_title'),
					'title.min'         => trans('custom_admin.error_title_min'),
                    'title.max'         => trans('custom_admin.error_title_max'),
                    'title_de.required' => trans('custom_admin.error_title_dutch'),
                    'title_de.min'      => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'      => trans('custom_admin.error_title_dutch_max'),
                    // 'image.dimensions'  => trans('custom_admin.error_image_dimension'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    $newSlug = Helper::generateUniqueSlug(new Category(), $request->title, $id);

                    $update = array(
                        'title'  => trim($request->title, ' '),
                        'slug'  => $newSlug
                    );

                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat = $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'category_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/category/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_CATEGORY_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/category/thumbs/' . $filename));
                        
                        $largeImage = public_path().'/uploads/category/'.$details->image;
                        @unlink($largeImage);
                        $thumbImage = public_path().'/uploads/category/thumbs/'.$details->image;
                        @unlink($thumbImage);
                        $update['image'] = $filename;
                    }

                    $save = Category::where('id', $id)->update($update);                        
                    if ($save) {
                        // Category local
                        CategoryLocal::where('category_id', $id)->delete();
                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal               = new CategoryLocal;
                            $newLocal->category_id  = $id;
                            $newLocal->lang_code    = $language;
                            if ($language == 'EN') {
                                $newLocal->local_title   = trim($request->title, ' ');
                            } else {
                                $newLocal->local_title   = trim($request->title_de, ' ');
                            }
                            $saveLocal = $newLocal->save();
                        }

                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.category.list', ['page' => $pageNo]);
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.category.list', ['page' => $pageNo]);
                    }
                }
            }
            
            return view('admin.category.edit')->with(['details' => $details, 'data' => $data]);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.category.list');
            }
            $details = Category::where('id', $id)->first();
            if ($details != null) {
                if ($details->status == 1) {
                    // Checking this Category is already assigned to product
                    // $productCount = Product::where('category_id', $id)->whereNull('deleted_at')->count();
                    // if ($productCount > 0) {
                    //     $request->session()->flash('alert-warning', trans('custom_admin.error_category_exist_in_product'));
                    //     return redirect()->back();
                    // }

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
                return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.category.list');
            }

            $details = Category::where('id', $id)->first();
            if ($details != null) {
                // Checking this Category is already assigned to product
                $productCount = Product::where('category_id', $id)->whereNull('deleted_at')->count();
                if ($productCount > 0) {
                    $request->session()->flash('alert-warning', trans('custom_admin.error_category_exist_in_product'));
                    return redirect()->back();
                }

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
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : sortCategory
    # Params        : Request $request
    /*****************************************************/
    public function sortCategory(Request $request)
    {
        $data['page_title'] = trans('custom_admin.lab_sort_category');
        $data['panel_title']= trans('custom_admin.lab_sort_category');
        
        try
        {
            $data['order_by']   = 'sort';
            $data['order']      = 'asc';

            $query = Category::whereNull('deleted_at');

            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }
            return view('admin.category.sort_category', $data);
            
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : saveSortCategory
    # Params        : Request $request
    /*****************************************************/
    public function saveSortCategory(Request $request)
    {
        if ($request->isMethod('POST')) {
            // dd($request->order);
            foreach ($request->order as $sort => $id) {
                Category::where(['id' => $id])->update(['sort' => $sort]);
            }
            $result = array('status' => 1, 'message' => trans('custom_admin.success_sorted_successfully'));
        } else {
            $result = array('status' => 0, 'message' => trans('custom_admin.error_something_went_wrong'));
        }
        echo json_encode($result);
        exit;
    }

    /*****************************************************/
    # Function name : sortProduct
    # Params        : Request $request
    /*****************************************************/
    public function sortProduct(Request $request, $categoryId = null)
    {
        $data['page_title'] = trans('custom_admin.lab_sort_product');
        $data['panel_title']= trans('custom_admin.lab_sort_product');
        
        try
        {
            $data['order_by']   = 'sort';
            $data['order']      = 'asc';

            $data['categoryId'] = $categoryId;
            $data['details']    = Category::find($categoryId);

            $query = Product::where(['category_id' => $categoryId])->whereNull('deleted_at');

            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }
            return view('admin.category.sort_product', $data);
        
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.category.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : saveSortProduct
    # Params        : Request $request
    /*****************************************************/
    public function saveSortProduct(Request $request)
    {
        if ($request->isMethod('POST')) {
            foreach ($request->order as $sort => $id) {
                Product::where(['id' => $id])->update(['sort' => $sort]);
            }
            $result = array('status' => 1, 'message' => trans('custom_admin.success_sorted_successfully'));
        } else {
            $result = array('status' => 0, 'message' => trans('custom_admin.error_something_went_wrong'));
        }
        echo json_encode($result);
        exit;
    }
    
}