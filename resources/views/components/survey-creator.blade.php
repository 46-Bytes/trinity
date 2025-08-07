@props(['formId', 'formJson', 'themeJson' => '', 'isReadOnly' => false])

<div id="surveyContainer-{{ $formId }}"></div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        {{--var surveyJSON = {{$formJson}};--}}
        {{--var surveyJSON = {!! json_encode($formJson) !!};--}}
        {{--var surveyJSON = {!! json_encode($formJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};--}}
        var surveyJSON = @json($formJson);
        var survey = new Survey.Model(surveyJSON);
        console.log(surveyJSON);

        // Check if themeJson is provided
        var themeJSON = {!! json_encode($themeJson) !!};
        if (themeJSON) {
            Survey.StylesManager.applyTheme(themeJSON);
        }

        survey.mode = {{ $isReadOnly ? '"display"' : '"edit"' }};

        Survey.SurveyNG.render("surveyContainer-{{ $formId }}", {model: survey});
    });
</script>
