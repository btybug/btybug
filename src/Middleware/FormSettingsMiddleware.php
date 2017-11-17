<?php
/**
 * Copyright (c) 2017. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Btybug\btybug\Middleware;

use Closure;
use Response;
use Btybug\btybug\Helpers\helpers;
use Btybug\Console\Repository\FormEntriesRepository;
use Btybug\Console\Repository\FormsRepository;
use Btybug\Console\Services\FormService;
use Btybug\User\Repository\UserRepository;
use Session;

/**
 * Class FormSettingsMiddleware
 *
 * @package App\Http\Middleware
 */
class FormSettingsMiddleware
{
    /**
     * @var helpers
     */
    private $helper;
    /**
     * @var
     */
    private $response;
    private $user;
    private $form;
    private $formRepo;
    private $formService;
    private $entries;
    private $userRepo;

    /**
     * FormSettingsMiddleware constructor.
     */
    public function __construct(
        FormsRepository $formsRepository,
        FormService $formService,
        FormEntriesRepository $formEntries,
        UserRepository $userRepository
    )
    {
        $this->helper = new helpers;
        $this->formRepo = $formsRepository;
        $this->formService = $formService;
        $this->entries = $formEntries;
        $this->userRepo = $userRepository;
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
        if ($request->isMethod('post')) {
            $formID = $request->get('form_id');
            if ($this->form = $this->formRepo->find($formID)) {
                if ($this->form->blocked) return false;

                $result = $this->formService->validate($formID, $request->except('_token', 'form_id'));
                if ($result['errors']) return redirect()->back()->withErrors($result['errors'])->withInput();

                $collected = $this->formService->collectTables();
                $this->response = $collected->saveForm($request->except('_token', 'form_id'));

                $this->formService->newEntry($request, $this->response, $this->form);

                if ($this->response) {
                    if (\Auth::check()) {
                        $this->user = \Auth::user();
                    } elseif ($this->form->id == 1) {
                        $this->user = $this->userRepo->findBy('email', $request->users_email);
                    } else {
                        $this->user = null;
                    }
//                    \event(new FormSubmit($this->user, $this->form, $this->response));
                    return $this->options($request);
                }
                dd('error');

            }
        }

        return $next($request);
    }

    /**
     * @param $settings
     * @param $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function options($request)
    {
        $formID = $request->get('form_id');
        $settings = json_decode($this->form->settings, true);

        if (!$settings) return redirect()->back();

        if (isset($settings['error']) and $settings['error']) {
            if ($request->ajax()) {
                return Response::json(['error_messages' => $settings['validator']]);
            } else {
                return redirect()->back()->withErrors($settings['validator'])->withInput();
            }
        }


        if (isset($settings['event']) && !empty($settings['event'])) {
            $class = $settings['event'];
            \event(new $class($this->user, $this->form, $this->response));
        }

//        switch ($settings['message_type']) {
//            case "popup":
//                if ($settings['redirect_page']) {
//                    if ($request->ajax()) {
//                        Session::flash('message_code', 200);
//                        Session::flash('success_mes', $settings['message']);
//
//                        return Response::json(
//                            [
//                                'redirect_page' => $settings['redirect_page'],
//                                'message_code' => 200,
//                                'success_mes' => $settings['message']
//                            ]
//                        );
//                    } else {
//                        return redirect()->to($settings['redirect_page'])->with('message_code', 200)->with(
//                            'success_mes',
//                            $settings['message']
//                        );
//                    }
//                }
//
//                if ($request->ajax()) {
//                    return Response::json(['message_code' => 200, 'success_mes' => $settings['message']]);
//                } else {
//                    return redirect()->back()->with('message_code', 200)->with(
//                        'success_mes',
//                        $settings['message']
//                    );
//                }
//                break;
//            case "inline":
//                break;
//            case "alert":
//                if ($request->ajax()) {
//                    if ($settings['redirect_page']) {
//                        $this->helper->updatesession($settings['message']);
//
//                        return Response::json(['redirect_page' => $settings['redirect_page']]);
//                    } else {
//                        return Response::json(
//                            ['message_code' => 'alert', 'success_mes' => $settings['message']]
//                        );
//                    }
//                } else {
//                    $this->helper->updatesession($settings['message']);
//                }
//                break;
//        }

        if (isset($settings['redirect_page']) && $settings['redirect_page']) {
            if ($request->ajax()) {
                Session::flash('message_code', 200);
                Session::flash('message', isset($settings['message']) ? $settings['message'] : '');

                return Response::json(
                    [
                        'redirect_page' => $settings['redirect_page'],
                        'message_code' => 200,
                        'message' => isset($settings['message']) ? $settings['message'] : ''
                    ]
                );
            } else {
                return redirect()->to($settings['redirect_page'])->with('message_code', 200)->with(
                    'message',
                    isset($settings['message']) ? $settings['message'] : ''
                );
            }
        }

        if ($request->ajax()) {
            return Response::json(['message_code' => 200, 'message' => isset($settings['message']) ? $settings['message'] : '']);
        } else {
            return redirect()->back()->with('message_code', 200)->with(
                'message',
                isset($settings['message']) ? $settings['message'] : ''
            );
        }
    }
}

