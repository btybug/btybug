<?php

namespace Btybug\btybug\Middleware;

use Closure;

class HomeShortcodeMiddleware
{
    /**
     * @var
     */
    public $conf = [];
    protected $except = [
        '/admin/create/get-sortcodes',
        '/admin/create/test',
        '/admin/uploads/gears/component',
        '/admin',
        '/admin/manage/emails/update/1'
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
        $links = explode('/', $request->getRequestUri());
        unset($links[0]);
        $tester = false;
        if ($links[1] === 'admin') {
            if (!empty($this->except)) {
                return $response;
            }

            foreach ($this->except as $exept) {
                if ($request->getRequestUri() == $exept) {
                    $tester = true;
                }
            }
            if (!$tester) {
                return $response;
            }
        }
        //get registred shortcodes
        if (!method_exists($response, 'content')):
            return $response;
        endif;
        $content = $response->getContent();
        //replace loop
        $content = $this->searcer($content);
        $response->setContent($content);
        return $response;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function searcer($content)
    {
        $this->conf = \config::get('shortcode.code', []);
        foreach ($this->conf as $fn) {
            $content = $this->sortCoder($fn, $content);
            $posCode = "[$fn";
            if (strpos($content, $posCode)) {
                $content = $this->searcer($content);
            }
        }
        return $content;
    }

    /**
     * @param $fn
     * @param $content
     * @return mixed
     */
    protected function sortCoder($fn, $content)
    {
        $sc = 'sc';
        $posCode = "[$fn";
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
            $final_arg[$arg[0]] = $arg[1];
        }
        $code = $sc($fn, $final_arg);
        $newContent = str_replace('[' . $endLen . ']', $code, $content);
        return $newContent;
    }
}
