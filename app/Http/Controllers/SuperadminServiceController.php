<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperadminServiceController extends Controller
{
    public function index()
    {
        $this->checkSuperadmin();
        $services = Service::orderBy('service_id')->get();

        return view('superadmin.service-manage.services', compact('services'));
    }

    public function create()
    {
        $this->checkSuperadmin();
        $service = new Service;

        return view('superadmin.service-manage.service-form', compact('service'));
    }

    public function store(Request $request)
    {
        $this->checkSuperadmin();
        $data = $this->validateService($request);
        unset($data['service_image']);
        if ($request->hasFile('service_image')) {
            $data['service_image_path'] = $request->file('service_image')->store('service-images', 'public');
        }
        Service::create($data);

        return redirect()->route('superadmin_services_index')->with('success', 'เพิ่มบริการแล้ว');
    }

    public function edit($serviceId)
    {
        $this->checkSuperadmin();
        $service = Service::findOrFail($serviceId);

        return view('superadmin.service-manage.service-form', compact('service'));
    }

    public function update(Request $request, $serviceId)
    {
        $this->checkSuperadmin();
        $service = Service::findOrFail($serviceId);
        $data = $this->validateService($request);
        unset($data['service_image']);
        if ($request->hasFile('service_image')) {
            $previousImage = $service->service_image_path;
            $data['service_image_path'] = $request->file('service_image')->store('service-images', 'public');
            $service->update($data);
            if ($previousImage) {
                Storage::disk('public')->delete($previousImage);
            }
        } else {
            $service->update($data);
        }

        return redirect()->route('superadmin_services_index')->with('success', 'บันทึกการแก้ไขบริการแล้ว');
    }

    public function destroy($serviceId)
    {
        $this->checkSuperadmin();
        Service::findOrFail($serviceId)->delete();

        return redirect()->route('superadmin_services_index')->with('success', 'นำบริการออกจากรายการแล้ว');
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
            'service_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function checkSuperadmin(): void
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);
    }
}
