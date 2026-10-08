<nav class="site-nav" aria-label="เมนูหลัก">
    <a class="{{ request()->routeIs('home') ? 'current' : '' }}" href="{{ route('home') }}">หน้าแรก</a>
    <a class="{{ request()->routeIs('services*') || request()->routeIs('booking.*') || request()->routeIs('payments.*') ? 'current' : '' }}" href="{{ route('services') }}">บริการ</a>
    @auth
        @if (auth()->user()->user_role === 'customer')
            <a class="{{ request()->routeIs('pets.*') ? 'current' : '' }}" href="{{ route('pets.index') }}">สัตว์เลี้ยงของฉัน</a>
            <a class="{{ request()->routeIs('my-bookings.*') ? 'current' : '' }}" href="{{ route('my-bookings.index') }}">การจองของฉัน</a>
        @endif
    @else
        <a href="{{ route('login') }}">สัตว์เลี้ยงของฉัน</a>
        <a href="{{ route('login') }}">การจองของฉัน</a>
    @endauth
    <a href="{{ route('home') }}#contact">ติดต่อเรา</a>
</nav>
<script>
    (() => {
        const updateCurrentNav = () => {
            if (window.location.hash !== '#contact') return;

            const nav = document.querySelector('.site-nav');
            if (!nav) return;

            nav.querySelectorAll('a.current').forEach(link => link.classList.remove('current'));
            nav.querySelector('a[href$="#contact"]')?.classList.add('current');
        };

        updateCurrentNav();
        window.addEventListener('hashchange', updateCurrentNav);
    })();
</script>
