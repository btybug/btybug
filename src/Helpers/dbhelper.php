<?php

namespace Btybug\btybug\Helpers;


use DB;
use Illuminate\Http\JsonResponse;
use Schema;

/**
 * Class dbhelper
 *
 * @package App\helpers
 */
class dbhelper
{

    /**
     * @var array
     */
    private $data = [];
    /**
     * @var string
     */
    private $table = '';

    /**
     * dbhelper constructor.
     */
    public function __construct()
    {

    }

    /**
     * This function provides the column names of given table from database
     *
     * @param type $table - name of tabe we want to get columns e.g. users,media,menues
     * @return Array of Column names
     */
    public function getTbCols($table)
    {
        $this->table = $table;
        $columns = Schema::getColumnListing($this->table);
        foreach ($columns as $column) {
            $this->data[$column] = $this->formatTitle($column);
        }
        $this->getExtraFields();

        return $this->data;
    }


    /**
     *
     * @param type $value
     * @return type
     */
    public function formatTitle($value)
    {
        return ucwords(str_replace(['_'], ' ', $value));
    }

    /**
     * This function get extra fields names from system and merge them inside the regular columns
     *
     *
     */
    public function getExtraFields()
    {
        //Get Form Attach to table
        $form = DB::table('forms')->where('table', $this->table)->select(['id'])->first();
        if ($form) {
            $f_id = $form->id;

            //Get Form Fields
            $fields = DB::table('form_fields')
                ->where('form_id', $f_id)
                ->where('method', 'custom')
                ->where('active', 1)
                ->select(['field'])->get();
            foreach ($fields as $field) {
                $this->data[$field->field] = $this->formatTitle($field->field);
            }
        }

    }

    /**
     * Format the custom fields from array to object properties
     *
     * @param type $obj
     * @return type
     */
    public function formatCustomFld($obj)
    {
        if ($obj->meta_data != "") {
            $extra = unserialize($obj->meta_data);
            if (is_array($extra)) {
                foreach ($extra as $key => $val) {
                    $obj->$key = $val;
                }
            }
        }

        return $obj;
    }

    /**
     * @param $obj
     * @param array $fields
     * @return mixed
     */
    public function formatAdditionalFld($obj, $fields = [])
    {
        if (!empty($fields)) {
            foreach ($fields as $key => $val) {
                $obj->$key = $val;
            }
        }

        return $obj;
    }

    /**
     *
     * @param type $form_id
     * @return type
     */
    public function getFormFields($form_id)
    {
        $this->data = [];
        $form = Forms::find($form_id);

        $core = explode(",", $form->core_fields);
        $custom = explode(",", $form->custom_fields);
        $fields = array_filter(array_merge($core, $custom));
        foreach ($fields as $field) {
            $this->data[$field] = $this->formatTitle($field);
        }
        $this->data['title'] = 'Title';
        $this->data['action'] = 'Action';

        return $this->data;
    }

    /**
     *
     * @param type $form_id
     * @return type
     */
    public function getCustomSelect($form_id)
    {
        $cols = [];
        $form = Forms::find($form_id);
        $ids = $form->custom_fields;
        $data = explode(',', $ids);

        foreach ($data as $rec) {
            $cols[] = $rec;
        }

        return $cols;
    }

    /**
     * Get Array of key value pair shape and retunr the json data fro table
     *
     * @param type $columns
     * @return type
     */
    public function getColumnsJson($columns)
    {
        $data = "";
        foreach ($columns as $key => $val) {
            $data[] = ($key != 'action' && $key != '#') ? ['data' => $key] : ['data' => $key, 'orderable' => false];
        }

        return json_encode($data);
    }

    /**
     * @param $tot_obj
     * @param $raw_data
     * @param $custom_columns
     * @return JsonResponse
     */
    public function addCustomData($tot_obj, $raw_data, $custom_columns)
    {
        $new_data = [];
        foreach ($raw_data as $sngl_rec) {
            $meta_arr = [];
            $meta = @$sngl_rec->meta_data;
            if ($meta != "") {
                $meta_arr = unserialize($meta);
            }
            foreach ($custom_columns as $sngl_col) {
                if (isset($sngl_rec->$sngl_col)) {
                    $sngl_rec->$sngl_col = @$meta_arr[$sngl_col];
                }
            }
            $new_data[] = $sngl_rec;
        }
        $tot_obj->data = $new_data;

        return new JsonResponse($tot_obj);
    }

