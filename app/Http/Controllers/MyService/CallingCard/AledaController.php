<?php

namespace App\Http\Controllers\Myservice\CallingCard;

use App\Http\Controllers\Api\DematSoapController;
use app\Library\ApiHelper;
use app\Library\AppHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AledaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Aleda Manage Settings Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $dematSoap = new DematSoapController();
        $balance = $dematSoap->getIncurBalance();
        if(isset($balance->error)){
            $balance = '0.00';
        }else{
            $balance = AppHelper::formatAmount('EUR', number_format(($balance /100), 2, '.', ''));
        }
        $this->data = [
            'balance' => $balance
        ];
        AppHelper::logger('info','SFTP Manager',auth()->user()->username ." manage files on SFTP");
        return view('app.api.sftp.elfinder',$this->data);
    }


    function syncCatalogue(Request $request){
        if(!$request->ajax()){
            return ApiHelper::response('500',200,"Cannot access directly");
        }
        //read file from sftp
        $file = Storage::disk('sftp')->get('Catalogue/catalogue.xml');
        //save it local storage folder
        Storage::disk('public')->put('catalogue/catalogue.xml', $file);
        return ApiHelper::response('200',200,"Sync finished!");
    }

}
