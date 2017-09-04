<?php

namespace Sahakavatar\Cms\Http\Controllers;

use Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts;
use Sahakavatar\Cms\Models\Home;
use Sahakavatar\Cms\Models\Templates\Units;
use Illuminate\Http\Request;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{


    /**
     * @var Home
     */
    private $homemodel;

    /**
     * HomeController constructor.
     *
     * @param page $page
     */
    public function __construct(Home $homemodel)
    {
        $this->homemodel = $homemodel;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pages(Request $request)
    {
        $url = $request->path();
        $settings = $request->all();
        //TODO: settings if live preview pass as argument  settings = $request->all()
        return $this->homemodel->render($url, $settings);
    }

    public function blog_pages(Request $request)
    {
        $settings = [];
        $url = $request->route()->uri();
        return $this->homemodel->render($url, $settings);
    }

//    public function unitStyles($slug, $path)
    public function unitStyles($css)
    {
        $styles=\Session::get('custom.styles', []);
        $file='';
        foreach ($styles as $style){
            $file.=$style;
        }
        $file=str_replace(' ','',$file);
        \Session::forget('custom.styles');
        $response = \Response::make($file);
        $response->header('Content-Type', 'text/css');
        $response->header('Cache-Control', 'max-age=31104000');
        return $response;
    }
    public function unitScripts($js)
    {
        $stiles=\Session::get('custom.scripts', []);
        $file='';
        foreach ($stiles as $stile){
            $file.=$stile;
        }
        \Session::forget('custom.scripts');
        $response = \Response::make($file);
        $response->header('Content-Type', 'application/javascript',false);
        $response->header('Cache-Control', 'max-age=31536000');

        return $response;
    }
    public function unitImg($slug, $path)
    {

        $unit = ContentLayouts::find($slug);
        if(!$unit) $unit = Units::find($slug);
        if(!\File::exists($unit->getPath().DS.$path)) abort(500);
        $file=\File::get($unit->getPath().DS.$path);
        $response = \Response::make($file);
        $response->header('Cache-Control', 'max-age=31104000');
        return $response;
    }
}