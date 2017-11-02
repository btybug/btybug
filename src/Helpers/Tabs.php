<?php

namespace Btybug\btybug\Helpers;

use Auth;
use View;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tabs
{

    /**
     * @param string $page
     * @return array
     */
    public function getTabs($page = 'settings')
    {
        $data = \Config::get('tabs');
        $section = [];
        foreach ($data as $value) {
            if (isset($value[$page])) {
                foreach ($value[$page] as $tab) {
                    if (isset($tab['permission'])) {
                        if (Auth::user()->can($tab['permission'])) {
                            $section[] = $tab;
                        }
                    } else {
                        $section[] = $tab;
                    }
                }
            }
        }
        ksort($section);
        return $section;
    }

    /**
     * @param $str
     * @return bool
     */
    public function just_text_in_quotes($str)
    {
        preg_match('/{(.*?)}/', $str, $matches);
        return isset($matches) ? $matches : FALSE;
    }

    public function toggleTabs($args)
    {
        $page = $args['page'];
        $data = \Config::get('toogleTabs');
        $section = [];
        foreach ($data as $value) {
            if (isset($value[$page])) {
                foreach ($value[$page] as $tab) {
                    if (isset($tab['permission'])) {
                        if (Auth::user()->can($tab['permission'])) {
                            $section[] = $tab;
                        }
                    } else {
                        $section[] = $tab;
                    }
                }
            }
        }

        $tabs = View::make('admin.toggle_tabs')->with('tabs', $section)->with($args);
        return $tabs;
    }

    public function panels($args)
    {
        $page = $args['page'];
        $data = \Config::get('tooglePanels');
        $section = [];
        if ($data) {
            foreach ($data as $value) {
                if (isset($value[$page])) {
                    foreach ($value[$page] as $tab) {
                        if (isset($tab['permission'])) {
                            if (Auth::user()->can($tab['permission'])) {
                                $section[] = $tab;
                            }
                        } else {
                            $section[] = $tab;
                        }
                    }
                }
            }
        }

        $panels = View::make('admin.toggle_panels')->with('panels', $section)->with($args);
        return $panels;
    }


}
