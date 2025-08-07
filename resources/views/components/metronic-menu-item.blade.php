@props(['title', 'route'])

<div class="menu-item">
    <a class="menu-link" href="{{ $route }}">
        <span class="menu-bullet">
            <span class="bullet bullet-dot"></span>
        </span>
        <span class="menu-title">{{ $title }}</span>
    </a>
</div>
