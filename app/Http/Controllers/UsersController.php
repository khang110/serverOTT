<?php

namespace App\Http\Controllers;

use App\Models\User;
class UsersController extends Controller
{
    public function index($logged_id)
    {
        return User::where('id','!=',$logged_id)->get();

    }
}