    /**
     * @param $btns
     * @return string
     */
    public function actionBtns($btns)
    {
        $str = "";
        $map = [
            'delete_post' => 'delBtnPost',
            'delete' => 'deleteBtn',
            'view' => 'viewBtn',
            'edit' => 'editBtn',
            'settings' => 'settingsBtn',
            'mapping' => 'mappingBtn',
            'copy' => 'copyBtn',
            'variations' => 'variationsBtn',
            'view_same' => 'viewBtnSameWindow',
            'setting_same' => 'settingsBtnSameWindow',
            'settings_block' => 'settingsBtnBlock',
            'options' => 'optionsBtn',
            'list' => 'listBtn',
            'active' => 'activeBtn',
            'de_active' => 'deActiveBtn',
        ];
        foreach ($btns as $key => $data) {
            if ($key == 'delete_post') {
                $button = $map[$key];
                $str .= $this->$button($data['link'], $data['id']);
            } else {
                $button = $map[$key];
                $str .= $this->$button($data['link']);
            }
        }

        return $str;
    }

    public function plainBtn(
        $label = 'View',
        $link = "javascript:",
        $params = ['class' => 'btn btn-success btn-xs']
    )
    {
        $param_str = "";
        foreach ($params as $key => $val) {

            $param_str .= '' . $key . '="' . $val . '" ';
        }

        return '<a href="' . $link . '" ' . $param_str . '>' . $label . '</a>';
    }


    /**
     * @param string $link
     * @return string
     */
    public function listBtn($link = "#", $count = '0')
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-success btn-xs" ><i class="fa fa-list"></i> &nbsp;' . $count . '</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function optionsBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-success btn-xs" > &nbsp;<i class="fa fa-map-signs"></i> &nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function deleteBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-danger btn-primary btn-xs" onclick="return confirm(\'Are you sure to delete\')"> &nbsp;<i class="fa fa-trash"></i> &nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function variationsBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-default btn-primary btn-xs">&nbsp;<i class="fa fa-desktop"></i> Variations&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @param $id
     * @return string
     */
    public function delBtnPost($link = "#", $id)
    {
        return '<div class="pull-left m-r-10"><form method="post" action="' . $link . '"><input type="hidden" name="id" value="' . $id . '"/> <input type="hidden" name="_token" value="' . csrf_token() . '"/> <button type="submit" class="btn btn-danger btn-primary btn-xs" onclick="return confirm(\'are you sure\')">&nbsp;<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;</button> </form></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function editBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-default btn-primary btn-xs">&nbsp;<i class="fa fa-pencil-square-o"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function copyBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-default btn-info btn-xs">&nbsp;<i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function viewBtnSameWindow($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-default btn-success btn-xs">&nbsp;<i class="fa fa-eye set-iconz"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function viewBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" target="_blank" class="btn btn-default btn-success btn-xs">&nbsp;<i class="fa fa-eye set-iconz"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function settingsBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" target="_blank" class="btn btn-default btn-warning btn-xs">&nbsp;<i class="fa fa-cog set-iconz"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function settingsBtnSameWindow($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-default btn-warning btn-xs">&nbsp;<i class="fa fa-cog set-iconz"></i>&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function settingsBtnBlock($link = "#")
    {
        return '<div><a href="' . $link . '" class="btn btn-default btn-warning btn-block btn-xs">&nbsp;<i class="fa fa-cog set-iconz"></i>&nbsp;</a></div>';
    }


    /**
     * @param string $link
     * @return string
     */
    public function mappingBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" target="_blank" class="btn btn-default btn-success btn-xs">&nbsp;<i class="fa fa-sitemap"></i> Mapping&nbsp;</a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function activeBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-success btn-xs">&nbsp;<i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Activate"></i></a></div>';
    }

    /**
     * @param string $link
     * @return string
     */
    public function deActiveBtn($link = "#")
    {
        return '<div class="pull-left m-r-10"><a href="' . $link . '" class="btn btn-danger  btn-xs" >&nbsp;<i data-toggle="tooltip" data-placement="top" title="De Activate" class="fa fa-minus"></i></a></div>';
    }


    public function mkPrgBar($val, $class = '')
    {

        return '<div class="progress m-b-0"><div class="progress-bar ' . $class . '" role="progressbar" aria-valuenow="' . $val . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $val . '%;">' . $val . '%</div>';
    }

    /**
     * @return array
     */
    public function getTableNames()
    {
        $tab_arr = [];
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                $tab_arr[$value] = $value;
            }
        }

        return $tab_arr;
    }

    public function getTableColsByName($table)
    {
        $col_arr = [];
        $cols = \Schema::getColumnListing($table);

        if (count($cols)) {
            foreach ($cols as $key => $val) {
                $col_arr[$val] = $val;
            }
        }

        return $col_arr;
    }

    public function getDataWithTableAndCol($table, $column)
    {
        if (\Schema::hasTable($table)) {
            if (\Schema::hasColumn($table, $column)) {
                return DB::table($table)->select($column)->pluck($column, $column)->toArray();
            }
        }

        return [];
    }

}
