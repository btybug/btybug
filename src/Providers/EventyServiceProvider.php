<?php

namespace Btybug\btybug\Providers;

//use TorMorten\Eventy;

use App\User;
use Illuminate\Support\ServiceProvider;
use Btybug\Console\Models\BackendTh;


class EventyServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Eventy::addAction('emil.user', function ($what) {
            \Config::set('email_user', $what);
            return (\Config::get('email_user'));
        });

        \Eventy::addAction('emil.form', function ($what) {
            \Config::set('email_form', $what);
            return (\Config::get('email_form'));
        });
        \Eventy::addAction('emil.entry', function ($what) {
            \Config::set('email_entry', $what);
            return (\Config::get('email_entry'));
        });

        BackendTh::getCheckTheme();
        \Eventy::addAction('my.script', function ($what) {
            $codes = \Config::get('script');
            $codes .= $what;
            \Config::set('script', $codes);
            return (\Config::get('script'));
        });

//        \Eventy::addAction('add.validation', function ($what) {
//            $validations = BBValidations();
//            if (is_array($what)) {
//                $data = array_merge($validations, $what);
//                \Config::set('validations', $data);
//            }
//            return (\Config::get('validations'));
//        });

        \Eventy::addAction('admin.menus', function ($what) {
            if (!(\Config::get('admin_menus'))) {
                if (\File::exists(base_path('resources/menus/admin/1.json'))) {
                    $menu_json_file = \File::get(base_path('resources/menus/admin/1.json'));
                    $menu_array = json_decode($menu_json_file, true);
                    \Config::set('admin_menus', $menu_array);
                }

            } else {
                $menu_array = \Config::get('admin_menus');
            }
            $sub = true;
            foreach ($menu_array as $key => $value) {
                if ($value['title'] == $what['title']) {
                    $sub = false;
                    if (isset($what['main']) and $what['main']) {
                        $main_items = $what;
                        $children = $menu_array[$key]['children'];
                        unset($main_items['children']);
                        $menu_array[$key] = $main_items;
                        $menu_array[$key]['children'] = $children;
                    }
                    foreach ($what['children'] as $children) {
                        array_push($menu_array[$key]['children'], $children);
                    }
                }
            }
            if ($sub) {
                array_push($menu_array, $what);
            }
            \Config::set('admin_menus', $menu_array);
            return (\Config::get('admin_menus'));

        });

        \Eventy::addAction('admin.menu_child', function ($what) {
            if (!(\Config::get('admin_menus'))) {
                if (File::exists(base_path('resources/menus/admin/1.json'))) {
                    $menu_json_file = File::get(base_path('resources/menus/admin/1.json'));
                    $menu_array = json_decode($menu_json_file, true);
                    \Config::set('admin_menus', $menu_array);
                }

            } else {
                $menu_array = \Config::get('admin_menus');
            }
            foreach ($menu_array as $key => $value) {
                if ($value['title'] == $what['title']) {
                    foreach ($what['children'] as $children) {
                        array_push($menu_array[$key]['children'], $children);
                    }

                } else {
                    array_push($menu_array, $what);
                }
            }


            \Config::set('admin_menus', $menu_array);
            return (\Config::get('admin_menus'));

        });

        \Eventy::addFilter('my.script', function ($what) {
            $what = '<script type="text/javascript">' . $what . '</script>';
            return $what;
        }, 20, 1);

        \Eventy::addAction('my.shortcode', function ($what) {
//            echo $what;
            $codes = \Config::get('shortcode.code', []);
            foreach ($what as $key => $value) {
                $codes[] = $value;
            }
            \Config::set('shortcode.code', $codes);
            return (\Config::get('shortcode.code'));
        });
        \Eventy::addAction('my.scripts', function ($what) {
            $codes = \Config::get('scripts', []);
            $codes[] = $what;
            \Config::set('scripts', $codes);
            return (\Config::get('script'));
        });

        \Eventy::addAction('script.groups', function ($what) {
            $codes = \Config::get('script_groups', []);
            $codes[] = $what;
            \Config::set('script_groups', $codes);
            return (\Config::get('script_groups'));
        });

        \Eventy::addAction('my.sections', function ($what) {
            $codes = \Config::get('sections');

            $codes[] = $what;
            \Config::set('sections', $codes);
            return (\Config::get('sections'));
        });

        \Eventy::addAction('my.tab', function ($what) {
            $codes = \Config::get('tabs');
            $codes[] = $what;
            \Config::set('tabs', $codes);
            return (\Config::get('tabs'));
        });

        \Eventy::addAction('user.options', function ($what) {
            $options = \Config::get('user_options');
            $options[] = $what;
            \Config::set('user_options', $options);
            return (\Config::get('user_options'));
        });

        \Eventy::addAction('user_send_email', function ($what) {
            $this->emailsc = new EmailScHelper();
            \Config::set('user_send_email.user', $what[0]);
            \Config::set('user_send_email.email', $what[1]);
            $this->emailsc->newContent($what[1]);
            return (\Config::get('user_send_email'));
        });

        \Eventy::addAction('toggle.tabs', function ($what) {
            $codes = \Config::get('toogleTabs');
            $codes[] = $what;
            \Config::set('toogleTabs', $codes);
            return (\Config::get('toogleTabs'));
        });

        \Eventy::addFilter('toggle.tabs', function ($what) {
            $tabs = new Tabs();
            $view = $tabs->toggleTabs($what);
            echo $view;
        });
        //togle panels
        \Eventy::addAction('toggle.panels', function ($what) {
            $codes = \Config::get('tooglePanels');
            $codes[] = $what;
            \Config::set('tooglePanels', $codes);
            return (\Config::get('tooglePanels'));
        });

        \Eventy::addFilter('toggle.panels', function ($what) {
            $tabs = new Tabs();
            $view = $tabs->panels($what);
            echo $view;
        });

        \Eventy::addAction('curent.page', function ($what) {
            \Session::set('curent.page', $what);
        });

        \Eventy::addAction('curent.term', function ($what) {
            \Session::set('curent.term', $what);
        });

        \Eventy::addAction('curent.post', function ($what) {
            \Session::set('curent.post', $what);
        });

        \Eventy::addAction('menu', function ($what) {

            if (isset($what['parent'])) {
                $json = json_decode(\File::get('appdata/resources/menus/admin/1.json'), true);

                if ($what['parent'] == false) {
                    //add new link
                    $json[] = $what['data'];
                } else {
                    //add child(children)
                    if (isset($what['data']['title'])) {
                        foreach ($json as $k => $v) {
                            if ($json[$k]['title'] == $what['parent']) {
                                $json[$k]['children'][] = $what['data'];
                            }
                        }
                    } else {
                        // for multyple child
                        foreach ($json as $k => $v) {
                            foreach ($what['data'] as $child) {
                                if ($json[$k]['title'] == $what['parent']) {
                                    $json[$k]['children'][] = $child;
                                }
                            }
                        }
                    }
                }
                return \File::put('appdata/resources/menus/admin/1.json', json_encode($json, true));
            }

            return false;
        });

        \Eventy::addAction('removeMenuItem', function ($what) {
            if (isset($what['parent'])) {
                $json = json_decode(\File::get('appdata/resources/menus/admin/1.json'), true);
                if ($what['parent'] == null) {
                    //add new link
                    foreach ($json as $k => $v) {
                        if ($json[$k]['title'] == $what['data']['title']) {
                            unset($json[$k]);
                        }
                    }
                } else {
                    if (isset($what['data']['title'])) {
                        foreach ($json as $k => $v) {
                            if ($json[$k]['title'] == $what['parent']) {
                                foreach ($json[$k]['children'] as $key => $value) {
                                    if ($value['title'] == $what['data']['title']) {
                                        unset($json[$k]['children'][$key]);
                                    }
                                }
                            }
                        }
                    } else {
                        // for multyple child
                        foreach ($json as $k => $v) {
                            foreach ($what['data'] as $child) {
                                if ($json[$k]['title'] == $what['parent']) {
                                    foreach ($json[$k]['children'] as $key => $value) {
                                        if ($value['title'] == $child['title']) {
                                            unset($json[$k]['children'][$key]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return \File::put('appdata/resources/menus/admin/1.json', json_encode($json, true));
            }

            return false;
        });

        User::created(function ($user) {
            // if ($user) $user->assignRole(User::ROLE_USER);
        });

        \View::creator(
            'layouts.master', 'App\helpers\creators\ScriptCreator'
        );

        // Custom blade directives
        \Blade::directive('set', function ($expression) {
            list($name, $val) = explode(',', $expression);
            return "<?php {$name} = {$val}; ?>";
        });

        // BBGetPostField
        \Blade::directive('BBGetPostField', function ($expression) {
            return "<?php echo BBGetPostField($expression) ?>";
        });

        // BBPreparePost
        \Blade::directive('BBPreparePost', function ($expression) {
            return "<?php BBPreparePost($expression) ?>";
        });

        // BBPostLink
        \Blade::directive('BBPostLink', function ($expression) {
            if ($expression) {
                return "<?php echo BBPostLink() ?>";
            }
            return "<?php echo BBPostLink($expression) ?>";
        });

        // BBWidgetArea
        \Blade::directive('BBWidgetArea', function ($expression) {
            return "<?php BBWidgetArea($expression) ?>";
        });

        // BBImage
        \Blade::directive('BBImage', function ($expression) {
            return "<?php BBImage($expression) ?>";
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

}
