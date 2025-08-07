<x-app-layout>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">

            <!-- SurveyJS Container -->
            <div id="surveyContainer"></div>

        </div>
    </div>

    <script>
        // Parse the form JSON from Laravel
        var formJson = {!! json_encode($form->form_json) !!};
        {{--var themeJson = {!! json_encode($form->theme_json) !!};--}}

        // Load previous form responses and active page from Laravel (if available)
        var savedResponses = {!! json_encode($incompleteFormEntry ? $incompleteFormEntry->responses : null) !!};
        var savedActivePage = {!! json_encode($incompleteFormEntry ? $incompleteFormEntry->active_page : null) !!};

        // User's personal data to prefill the form
        var userData = {
            first_name: "{{ auth()->user()->first_name }}",
            last_name: "{{ auth()->user()->last_name }}",
            email: "{{ auth()->user()->email }}"
        };

        // Initialize the survey model
        var survey = new Survey.Model(formJson);

        // Prefill the form with user's data
        var prefillData = {
            first_name: userData.first_name,
            last_name: userData.last_name,
            email: userData.email
        };

        // If there are saved responses, merge them with prefilled data
        if (savedResponses) {
            savedResponses = JSON.parse(savedResponses);
            prefillData = {...prefillData, ...savedResponses};
        }

        // Set the survey's data with prefilled and saved responses
        survey.data = prefillData;

        // If there is a saved active page, set the survey to start on that page
        if (savedActivePage) {
            let activePageIndex = survey.pages.findIndex(page => page.name === savedActivePage);
            if (activePageIndex !== -1) {
                survey.currentPageNo = activePageIndex; // Set the current page to the saved active page
            }
        }

        // Load and apply the theme JSON to a custom theme name
        // Survey.StylesManager.ThemeColors["defaultV2"] = themeJson;
        // Apply the custom theme
        // Survey.StylesManager.applyTheme("defaultV2");

        survey.PopupUtils.popupRootElement = document.body;

        // survey.StylesManager.applyTheme("defaultV2");

        // Render the survey
        survey.render("surveyContainer");

        // Function to save form progress
        function saveSurveyProgress() {
            // Calculate percentage complete, setting it to 99% if the user is on the last page but hasn't completed the form
            var percentageComplete = Math.round((survey.currentPageNo + 1) / survey.pages.length * 100);
            if (survey.currentPageNo === survey.pages.length - 1 && !survey.isCompleted) {
                percentageComplete = 99; // Set it to 99 if it's the last page but form is not completed
            }

            var formData = {
                _token: "{{ csrf_token() }}",
                form_id: "{{ $form->id }}",
                responses: JSON.stringify(survey.data),  // Make sure the responses are a valid JSON string
                active_page: survey.currentPage.name,  // Use the current page's name
                percentage_complete: percentageComplete,
                status: 'in-progress'
            };

            axios.post("{{ route('form_entries.store') }}", formData)
                .then(function (response) {
                    console.log('Progress saved:', response.data);
                })
                .catch(function (error) {
                    console.error('Error saving progress:', error);
                });
        }

        // Listen for page changes (Next, Previous) to save the form progress
        survey.onCurrentPageChanged.add(function () {
            saveSurveyProgress();
        });

        // Handle form completion
        survey.onComplete.add(function (result) {
            let formData = {
                _token: "{{ csrf_token() }}",
                form_id: "{{ $form->id }}",
                responses: JSON.stringify(survey.data),
                status: 'completed',
                percentage_complete: 100 // Set to 100% when the form is fully completed
            };

            axios.post("{{ route('form_entries.store') }}", formData)
                .then(function (response) {
                    console.log('Form completed and progress saved:', response.data);
                })
                .catch(function (error) {
                    console.error('Error saving progress:', error);
                });
        });
    </script>


    @if($form->scripts)
        <script>
            {!! $form->scripts !!}
        </script>
    @endif

</x-app-layout>
