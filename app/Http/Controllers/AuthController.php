<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;  
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($data);

        return $result;

    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());

        return $result;
    }

    public function account(Request $request)
    {       
        return response()->json([
            'data' => $request->user()
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $result = $this->authService->register($data);

        return $result;
    }

    public function get_users() {
        
        $result = $this->userService->get_users();

        return $result;
    }

    public function update_user(Request $request) {
        $data = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
        ]);

        $result = $this->userService->update_user($data);

        return $result;
    }

    public function delete_user(Request $request){
        $data = $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);

        $result = $this->userService->delete_user($data);

        return $result;
    }

}
