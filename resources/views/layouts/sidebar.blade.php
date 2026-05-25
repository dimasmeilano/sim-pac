<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/dashboard') }}" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">SIM PAC</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->foto ?? asset('images/default-avatar.png') }}" class="img-circle elevation-2">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                <small class="text-muted">
                    @foreach(Auth::user()->getRoleNames() as $role)
                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                    @endforeach
                </small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
                @php
                $menus = App\Models\Menu::where('status', 'active')
                ->whereNull('parent_id')
                ->orderBy('urutan')
                ->get();

                $userPermissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();
                @endphp

                @foreach($menus as $menu)
                @php
                $submenus = App\Models\Menu::where('parent_id', $menu->id)
                ->where('status', 'active')
                ->orderBy('urutan')
                ->get()
                ->filter(function($sub) use ($userPermissions) {
                return !$sub->permission_required || in_array($sub->permission_required, $userPermissions);
                });
                @endphp

                @if($submenus->count() > 0)
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon {{ $menu->icon }}"></i>
                        <p>
                            {{ $menu->title }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach($submenus as $submenu)
                        <li class="nav-item">
                            <a href="{{ url($submenu->route) }}" class="nav-link">
                                <i class="{{ $submenu->icon }}"></i>
                                <p>{{ $submenu->title }}</p>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @else
                @if(!$menu->permission_required || in_array($menu->permission_required, $userPermissions))
                <li class="nav-item">
                    <a href="{{ url($menu->route) }}" class="nav-link">
                        <i class="nav-icon {{ $menu->icon }}"></i>
                        <p>{{ $menu->title }}</p>
                    </a>
                </li>
                @endif
                @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>