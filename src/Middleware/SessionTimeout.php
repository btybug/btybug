<?php namespace Btybug\btybug\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;

//use Btybug\btybug\Helpers\helpers;
//use Btybug\btybug\Repositories\AdminsettingRepository as Settings;
//TODO Clean or remove
class SessionTimeout
{
    protected $session;
    protected $timeout;
    protected $auth;
    private $helpers;
    private $settings;

    public function __construct(Guard $auth, Store $session
//        ,Settings $settings
    )
    {
        $this->session = $session;
        $this->auth = $auth;
//        $this->settings = $settings;
//        $this->helpers = new helpers;
//        $sessionOutTimeInMinutes = $this->settings->getSystemTimeOut();
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
//        if($this->auth->user()){
//            if(!$this->session->has('lastActivityTime')){
//                $this->session->put('lastActivityTime',time());
//            }elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){
//                $this->session->forget('lastActivityTime');
//                $this->auth->logout();
//                $this->helpers->updatesession('You had not activity in '.$this->timeout/60 .' minutes ago.', 'alert-danger');
//                return redirect('login');
//            }
//            $this->session->put('lastActivityTime',time());
//        }

        return $next($request);
    }

}