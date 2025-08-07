@php
    use App\Models\Diagnostic;
    auth()->user()->checkStatus();
@endphp
<x-toggle-menu :items="[
    [
        'title'=> 'Admin',
        'icon' => 'fa-solid fa-user-shield',
        'roles' => ['admin'],
        'children' => [
            [
                'title' => 'Prompts',
                'url' => '/admin/prompts',
                'icon'=>'fa-regular fa-comment-dots'
            ],
            [
                'title' => 'Users',
                'url' => '/admin/users',
                'icon'=>'fa-solid fa-users-gear'
            ],
            [
                'title' => 'Orgs',
                'url' => '/admin/orgs',
                'icon'=>'fa-solid fa-building-user'
            ],
        ],
        'active'=>true
    ],
    [
        'title'=>'Dashboard',
        'icon'=>'fas fa-tachometer-alt',
        'url'=>'/dashboard',
        'roles' => ['client'],
        'condition'=>[],
        'active'=>true
    ],
    [
        'title'=>'Diagnostics',
        'icon'=>'fas fa-clipboard-list',
        'url'=>'/diagnostics',
        'roles' => ['client'],
        'condition'=>[
            'diagnosticStatus'=>true
            ],
        'active'=>true
    ],
    [
        'title'=>'Chat',
        'icon'=>'far fa-comments',
        'url'=>'/chat',
        'roles' => ['client'],
        'condition'=>[
            'subscriptionIsActive'=>true,
            'diagnosticStatus'=>true
            ],
        'active'=>true
    ],
    [
        'title'=>'Files',
        'icon'=>'fas fa-folder',
        'url'=>'/files',
        'roles' => ['client'],
        'condition'=>[
            'subscriptionIsActive'=>true
            ],
        'active'=>true
    ],
    [
        'title'=>'Tasks',
        'icon'=>'fas fa-tasks',
        'url'=>'/tasks',
        'roles' => ['client'],
        'condition'=>[
            'subscriptionIsActive'=>true
            ],
        'active'=>true
    ],
    [
        'title'=>'Notes',
        'icon'=>'fas fa-sticky-note',
        'url'=>'/notes',
        'roles' => ['client'],
        'condition'=>[
            'subscriptionIsActive'=>true
            ],
        'active'=>true
    ],
    [
        'title'=>'Calendar',
        'icon'=>'fas fa-calendar-alt',
        'url'=>'/calendar',
        'roles' => ['client'],
        'condition'=>[
            'subscriptionIsActive'=>true
            ],
        'active'=>false
    ]

]"/>
