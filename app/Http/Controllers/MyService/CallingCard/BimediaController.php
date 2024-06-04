<?php

namespace App\Http\Controllers\Myservice\CallingCard;

use App\Http\Controllers\Api\DematSoapBimediaController;
use app\Library\ApiHelper;
use app\Library\AppHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;
use SimpleXMLElement;
use App\Models\Bimedia_statistics;
use Yajra\DataTables\Facades\DataTables;

class BimediaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Bimedia Manage Settings Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    function index()
    {
        $bimedia = new DematSoapBimediaController();
        $balance = $bimedia->FetchBalance();
        try
        {
            $data = [
                'code_client' => $balance->code_client_srd,
                'remaining_bal' => $balance->conso_srd,
                'total_amount' => $balance->max_srd,
                'conso_srdmp' => $balance->conso_srdmp,
                'max_srdmp' => $balance->max_srdmp,
            ];
            AppHelper::logger('info','SFTP Manager',auth()->user()->username ." manage files on SFTP");
            return view('myservice.calling-cards.Bimedia.index',$data);
        }
        catch(\Exception $e){
            $data = [
                'code_client' => '0.00',
                'remaining_bal' => '0.00',
                'total_amount' =>'0.00',
                'conso_srdmp' =>'0.00',
                'max_srdmp' => '0.00',
            ];
            AppHelper::logger('info','SFTP Manager',auth()->user()->username ." manage files on SFTP");
            return view('myservice.calling-cards.Bimedia.index',$data);
        }
    }

    function syncCatalogue(Request $request){
        $bimedia = new DematSoapBimediaController();
        $data = $bimedia->FetchCatalogue();
        $param =json_decode(json_encode($data->catalogueInfo), true);
        $result = ArrayToXml::convert(['products' => $param]);
        Storage::disk('public')->put('catalogue/bimedia_catalogue.xml', $result);
        return ApiHelper::response('200',200,"Sync finished!");
    }

    function bimedia_stat(Request $request){
        $providers= Bimedia_statistics::get();
        return view('myservice.calling-cards.Bimedia.statistics', $providers);
    }
    function fetch_data(Request $request)
    {
        $query = Bimedia_statistics::orderBy('id',"DESC");
        if (empty($request->input('from_date')) && empty($request->input('to_date'))) {
            $query->whereDate('bimedia_statistics.date',date('Y-m-d'))->get();
        }else{
            $from_date = $request->input('from_date').' 00:00:00';
            $to_date = $request->input('to_date').' 23:59:59';
            $query->whereBetween('bimedia_statistics.date',[$from_date,$to_date])->get();
        }
        return Datatables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
}
