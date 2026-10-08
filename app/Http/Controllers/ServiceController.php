<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('service_status', 'active')->get()->map(function ($service) {
            $prices = array_filter([
                (float) $service->price_small,
                (float) $service->price_medium,
                (float) $service->price_large,
            ], fn ($price) => $price > 0);

            $service->starting_price = count($prices) ? min($prices) : null;
            $service->duration_text = $this->formatDuration($service->service_duration_minutes);

            return $service;
        });

        return view('services', compact('services'));
    }

    public function show($id)
    {
        $service = Service::where('service_status', 'active')->findOrFail($id);
        $prices = array_filter([
            (float) $service->price_small,
            (float) $service->price_medium,
            (float) $service->price_large,
        ], fn ($price) => $price > 0);

        $startingPrice = count($prices) ? min($prices) : null;
        $durationText = $this->formatDuration($service->service_duration_minutes);

        return view('service-detail', compact('service', 'startingPrice', 'durationText'));
    }

    private function formatDuration(int $minutes): string
    {
        if ($minutes >= 60 && $minutes % 60 === 0) {
            return ($minutes / 60).' ชม.';
        }

        return $minutes.' นาที';
    }
}
