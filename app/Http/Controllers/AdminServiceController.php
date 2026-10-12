<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class AdminServiceController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);

        $services = Service::orderBy('service_id')->get();

        return view('service-manage.services', compact('services'));
    }
}
