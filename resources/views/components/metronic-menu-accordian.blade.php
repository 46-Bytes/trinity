@props([
    'parentMenuItemName' => null,
    'parentMenuItemIcon' => null,
    'parentMenuItemColor' => null,
    'menuItems' => []
])

<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $active ? 'here show' : '' }}">
    <!-- Begin: Menu link -->
    <span class="menu-link">
        <span class="menu-icon">
            <!-- Metronic Icons or Font Awesome -->
            <i class="{{ $parentMenuItemIcon }} fs-2" style="color: {{ $parentMenuItemColor }};"></i>
        </span>
        <span class="menu-title">{{ $parentMenuItemName }}</span>
        <span class="menu-arrow"></span>
    </span>
    <!-- End: Menu link -->

    <!-- Begin: Menu sub -->
    <div class="menu-sub menu-sub-accordion">
        @foreach ($menuItems as $item)
            <!-- Begin: Menu item -->
            <div class="menu-item">
                <a class="menu-link" href="{{ $item['route'] }}">
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">{{ $item['title'] }}</span>
                </a>
            </div>
            <!-- End: Menu item -->
        @endforeach
    </div>
    <!-- End: Menu sub -->
</div>
