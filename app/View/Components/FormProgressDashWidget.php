<?php

namespace App\View\Components;

use App\Models\FormEntry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;

class FormProgressDashWidget extends Component {
    public ?FormEntry $formEntry;
    public int $formId;

    /**
     * Create a new component instance.
     *
     * @param FormEntry|null $formEntry
     * @param int $formId
     * @return void
     */
    public function __construct(?FormEntry $formEntry, int $formId) {
        $this->formEntry = $formEntry;
        $this->formId = $formId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Factory|View|Application
     */
    public function render() {
        return view('components.form-progress-dash-widget');
    }
}
