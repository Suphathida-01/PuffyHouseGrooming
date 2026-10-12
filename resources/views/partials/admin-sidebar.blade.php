@php
    $isSuperAdmin = auth()->user()->user_role === 'superadmin';
    $routePrefix = $isSuperAdmin ? 'superadmin' : 'admin';
    $menuItems = [
        ['label' => 'หน้าหลัก', 'icon' => '⌂', 'route' => $routePrefix . '.dashboard', 'active' => request()->routeIs($routePrefix . '.dashboard')],
        ['label' => 'จัดการการจอง', 'icon' => '▣', 'route' => $routePrefix . '.bookings.index', 'active' => request()->routeIs($routePrefix . '.bookings.*')],
        ['label' => 'จัดการลูกค้า', 'icon' => '♧', 'route' => $routePrefix . '.customers.index', 'active' => request()->routeIs($routePrefix . '.customers.*')],
        ['label' => 'บริการ & แพ็กเกจ', 'icon' => '✂', 'route' => $routePrefix . '.services.index', 'active' => request()->routeIs($routePrefix . '.services.*')],
        ['label' => 'จัดการช่าง', 'icon' => '♧', 'route' => $routePrefix . '.staff.index', 'active' => request()->routeIs($routePrefix . '.staff.*')],
    ];

    if ($isSuperAdmin) {
        $menuItems[] = ['label' => 'จัดการชำระเงิน', 'icon' => '▥', 'route' => 'superadmin.payments', 'active' => request()->routeIs('superadmin.payments*')];
    }
@endphp

<aside class="admin-sidebar">
    <a class="admin-brand" href="{{ route($routePrefix . '.dashboard') }}">
        <img class="admin-brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt="">
        <span><strong>Puffly House</strong><small>BACK-OFFICE</small></span>
    </a>
    <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
        @foreach ($menuItems as $item)
            <a class="{{ $item['active'] ? 'active' : '' }}" href="{{ route($item['route']) }}"><span>{{ $item['icon'] }}</span>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="admin-profile">
        <span class="admin-avatar">ADM</span>
        <span><strong>{{ auth()->user()->name }}</strong><small>{{ $isSuperAdmin ? 'SuperAdmin' : 'ผู้ดูแลระบบ' }}</small></span>
        <form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">ออกจากระบบ</button></form>
    </div>
</aside>
