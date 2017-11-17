<?php namespace Btybug\btybug\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;

//use Btybug\btybug\Helpers\helpers;
//use Btybug\btybug\Repositories\AdminsettingRepository as Settings;
//TODO Clean or remove
class SystemSettings
{
    protected $session;
    protected $timeout;
    protected $auth;
    private $helpers;
    private $settings;
    private $timzones;

    public function __construct(Guard $auth, Store $session
//        , Settings $settings
    )
    {
        $this->session = $session;
        $this->auth = $auth;
//        $this->settings = $settings;
        $this->timzones = \Config::get('timezone');
//        $this->helpers = new helpers;
//        $this->settings = $this->settings->getSystemSettings();
        // to seconds
//        $this->timeout = (int)$sessionOutTimeInMinutes * 60;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        if(isset($this->settings['timezone_id']))
//            date_default_timezone_set($this->timzones[$this->settings['timezone_id']]);
//
//        if(isset($this->settings['default_language']))
//            setlocale(LC_TIME,$this->settings['default_language']);
//
//        if(isset($this->settings['error_display']))
//            Config::set('app.debug', $this->settings['error_display']);
//
//        if(isset($this->settings['browser_close']))
//            Config::set('session.expire_on_close', $this->settings['browser_close']);

        return $next($request);
    }

}