<div class="menu menu-rounded menu-column menu-title-gray-700 menu-icon-gray-500 menu-arrow-gray-500 menu-bullet-gray-500 menu-state-bg fw-semibold w-240px" data-kt-menu="true">
    @foreach ($items as $item)
        @php
            $user = auth()->user();
            // Check if the user has the required role

            $conditionsMet = true;
            // if user is admin, hasRole is always true
            if ($user->hasRole('admin')) {
                $hasRole = true;
            } else {
                $hasRole = !isset($item['roles']) || array_intersect($user->roles->pluck('name')->toArray(), $item['roles']);
                // Check conditions
                if (isset($item['condition'])) {
                    foreach ($item['condition'] as $key => $value) {
                        if (!isset($user->$key) || $user->$key !== $value) {
                            $conditionsMet = false;
                            break;
                        }
                    }
                }
            }
            $isActive = $item['active'] === true;
        @endphp

        @if ($hasRole && $conditionsMet && $isActive)
            <div class="menu-item menu-sub-indention menu-accordion" data-kt-menu-trigger="click">
                <a class="menu-link py-3" href="{{ $item['url'] ?? '#' }}">
                    <span class="menu-icon">
                        <i class="{{ $item['icon'] }} fs-3"></i>
                    </span>
                    <span class="menu-title">{{ $item['title'] }}</span>
                    @if (!empty($item['children']))
                        <span class="menu-arrow"></span>
                    @endif
                </a>

                @if (!empty($item['children']))
                    <div class="menu-sub menu-sub-accordion pt-3">
                        @foreach ($item['children'] as $child)
                            @php
                                $icon = $child['icon'] ?? 'bullet bullet-dot';
                            @endphp
                            <div class="menu-item">
                                <a class="menu-link py-3" href="{{ $child['url'] }}">
                                    <span class="menu-bullet">
                                        <span class="{{$icon}}"></span>
                                    </span>
                                    <span class="menu-title fs-5">{{ $child['title'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endforeach
</div>
