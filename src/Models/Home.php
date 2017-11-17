<?php
/**
 * Created by PhpStorm.
 * User: muzammal
 * Date: 8/17/2016
 * Time: 10:43 AM
 */

namespace Btybug\btybug\Models;

//use Btybug\btybug\Helpers\helpers;
use Assets;
use Btybug\FrontSite\Models\FrontendPage;

/**
 * @property Page page
 */
class Home
{

    /**
     * Home constructor.
     *
     * @param Page $page
     */
    public function __construct()
    {
//        $this->helpers = new helpers;
    }


    /**
     * @param $url
     * @param array $settings
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function render($url, $settings = [])
    {
        $page = FrontendPage::where('url', $url)->orWhere('url', "/" . $url)->first();
        if($page->content_type=='special') return view('btybug::app', compact('page'));
        if ($page) {
            if (!isset($settings['pl_live_settings'])) {
                if ($page->status == 'draft')
                    abort(404);
                if (BBCheckMemberAccessEnabled() && $page->url != "/") {

                    return view('frontend.login', compact('page', 'settings'));
                }
                if (is_array($page->page_layout_settings)) {
                    $settings = array_merge($settings, $page->page_layout_settings);
                }
                $page_settings = json_decode($page->settings, true);
                if (!is_array($page_settings)) {
                    $page_settings = [];
                }
                $settings = array_merge($settings, $page_settings);
            }
            $settings['main_content'] = $page->main_content;
            $settings['content_type'] = $page->content_type;
            $settings['template'] = $page->template;
            return view('btybug::front_pages', compact('page', 'settings'));
        }

        abort(404);
    }
}