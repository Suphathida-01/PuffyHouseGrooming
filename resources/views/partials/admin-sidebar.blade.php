@php
$isSuperAdmin = auth()->user()->user_role === 'superadmin';
$routePrefix = $isSuperAdmin ? 'superadmin' : 'admin';
$menuItems = [
['label' => 'หน้าหลัก', 'icon' => 'home', 'route' => $routePrefix . '_dashboard', 'active' =>
request()->routeIs($routePrefix . '_dashboard')],
['label' => 'จัดการการจอง', 'icon' => 'calendar', 'route' => $routePrefix . '_bookings_index', 'active' =>
request()->routeIs($routePrefix . '_bookings_*')],
['label' => 'จัดการลูกค้า', 'icon' => 'user', 'route' => $routePrefix . '_customers_index', 'active' =>
request()->routeIs($routePrefix . '_customers_*')],
['label' => 'บริการ & แพ็กเกจ', 'icon' => 'scissors', 'route' => $routePrefix . '_services_index', 'active' =>
request()->routeIs($routePrefix . '_services_*')],
['label' => 'จัดการช่าง', 'icon' => 'staff', 'route' => $routePrefix . '_staff_index', 'active' =>
request()->routeIs($routePrefix . '_staff_*')],
];

if ($isSuperAdmin) {
$menuItems[] = ['label' => 'จัดการชำระเงิน', 'icon' => 'credit-card', 'route' => 'superadmin_payments', 'active' =>
request()->routeIs('superadmin_payments*')];
}
@endphp

<aside class="admin-sidebar">
    <a class="admin-brand" href="{{ route($routePrefix . '_dashboard') }}">
        <img class="admin-brand-icon"
            src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt="">
        <span><strong>Puffly House</strong><small>BACK-OFFICE</small></span>
    </a>
    <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
        @foreach ($menuItems as $item)
        <a class="{{ $item['active'] ? 'active' : '' }}" href="{{ route($item['route']) }}"><span aria-hidden="true">
                @switch($item['icon'])
                @case('home')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m3 10 9-7 9 7" />
                    <path d="M5 9v11h14V9M9 20v-7h6v7" />
                </svg>
                @break
                @case('calendar')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18M8 14h2m4 0h2m-8 4h2" />
                </svg>
                @break
                @case('user')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M4 21a8 8 0 0 1 16 0" />
                </svg>
                @break
                @case('scissors')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="6" cy="6" r="3" />
                    <circle cx="6" cy="18" r="3" />
                    <path d="m8.1 8.1 12.4 12.4M14 14l6-6M8.1 15.9 12 12" />
                </svg>
                @break
                @case('staff')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="9" cy="8" r="3.5" />
                    <path d="M2.5 20a6.5 6.5 0 0 1 13 0M17 8l2 2 3-3" />
                    <path d="M17 14h4v6h-4z" />
                </svg>
                @break
                @case('credit-card')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="2.5" y="5" width="19" height="14" rx="2" />
                    <path d="M2.5 10h19M6 15h4" />
                </svg>
                @break
                @endswitch
            </span>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="admin-profile">
        <span class="admin-avatar">ADM</span>
        <span><strong>{{ auth()->user()->name }}</strong><small>{{ $isSuperAdmin ? 'SuperAdmin' : 'ผู้ดูแลระบบ' }}</small></span>
        <form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ"
                aria-label="ออกจากระบบ">ออกจากระบบ</button></form>
    </div>
</aside>