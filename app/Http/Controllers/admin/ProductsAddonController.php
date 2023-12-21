<?php
/*****************************************************/
# Page/Class name   : ProductsController
/*****************************************************/
namespace App\Http\Controllers\admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Helper;
use DB;
use AdminHelper;
use App\Models\Category;
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
class ProductsAddonController extends Controller {
    /**
     * Change addon status
     */
    public function changeStatus(Request $request, $id = null) {
        $couponDetails = ProductAddon::where('id', $id)->first();
        $status = 1;
        if ($couponDetails->status) {
            $status = 0;
        }
        $updateCoupon = ProductAddon::where('id', $id)->update(['status' => $status]);
        if ($updateCoupon) {
            $request->session()->flash('alert-success', trans('custom_admin.success_status_updated_successfully'));
            return redirect()->back();
        } else {
            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_updating'));
            return redirect()->back();
        }
    }
    /**
     * Addon Delete
     */
    public function deleteAddon(Request $request, $id = null) {
        $details = ProductAddon::where('id', $id)->first();
        //$menuvalue=ProductMenuValue::where('sub_addon_id',$id)->first();
        //$menuvalues=ProductMenuTitleLocal::where('addon_id',$id)->first();
        if ($details != null) {
            $checkmeavalue = DB::table('products')->join('product_menu_values', "products.id", '=', "product_menu_values.product_id")->where('products.deleted_at', '=', NULL)->where('product_menu_values.sub_addon_id', '=', $id)->get();
            $checkmeavalue1 = DB::table('products')->join('product_menu_title_locals', "products.id", '=', "product_menu_title_locals.product_id")->where('products.deleted_at', '=', NULL)->where('product_menu_title_locals.addon_id', '=', $id)->get();
            $mvalue = !empty($checkmeavalue[0]->id) ? $checkmeavalue[0]->id : '';
            $mtvalue = !empty($checkmeavalue1[0]->id) ? $checkmeavalue1[0]->id : '';
            if (empty($mvalue) && empty($mtvalue)) {
                $delete = $details->delete();
                if ($delete) {
                    $details1 = ProductAddon::where('parent_id', $id)->first();
                    if ($details1 != null) {
                        $details1->delete();
                    }
                    $request->session()->flash('alert-success', trans('custom_admin.success_data_deleted_successfully'));
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
                }
                return redirect()->back();
            } else {
                $request->session()->flash('alert-danger', trans('custom_admin.not_authorized_delete'));
                return redirect()->back();
            }
        } else {
            $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_deleting'));
            return redirect()->back();
        }
    }
    public function deleteAddonAjax(Request $request, $id = null) {
        $details = ProductAddon::where('id', $id)->first();
        //$details1 = ProductAddon::where('parent_id', $id)->get();
        // $menuvalue=ProductMenuValue::where('sub_addon_id',$id)->first();
        //$menuvalues=ProductMenuTitleLocal::where('addon_id',$id)->first();
        if ($details != null) {
            $checkmeavalue = DB::table('products')->join('product_menu_values', "products.id", '=', "product_menu_values.product_id")->where('products.deleted_at', '=', NULL)->where('product_menu_values.sub_addon_id', '=', $id)->get();
            $checkmeavalue1 = DB::table('products')->join('product_menu_title_locals', "products.id", '=', "product_menu_title_locals.product_id")->where('products.deleted_at', '=', NULL)->where('product_menu_title_locals.addon_id', '=', $id)->get();
            $mvalue = !empty($checkmeavalue[0]->id) ? $checkmeavalue[0]->id : '';
            $mtvalue = !empty($checkmeavalue1[0]->id) ? $checkmeavalue1[0]->id : '';
            if (empty($mvalue) && empty($mtvalue)) {
                $delete = $details->delete();
                if ($delete) {
                    $details1 = ProductAddon::where('parent_id', $id)->first();
                    if ($details1 != null) {
                        $details1->delete();
                    }
                    return json_encode(['type' => 'success', 'message' => trans('custom_admin.success_data_deleted_successfully') ]);
                } else {
                    return json_encode(['type' => 'error', 'message' => trans('custom_admin.error_took_place_while_deleting') ]);
                }
            } else {
                return json_encode(['type' => 'error', 'message' => trans('custom_admin.not_authorized_delete') ]);
            }
        } else {
            return json_encode(['type' => 'error', 'message' => trans('custom_admin.not_authorized_delete') ]);
        }
    }
    public function editAddon(Request $request, $id = null) {
        $data['page_title'] = trans('custom_admin.lab_addon_edit');
        $data['panel_title'] = trans('custom_admin.lab_addon_edit');
        $data['list'] = ProductAddon::where('parent_id', $id)->orWhere('id', $id)->get();
        $data['addon_id'] = $id;
        return view('admin.addon.editaddon', $data);
    }
    /**
     * Addon features added
     */
    public function addonList(Request $request) {
        $pageNo = $request->input('page');
        //Session::put('pageNo',$pageNo);
        $data['order_by'] = 'code';
        $data['order'] = 'asc';
        $data['list'] = ProductAddon::where('parent_id', 0)->get();
        $data['page_title'] = trans('custom_admin.lab_addon_list');
        $data['panel_title'] = trans('custom_admin.lab_addon_list');
        return view('admin.addon.addonlist', $data);
    }
    /**
     * AddAddon
     */
    public function addonSubmit(Request $request) {
        $data['page_title'] = 'Addon ';
        if ($request->isMethod('POST')) {
            if (isset($_POST['update_id'])) {
                $new = ProductAddon::find($request->update_id);
                $new->en_title = trim($request->english_title);
                $new->de_title = trim($request->german_title);
                $save = $new->save();
                $paranet_id = $new->id;
                for ($i = 0;$i < count($request->english_value);$i++) {
                    if (isset($request->sub_addon_id[$i])) {
                        $new = ProductAddon::find($request->sub_addon_id[$i]);
                    } else {
                        $new = new ProductAddon;
                    }
                    $new->status = trim($request->status[$i]);
                    $new->en_title = trim($request->english_title);
                    $new->de_title = trim($request->german_title);
                    $new->parent_id = $paranet_id;
                    $new->en_value = trim($request->english_value[$i]);
                    $new->de_value = trim($request->value_german[$i]);
                    $new->price = trim($request->price[$i]);
                    $new->save();
                }
                if ($save) {
                    $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
                    return redirect()->back();
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
                    return redirect()->back();
                }
            } else {
                $new = new ProductAddon;
                $new->en_title = trim($request->english_title);
                $new->de_title = trim($request->german_title);
                $save = $new->save();
                $paranet_id = $new->id;
                for ($i = 0;$i < count($request->english_value);$i++) {
                    $new = new ProductAddon;
                    $new->en_title = trim($request->english_title);
                    $new->de_title = trim($request->german_title);
                    $new->parent_id = $paranet_id;
                    $new->en_value = trim($request->english_value[$i]);
                    $new->de_value = trim($request->value_german[$i]);
                    $new->price = trim($request->price[$i]);
                    $new->save();
                }
                if ($save) {
                    $request->session()->flash('alert-success', trans('custom_admin.success_data_added_successfully'));
                    return redirect()->route('admin.' . \App::getLocale() . '.product.add-addon');
                } else {
                    $request->session()->flash('alert-danger', trans('custom_admin.error_took_place_while_adding'));
                    return redirect()->back();
                }
            }
        }
        //return redirect()->route('admin.'.\App::getLocale().'.product.add-addon');
        
    }
    /**
     * AddAddon
     */
    public function addAddon() {
        $data['page_title'] = trans('custom_admin.lab_addon_add');
        $data['panel_title'] = trans('custom_admin.lab_addon_add');
        return view('admin.addon.addaddon', $data);
    }
}
