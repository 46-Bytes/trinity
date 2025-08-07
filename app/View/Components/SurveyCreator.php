<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SurveyCreator extends Component {
    public mixed $formJSON;

    public function __construct($formJSON = null) {
        $this->formJSON = $formJSON;
    }

    public function render() {
        return view('components.survey-creator');
    }
}
