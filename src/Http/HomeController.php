<?php

namespace Sahakavatar\Cms\Http;

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

    public function unitStyles($model, $slug, $path)
    {
        $unit = ContentLayouts::find($slug);
        if(!$unit) $unit = Units::find($slug);
        $file=\File::get($unit->getPath().DS.$path);
        $response = \Response::make($file);
        $response->header('Content-Type', 'text/css');
        return $response;
    }
    public function unitScripts($model, $slug, $path)
    {
        $unit = ContentLayouts::find($slug);
        if(!$unit) $unit = Units::find($slug);
        $file=\File::get($unit->getPath().DS.$path);
        $response = \Response::make($file);

        $response->header('Content-Type', 'application/javascript',false);
        return $response;
    }
}