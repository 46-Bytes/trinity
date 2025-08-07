<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SurveyRender extends Component {
    public mixed $responseJSON;

    public function __construct($responseJSON = null) {
        $this->responseJSON = $responseJSON;
    }

    public function render() {
        return view('components.survey-render');
    }
}
