<?php

namespace App\Repositories;

use Illuminate\Http\Request;

interface AuthRepositoryInterface
{
    public function register(Request $request);
    public function attemptLogin(Request $request);
    public function logoutCurrentToken(Request $request);
    public function getUserById(Request $request);
}
