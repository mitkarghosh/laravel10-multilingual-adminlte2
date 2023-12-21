<?php
/*****************************************************/
# Page/Class name   : ProductsController
/*****************************************************/
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use AdminHelper;
use App\Models\Category;
use App\Http\Helpers\Helper as HelpersHelper;
use App\Models\ProductAddon;
use App\Models\Product;
use App\Models\ProductLocal;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeLocal;
use App\Models\ProductTag;
use App\Models\ProductMenuTitle;
use App\Models\ProductMenuTitleLocal;
use App\Models\ProductMenuValue;
use App\Models\ProductMenuValueLocal;
use Image;

class ProductsController extends Controller
{
    /*****************************************************/
    # Function name : list
    # Params        : Request $request
    /*****************************************************/
    public function list(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_product_list');
        $data['panel_title']= trans('custom_admin.lab_product_list');
        
        try
        {
            $pageNo = $request->input('page');
            Session::put('pageNo',$pageNo);
            Session::put('searchUrl','');
            
            $data['order_by']   = 'id';
            $data['order']      = 'desc';

            $query = Product::whereNull('deleted_at');

            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                // if the search key is provided, proceed to build query for search
                $query->where(function ($q) use ($key) {
                    $q->where('title', 'LIKE', '%' . $key . '%');
                });
            }
            $data['category'] = $category = $request->category;
            if ($category) {
                $query->whereIn('category_id', $category);
            }

            if ($request->searchText || $request->category) {
                Session::put('searchUrl',\Request::fullUrl());
            }

            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->paginate(AdminHelper::ADMIN_LIST_LIMIT);
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }
            $data['categoryList'] = Category::whereNull('deleted_at')->get();
            return view('admin.product.list', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : showAll
    # Params        : Request $request
    /*****************************************************/
    public function showAll(Request $request) {
        $data['page_title'] = trans('custom_admin.lab_product_list');
        $data['panel_title']= trans('custom_admin.lab_product_list');
        
        try
        {
            $data['order_by']   = 'id';
            $data['order']      = 'desc';

            $query = Product::whereNull('deleted_at');

            $data['searchText'] = $key = $request->searchText;
            if ($key) {
                // if the search key is provided, proceed to build query for search
                $query->where(function ($q) use ($key) {
                    $q->where('title', 'LIKE', '%' . $key . '%');
                });
            }
            $data['category'] = $category = $request->category;
            if ($category) {
                $query->whereIn('category_id', $category);
            }

            $exists = $query->count();
            if ($exists > 0) {
                $list = $query->orderBy($data['order_by'], $data['order'])->get();
                $data['list'] = $list;
            } else {
                $data['list'] = array();
            }
            $data['categoryList'] = Category::whereNull('deleted_at')->get();
            return view('admin.product.show_all', $data);
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.show-all')->with('error', $e->getMessage());
        }
    }
 

    /*****************************************************/
    # Function name : add
    # Params        : Request $request
    /*****************************************************/
    public function add(Request $request) {
        $data['page_title']     = trans('custom_admin.lab_add_product');
        $data['panel_title']    = trans('custom_admin.lab_add_product');
    
        try
        {   
            $data['categoryList']   = AdminHelper::getCategoriesLocal();
            $data['tagList']        = AdminHelper::getTags();

            $data['addon_list'] = ProductAddon::where('parent_id','=',0)->get();  
            $data['sub_addon_list'] = ProductAddon::where('parent_id','>',0)->get();  
    
            $data['addon_list_dropdown']=AdminHelper::addonDropDown($data['addon_list']);
            $data['sub_addon_list_dropdonw']=AdminHelper::addonDropDown($data['sub_addon_list']);
    

        	if ($request->isMethod('POST'))
        	{
				$validationCondition = array(
                    // 'title'         => 'required|min:2|max:255|unique:'.(new Product)->getTable().',title',
                    // 'title_de'      => 'required|min:2|max:255|unique:'.(new ProductLocal)->getTable().',local_title',
                    // 'description_en'	=> 'required|min:10',
                    // 'description_de' 	=> 'required|min:10',
                    'title'         => 'required|min:2|max:255|',
                    'title_de'      => 'required|min:2|max:255',
                    'category_id'       => 'required',
					// 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE.'|dimensions:min_width='.AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT,
				);
				$validationMessages = array(
                    'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    // 'description_en.required'	=> trans('custom_admin.error_description'),
					// 'description_en.min'        => trans('custom_admin.error_description_min'),
					// 'description_de.required'	=> trans('custom_admin.error_description_de'),
                    // 'description_de.min'     	=> trans('custom_admin.error_description_de_min'),
                    'category_id'               => trans('custom_admin.error_category'),
					// 'image.dimensions'          => trans('custom_admin.error_image_dimension'),
				);

				$Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
				if ($Validator->fails()) {
					return redirect()->route('admin.'.\App::getLocale().'.product.add')->withErrors($Validator)->withInput();
				} else {
                    // dd($request);
                    $newSlug = Helper::generateUniqueSlug(new Product(), $request->title);

                    $new = new Product;

                    $gettingLastSortedCount = Product::select('sort')->where(['category_id' => $request->category_id])->whereNull('deleted_at')->orderBy('sort','desc')->first();
                    $newSort = isset($gettingLastSortedCount->sort) ? ($gettingLastSortedCount->sort + 1) : 0;

                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat =  $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename  = 'product_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                        
                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/product/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/product/thumbs/' . $filename));

                        $new->image         = $filename;
                    }
                    
                    $new->category_id       = $request->category_id;
                    $new->title             = trim($request->title, ' ');
                    $new->slug              = $newSlug;
                    $new->has_attribute     = $request->has_attribute;
                    if ($request->has_attribute == 'Y') {
                        $new->price             = null;
                        $new->show_ingredients  = 'N';
                    } else {
                        $new->price             = isset($request->price) ? $request->price : null;
                        $new->show_ingredients  = isset($request->show_ingredients) ? $request->show_ingredients : 'N';
                    }
                    $new->is_menu           = isset($request->is_menu) ? $request->is_menu : 'N';
                    $new->sort              = $newSort;
                    $save = $new->save();
					if ($save) {
                        $insertedId = $new->id;
                        // Product local
                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach($languages as $language){
                            $newLocal = new ProductLocal;
                            $newLocal->product_id   = $insertedId;
                            $newLocal->lang_code    = $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                                $newLocal->local_description = isset($request->description_en) ? trim($request->description_en, ' ') : null;
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                                $newLocal->local_description = isset($request->description_de) ? trim($request->description_de, ' ') : null;
                            }
                            $saveLocal = $newLocal->save();
                        }
                        // Attribute local
                        if ($request->has_attribute == 'Y') {
                            foreach ($request->attr_title_en as $keyAttribute => $valueAttribute) {
                                $gettingAttributeLastSortedCount = ProductAttribute::select('sort')->where(['product_id' => $insertedId])->whereNull('deleted_at')->orderBy('sort','desc')->first();
                                $newAttributeSort = isset($gettingAttributeLastSortedCount->sort) ? ($gettingAttributeLastSortedCount->sort + 1) : 0;
                                // Inserting into product attribute table
                                $newProductAttribute                = new ProductAttribute;
                                $newProductAttribute->product_id    = $insertedId;
                                $newProductAttribute->title         = $valueAttribute;
                                $newProductAttribute->price         = $request->attr_price[$keyAttribute];
                                $newProductAttribute->sort          = $newAttributeSort;
                                $saveProductAttributeLocal          = $newProductAttribute->save();
                                if ($saveProductAttributeLocal) {
                                    // Inserting into product attribute local table
                                    foreach ($languages as $language) {
                                        $newProductAttributeLocal                       = new ProductAttributeLocal;
                                        $newProductAttributeLocal->product_id           = $insertedId;
                                        $newProductAttributeLocal->product_attribute_id = $newProductAttribute->id;
                                        $newProductAttributeLocal->lang_code            = $language;
                                        if ($language == 'EN') {
                                            $newProductAttributeLocal->local_title = trim($request->attr_title_en[$keyAttribute], ' ');
                                        } else {
                                            $newProductAttributeLocal->local_title = trim($request->attr_title_de[$keyAttribute], ' ');
                                        }
                                        $saveProductAttributeLocal = $newProductAttributeLocal->save();
                                    }
                                }
                            }
                        }
                        // Is menu
                        if ($request->is_menu) {                            
                            foreach ($request->dropdown as $keyDropdown => $valueDropdown) {
                                // Inserting into product menu title
                                $newProductMenuTitle                = new ProductMenuTitle;
                                $newProductMenuTitle->product_id    = $insertedId;
                                $newProductMenuTitle->title         = trim($valueDropdown['title_en'],' ');
                                $newProductMenuTitle->is_multiple   = isset($valueDropdown['is_multiple']) ? $valueDropdown['is_multiple'] : 'N';
                                $newProductMenuTitle->addon_id         = trim($valueDropdown['main_addon']);
                                $saveProductMenuTitle               = $newProductMenuTitle->save();                               
                                if ($saveProductMenuTitle) {
                                    // Inserting into product menu local table
                                    foreach ($languages as $language) {
                                        $newProductMenuTitleLocal                           = new ProductMenuTitleLocal;
                                        $newProductMenuTitleLocal->product_id               = $insertedId;
                                        $newProductMenuTitleLocal->product_menu_title_id    = $newProductMenuTitle->id;
                                        $newProductMenuTitleLocal->lang_code                = $language;
                                      //  $newProductMenuTitleLocal->addon_id         = trim($valueDropdown['main_addon']);
                                        if ($language == 'EN') {
                                            $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_en'], ' ');
                                        } else {
                                            $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_de'], ' ');
                                        }
                                        $saveProductMenuTitleLocal = $newProductMenuTitleLocal->save();
                                    }
        
                                    // Inserting into product value
                                    foreach ($valueDropdown['val_en'] as $keyValue => $valueValue) {
                                        // Inserting into product value
                                        $newProductMenuValue                        = new ProductMenuValue;
                                        $newProductMenuValue->product_id            = $insertedId;
                                        $newProductMenuValue->product_menu_title_id = $newProductMenuTitle->id;
                                        $newProductMenuValue->title                 = trim($valueValue,' ');
                                        $newProductMenuValue->sub_addon_id         = trim($valueDropdown['value_sub_addon_id'][$keyValue]);
                                        $newProductMenuValue->sub_addon_status=  trim($valueDropdown['value_sub_addon_status'][$keyValue]);
                                        $newProductMenuValue->price                 = trim($valueDropdown['val_price'][$keyValue],' ');

                                        $saveProductMenuValue                       = $newProductMenuValue->save();
        
                                        // Inserting into product menu local table
                                        foreach ($languages as $language) {
                                            $newProductMenuValueLocal                           = new ProductMenuValueLocal;
                                            $newProductMenuValueLocal->product_id               = $insertedId;
                                            $newProductMenuValueLocal->product_menu_title_id    = $newProductMenuTitle->id;
                                            $newProductMenuValueLocal->product_menu_value_id    = $newProductMenuValue->id;
                                            $newProductMenuValueLocal->lang_code                = $language;
                                            if ($language == 'EN') {
                                                $newProductMenuValueLocal->local_title = trim($valueValue, ' ');
                                            } else {
                                                $newProductMenuValueLocal->local_title = trim($valueDropdown['val_de'][$keyValue], ' ');
                                            }
                                            $saveProductMenuValueLocal = $newProductMenuValueLocal->save();
                                        }
                                    }
                                }
                            }
                        }
                        // Product tags
                        if (isset($request->tags)) {
                            if (count($request->tags) > 0) {
                                foreach ($request->tags as $tag) {
                                    $newProductTag = new ProductTag;
                                    $newProductTag->product_id  = $insertedId;
                                    $newProductTag->tag_id  = $tag;
                                    $newProductTag->save();
                                }
                            }
                        }
                        
						$request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
                        return redirect()->route('admin.'.\App::getLocale().'.product.list');                        
					} else {
						$request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
						return redirect()->back();
					}
				}
			}
			return view('admin.product.add', $data);
		} catch (Exception $e) {
			return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
		}
    }

    /*****************************************************/
    # Function name : edit
    # Params        : Request $request, $id
    /*****************************************************/
    public function copy(Request $request, $id = null) {
        $id=base64_decode($id);
        $data['page_title'] = trans('custom_admin.lab_copy_product');
        $data['panel_title']= trans('custom_admin.lab_copy_product');

        $data['addon_list'] = ProductAddon::where('parent_id','=',0)->get();  
        $data['sub_addon_list'] = ProductAddon::where('parent_id','>',0)->get();  

        $data['addon_list_dropdown']=AdminHelper::addonDropDown($data['addon_list']);
        $data['sub_addon_list_dropdonw']=AdminHelper::addonDropDown($data['sub_addon_list']);
            
        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = Product::find($id);
            $data['id'] = $id;
            $data['categoryList']   = AdminHelper::getCategoriesLocal();
            $data['tagList']        = AdminHelper::getTags();
            $productTagIds = [];
            foreach ($details->productTags as $pt) {
                $productTagIds[] = $pt->tag_id;
            }
            $data['productTagIds'] = $productTagIds;
 
            return view('admin.product.copy')->with(['details' => $details, 'data' => $data])->with($data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
        }
    }
  


    public function edit(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_edit_product');
        $data['panel_title']= trans('custom_admin.lab_edit_product');



        $data['addon_list'] = ProductAddon::where('parent_id','=',0)->get();  
        $data['sub_addon_list'] = ProductAddon::where('parent_id','>',0)->get();  

        $data['addon_list_dropdown']=AdminHelper::addonDropDown($data['addon_list']);
        $data['sub_addon_list_dropdonw']=AdminHelper::addonDropDown($data['sub_addon_list']);

        try
        {           
            $pageNo = Session::get('pageNo') ? Session::get('pageNo') : '';
            $data['pageNo'] = $pageNo;

            $details = Product::find($id);
            $data['id'] = $id;
            $data['categoryList']   = AdminHelper::getCategoriesLocal();
            $data['tagList']        = AdminHelper::getTags();
            $productTagIds = [];
            foreach ($details->productTags as $pt) {
                $productTagIds[] = $pt->tag_id;
            }
            $data['productTagIds'] = $productTagIds;

            if ($request->isMethod('POST')) {
                if ($id == null) {
                    return redirect()->route('admin.'.\App::getLocale().'.product.list');
                }
                $validationCondition = array(
                    // 'title'             => 'required|min:2|max:255|unique:'.(new Product)->getTable().',title,' .$id,
                    // 'title_de'          => 'required|min:2|max:255|unique:'.(new ProductLocal)->getTable().',local_title,' .$id.',product_id',
                    // 'description_en'	=> 'required|min:10',
                    // 'description_de' 	=> 'required|min:10',
                    'title'             => 'required|min:2|max:255',
                    'title_de'          => 'required|min:2|max:255',
                    'category_id'       => 'required',
                    // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:'.AdminHelper::ICON_MAX_UPLOAD_SIZE.'|dimensions:min_width='.AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH.',min_height='.AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT,
                );
                $validationMessages = array(
                    'title.required'            => trans('custom_admin.error_title'),
					'title.min'                 => trans('custom_admin.error_title_min'),
                    'title.max'                 => trans('custom_admin.error_title_max'),
                    'title_de.required'         => trans('custom_admin.error_title_dutch'),
                    'title_de.min'              => trans('custom_admin.error_title_dutch_min'),
                    'title_de.max'              => trans('custom_admin.error_title_dutch_max'),
                    // 'description_en.required'	=> trans('custom_admin.error_description'),
					// 'description_en.min'        => trans('custom_admin.error_description_min'),
					// 'description_de.required'	=> trans('custom_admin.error_description_de'),
                    // 'description_de.min'     	=> trans('custom_admin.error_description_de_min'),
                    'category_id'               => trans('custom_admin.error_category'),
                    // 'image.dimensions'          => trans('custom_admin.error_image_dimension'),
                );
                
                $Validator = \Validator::make($request->all(), $validationCondition, $validationMessages);
                if ($Validator->fails()) {
                    return redirect()->back()->withErrors($Validator)->withInput();
                } else {
                    // dd($request);
                    $newSlug = Helper::generateUniqueSlug(new Product(), $request->title, $id);
                    
                    $image = $request->file('image');
                    if ($image != '') {
                        $originalFileNameCat = $image->getClientOriginalName();
                        $extension = pathinfo($originalFileNameCat, PATHINFO_EXTENSION);
                        $filename = 'product_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;

                        $image_resize = Image::make($image->getRealPath());
                        $image_resize->save(public_path('uploads/product/' . $filename));
                        $image_resize->resize(AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_WIDTH, AdminHelper::ADMIN_PRODUCT_THUMB_IMAGE_HEIGHT, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image_resize->save(public_path('uploads/product/thumbs/' . $filename));
                        
                        $largeImage = public_path().'/uploads/product/'.$details->image;
                        @unlink($largeImage);
                        $thumbImage = public_path().'/uploads/product/thumbs/'.$details->image;
                        @unlink($thumbImage);
                        $update['image'] = $filename;
                    }

                    $update['category_id']      = $request->category_id;
                    $update['title']            = trim($request->title, ' ');
                    $update['slug']             = $newSlug;
                    $update['has_attribute']    = $request->has_attribute;
                    if ($request->has_attribute == 'Y') {
                        $update['price']            = null;
                        $update['show_ingredients'] = 'N';    
                    } else {
                        $update['price']            = isset($request->price) ? $request->price : null;
                        $update['show_ingredients'] = isset($request->show_ingredients) ? $request->show_ingredients : 'N';
                    }
                    $update['is_menu']          = isset($request->is_menu) ? $request->is_menu : 'N';

                    $save = Product::where('id', $id)->update($update);
                    if ($save) {
                        // Product local
                        ProductLocal::where('product_id', $id)->delete();
                        $languages = AdminHelper::WEBITE_LANGUAGES;
                        foreach ($languages as $language) {
                            $newLocal = new ProductLocal;
                            $newLocal->product_id   = $id;
                            $newLocal->lang_code    = $language;
                            if ($language == 'EN') {
                                $newLocal->local_title = trim($request->title, ' ');
                                $newLocal->local_description = isset($request->description_en) ? trim($request->description_en, ' ') : null;
                            } else {
                                $newLocal->local_title = trim($request->title_de, ' ');
                                $newLocal->local_description = isset($request->description_de) ? trim($request->description_de, ' ') : null;
                            }
                            $saveLocal = $newLocal->save();
                        }

                        // Attribute Exist
                        if ($request->has_attribute == 'Y') {
                            foreach ($request->attr_title_en as $keyAttribute => $valueAttribute) {
                                // for existing attributes (Values will update)
                                if (array_key_exists($keyAttribute, $request->attr_id)) {
                                    $attributeDetails = ProductAttribute::where(['id' => $request->attr_id[$keyAttribute], 'product_id' => $id])->first();                                    
                                    $attributeDetails->title        = $valueAttribute;
                                    $attributeDetails->price        = $request->attr_price[$keyAttribute];
                                    $updateProductAttributeLocal    = $attributeDetails->save();
                                    if ($updateProductAttributeLocal) {
                                        ProductAttributeLocal::where(['product_id' => $id, 'product_attribute_id' => $request->attr_id[$keyAttribute]])->forceDelete();
                                        // Inserting into product attribute local table
                                        foreach ($languages as $language) {
                                            $newProductAttributeLocal                       = new ProductAttributeLocal;
                                            $newProductAttributeLocal->product_id           = $id;
                                            $newProductAttributeLocal->product_attribute_id = $request->attr_id[$keyAttribute];
                                            $newProductAttributeLocal->lang_code            = $language;
                                            if ($language == 'EN') {
                                                $newProductAttributeLocal->local_title = trim($request->attr_title_en[$keyAttribute], ' ');
                                            } else {
                                                $newProductAttributeLocal->local_title = trim($request->attr_title_de[$keyAttribute], ' ');
                                            }
                                            $saveProductAttributeLocal = $newProductAttributeLocal->save();
                                        }
                                    }
                                }
                                // for new attributes (will add new attributes)
                                else {
                                    $gettingAttributeLastSortedCount = ProductAttribute::select('sort')->where(['product_id' => $id])->whereNull('deleted_at')->orderBy('sort','desc')->first();
                                    $newAttributeSort = isset($gettingAttributeLastSortedCount->sort) ? ($gettingAttributeLastSortedCount->sort + 1) : 0;
                                    // Inserting into product attribute table
                                    $newProductAttribute                = new ProductAttribute;
                                    $newProductAttribute->product_id    = $id;
                                    $newProductAttribute->title         = $valueAttribute;
                                    $newProductAttribute->price         = $request->attr_price[$keyAttribute];
                                    $newProductAttribute->sort          = $newAttributeSort;
                                    $saveProductAttributeLocal          = $newProductAttribute->save();
                                    if ($saveProductAttributeLocal) {
                                        // Inserting into product attribute local table
                                        foreach ($languages as $language) {
                                            $newProductAttributeLocal                       = new ProductAttributeLocal;
                                            $newProductAttributeLocal->product_id           = $id;
                                            $newProductAttributeLocal->product_attribute_id = $newProductAttribute->id;
                                            $newProductAttributeLocal->lang_code            = $language;
                                            if ($language == 'EN') {
                                                $newProductAttributeLocal->local_title = trim($request->attr_title_en[$keyAttribute], ' ');
                                            } else {
                                                $newProductAttributeLocal->local_title = trim($request->attr_title_de[$keyAttribute], ' ');
                                            }
                                            $saveProductAttributeLocal = $newProductAttributeLocal->save();
                                        }
                                    }
                                }                                
                            }
                        }
                        // Attribute NOT Exist
                        else {
                            ProductAttribute::where(['product_id' => $id])->forceDelete();
                            ProductAttributeLocal::where(['product_id' => $id])->forceDelete();
                        }

                        // Is menu
                        if ($request->is_menu) {
                          
                            foreach ($request->dropdown as $keyDropdown => $valueDropdown) {
                                // for existing dropdowns (Values will update)
                                if (array_key_exists('id', $valueDropdown)) {
                                    $menuTitleDetails = ProductMenuTitle::where(['id' => $valueDropdown['id'], 'product_id' => $id])->first();
                                    $menuTitleDetails->title        = trim($valueDropdown['title_en'],' ');
                                    $menuTitleDetails->is_multiple  = isset($valueDropdown['is_multiple']) ? $valueDropdown['is_multiple'] : 'N';
                                    $menuTitleDetails->addon_id         = !empty($valueDropdown['main_addon'])?$valueDropdown['main_addon']:0;
                                    $updateProductMenuTitle     = $menuTitleDetails->save();
                                    if ($updateProductMenuTitle) {
                                        ProductMenuTitleLocal::where(['product_id' => $id, 'product_menu_title_id' => $valueDropdown['id']])->forceDelete();
                                        // Inserting into product menu local table
                                        foreach ($languages as $language) {
                                            $newProductMenuTitleLocal                           = new ProductMenuTitleLocal;
                                            $newProductMenuTitleLocal->product_id               = $id;
                                            $newProductMenuTitleLocal->product_menu_title_id    = $valueDropdown['id'];
                                            $newProductMenuTitleLocal->lang_code                = $language;
                                            if ($language == 'EN') {
                                                $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_en'], ' ');
                                            } else {
                                                $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_de'], ' ');
                                            }
                                            $saveProductMenuTitleLocal = $newProductMenuTitleLocal->save();
                                        }

                                        // Product dropdown value
                                        foreach ($valueDropdown['val_en'] as $keyDropdownValue => $valueDropdownValue) {
                                            // existing value update


                                          $deleteids=!empty($valueDropdown['delete_ids'][$keyDropdownValue])?$valueDropdown['delete_ids'][$keyDropdownValue]:'';
                                          
                                           $deletemenu=!empty($valueDropdown['delete_menu_ids'][$keyDropdownValue])?$valueDropdown['delete_menu_ids'][$keyDropdownValue]:'';
                                          
                                                if($deleteids && $deletemenu){
                                                    $delete_id=explode(',',$deleteids);
                                                    $dropdownkeyId=$deletemenu;
                                                    foreach($delete_id as $listdelete){
                                                        $checkingValueExist = ProductMenuValue::where(['product_id' => $id, 'product_menu_title_id' => $dropdownkeyId])->where('id', '!=', $listdelete)->whereNull('deleted_at')->count();
                                                    if ($checkingValueExist == 0) {
                                                        
                                                    } else {
                                                        ProductMenuValue::where(['id' => $listdelete, 'product_id' => $id, 'product_menu_title_id' => $dropdownkeyId])->delete();
                                                        ProductMenuValueLocal::where(['product_id' => $id, 'product_menu_title_id' => $dropdownkeyId, 'product_menu_value_id' => $listdelete])->delete();
                                                    }
                                                }

                                            }


                                            if (array_key_exists($keyDropdownValue, $valueDropdown['val_id']) && $valueDropdown['val_id'][$keyDropdownValue]) {
                                                $menuValueDetails = ProductMenuValue::where(['id' => $valueDropdown['val_id'][$keyDropdownValue], 'product_id' => $id, 'product_menu_title_id' => $valueDropdown['id']])->first();
                                                $menuValueDetails->title    = trim($valueDropdownValue,' ');
                                                $menuValueDetails->price    = trim($valueDropdown['val_price'][$keyDropdownValue],' ');
                                                $menuValueDetails->sub_addon_id                 = !empty($valueDropdown['value_sub_addon_id'][$keyDropdownValue])?$valueDropdown['value_sub_addon_id'][$keyDropdownValue]:0;
                                                $menuValueDetails->sub_addon_status                 = !empty($valueDropdown['value_sub_addon_status'][$keyDropdownValue])?$valueDropdown['value_sub_addon_status'][$keyDropdownValue]:0;
                                                $updateProductMenuValue     = $menuValueDetails->save();
                                                if ($updateProductMenuValue) {
                                                    ProductMenuValueLocal::where(['product_id' => $id, 'product_menu_title_id' => $valueDropdown['id'], 'product_menu_value_id' => $valueDropdown['val_id'][$keyDropdownValue]])->forceDelete();
                                                    // Inserting into product menu value local table
                                                    foreach ($languages as $language) {
                                                        $newProductMenuValueLocal                           = new ProductMenuValueLocal;
                                                        $newProductMenuValueLocal->product_id               = $id;
                                                        $newProductMenuValueLocal->product_menu_title_id    = $valueDropdown['id'];
                                                        $newProductMenuValueLocal->product_menu_value_id    = $valueDropdown['val_id'][$keyDropdownValue];

                                                        $newProductMenuValueLocal->lang_code                = $language;
                                                        if ($language == 'EN') {
                                                            $newProductMenuValueLocal->local_title = trim($valueDropdownValue,' ');
                                                        } else {
                                                            $newProductMenuValueLocal->local_title = trim($valueDropdown['val_de'][$keyDropdownValue], ' ');
                                                        }
                                                        $saveProductMenuValueLocal = $newProductMenuValueLocal->save();
                                                    }
                                                }
                                            }
                                            // insert new product dropdown value
                                            else {
                                                // Inserting into product value

                                                $newProductMenuValue                        = new ProductMenuValue;
                                                $newProductMenuValue->product_id            = $id;
                                                $newProductMenuValue->product_menu_title_id = $valueDropdown['id'];
                                                $newProductMenuValue->title                 = trim($valueDropdownValue,' ');
                                                $newProductMenuValue->sub_addon_id                 = !empty($valueDropdown['value_sub_addon_id'][$keyDropdownValue])?$valueDropdown['value_sub_addon_id'][$keyDropdownValue]:0;
                                                $newProductMenuValue->sub_addon_status                 = !empty($valueDropdown['value_sub_addon_status'][$keyDropdownValue])?$valueDropdown['value_sub_addon_status'][$keyDropdownValue]:0;
                                                $newProductMenuValue->price                 = trim($valueDropdown['val_price'][$keyDropdownValue],' ');
                                                $saveProductMenuValue                       = $newProductMenuValue->save();
                
                                                // Inserting into product menu local table
                                                foreach ($languages as $language) {
                                                    $newProductMenuValueLocal                           = new ProductMenuValueLocal;
                                                    $newProductMenuValueLocal->product_id               = $id;
                                                    $newProductMenuValueLocal->product_menu_title_id    = $valueDropdown['id'];
                                                    $newProductMenuValueLocal->product_menu_value_id    = $newProductMenuValue->id;
                                                    $newProductMenuValueLocal->lang_code                = $language;
                                                    if ($language == 'EN') {
                                                        $newProductMenuValueLocal->local_title = trim($valueDropdownValue,' ');
                                                    } else {
                                                        $newProductMenuValueLocal->local_title = trim($valueDropdown['val_de'][$keyDropdownValue], ' ');
                                                    }
                                                    $saveProductMenuValueLocal = $newProductMenuValueLocal->save();
                                                }
                                            }
                                        }
                                    }
                                }
                                // for new dropdowns (will add new dropdowns)
                                else {
                                    // Inserting into product menu title
                                    $newProductMenuTitle                = new ProductMenuTitle;
                                    $newProductMenuTitle->product_id    = $id;
                                    $newProductMenuTitle->title         = trim($valueDropdown['title_en'],' ');
                                    $newProductMenuTitle->is_multiple   = isset($valueDropdown['is_multiple']) ? $valueDropdown['is_multiple'] : 'N';
                                    $newProductMenuTitle->addon_id         = !empty($valueDropdown['main_addon'])?$valueDropdown['main_addon']:0;
                                    $saveProductMenuTitle               = $newProductMenuTitle->save();
                                    if ($saveProductMenuTitle) {
                                        // Inserting into product menu local table
                                        foreach ($languages as $language) {
                                            $newProductMenuTitleLocal                           = new ProductMenuTitleLocal;
                                            $newProductMenuTitleLocal->product_id               = $id;
                                            $newProductMenuTitleLocal->product_menu_title_id    = $newProductMenuTitle->id;
                                            $newProductMenuTitleLocal->lang_code                = $language;
                                            if ($language == 'EN') {
                                                $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_en'], ' ');
                                            } else {
                                                $newProductMenuTitleLocal->local_title = trim($valueDropdown['title_de'], ' ');
                                            }
                                            $saveProductMenuTitleLocal = $newProductMenuTitleLocal->save();
                                        }
            
                                        // Inserting into product value
                                        foreach ($valueDropdown['val_en'] as $keyValue => $valueValue) {
                                            // Inserting into product value
                                            $newProductMenuValue                        = new ProductMenuValue;
                                            $newProductMenuValue->product_id            = $id;
                                            $newProductMenuValue->product_menu_title_id = $newProductMenuTitle->id;
                                            $newProductMenuValue->title                 = trim($valueValue,' ');
                                            $newProductMenuValue->price                 = trim($valueDropdown['val_price'][$keyValue],' ');
                                            
                                            $newProductMenuValue->sub_addon_id                 = !empty($valueDropdown['value_sub_addon_id'][$keyValue])?$valueDropdown['value_sub_addon_id'][$keyValue]:0;
                                            $newProductMenuValue->sub_addon_status                 = !empty($valueDropdown['value_sub_addon_status'][$keyValue])?$valueDropdown['value_sub_addon_status'][$keyValue]:0;
                                            
                                            $saveProductMenuValue                       = $newProductMenuValue->save();
            
                                            // Inserting into product menu local table
                                            foreach ($languages as $language) {
                                                $newProductMenuValueLocal                           = new ProductMenuValueLocal;
                                                $newProductMenuValueLocal->product_id               = $id;
                                                $newProductMenuValueLocal->product_menu_title_id    = $newProductMenuTitle->id;
                                                $newProductMenuValueLocal->product_menu_value_id    = $newProductMenuValue->id;
                                                $newProductMenuValueLocal->lang_code                = $language;
                                                if ($language == 'EN') {
                                                    $newProductMenuValueLocal->local_title = trim($valueValue, ' ');
                                                } else {
                                                    $newProductMenuValueLocal->local_title = trim($valueDropdown['val_de'][$keyValue], ' ');
                                                }
                                                $saveProductMenuValueLocal = $newProductMenuValueLocal->save();
                                            }
                                        }
                                    }
                                }
                            }

                        }
                        else {
                            ProductMenuTitle::where(['product_id' => $id])->forceDelete();
                            ProductMenuTitleLocal::where(['product_id' => $id])->forceDelete();
                            ProductMenuValue::where(['product_id' => $id])->forceDelete();
                            ProductMenuValueLocal::where(['product_id' => $id])->forceDelete();
                        }

                        // Product tags
                        if (isset($request->tags)) {
                            if (count($request->tags) > 0) {
                                ProductTag::where(['product_id' => $id])->delete();
                                foreach ($request->tags as $tag) {
                                    $newProductTag = new ProductTag;
                                    $newProductTag->product_id  = $id;
                                    $newProductTag->tag_id      = $tag;
                                    $newProductTag->save();
                                }
                            } else {
                                ProductTag::where(['product_id' => $id])->delete();
                            }
                        } else {
                            ProductTag::where(['product_id' => $id])->delete();
                        }

                        $request->session()->flash('alert-success', trans('custom_admin.success_data_updated_successfully'));
                        if (Session::get('searchUrl') == '') {
                            return redirect()->route('admin.'.\App::getLocale().'.product.list', ['page' => $pageNo]);
                        } else {
                            return redirect()->to(Session::get('searchUrl'));
                        }
                        
                    } else {
                        $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
                        return redirect()->route('admin.'.\App::getLocale().'.product.list', ['page' => $pageNo]);
                    }
                }
            }
            return view('admin.product.edit')->with(['details' => $details, 'data' => $data])->with($data);

        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.product.list');
            }
            $details = Product::where('id', $id)->first();
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
                return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', trans('custom_admin.error_invalid'));
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
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
                return redirect()->route('admin.'.\App::getLocale().'.product.list');
            }

            $details = Product::where('id', $id)->first();
            if ($details != null) {
                $delete = $details->delete();
                if ($delete) {
                    $request->session()->flash('alert-danger', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }                
                return redirect()->back();

            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.error_invalid'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            return redirect()->route('admin.'.\App::getLocale().'.product.list')->with('error', $e->getMessage());
        }
    }

    /*****************************************************/
    # Function name : changeStatusProductAttribute
    # Params        : Request $request
    /*****************************************************/
    public function changeStatusProductAttribute(Request $request) {
        $attributeId       = $request->attribute_id;
        $productId         = $request->product_id;

        $result['has_error']    = 1;
        $result['status']       = 1;
        $result['message']      = trans('custom_admin.error_something_went_wrong');

        $details = ProductAttribute::where(['id' => $attributeId, 'product_id' => $productId])->first();
        if ($details != null) {
            if ($details->status == 1) {
                // checking atleast one attribute active or not
                $checkingAttributeActiveStatus = ProductAttribute::where(['product_id' => $productId, 'status' => '1'])->where('id', '!=', $attributeId)->whereNull('deleted_at')->count();
                if ($checkingAttributeActiveStatus == 0) {
                    $result['has_error']    = 1;
                    $result['status']       = 0;
                    $result['message']      = trans('custom_admin.error_attribute_exist');
                } else {
                    $details->status = '0';
                    $details->save();

                    $result['has_error']= 0;
                    $result['status']   = 0;
                    $result['message']  = trans('custom_admin.success_status_updated_successfully');
                }
            } else {
                $details->status = '1';
                $details->save();

                $result['has_error']= 0;
                $result['status']   = 1;
                $result['message']  = trans('custom_admin.success_status_updated_successfully');
            }
        }
        echo json_encode($result);
    }
    
    /*****************************************************/
    # Function name : deleteProductAttribute
    # Params        : Request $request
    /*****************************************************/
    public function deleteProductAttribute(Request $request) {
        $attributeId = $request->attribute_id;
        $productId   = $request->product_id;

        $result['has_error']= 1;
        $result['status']   = 1;
        $result['message']  = trans('custom_admin.error_something_went_wrong');

        $details = ProductAttribute::where(['id' => $attributeId, 'product_id' => $productId])->first();
        if ($details != null) {
            // checking atleast one attribute exist or not
            $checkingAttributeExist = ProductAttribute::where(['product_id' => $productId])->where('id', '!=', $attributeId)->whereNull('deleted_at')->count();
            if ($checkingAttributeExist == 0) {
                $result['has_error']= 1;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_attribute_exist');
            } else {
                ProductAttribute::where(['id' => $attributeId, 'product_id' => $productId])->delete();

                ProductAttributeLocal::where(['product_id' => $productId, 'product_attribute_id' => $attributeId])->delete();

                $result['has_error']= 0;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_attribute_delete');
            }
        }
        echo json_encode($result);
    }
    
    /*****************************************************/
    # Function name : deleteProductDropdownTitle
    # Params        : Request $request
    /*****************************************************/
    public function deleteProductDropdownTitle(Request $request) {
        $dropdowntitleId    = $request->dropdowntitle_id;
        $productId          = $request->product_id;

        $result['has_error']= 1;
        $result['status']   = 1;
        $result['message']  = trans('custom_admin.error_something_went_wrong');

        $details = ProductMenuTitle::where(['id' => $dropdowntitleId, 'product_id' => $productId])->first();
        if ($details != null) {
            // checking atleast one title exist or not
            $checkingTitleExist = ProductMenuTitle::where(['product_id' => $productId])->where('id', '!=', $dropdowntitleId)->whereNull('deleted_at')->count();
            if ($checkingTitleExist == 0) {
                $result['has_error']= 1;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_dropdown_title_exist');
            } else {
                ProductMenuTitle::where(['id' => $dropdowntitleId, 'product_id' => $productId])->delete();
                ProductMenuTitleLocal::where(['product_id' => $productId, 'product_menu_title_id' => $dropdowntitleId])->delete();

                ProductMenuValue::where(['product_id' => $productId, 'product_menu_title_id' => $dropdowntitleId])->delete();
                ProductMenuValueLocal::where(['product_id' => $productId, 'product_menu_title_id' => $dropdowntitleId])->delete();

                $result['has_error']= 0;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_dropdown_title_delete');
            }
        }
        echo json_encode($result);
    }

    /*****************************************************/
    # Function name : deleteProductDropdownValues
    # Params        : Request $request
    /*****************************************************/
    public function deleteProductDropdownValues(Request $request) {
        $dropdownkeyId      = $request->dropdownkey_id;
        $dropdownvalueId    = $request->dropdownvalue_id;
        $productId          = $request->product_id;

        $result['has_error']= 1;
        $result['status']   = 1;
        $result['message']  = trans('custom_admin.error_something_went_wrong');

        $details = ProductMenuValue::where(['id' => $dropdownvalueId, 'product_id' => $productId])->first();
        if ($details != null) {
            // checking atleast one value exist or not
            $checkingValueExist = ProductMenuValue::where(['product_id' => $productId, 'product_menu_title_id' => $dropdownkeyId])->where('id', '!=', $dropdownvalueId)->whereNull('deleted_at')->count();
            if ($checkingValueExist == 0) {
                $result['has_error']= 1;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_dropdown_value_exist');
            } else {
                ProductMenuValue::where(['id' => $dropdownvalueId, 'product_id' => $productId, 'product_menu_title_id' => $dropdownkeyId])->delete();
                ProductMenuValueLocal::where(['product_id' => $productId, 'product_menu_title_id' => $dropdownkeyId, 'product_menu_value_id' => $dropdownvalueId])->delete();

                $result['has_error']= 0;
                $result['status']   = 0;
                $result['message']  = trans('custom_admin.error_dropdown_value_delete');
            }
        }
        echo json_encode($result);
    }
}