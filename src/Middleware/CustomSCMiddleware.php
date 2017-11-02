<?php

namespace Btybug\btybug\Middleware;

use Closure;

/**
 * Class CustomSCMiddleware
 * @package App\Http\Middleware
 */
class CustomSCMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    /**
     * @var
     */
    public $conf;
    protected $except = [
        '/admin/manage/emails/update/*',
        '/admin/manage/event/*',
        '/admin/create/get-sortcodes',
        '/admin/create/test',
        '/admin/uploads/gears/component',
        '/admin/console/structure/fields',
        '/admin/console/structure/forms/get-available-fields',
        '/admin/console/structure/forms',
        '/admin/console/structure/edit-forms',
        '/admin/manage/frontend/pages/settings/*',
        '/admin/manage/emails/get-forms-shortcodes',
        '/admin/manage/emails/data/*',
        '/admin/manage/emails/update/*'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($this->urlChecker($request->getRequestUri())) {
            return $response;
        }
        //get registred shortcodes
        if (!method_exists($response, 'content')):
            return $response;
        endif;
        $content = $response->getContent();
        //replace loop
        $content = $this->htmlContentHandler($content);
        $response->setContent($content);
        return $response;
    }

    public function urlChecker($url)
    {

        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        $explodeUrl = explode('/', $url);
        foreach ($this->except as $except) {
            $flag = 1;
            $exceptExp = explode('/', $except);
            foreach ($explodeUrl as $key => $url) {
                if (isset($exceptExp[$key])) {
                    if ($exceptExp[$key] == $url || $exceptExp[$key] == '*') {
                        $flag *= 1;
                    } else {

                        $flag *= 0;
                    }
                } else {
                    $flag *= 0;
                }

            }
            if ($flag) {
                return (bool)$flag;
            }
        }
        return false;
    }

    /**
     * @param $content
     * @return mixed
     */
    public function htmlContentHandler($content)
    {
        $this->conf = \config::get('shortcode.extra');
        foreach ($this->conf as $val) {
            $key = array_keys($val)[0];
            $fn = $val[$key];
            $content = $this->sortCoder($key, $fn, $content);
            $posCode = "[$key";
            if (strpos($content, $posCode)) {
                $content = $this->htmlContentHandler($content);
            }
        }
        return $content;
    }

    /**
     * @param $fn
     * @param $content
     * @return mixed
     */
    protected function sortCoder($key, $fn, $content)
    {
        $posCode = "[$key";
        $endLen = '';
        $pos = strpos($content, $posCode);
        if (!$pos) {
            return $content;
        }
        $pos = $pos + 1;
        for ($pos; $pos < strlen($content); $pos++) {
            if ($content[$pos] != ']') {
                $endLen .= $content[$pos];
            } else {
                break;
            }
        }
        $result = explode(' ', $endLen);

        //removing function name
        unset($result[0]);
        $final_arg = [];
        foreach (array_filter($result) as $key => $value) {
            $arg = explode('=', $value);
            if (isset($arg[0]) && isset($arg[1]))
                $final_arg[$arg[0]] = $arg[1];
        }
        $code = $fn($final_arg);
        $newContent = str_replace('[' . $endLen . ']', $code, $content);
        return $newContent;
    }
}
