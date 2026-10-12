<div class="header-actions">
    @auth
        @if (!(request()->routeIs('home') && in_array(auth()->user()->user_role, ['admin', 'superadmin'], true)))
            @if (in_array(auth()->user()->user_role, ['admin', 'superadmin'], true))
                <a class="text-button" href="{{ route(auth()->user()->user_role === 'superadmin' ? 'superadmin.dashboard' : 'admin.dashboard') }}">หลังบ้าน</a>
            @endif
            <details class="account-menu">
                <summary class="welcome-user">สวัสดี {{ auth()->user()->name }}</summary>
                <div class="account-menu-dropdown">
                    <a href="{{ auth()->user()->user_role === 'superadmin' ? route('superadmin.profile.edit') : (auth()->user()->user_role === 'admin' ? route('admin.profile.edit') : route('profile.edit')) }}">ข้อมูลส่วนตัว</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-button" type="submit">ออกจากระบบ</button></form>
                </div>
            </details>
        @endif
    @else
        <a class="text-button" href="{{ route('login') }}">เข้าสู่ระบบ</a>
        <a class="button button-small" href="{{ route('register') }}">สมัครสมาชิก</a>
    @endauth
</div>
