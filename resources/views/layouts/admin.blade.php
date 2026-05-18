<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
        $hmAdminSiteName = \App\Models\SystemSetting::get('general.site_name', 'HeavenlyMatch');
        $hmAdminTagline = \App\Models\SystemSetting::get('general.site_tagline', 'The ultimate matrimony platform');
        $hmAdminLogo = \App\Models\SystemSetting::get('media.admin_logo', \App\Models\SystemSetting::get('media.logo'));
        $hmAdminFavicon = \App\Models\SystemSetting::get('media.favicon');
    ?>
    <title>@yield('title', 'Admin Dashboard') | {{ $hmAdminSiteName }}</title>
    @if($hmAdminFavicon)
        <link rel="icon" href="{{ asset($hmAdminFavicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/hm-admin.css') }}">
    @stack('styles')
    <?php $hmAdminCustomCss = \App\Models\SystemSetting::get('custom.css'); ?>
    @if($hmAdminCustomCss)
        <style>{!! $hmAdminCustomCss !!}</style>
    @endif
</head>
<body class="hm-admin-body">
    <div class="hm-admin-sidebar-overlay" id="hmAdminOverlay" onclick="toggleAdminSidebar(false)"></div>

    <?php
        $pendingPaymentsCount = \Illuminate\Support\Facades\Schema::hasTable('payment_transactions')
            ? \App\Models\PaymentTransaction::whereIn('status', ['pending', 'submitted'])->count()
            : 0;
        $pendingTicketsCount = 2;

        $menuGroups = [
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'icon' => '⌂',
                'route' => 'admin.dashboard',
            ],
            [
                'type' => 'link',
                'label' => 'Manage Packages',
                'icon' => '◫',
                'route' => 'admin.settings.packages',
            ],
            [
                'type' => 'group',
                'label' => 'User Attributes',
                'icon' => '⚯',
                'children' => [
                    ['label' => 'Religion', 'route' => 'admin.attributes.show', 'params' => ['type' => 'religion']],
                    ['label' => 'Blood Group', 'route' => 'admin.attributes.show', 'params' => ['type' => 'blood-group']],
                    ['label' => 'Marital Status', 'route' => 'admin.attributes.show', 'params' => ['type' => 'marital-status']],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'Manage Users',
                'icon' => '⚇',
                'children' => [
                    ['label' => 'Active Users', 'route' => 'admin.users.index', 'params' => ['filter' => 'active']],
                    ['label' => 'Banned Users', 'route' => 'admin.users.index', 'params' => ['filter' => 'banned']],
                    ['label' => 'Email Unverified', 'route' => 'admin.users.index', 'params' => ['filter' => 'email-unverified']],
                    ['label' => 'Mobile Unverified', 'route' => 'admin.users.index', 'params' => ['filter' => 'mobile-unverified']],
                    ['label' => 'KYC Unverified', 'route' => 'admin.users.index', 'params' => ['filter' => 'kyc-unverified']],
                    ['label' => 'KYC Pending', 'route' => 'admin.users.index', 'params' => ['filter' => 'kyc-pending']],
                    ['label' => 'All Users', 'route' => 'admin.users.index'],
                    ['label' => 'Send Notification', 'route' => 'admin.notifications.send'],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'User Interactions',
                'icon' => '⚭',
                'children' => [
                    ['label' => 'Interests', 'route' => 'admin.interactions.show', 'params' => ['type' => 'interests']],
                    ['label' => 'Ignored Profile', 'route' => 'admin.interactions.show', 'params' => ['type' => 'ignored-profile']],
                    ['label' => 'Reports', 'route' => 'admin.interactions.show', 'params' => ['type' => 'reports']],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'Payments',
                'icon' => '৳',
                'badge' => $pendingPaymentsCount > 0 ? $pendingPaymentsCount : null,
                'children' => [
                    ['label' => 'Pending Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'pending'], 'badge' => $pendingPaymentsCount > 0 ? $pendingPaymentsCount : null],
                    ['label' => 'Approved Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'approved']],
                    ['label' => 'Successful Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'successful']],
                    ['label' => 'Rejected Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'rejected']],
                    ['label' => 'Initiated Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'initiated']],
                    ['label' => 'All Payments', 'route' => 'admin.payments.index', 'params' => ['scope' => 'all']],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'Support Ticket',
                'icon' => '✉',
                'badge' => $pendingTicketsCount,
                'children' => [
                    ['label' => 'Pending Ticket', 'route' => 'admin.tickets.show', 'params' => ['scope' => 'pending'], 'badge' => $pendingTicketsCount],
                    ['label' => 'Closed Ticket', 'route' => 'admin.tickets.show', 'params' => ['scope' => 'closed']],
                    ['label' => 'Answered Ticket', 'route' => 'admin.tickets.show', 'params' => ['scope' => 'answered']],
                    ['label' => 'All Ticket', 'route' => 'admin.tickets.show', 'params' => ['scope' => 'all']],
                ],
            ],
            [
                'type' => 'link',
                'label' => 'System Setting',
                'icon' => '⚙',
                'route' => 'admin.settings.index',
            ],
            [
                'type' => 'group',
                'label' => 'Report',
                'icon' => '☰',
                'children' => [
                    ['label' => 'Login History', 'route' => 'admin.reports.show', 'params' => ['type' => 'login-history']],
                    ['label' => 'Notification History', 'route' => 'admin.reports.show', 'params' => ['type' => 'notification-history']],
                    ['label' => 'Purchase History', 'route' => 'admin.reports.show', 'params' => ['type' => 'purchase-history']],
                ],
            ],
            [
                'type' => 'group',
                'label' => 'Extra',
                'icon' => '⋯',
                'children' => [
                    ['label' => 'Application', 'route' => 'admin.extra.show', 'params' => ['type' => 'application']],
                    ['label' => 'Server', 'route' => 'admin.extra.show', 'params' => ['type' => 'server']],
                    ['label' => 'Cache', 'route' => 'admin.extra.show', 'params' => ['type' => 'cache']],
                    ['label' => 'Update', 'route' => 'admin.extra.show', 'params' => ['type' => 'update']],
                ],
            ],
        ];
    ?>

    <div class="hm-admin-shell">
        <aside class="hm-admin-sidebar" id="hmAdminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="hm-admin-brand">
                @if($hmAdminLogo)
                    <img src="{{ asset($hmAdminLogo) }}" alt="{{ $hmAdminSiteName }}" class="hm-admin-brand-image">
                @else
                    <div class="hm-admin-logo">♥</div>
                @endif
                <div>
                    <div class="hm-admin-brand-title">{{ $hmAdminSiteName }}</div>
                    <div class="hm-admin-brand-sub">{{ $hmAdminTagline }}</div>
                </div>
            </a>

            <nav class="hm-admin-nav-groups">
                @foreach($menuGroups as $group)
                    @if($group['type'] === 'link')
                        <?php
                            $active = request()->routeIs($group['route']);
                            if (($group['label'] ?? '') === 'Manage Packages') {
                                $active = request()->routeIs('admin.settings.packages') || request()->routeIs('admin.settings.plans.*');
                            } elseif (($group['label'] ?? '') === 'System Setting') {
                                $active = (
                                    request()->routeIs('admin.settings.index') ||
                                    request()->routeIs('admin.settings.edit') ||
                                    request()->routeIs('admin.settings.update') ||
                                    request()->routeIs('admin.settings.gateways.*') ||
                                    request()->routeIs('admin.settings.payments.*')
                                ) && ! request()->routeIs('admin.settings.packages') && ! request()->routeIs('admin.settings.plans.*');
                            }
                        ?>
                        <a href="{{ route($group['route']) }}" class="hm-admin-nav-link {{ $active ? 'active' : '' }}">
                            <span class="hm-admin-nav-icon">{{ $group['icon'] }}</span>
                            <span>{{ $group['label'] }}</span>
                        </a>
                    @else
                        <?php
                            $groupActive = collect($group['children'])->contains(function ($child) {
                                if (!request()->routeIs($child['route'])) {
                                    return false;
                                }

                                if (empty($child['params'])) {
                                    return true;
                                }

                                foreach ($child['params'] as $key => $value) {
                                    if ((string) request()->route($key) !== (string) $value && (string) request()->query($key) !== (string) $value) {
                                        return false;
                                    }
                                }

                                return true;
                            });
                        ?>
                        <div class="hm-admin-nav-group {{ $groupActive ? 'is-open' : '' }}">
                            <button type="button" class="hm-admin-nav-trigger" onclick="this.parentElement.classList.toggle('is-open')">
                                <span class="hm-admin-nav-trigger-left">
                                    <span class="hm-admin-nav-icon">{{ $group['icon'] }}</span>
                                    <span>{{ $group['label'] }}</span>
                                </span>
                                <span class="hm-admin-nav-trigger-right">
                                    @if(!empty($group['badge']))<span class="hm-admin-nav-badge">{{ $group['badge'] }}</span>@endif
                                    <span class="hm-admin-nav-caret">⌄</span>
                                </span>
                            </button>
                            <div class="hm-admin-subnav">
                                @foreach($group['children'] as $child)
                                    <?php
                                        $params = $child['params'] ?? [];
                                        $childActive = request()->routeIs($child['route']);
                                        if ($childActive && !empty($params)) {
                                            foreach ($params as $key => $value) {
                                                if ((string) request()->route($key) !== (string) $value && (string) request()->query($key) !== (string) $value) {
                                                    $childActive = false;
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                    <a href="{{ route($child['route'], $params) }}" class="hm-admin-subnav-link {{ $childActive ? 'active' : '' }}">
                                        <span class="hm-admin-subnav-dot">∘</span>
                                        <span>{{ $child['label'] }}</span>
                                        @if(!empty($child['badge']))<span class="hm-admin-nav-badge">{{ $child['badge'] }}</span>@endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="hm-admin-sidebar-footer">HEAVENLYMATCH <span>V2.3</span></div>
        </aside>

        <main class="hm-admin-main">
            <header class="hm-admin-topbar">
                <div class="hm-admin-topbar-left">
                    <button type="button" class="hm-admin-btn light hm-admin-mobile-menu" onclick="toggleAdminSidebar(true)">☰</button>
                    <form action="{{ route('admin.users.index') }}" method="GET" class="hm-admin-search">
                        <span>⌕</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search here...">
                    </form>
                </div>

                <div class="hm-admin-topbar-right">
                    <span class="hm-admin-top-icon">🌐</span>
                    <span class="hm-admin-top-icon hm-admin-top-icon-alert">🔔</span>
                    <span class="hm-admin-top-icon">🛠</span>
                    <div class="hm-admin-user-mini">
                        <div class="hm-admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                        <div class="hm-admin-user-mini-text">
                            <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                            <span>admin</span>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="hm-admin-logout-btn">⏻</button>
                        </form>
                    </div>
                </div>
            </header>

            <section class="hm-admin-content">
                @if(session('success'))
                    <div class="hm-admin-alert success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="hm-admin-alert error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="hm-admin-alert error">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script>
        function toggleAdminSidebar(force) {
            const sidebar = document.getElementById('hmAdminSidebar');
            const overlay = document.getElementById('hmAdminOverlay');
            const shouldOpen = typeof force === 'boolean' ? force : !sidebar.classList.contains('open');
            sidebar.classList.toggle('open', shouldOpen);
            overlay.classList.toggle('open', shouldOpen);
        }
    </script>
    @stack('scripts')
</body>
</html>
