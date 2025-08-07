<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component {
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View {
        // Load the Metronic layout for all views that use <x-app-layout>
        return view('layouts.metronic');
    }
}
