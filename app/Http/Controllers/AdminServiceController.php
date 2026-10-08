<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminServiceController extends Controller
{
    public function index()
    {
        $this->checkAdmin();
        $services = Service::orderBy('service_id')->get();

        return view('service-manage.services', compact('services'));
    }

    public function create()
    {
        $this->checkAdmin();
        $service = new Service;

        return view('service-manage.service-form', compact('service'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        Service::create($this->validateService($request));

        return redirect()->route('admin.services.index')->with('success', 'เพิ่มบริการแล้ว');
    }

    public function edit($serviceId)
    {
        $this->checkAdmin();
        $service = Service::findOrFail($serviceId);

        return view('service-manage.service-form', compact('service'));
    }

    public function update(Request $request, $serviceId)
    {
        $this->checkAdmin();
        $service = Service::findOrFail($serviceId);
        $service->update($this->validateService($request));

        return redirect()->route('admin.services.index')->with('success', 'บันทึกการแก้ไขบริการแล้ว');
    }

    public function destroy($serviceId)
    {
        $this->checkAdmin();
        Service::findOrFail($serviceId)->delete();

        return redirect()->route('admin.services.index')->with('success', 'นำบริการออกจากรายการแล้ว');
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'service_description' => ['required', 'string', 'max:5000'],
            'service_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'service_status' => ['required', 'in:active,inactive'],
            'price_small' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_medium' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_large' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);
    }

    private function checkAdmin(): void
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);
    }
}
