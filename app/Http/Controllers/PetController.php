<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pet;
use App\Models\PetBreed;
use App\Models\PetType;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    public function index()
    {
        $customer = $this->customer();
        $pets = $customer->pets()->with(['breed.petType', 'bookings'])->latest()->get();

        return view('pets.index', compact('pets'));
    }

    public function create(Request $request)
    {
        $this->customer();
        if ($request->filled('booking_service_id') && Service::where('service_status', 'active')->where('service_id', $request->booking_service_id)->exists()) {
            session(['booking_return_service_id' => $request->booking_service_id]);
        }
        $types = PetType::orderBy('pet_type_name')->get();

        return view('pets.select-type', compact('types'));
    }

    public function createForm(Request $request)
    {
        $this->customer();
        $typeId = $request->query('pet_type_id');

        if (! $typeId) {
            return redirect()->route('pets.create')->with('type_error', 'กรุณาเลือกประเภทสัตว์ก่อน');
        }

        $selectedType = PetType::findOrFail($typeId);
        $breeds = PetBreed::where('pet_type_id', $selectedType->pet_type_id)
            ->orderBy('pet_breed_name')
            ->get();

        return view('pets.create-form', compact('selectedType', 'breeds'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePet($request);
        if ($request->hasFile('pet_image')) {
            $data['pet_image'] = $request->file('pet_image')->store('pet-images', 'public');
        }
        $pet = $this->customer()->pets()->create($data);
        $serviceId = session()->pull('booking_return_service_id');
        if ($serviceId && Service::where('service_status', 'active')->where('service_id', $serviceId)->exists()) {
            return redirect()->route('booking.show', ['serviceId' => $serviceId, 'pet_id' => $pet->id]);
        }

        return redirect()->route('pets.index')->with('success', 'เพิ่มข้อมูลสัตว์เลี้ยงแล้ว');
    }

    public function edit($id, Request $request)
    {
        $pet = $this->customer()->pets()->with('breed')->findOrFail($id);
        $selectedTypeId = $request->query('pet_type_id', old('pet_type_id', $pet->breed->pet_type_id));
        $showTypeError = $request->query->has('pet_type_id') && ! $selectedTypeId;
        $types = PetType::orderBy('pet_type_name')->get();
        $breeds = $selectedTypeId
            ? PetBreed::where('pet_type_id', $selectedTypeId)->orderBy('pet_breed_name')->get()
            : collect();
        $allBreeds = PetBreed::orderBy('pet_breed_name')
            ->get(['pet_breed_id', 'pet_type_id', 'pet_breed_name']);

        return view('pets.form', compact('pet', 'types', 'breeds', 'allBreeds', 'selectedTypeId', 'showTypeError') + ['isEdit' => true]);
    }

    public function update(Request $request, $id)
    {
        $pet = $this->customer()->pets()->findOrFail($id);
        $data = $this->validatePet($request);
        unset($data['pet_image']);

        if ($request->hasFile('pet_image')) {
            $previousImage = $pet->pet_image;
            $data['pet_image'] = $request->file('pet_image')->store('pet-images', 'public');
            $pet->update($data);

            if ($previousImage) {
                Storage::disk('public')->delete($previousImage);
            }
        } else {
            $pet->update($data);
        }

        return redirect()->route('pets.index')->with('success', 'แก้ไขข้อมูลสัตว์เลี้ยงแล้ว');
    }

    public function destroy($id)
    {
        $pet = $this->customer()->pets()->findOrFail($id);
        $pet->delete();

        return redirect()->route('pets.index')->with('success', 'ลบข้อมูลสัตว์เลี้ยงแล้ว');
    }

    private function customer(): Customer
    {
        abort_unless(auth()->user()->user_role === 'customer', 403);

        return Customer::firstOrCreate(['user_id' => auth()->id()]);
    }

    private function validatePet(Request $request): array
    {
        $data = $request->validate([
            'pet_name' => ['required', 'string', 'max:100'],
            'pet_type_id' => ['required', 'exists:pet_types,pet_type_id'],
            'pet_birth_unknown' => ['nullable', 'boolean'],
            'pet_birth' => [Rule::requiredIf(! $request->boolean('pet_birth_unknown')), 'nullable', 'date', 'before_or_equal:today'],
            'pet_gender' => ['required', 'in:male,female'],
            'pet_weight' => ['required', 'numeric', 'gt:0', 'max:999.99'],
            'pet_notes' => ['nullable', 'string', 'max:2000'],
            'pet_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'pet_breed_id' => [
                'required',
                Rule::exists('pet_breeds', 'pet_breed_id')->where(function ($query) use ($request) {
                    $query->where('pet_type_id', $request->input('pet_type_id'));
                }),
            ],
        ]);

        $data['pet_birth'] = $request->boolean('pet_birth_unknown') ? null : $data['pet_birth'];
        unset($data['pet_birth_unknown']);
        unset($data['pet_type_id']);

        return $data;
    }
}
