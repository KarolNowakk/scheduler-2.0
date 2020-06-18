<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function store(Request $request)
    {
        $data = $this->validator($request->all())->validate();
    }

    public function validator($request)
    {
        return Validator::make($request, [
            'name' => 'string|max:50|required',
            'logo_path' => 'string|max:1000',
            'adress' => 'string|max:255'
        ]);
    }
}
