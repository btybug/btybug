<?php
/**
 * Copyright (c) 2016.
 * *
 *  * Created by PhpStorm.
 *  * User: Edo
 *  * Date: 10/3/2016
 *  * Time: 10:44 PM
 *
 */

namespace Btybug\btybug\Helpers;

use App\CustomField;
use App\CustomFieldValue;
use App\Designation;
use DB;
use Entrust;
use File;
use Services_Twilio;
use Services_Twilio_RestException;
use Session;

Class MainHelper
{
    public static function sendSMS($to, $message)
    {
        $client = new Services_Twilio(config('twilio.sid'), config('twilio.token'));
        try {
            $message = $client->account->messages->create(array(
                "From" => config('twilio.from'),
                "To" => $to,
                "Body" => $message
            ));
            return 1;
        } catch (Services_Twilio_RestException $e) {
            return $e->getMessage();
        }
    }

    public static function getServiceTime($priority)
    {
        $service_time = \App\ServiceTime::where('priority', '=', $priority)->first();

        $time = array();

        if ($service_time) {
            $time['response_time'] = $service_time->response_time;
            $time['response_time_type'] = $service_time->response_time_type;
            $time['resolution_time'] = $service_time->resolution_time;
            $time['resolution_time_type'] = $service_time->resolution_time_type;

            if ($service_time->response_unit == 'hour')
                $time['response_time'] *= 60;
            elseif ($service_time->response_unit == 'days')
                $time['response_time'] *= 60 * 24;

            if ($service_time->resolution_unit == 'hour')
                $time['resolution_time'] *= 60;
            elseif ($service_time->resolution_unit == 'days')
                $time['resolution_time'] *= 60 * 24;
        } else {
            $time['response_time'] = 60;
            $time['response_time_type'] = 'business_hour';
            $time['resolution_time'] = 240;
            $time['resolution_time_type'] = 'business_hour';
        }
        return $time;
    }

    public static function showTicketStatus($status)
    {
        if ($status == 'open')
            return '<span class="badge badge-danger span-highlight">Open</span>';
        elseif ($status == 'pending')
            return '<span class="badge badge-warning span-highlight">Pending</span>';
        elseif ($status == 'wait-on-customer-reply')
            return '<span class="badge badge-info span-highlight">Waiting for customer reply</span>';
        elseif ($status == 'wait-on-third-party-reply')
            return '<span class="badge badge-primary span-highlight">Waiting for third party reply</span>';
        elseif ($status == 'close')
            return '<span class="badge badge-success span-highlight">Closed</span>';
    }

    public static function showTicketPriority($priority)
    {
        if ($priority == 'low')
            return '<span class="badge badge-default span-highlight">Low</span>';
        elseif ($priority == 'medium')
            return '<span class="badge badge-info span-highlight">Medium</span>';
        elseif ($priority == 'high')
            return '<span class="badge badge-warning span-highlight">High</span>';
        elseif ($priority == 'critical')
            return '<span class="badge badge-danger span-highlight">Critical</span>';
    }

    public static function ticketTime($ticket, $type)
    {
        if ($type == 'response') {
            $word = 'responded';
            $time = 'response_due_time';
        } else {
            $word = 'resolved';
            $time = 'resolution_due_time';
        }

        if ($ticket[$word])
            echo '<span class="badge badge-success">' . ucfirst($word) . '</span>';
        elseif (!$ticket[$word] && strtotime($ticket[$time]) >= time())
            echo '<span class="badge badge-warning">Due</span>';
        elseif (!$ticket[$word] && strtotime($ticket[$time]) < time())
            echo '<span class="badge badge-danger">Overdue</span>';
    }

    public static function calculateDueTime($time, $created_at)
    {
        $days = config('list.week');
        foreach ($days as $d) {
            $business_hour[$d]['start'] = '00:00:00';
            $business_hour[$d]['end'] = '00:00:00';
        }

        $business_hours = \App\BusinessHour::all();
        foreach ($business_hours as $hour) {
            $business_hour[$hour->day]['start'] = $hour->start;
            $business_hour[$hour->day]['end'] = $hour->end;
        }
        $holidays = \App\Holiday::where('date', '>=', date('Y-m-d', strtotime($created_at)))->lists('date')->all();

        while ($time > 0) {
            $date = (isset($date)) ? date('Y-m-d', strtotime($date . ' +1 day')) : $created_at;

            $day = date('l', strtotime($date));

            $end = date('Y-m-d', strtotime($date)) . ' ' . $business_hour[$day]['end'];
            $start = (!isset($start)) ? $date : date('Y-m-d', strtotime($date)) . ' ' . $business_hour[$day]['start'];

            if (in_array(date('Y-m-d', strtotime($date)), $holidays))
                continue;

            $diff = round((strtotime($end) - strtotime($start)) / 60);

            if ($time <= $diff)
                return date('Y-m-d, H:i', (strtotime($start) + $time * 60));
            else
                $time -= $diff;
        }

    }

    public static function help($content = '')
    {
        echo '<a data-trigger="focus" role="button" tabindex="0" class="additional-icon" data-placement="bottom" data-toggle="popover" title="Help" data-content="' . $content . '" ><i class="fa fa-question"></i></a>';
    }

    public static function visitorCount($name, $id)
    {
        $cookie = $name . '_' . $id;
        if (!isset($_COOKIE[$cookie])) {
            setcookie($cookie, 1, time() + config('session.cookie_lifetime'));
            return true;
        } else
            return false;
    }

    public static function convertToHoursMins($time, $format = '%d:%d')
    {
        settype($time, 'integer');
        if ($time < 1) {
            return sprintf($format, 0, 0);
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public static function toWord($word)
    {
        $word = str_replace('_', ' ', $word);
        $word = str_replace('-', ' ', $word);
        $word = ucwords($word);
        return $word;
    }

    public static function activityShow()
    {
        $side = ['left', 'right'];
        $index = array_rand($side);
        echo $side[$index];
    }

    public static function showColor()
    {
        $side = ['default', 'success', 'danger', 'info', 'warning'];
        $index = array_rand($side);
        echo $side[$index];
    }

    public static function verifyCsrf($token)
    {
        if ($token == Session::token())
            return 1;
        else
            return 0;
    }

    public static function createSlug($string)
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
        return $slug;
    }

    public static function getCustomFields($form, $custom_field_values = array())
    {

        $custom_fields = CustomField::whereForm($form)->get();

        foreach ($custom_fields as $custom_field) {

            $c_values = (array_key_exists($custom_field->field_name, $custom_field_values)) ? $custom_field_values[$custom_field->field_name] : '';
            $field_values = explode(',', $custom_field->field_values);

            $required = '';

            if ($custom_field->field_required)
                $required = 'required';

            echo '<div class="form-group">';
            echo '<label for="' . $custom_field->field_name . '">' . $custom_field->field_title . '</label>';

            if ($custom_field->field_type == 'select') {
                echo '<select class="form-control input-xlarge select2me" placeholder="Select One" id="' . $custom_field->field_name . '" name="' . $custom_field->field_name . '"' . $required . '>
			  	<option value=""></option>';
                foreach ($field_values as $field_value) {
                    if ($field_value == $c_values)
                        echo '<option value="' . $field_value . '" selected>' . ucfirst($field_value) . '</option>';
                    else
                        echo '<option value="' . $field_value . '">' . ucfirst($field_value) . '</option>';
                }
                echo '</select>';
            } elseif ($custom_field->field_type == 'radio') {
                echo '<div>
					<div class="radio">';
                foreach ($field_values as $field_value) {
                    if ($field_value == $c_values)
                        $checked = "checked";
                    else
                        $checked = "";
                    echo '<label><input type="radio" name="' . $custom_field->field_name . '" id="' . $custom_field->field_name . '" value="' . $field_value . '" ' . $required . ' ' . $checked . ' > ' . ucfirst($field_value) . '</label> ';
                }
                echo '</div>
				</div>';
            } elseif ($custom_field->field_type == 'checkbox') {
                echo '<div>
					<div class="checkbox">';
                foreach ($field_values as $field_value) {
                    if ($field_value == $c_values)
                        $checked = "checked";
                    else
                        $checked = "";
                    echo '<label><input type="checkbox" name="' . $custom_field->field_name . '" id="' . $custom_field->field_name . '" value="' . $field_value . '" ' . $checked . '> ' . ucfirst($field_value) . '</label> ';
                }
                echo '</div>
				</div>';
            } elseif ($custom_field->field_type == 'textarea')
                echo '<textarea class="form-control" placeholder="Enter ' . $custom_field->field_title . '" name="' . $custom_field->field_name . '" cols="30" rows="3" id="' . $custom_field->field_name . '"' . $required . '>' . $c_values . '</textarea>';
            else
                echo '<input class="form-control" value="' . $c_values . '" placeholder="Enter ' . $custom_field->field_title . '" name="' . $custom_field->field_name . '" type="' . $custom_field->field_type . '" value="" id="' . $custom_field->field_name . '"' . $required . '>';
            echo '</div>';
        }
    }

    public static function putCustomHeads($form, $col_heads)
    {
        $custom_fields = CustomField::whereForm($form)->get();
        foreach ($custom_fields as $custom_field)
            array_push($col_heads, $custom_field->field_title);
        return $col_heads;
    }

    public static function fetchCustomValues($form)
    {
        $rows = DB::table('custom_fields')
            ->join('custom_field_values', 'custom_field_values.field_id', '=', 'custom_fields.id')
            ->where('form', '=', $form)
            ->select(DB::raw('unique_id,field_id,value'))
            ->get();
        $values = array();
        foreach ($rows as $row)
            $values[$row->unique_id][$row->field_id] = $row->value;

        return $values;
    }

    public static function getCustomFieldValues($form, $id)
    {
        return DB::table('custom_fields')
            ->join('custom_field_values', 'custom_field_values.field_id', '=', 'custom_fields.id')
            ->where('form', '=', $form)
            ->where('unique_id', '=', $id)
            ->lists('value', 'field_name');
    }

    public static function getCustomColId($form)
    {
        return CustomField::whereForm($form)->lists('id');
    }

    public static function storeCustomField($form, $id, $request)
    {
        $custom_fields = CustomField::whereForm($form)->get();
        foreach ($custom_fields as $custom_field) {
            $custom_field_value = new CustomFieldValue;
            $custom_field_value->value = $request[$custom_field->field_name];
            $custom_field_value->field_id = $custom_field->id;
            $custom_field_value->unique_id = $id;
            $custom_field_value->save();
        }
    }

    public static function updateCustomField($form, $id, $request)
    {
        $custom_fields = CustomField::whereForm($form)->get();
        foreach ($custom_fields as $custom_field) {
            $value = array_key_exists($custom_field->field_name, $request) ? $request[$custom_field->field_name] : '';
            $custom = DB::table('custom_fields')
                ->join('custom_field_values', 'custom_field_values.field_id', '=', 'custom_fields.id')
                ->where('form', '=', $form)
                ->where('field_name', '=', $custom_field->field_name)
                ->where('unique_id', '=', $id)
                ->select(DB::raw('custom_field_values.id'))
                ->first();

            if ($custom)
                $custom_field_value = CustomFieldValue::find($custom->id);
            else
                $custom_field_value = new CustomFieldValue;
            $custom_field_value->value = $value;
            $custom_field_value->field_id = $custom_field->id;
            $custom_field_value->unique_id = $id;
            $custom_field_value->save();
        }
    }

    public static function deleteCustomField($form, $id)
    {
        $data = DB::table('custom_fields')
            ->join('custom_field_values', 'custom_field_values.field_id', '=', 'custom_fields.id')
            ->where('form', '=', $form)
            ->where('unique_id', '=', $id)
            ->delete();
    }

    public static function showDateTime($time = '')
    {
        if ($time == '')
            return;

        if (config('config.date_format') == 'dd mm YYYY')
            $format = 'd-m-Y';
        elseif (config('config.date_format') == 'dd MM YYYY')
            $format = 'd-M-Y';
        elseif (config('config.date_format') == 'mm dd YYYY')
            $format = 'm-d-Y';
        elseif (config('config.date_format') == 'MM dd YYYY')
            $format = 'M-d-Y';
        else
            $format = 'd M Y';

        if (config('config.time_format') == '24hrs')
            return date($format . ',H:i', strtotime($time));
        else
            return date($format . ',h:i a', strtotime($time));
    }

    public static function showTime($time = '')
    {
        if ($time == '')
            return;
        if (config('config.time_format') == '24hrs')
            return date('H:i', strtotime($time));
        else
            return date('h:i a', strtotime($time));
    }

    public static function showDate($date = '')
    {
        if ($date == '' || $date == null)
            return;
        return date('d M Y', strtotime($date));
    }

    public static function getTranslationWords()
    {
        return File::getRequire(base_path() . config('paths.LANGUAGE_PATH'));
    }

    public static function getConfiguration()
    {
        return File::getRequire(base_path() . config('paths.CONFIG_PATH'));
    }

    public static function getServices()
    {
        return File::getRequire(base_path() . config('paths.SERVICE_PATH'));
    }

    public static function getPages()
    {
        return File::getRequire(base_path() . config('paths.PAGE_PATH'));
    }

    public static function getMail()
    {
        return File::getRequire(base_path() . config('paths.MAIL_PATH'));
    }

    public static function getSMS()
    {
        return File::getRequire(base_path() . config('paths.SMS_PATH'));
    }

    public static function getTemplate()
    {
        return File::getRequire(base_path() . config('paths.TEMPLATE_PATH'));
    }

    public static function getSMSTemplate()
    {
        return File::getRequire(base_path() . config('paths.SMS_TEMPLATE_PATH'));
    }

    public static function getAllLanguages()
    {
        return File::getRequire(base_path() . config('paths.LANG_PATH'));
    }

    public static function getAllTimezones()
    {
        return File::getRequire(base_path() . config('paths.TIMEZONE_PATH'));
    }

    public static function activityColorShow()
    {
        $color = ['warning', 'danger', 'success', 'info', ''];
        $index = array_rand($color);
        echo $color[$index];
    }

    public static function getMode()
    {
        return config('constants.MODE');
    }

    public static function getYears($start_year = 1980, $end_year = 2020)
    {
        for ($i = $start_year; $i <= $end_year; $i++)
            $years[$i] = $i;
        return $years;
    }

    public static function getEnumValues($table, $column)
    {
        $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum = array_add($enum, $v, ucwords($v));
        }
        return $enum;
    }

    public static function writeResult($col_data)
    {
        $datatable['aaData'] = $col_data;
        $fp = fopen('data.txt', 'w');
        fwrite($fp, json_encode($datatable));
        fclose($fp);
    }

    public static function getAvatar($id)
    {
        $user = \App\User::find($id);
        $profile = $user->Profile;
        $name = ($user->name) ?: $user->username;
        foreach ($user->roles as $role)
            $role_name = $role->display_name;
        $tooltip = ucwords($name) . ' (Role: ' . $role_name . ')';
        $tooltip .= (!Entrust::hasRole('user')) ? ' Email: ' . $user->email : '';

        if (isset($profile->photo))
            echo '<img src="/assets/user/' . DOMAIN . '/' . $profile->photo . '" class="media-object img-circle" style="width:57px;" alt="User avatar" data-toggle="tooltip" title="' . $tooltip . '">';
        else
            echo '<div class="textAvatar" data-toggle="tooltip" title="' . $tooltip . '">' . $name . '</div>';
    }

    public static function hideEmail($email)
    {
        $var = explode('@', $email);
        return 'xxxxx@' . $var[1];
    }


    public static function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', "", $string);
    }

    public static function mynl2br($string)
    {
        $string = str_replace("'", "&#039;", $string);
        $string = nl2br($string);
        return ($string);
    }

    public static function inWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . Helper::inWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . Helper::inWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = Helper::inWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= Helper::inWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}

?>
