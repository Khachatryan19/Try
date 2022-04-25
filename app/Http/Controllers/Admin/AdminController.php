<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{

    protected function role(): string
    {
        $role = Auth::user()->role;

        return $role;
    }

    public function getAdminUsername()
    {
        if($this->role() !== 'admin'){
            return new JsonResponse([
                'message' => 'Unauthorized'
            ], 401);
        }

        return new JsonResponse([
            'user' => Auth::user()->username
        ], 200);
    }
}
