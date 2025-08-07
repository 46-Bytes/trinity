@php
    use App\Models\Diagnostic;
@endphp
        <!-- Dashboard -->
<div class="menu-item">
    <a class="menu-link" href="{{route('dashboard')}}">
        <span class="menu-icon">
            <i class="fas fa-tachometer-alt fs-2"></i>
            <!-- Blue for Dashboard -->
        </span>
        <span class="menu-title">Dashboard</span>
    </a>
</div>
@if(auth()->user()->isAdmin() || auth()->user()->subscriptionIsActive())
    {{--@if(session('diagnostic') !== null && session('diagnostic')->status === Diagnostic::STATUS_COMPLETED)--}}
    @if(session()->has('diagnostic') && session('diagnostic') instanceof Diagnostic && session('diagnostic')->status ===
    Diagnostic::STATUS_COMPLETED && session('diagnostic')->type === Diagnostic::TYPE_PRIMARY)
        <!-- Chats -->
        <div class="menu-item">
            <a class="menu-link" href="{{route('chat.index')}}">
                <span class="menu-icon">
                    <i class="far fa-comments fs-2"></i>
                </span>
                <span class="menu-title">Chat</span>
            </a>
        </div>
    @endif

    <!-- Tasks -->
    <div class="menu-item">
        <a class="menu-link" href="{{route('tasks.index')}}">
            <span class="menu-icon">
                <i class="fas fa-tasks fs-2"></i> <!-- Yellow for Tasks -->
            </span>
            <span class="menu-title">Tasks</span>
        </a>
    </div>

    <!-- Notes -->
    <div class="menu-item">
        <a class="menu-link" href="{{route('notes.index')}}">
            <span class="menu-icon">
                <i class="fas fa-sticky-note fs-2"></i> <!-- Teal for Notes -->
            </span>
            <span class="menu-title">Notes</span>
        </a>
    </div>
    <!-- Files -->
    <div class="menu-item">
        <a class="menu-link" href="{{route('files.index')}}">
            <span class="menu-icon">
                <i class="fas fa-folder fs-2"></i> <!-- Green for Files -->
            </span>
            <span class="menu-title">Files</span>
        </a>
    </div>


    <!-- Calendar -->
    {{--<div class="menu-item">--}}
    {{--    <a class="menu-link" href="{{route('calendar.index')}}">--}}
    {{--                        <span class="menu-icon">--}}
    {{--                            <i class="fas fa-calendar-alt fs-2" style="color: #dc3545;"></i> <!-- Red for Calendar -->--}}
    {{--                        </span>--}}
    {{--        <span class="menu-title">Calendar</span>--}}
    {{--    </a>--}}
    {{--</div>--}}
@endif
