<?php namespace Btybug\btybug\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Layouts;
use Illuminate\Http\Request;
use Notifynder;
use RegEmails;


/**
 * Class DashboardController
 *
 * @package App\Http\Controllers\Admincp
 */
class DashboardController extends Controller
{

    /**
     * @var Widget|null
     */
    private $widget = null;
    private $helpers;

    /**
     * DashboardController constructor.
     *
     * @param Widget $widget
     */
    public function __construct()
    {
//        $this->helpers = new helpers;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('btybug::admin.dashboard');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAdminMenus()
    {
        return view('admin.menus');
    }

    /**
     * @param Request $request
     */
    public function getChanglayout(Request $request)
    {
        $req = $request->all();
        $pg = Pagesetting::find(1);
        $layout = $req['layout'];
        if ($layout == 1) {
            $pg->fisrt_col_widget .= "," . $pg->sec_col_widget;
            $pg->sec_col_widget = "";
        }
        $pg->layout = $layout;
        $pg->save();
    }

    /**
     * @param Request $request
     */
    public function getUpdatesetting(Request $request)
    {
        $req = $request->all();
        $pg = Pagesetting::find(1);
        $container_id = $request['containerId'];
        if ($container_id == 1) {
            $col = 'fisrt_col_widget';
        } else {
            $col = 'sec_col_widget';
        }
        $new_data = [];
        $data = $pg->$col;
        $data = explode(",", $data);
        foreach ($data as $sngl) {
            if ($sngl != "") {
                $new_data[$sngl] = $sngl;
            }
        }
        if ($req['action'] == 'delete') {
            unset($new_data[$req['Widgetid']]);
        } else {
            $new_data[$req['Widgetid']] = $req['Widgetid'];
        }

        $pg->$col = implode(",", $new_data);
        $pg->save();
    }

    /**
     * @param Request $request
     */
    public function getShiftwidget(Request $request)
    {
        $req = $request->all();
        $pg = Pagesetting::find(1);
        $from = $req['from'];
        $to = $req['to'];
        $id = $req['id'];

        if ($from == 1) {
            $from = 'fisrt_col_widget';
            $to = 'sec_col_widget';
        } else {
            $from = 'sec_col_widget';
            $to = 'fisrt_col_widget';
        }
        $new_data = [];
        $data = $pg->$from;
        $data = explode(",", $data);
        foreach ($data as $sngl) {
            if ($sngl != "") {
                $new_data[$sngl] = $sngl;
            }
        }
        unset($new_data[$id]);
        $pg->$from = implode(",", $new_data);
        $pg->$to = ($pg->$to == '') ? $id : $pg->$to . "," . $id;
        $pg->save();
    }

    /**
     * @param Request $request
     */
    public function getSwapewidget(Request $request)
    {
        $req = $request->all();
        $pg = Pagesetting::find(1);
        $itemlsid = $req['itemlsid'];

        if ($req['ContainerId'] == 1) {
            $col = 'fisrt_col_widget';
        } else {
            $col = 'sec_col_widget';
        }

        $pg->$col = $itemlsid;
        $pg->save();
    }


    /**
     * @param $settings
     * @param $widget_list
     * @return array
     */
    public function sortWidgets($settings, $widget_list)
    {
        $first_col = explode(",", $settings->fisrt_col_widget);
        $sec_col = explode(",", $settings->sec_col_widget);
        $used = array_merge($first_col, $sec_col);
        $first_col_data = [];
        $sec_col_data = [];
        $avail = [];
        $used_arr = [];
        //Availabe Widgets
        foreach ($widget_list as $widg) {
            if (!in_array($widg->id, $used)) {
                $avail[] = $widg;
            } else {
                $used_arr[$widg->id] = $widg;
            }
        }

        //Fill First Column
        foreach ($first_col as $col) {
            if ($col != "" and isset($used_arr[$col])) {
                $first_col_data[] = $used_arr[$col];
            }
        }

        //Fill Second Column
        foreach ($sec_col as $col) {
            if ($col != "") {
                $sec_col_data[] = @$used_arr[$col];
            }
        }

        return [
            'layout' => $settings->layout,
            'widget_list' => $avail,
            'fisrt_col_widget' => $first_col_data,
            'sec_col_widget' => $sec_col_data
        ];
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTest()
    {
        echo(Layouts::render("5784b8740b4e7"));
        die();

        return view('test');
    }

    public function getFormTest()
    {
        return view('ftest');
    }

    public function postFormTest(Request $request)
    {
        return \Response::json(['success' => true, 'html' => Forms::typeRules($request->get('type'))]);
    }

    public function postFormRule(Request $request)
    {
        return \Response::json(['success' => true, 'html' => Forms::ruler($request->get('rule'))]);
    }

    public function postFormRuleSave(Request $request)
    {
        return \Response::json(['success' => true, 'data' => Forms::rulerSave($request)]);
    }

    public function getFake()
    {
//       BBGenerateFakeUsers();
//        echo "<a href='/admin/users'>Fake user added </a>";
        return view('fake');
    }

    public function postFake(Request $request)
    {
        $count = $request->get('count_users');

        if ($count) {
            BBGenerateFakeUsers($count);
            $this->helpers->updatesession('Fake users are generated, count : ' . $count);

            return redirect()->back();
        }

        $this->helpers->updatesession('Count is Required !!! ', 'alert-danger');

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postTest(Request $request)
    {

        $value = $request->get('value');
        $data = BBGetFunctionData();
        if (isset($data[$value])) {
            if (isset($data[$value]['have_wizard'])) {
                return \Response::json(['data' => $data[$value], 'error' => false]);
            }

            if (isset($data[$value]['dropdown'])) {
                return \Response::json(['data' => $data[$value]['dropdown'], 'error' => false]);
            }

            return \Response::json(['data' => key($data[$value]), 'error' => false]);
        }

        return \Response::json(['error' => true]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postPut(Request $request)
    {
        $bb = $request->get('bb');
        if ($bb()) {
            return \Response::json(['data' => $bb(), 'error' => false]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRend(Request $request)
    {
        $bb = $request->get('rend');
        $id = $request->get('id');

        return \Response::json(['data' => $bb($id), 'error' => false]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function postBBform(Request $request)
    {
        Return '<div class="col-md-12">Editable<input type="checkbox" class="form-control input-md"><br><input name="name" type="radio">All<br><div><input type="radio" name="name" ><input type="number" placeholder="from" >to<input type="number"></div><br><input type="radio" name="name" ><input type="text" placeholder="Coma seperated" ></div>';
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTesting()
    {
        $modules = Moduledb::lists('title', 'id')->all();

        return view('testing', compact(['modules']));
    }

}
