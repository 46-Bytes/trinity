<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Request;

// For checking active routes

class MetronicMenuAccordian extends Component {
    public string $parentMenuItemName;
    public string $parentMenuItemIcon;
    public string $parentMenuItemColor;
    public array $menuItems;
    public bool $active; // Boolean to mark active status

    public function __construct(?string $parentMenuItemName = null, ?string $parentMenuItemIcon = null, ?string $parentMenuItemColor = null, ?array $menuItems = [], $active = false) {
        $this->parentMenuItemName = $parentMenuItemName;
        $this->parentMenuItemIcon = $parentMenuItemIcon;
        $this->parentMenuItemColor = $parentMenuItemColor;
        $this->menuItems = $menuItems;
        $this->active = $active;  // Active flag for the parent item
    }

// Function to check if a menu item is active based on the current URL
    public function isActive($route): bool {
        return request()->url() === $route;
    }

    public function render() {
        return view('components.metronic-menu-accordian');
    }
}
