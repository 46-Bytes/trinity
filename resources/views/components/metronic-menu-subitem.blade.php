@props(['menuItems' => []])

<div class="menu-sub menu-sub-accordion">
    @foreach($menuItems as $item)
        <!-- Begin: Menu item -->
        <div class="menu-item">
            <a class="menu-link" href="{{ $item['route'] ?? '#' }}">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">{{ $item['title'] }}</span>
            </a>
        </div>
        <!-- End: Menu item -->
    @endforeach
</div>
