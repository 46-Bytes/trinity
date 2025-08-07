<div id="surveyElement"></div>
<div id="surveyResult"></div>

<!-- Include SurveyJS JS -->
<script src="{{ asset('node_modules/survey-core/survey.core.min.js') }}"></script>

<script>
    let responseJSON =;  // Pass the response JSON to the component

    // Survey JSON structure (can also be loaded dynamically from the backend)
    const surveyJSON = {
        title: "Your Online Business Advisor",
        pages: [
            {
                name: "page1",
                questions: [
                    {type: "text", name: "businessName", title: "What is your business name?"},
                    {type: "text", name: "businessType", title: "What type of business do you operate?"},
                    {
                        type: "rating",
                        name: "satisfaction",
                        title: "How satisfied are you with your business performance?",
                        rateMax: 5
                    }
                ]
            }
        ]
    };

    // Initialize SurveyJS Model
    const survey = new Survey.Model(surveyJSON);

    // Load existing responses if provided
    if (responseJSON) {
        survey.data = responseJSON;
    }

    // Handle Survey Completion
    survey.onComplete.add(function (result) {
        document.querySelector('#surveyResult').innerHTML = "Form Results: " + JSON.stringify(result.data);

        // Handle submission of results to backend via AJAX
        $.ajax({
            url: '/api/form/save-responses',
            type: 'POST',
            data: JSON.stringify(result.data),
            contentType: 'application/json; charset=utf-8',
            success: function (response) {
                console.log('Form results saved successfully');
            },
            error: function (xhr, status, error) {
                console.error('Error saving form results:', error);
            }
        });
    });

    // Render the survey
    survey.render("surveyElement");
</script>
