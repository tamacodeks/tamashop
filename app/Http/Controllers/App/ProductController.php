<?php

namespace App\Http\Controllers\App;

use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceConfig;
use App\Models\StockStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kalamsoft\Langman\Lman;
use Yajra\DataTables\Facades\DataTables;
use Validator;
use app\Library\AppHelper;

class ProductController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * View All Services
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(){
        $config = ServiceConfig::where('service_id', 1)->where('type', 'default')->first();
        $selected_countries = json_decode($config->config,true);
        $countries = Country::whereIn('id',$selected_countries['access_data'])->select('id','nice_name')->get();
        $this->data = [
            'page_title' => "Manage Products",
            'countries' => $countries
        ];
        return view('app.products.index',$this->data);
    }

    /**
     * Ajax - Render services into data table
     * @param Request $request
     * @return mixed
     */
    function render_products(Request $request){
        $query = Product::join('countries','countries.id','products.country_id')
            ->join('product_categories','product_categories.id','products.category_id')
            ->join('stock_status','stock_status.id','products.stock_status_id')
            ->select([
                'products.id',
                'countries.nice_name as country',
                'product_categories.name as category',
                'stock_status.name as stock_status',
                'products.name as product_name',
                'products.description as product_desc',
                'products.cost as product_cost',
                'products.price as product_price',
                'products.own_price as product_own_price',
                'products.reseller_price as product_reseller_price',
                'products.shipping_charge as shipping_charge',
                'products.status as status',
                'products.min_to_order as min_to_order',
                'products.max_to_order as max_to_order',
            ])
            ->orderBy('products.country_id',"ASC");
        $products = $query;
        return Datatables::of($products)
            ->addColumn('action', function ($products) {
                return '<a href="'.secure_url('product/update/'.$products->id).'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> '.trans('common.lbl_edit').'</a>&nbsp;&nbsp;<a onclick="AppConfirmDelete(this.href,\''.trans('common.lbl_remove').' '.$products->product_name.'\',\''.trans('common.ask_remove').'\' );return false;" href="'.secure_url('product/remove/'.$products->id).'" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i> '.trans('common.btn_delete').'</a>';
            })
            ->editColumn('product_cost', '€{{$product_cost}}')
            ->rawColumns(['action','product_desc'])
            ->filter(function ($query) use ($request) {
                if (!empty($request->input('query'))) {
                    $qry = $request->input('query');
                    $query->Where(function ($q) use ($qry) {
                        $q->Where('products.name', "like", "%{$qry}%");
                        $q->orWhere('countries.nice_name', "like", "%{$qry}%");
                        $q->orWhere('product_categories.name', "like", "%{$qry}%");
                    });
                }
                if (!empty($request->input('country_id'))) {
                    $query->whereIn('products.country_id',$request->input('country_id'));
                }
            })
            ->make(true);
    }

    /**
     * Add or Update Service
     * @param string $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function edit($id=''){
        if(!empty($id)){
            $row = Product::find($id)->toArray();
            $product = Product::find($id);
            $row['image'] = count($product->getMedia('images')) > 0 ? $product->getMedia('images')->first()->getUrl('thumb') : asset('images/products/'.$row['image']);
        }else{
            $row = AppHelper::renderColumns('products');
            $row['image'] = asset('images/avatar.png');
        }
        $this->data['row'] = $row;
        $this->data['categories'] = ProductCategory::select('id','name')->get();
        $config = ServiceConfig::where('service_id', 1)->where('type', 'default')->first();
        $selected_countries = json_decode($config->config,true);
        $countries = Country::whereIn('id',$selected_countries['access_data'])->select('id','nice_name')->get();
        $this->data['countries'] = $countries;
        $this->data['stock_status'] = StockStatus::select('id','name')->get();
        return view('app.products.update',$this->data);
    }

    /**
     * Update Service
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request){
//        dd($request->all());
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'category_id' => 'required',
            'qty' => 'required',
            'own_price' => 'required',
            'reseller_price' => 'required',
        ]);
        if($validator->fails()){
            AppHelper::logger('warning','Product Validation Failed','Unable to update tha product info',$request->all());
            $html = AppHelper::create_error_bag($validator);
            return redirect()->back()
                ->with('message',$html)
                ->with('message_type','warning');
        }
        $to_update = [
            'name' => $request->name,
            'tags' => !empty($request->tags) ? $request->tags : "",
            'description' => $request->description,
            'country_id' => $request->country_id,
            'category_id' => $request->category_id,
            'stock_status_id' => $request->stock_status_id,
            'qty' => $request->qty,
            'cost' => $request->cost,
            'price' => $request->cost,
            'own_price' => $request->own_price,
            'reseller_price' => $request->reseller_price,
            'shipping_charge' => $request->shipping_charge,
            'sur_charge' => $request->sur_charge,
            'sur_charge_desc' => $request->sur_charge_desc,
            'track_qty' => $request->track_qty,
            'min_to_order' => $request->min_to_order,
            'max_to_order' => $request->max_to_order,
            'status' => !empty($request->status) ? 1 : 0,
            'is_track_qty' => !empty($request->is_track_qty) ? 1 : 0,
        ];
        if (ENABLE_MULTI_LANG == 1) {
            $lang = Lman::langOption();
            $language = array();
            foreach ($lang as $l) {
                if ($l['folder'] != 'en') {
                    $product_name_lang = (isset($_POST['product_name_trans'][$l['folder']]) ? $_POST['product_name_trans'][$l['folder']] : '');
                    $product_desc_lang = (isset($_POST['product_desc_trans'][$l['folder']]) ? $_POST['product_desc_trans'][$l['folder']] : '');
                    $language['product_name'][$l['folder']] = $product_name_lang;
                    $language['product_desc'][$l['folder']] = $product_desc_lang;
                }
            }
            $to_update['trans_lang'] = json_encode($language);
        }
        if(!empty($request->id)){
            $to_update['updated_at'] = date('Y-m-d H:i:s');
            $to_update['updated_by'] = auth()->user()->id;
            Product::where('id',$request->id)->update($to_update);
            $product_id = $request->id;
        }else{
            $to_update['created_at'] = date('Y-m-d H:i:s');
            $to_update['created_by'] = auth()->user()->id;
            $product_id = Product::insertGetId($to_update);
        }
        if($request->hasFile('image')){
            $product = Product::find($product_id);
            $fileTmp = $request->file('image');
            $fileName = str_slug($request->name,'_').'.'.$fileTmp->getClientOriginalExtension();
            $product->addMedia($request->file('image'))->usingFileName($fileName)->toMediaCollection('images');
        }
        AppHelper::logger('success','Product Updated','Product ID '.$product_id.' updated successfully');
        return redirect('products')
            ->with('message',trans('common.msg_update_success'))
            ->with('message_type','success');
    }

    /**
     * Delete Service
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function delete($id){
        $product = Product::find($id);
        $product->delete();
        AppHelper::logger('success',"Product Delete","Product ID $id has been deleted");
        return redirect()->back()->with('message',trans('common.msg_remove_success'))
            ->with('message_type','success');
    }

}
