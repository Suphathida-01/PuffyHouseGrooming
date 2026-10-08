<div class="header-actions">
    @auth
        @if (auth()->user()->user_role === 'admin')
            <a class="text-button" href="{{ route('dashboard') }}">หลังบ้าน</a>
        @endif
        <details class="account-menu">
            <summary class="welcome-user">สวัสดี {{ auth()->user()->name }} <span aria-hidden="true">⌄</span></summary>
            <div class="account-menu-dropdown">
                <a href="{{ route('profile.edit') }}">ข้อมูลส่วนตัว</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-button" type="submit">ออกจากระบบ</button></form>
            </div>
        </details>
    @else
        <a class="text-button" href="{{ route('login') }}">เข้าสู่ระบบ</a>
        <a class="button button-small" href="{{ route('register') }}">สมัครสมาชิก</a>
    @endauth
</div>
