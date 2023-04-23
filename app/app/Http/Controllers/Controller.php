<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Exception;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $message = [
        'success' => "",
        'error' => "",
    ];

    public $requestStore;

    public function store($request, $exe, $value) 
    {

        $this->requestStore = $request;

        try {
            DB::beginTransaction();
            return $this->$exe($request, $value);
        } catch(Exception $e) {
            DB::rollBack();
            print_r($e);
        }
    }

    public function view(String $route_name, Array $with) 
    {
        return view($route_name)->with($with);
    }

    public function addFlash(String $messageKbn, String $messageTxt)
    {
        $sessionStore = $this->requestStore->session();
        $messageKbn = 'message.' . $messageKbn;
        
        if ($sessionStore->has('$messageKbn')) {
            $sessionStore->push($messageKbn, $messageTxt);
        } else {
            $sessionStore->flash($messageKbn, $messageTxt);
        }
        
    }
}
