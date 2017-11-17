<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */
//Route::get('units/styles/{slug}/{path}', 'HomeController@unitStyles')->where('path', '.*');
Route::get('public-x/custom/css/{file}', 'HomeController@unitStyles');
Route::get('pages-optimize', 'HomeController@pagesOptimize');
Route::get('public-x/custom/js/{file}', 'HomeController@unitScripts');
//Route::get('units/scripts/{slug}/{path}', 'HomeController@unitScripts')->where('path', '.*');
Route::get('units/img/{slug}/{path}', 'HomeController@unitImg')->where('path', '.*');
if (\Illuminate\Support\Facades\Schema::hasTable('admin_pages')) {
    Route::get(BBGetAdminLoginUrl(), '\App\Modules\Users\Http\Controllers\Auth\AuthController@getAdminLogin')->middleware('guest');
    Route::post(BBGetAdminLoginUrl(), ['before' => 'throttle:2,60', 'uses' => '\App\Modules\Users\Http\Controllers\Auth\AuthController@postAdminLogin'])->middleware('guest');

}
Route::get('migrate', function () {

});


Route::get('register', '\App\Modules\Users\Http\Controllers\Auth\AuthController@getRegister')->middleware('guest');
Route::post('register', '\App\Modules\Users\Http\Controllers\Auth\AuthController@postRegister')->middleware('guest');
Route::get('activate/{username}/{token}', '\App\Modules\Users\Http\Controllers\Auth\AuthController@activate')->middleware('guest');
Route::group(
    ['domain' => env('DOMAIN')],
    function () {

        Route::get('form-test', function () {
            return view('test-form');
        });
        Route::post('save-form', function () {

        });
//        Route::get('/', 'HomeController@pages');
        //deletable
        Route::get('login', '\Btybug\User\Http\Controllers\Auth\AuthController@getLogin')->middleware('guest');
        Route::post('login', '\Btybug\User\Http\Controllers\Auth\AuthController@postLogin')->middleware('guest');
        //        Route::get(BBGetAdminLoginUrl(), '\Btybug\Modules\Users\Http\Controllers\Auth\AuthController@getAdminLogin')->middleware('guest');
        //        Route::post(BBGetAdminLoginUrl(), '\Btybug\Modules\Users\Http\Controllers\Auth\AuthController@postAdminLogin')->middleware('guest');
        Route::get('logout', '\Btybug\User\Http\Controllers\Auth\AuthController@getLogout')->middleware('auth');
        Route::post('/modality/settings-live', 'Admincp\ModalityController@postSettingsLive');
        Route::post('/modality/styles/options', 'Admincp\ModalityController@psotStylesOptions');
        Route::post('/modality/widgets/options', 'Admincp\ModalityController@psotWidgetsOptions');
        Route::post('/modality/templates/options', 'Admincp\ModalityController@postTplOptions');
        Route::post('/modality/units/options', 'Admincp\ModalityController@postUnitOptions');
        Route::post('/modality/page-sections/options', 'Admincp\ModalityController@postPageSectionOptions');
        Route::post('/modality/placeholder_section/options', 'Admincp\ModalityController@postSectionOptions');
        Route::post('/modality/main_body_modality/options', 'Admincp\ModalityController@postMainBodyOptions');

        Route::group(
            ['prefix' => '/admin', 'middleware' => ['admin:Users', 'sessionTimout', 'system']],
            function () {

                Route::get('/', 'Admincp\DashboardController@getIndex');

                Route::get('/menus', 'Admincp\DashboardController@getAdminMenus');
//                Route::controller('/dashboard', 'Admincp\DashboardController');

                Route::get(
                    '/notes',
                    function () {
                        return View('admin.notes');
                    }
                );

                Route::get(
                    '/functions',
                    function () {
                        $function = \config('functions');

                        return View('admin.functions')->with('functions', $function);
                    }
                );
            }
        );

        //ignore Routes For Common Pages
//        $ignores = config('ignoreroutes');//D:\wamp\www\avatar\appdata\config\ignoreroutes.php
        if (\Illuminate\Support\Facades\Schema::hasTable('frontend_pages')) {
            $url = \Request::server('REQUEST_URI'); //$_SERVER['REQUEST_URI'];
            if (!starts_with($url, '/admin')) {
                $pages = Btybug\FrontSite\Models\FrontendPage::pluck('id', 'url')->all();
                    Route::group(['middleware' => 'frontPermissions'], function () use ($pages) {
                        foreach ($pages as $key => $value) {
                            Route::get($key, function () use ($key) {
                                $home = new Btybug\btybug\Models\Home();
                                return $home->render($key, Request::all());
                            });
                        }
                    });


                Route::any('{all}', 'HomeController@pages')->where('all', '.*');
            }
        }
    }
);

