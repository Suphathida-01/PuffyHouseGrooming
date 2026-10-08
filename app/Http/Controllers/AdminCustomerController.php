<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->checkAdmin();
        $search = trim($request->query('search', ''));
        $sql = "SELECT customers.customer_id, users.name, users.email, users.user_phone,
                customers.created_at,
                (SELECT COUNT(*) FROM pets WHERE pets.customer_id = customers.customer_id AND pets.deleted_at IS NULL) AS pet_count
            FROM customers
            JOIN users ON users.id = customers.user_id
            WHERE customers.deleted_at IS NULL AND users.deleted_at IS NULL";
        $bindings = [];

        if ($search !== '') {
            $sql .= ' AND (users.name LIKE ? OR users.email LIKE ? OR users.user_phone LIKE ?)';
            $keyword = '%'.$search.'%';
            $bindings = [$keyword, $keyword, $keyword];
        }

        $sql .= ' ORDER BY customers.created_at DESC';
        $customers = DB::select($sql, $bindings);

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $this->checkAdmin();

        return view('admin.customers.form', ['customer' => null]);
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
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

        return redirect()->route('admin.customers.index')->with('success', 'เพิ่มข้อมูลลูกค้าแล้ว');
    }

    public function show($customerId)
    {
        $this->checkAdmin();
        $customer = Customer::whereNull('deleted_at')->with(['user', 'pets' => function ($query) {
            $query->with('breed.petType')->orderBy('pet_name');
        }])->findOrFail($customerId);
        $bookings = DB::select(
            "SELECT bookings.booking_id, bookings.booking_date, bookings.booking_start_time,
                bookings.booking_status, services.service_name, pets.pet_name
            FROM bookings
            JOIN services ON services.service_id = bookings.service_id
            JOIN pets ON pets.id = bookings.pet_id
            WHERE bookings.customer_id = ? AND bookings.deleted_at IS NULL
            ORDER BY bookings.booking_date DESC, bookings.booking_start_time DESC
            LIMIT 10",
            [$customerId],
        );

        return view('admin.customers.show', compact('customer', 'bookings'));
    }

    public function edit($customerId)
    {
        $this->checkAdmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);

        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, $customerId)
    {
        $this->checkAdmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);
        $data = $this->validateCustomer($request, false, $customer->user_id);
        $customer->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_phone' => $data['user_phone'] ?? null,
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'บันทึกข้อมูลลูกค้าแล้ว');
    }

    public function destroy($customerId)
    {
        $this->checkAdmin();
        $customer = Customer::whereNull('deleted_at')->with('user')->findOrFail($customerId);

        DB::transaction(function () use ($customer) {
            $customer->pets()->whereNull('deleted_at')->update(['deleted_at' => now()]);
            $customer->delete();
            $customer->user->delete();
        });

        return redirect()->route('admin.customers.index')->with('success', 'นำข้อมูลลูกค้าออกจากรายการแล้ว');
    }

    private function validateCustomer(Request $request, bool $includePassword, $userId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'user_phone' => ['nullable', 'string', 'max:20'],
        ];
        if ($includePassword) {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $request->validate($rules);
    }

    private function checkAdmin(): void
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);
    }
}
