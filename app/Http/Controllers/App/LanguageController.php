<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kalamsoft\Langman\Lman;

class LanguageController extends Controller
{
    private $language;

    public function __construct()
    {
        $this->middleware('auth')->except('switchLang');
        $this->language = [
            ['folder' => 'en'],
            ['folder' => 'fr'],
            // Add more languages as needed
        ];
    }

    /**
     * Switch the language
     * @param $lang
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($lang, Request $request)
    {
        foreach ($this->language as $all_lang) {
            if ($all_lang['folder'] == $lang) {
                \Session::put('locale', $all_lang['folder']);
                break;
            }
        }
        return redirect()->back();
    }

    /**
     * VIEW - Manage All Translations
     * @param Request $request
     * @param null $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request, $type = null)
    {
        if(!is_null($request->input('edit')))
        {
            $file = (!is_null($request->input('file')) ? $request->input('file') : 'auth.php');
            $files = scandir(base_path()."/resources/lang/".$request->input('edit')."/");
            $str = \File::getRequire(base_path()."/resources/lang/".$request->input('edit').'/'.$file);
            $this->data = array(
                'stringLang'	=> $str,
                'lang'			=> $request->input('edit'),
                'files'			=> $files ,
                'file'			=> $file ,
            );
            $template = 'edit';
        } else {
            $template = 'index';
            $this->data = [];
        }
        return view('translation.'.$template,$this->data);
    }

    /**
     * VIEW - Add New Translation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        return view("translation.create");
    }

    /**
     * POST - Add New Translation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save( Request $request)
    {
        $rules = array(
            'name'		=> 'required',
            'folder'	=> 'required|alpha'
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $template = base_path();
            $folder = $request->input('folder');
            if(\File::exists($template."/resources/lang/".$folder) == false){
                mkdir( $template."/resources/lang/".$folder ,0777 );
            }
            $info = json_encode(array("name"=> $request->input('name'),"folder"=> $folder , "author" => $request->input('author') ? $request->input('author') : ""));
            $fp=fopen(  $template.'/resources/lang/'.$folder.'/config.json',"w+");
            fwrite($fp,$info);
            fclose($fp);
            $files = scandir( $template .'/resources/lang/en/');
            foreach($files as $f)
            {
                if($f != "." and $f != ".." and $f != 'config.json')
                {
                    copy( $template .'/resources/lang/en/'.$f, $template .'/resources/lang/'.$folder.'/'.$f);
                }
            }
            return redirect('translation');
        } else {
            return redirect('translation')
                ->withInput()
                ->withErrors($validator);
        }
    }

    /**
     * POST - Update Translation Phrases
     * @param Request $request
     * @return mixed
     */
    public function update( Request $request)
    {
        $template = base_path();
        $form  	= "<?php \n";
        $form .= "/**
 * Updated by prabakaran-t/lman
 * Date: ".date("Y-m-d H:i:s")."
 */\n";
        $form 	.= "return array( \n";
        foreach($_POST as $key => $val)
        {
            if($key !='_token' && $key !='lang' && $key !='file')
            {
                if(!is_array($val))
                {
                    $form .= '"'.$key.'" => "'.strip_tags($val).'", '." \n ";

                } else {
                    $form .= '"'.$key.'" => array( '." \n ";
                    foreach($val as $k=>$v)
                    {
                        $form .= ' "'.$k.'" => "'.strip_tags($v).'", '." \n ";
                    }
                    $form .= "), \n";
                }
            }

        }
        $form .= ');';
        //echo $form; exit;
        $lang = $request->input('lang');
        $file	= $request->input('file');
        $filename = $template .'/resources/lang/'.$lang.'/'.$file;
        //	$filename = 'lang.php';
        $fp=fopen($filename,"w+");
        fwrite($fp,$form);
        fclose($fp);
        return redirect('translation?edit='.$lang.'&file='.$file);
    }

    /**
     * Remove Translation folder
     * @param $folder
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove( $folder )
    {
        self::removeDir( base_path()."/resources/lang/".$folder);
        return redirect('translation');
    }

    /**
     * UTILITY FN - Remove dir
     * @param $dir
     */
    function removeDir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                self::removedir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

}
