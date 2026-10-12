<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperadminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->checkSuperadmin();
        $search = trim($request->query('search', ''));
        $customersQuery = Customer::query()
            ->with('user:id,name,email,user_phone')
            ->withCount(['pets as pet_count'])
            ->whereHas('user');
        if ($search !== '') {
            $keyword = '%'.$search.'%';
            $customersQuery->whereHas('user', function ($query) use ($keyword) {
                $query->where(function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('user_phone', 'like', $keyword);
                });
            });
        }
        $customers = $customersQuery->orderByDesc('created_at')->get();

        return view('superadmin.customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $this->checkSuperadmin();

        return view('superadmin.customers.form', ['customer' => null]);
    }

    public function store(Request $request)
    {
        $this->checkSuperadmin();
        $data = $this->validateCustomer($request, true);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_phone' => $data['user_phone'] ?? null,
                'password' => $data['password'],
                'user_role' => 'customer',
            ]);
            Customer::create(['user_id' => $user->id]);
        });

        return redirect()->route('superadmin.customers.index')->with('success', 'เพิ่มข้อมูลลูกค้าแล้ว');
    }

    public function show($customerId)
    {
        $this->checkSuperadmin();
        $customer = Customer::whereNull('deleted_at')->with(['user', 'pets' => function ($query) {
            $query->with('breed.petType')->orderBy('pet_name');
        }])->findOrFail($customerId);
        $bookings = $customer->bookings()
            ->with([
                'service' => fn ($query) => $query->withTrashed(),
                'pet' => fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_start_time')
            ->limit(10)
            ->get();

        return view('superadmin.customers.show', compact('customer', 'bookings'));
    }

    public function edit($customerId)
    {
        $this->checkSuperadmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);

        return view('superadmin.customers.form', compact('customer'));
    }

    public function update(Request $request, $customerId)
    {
        $this->checkSuperadmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);
        $data = $this->validateCustomer($request, false, $customer->user_id);

        $customer->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_phone' => $data['user_phone'] ?? null,
        ]);

        return redirect()->route('superadmin.customers.index')->with('success', 'บันทึกข้อมูลลูกค้าแล้ว');
    }

    public function destroy($customerId)
    {
        $this->checkSuperadmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);

        DB::transaction(function () use ($customer) {
            $customer->pets()->whereNull('deleted_at')->update(['deleted_at' => now()]);
            $customer->delete();
            $customer->user->delete();
        });

        return redirect()->route('superadmin.customers.index')->with('success', 'นำข้อมูลลูกค้าออกจากรายการแล้ว');
    }

    private function validateCustomer(Request $request, bool $includePassword, $userId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($userId ? ','.$userId : '')],
            'user_phone' => ['nullable', 'string', 'max:20'],
        ];
        if ($includePassword) {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $request->validate($rules);
    }

    private function checkSuperadmin(): void
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);
    }
}
