<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;

class RegisterBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-register');
  }
}
