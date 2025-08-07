<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MetronicCard extends Component
{
    public ?string $title;
    public ?string $footer;
    /**
     * Create a new component instance.
     */
    public function __construct($title=null, $footer=null)
    {
        $this->title = $title;
        $this->footer = $footer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.metronic-card');
    }
}
