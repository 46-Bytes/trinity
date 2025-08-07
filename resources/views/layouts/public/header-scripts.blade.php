<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
<link href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('metronic/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
<!--end::Global Stylesheets Bundle-->
<style>

    .yoba-section {
        position: relative;
    }

    .yoba-section-bgi-header {
        background-image: url({{ asset('img/man-woman-pointing-bw-25.jpg') }});
        background-repeat: no-repeat;
        background-position: top;
        background-size: cover;
    }

    .yoba-section-bgi-register {
        {{--background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url({{ asset('img/yoba-2-coffees-25.jpg') }});--}}
                  background-image: url({{ asset('img/yoba-2-coffees-25.jpg') }});
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    {{--.yoba-section-bgi-why-choose {--}}
    {{--    background: linear-gradient(rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.7)), url({{ asset('img/woman-patio-writing-1.jpg') }});--}}
    {{--    background-repeat: no-repeat;--}}
    {{--    background-position: center center;--}}
    {{--    background-size: cover;--}}
    {{--}--}}

    .yoba-section-content {
        position: relative;
    }

    .yoba-section-bgi-why-choose {
        background-image: url({{ asset('img/woman-patio-writing-1.jpg') }});
        {{--background: linear-gradient(rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.7)), url({{ asset('img/woman-patio-writing-1.jpg') }});--}}
                 background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .yoba-section-bgi-why-wait {
        background-image: url({{ asset('img/yoba-mobile-1.jpg') }});
        {{--background: linear-gradient(rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.7)), url({{ asset('img/yoba-mobile-1.jpg') }});--}}
                background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .yoba-section-pricing {
        {{--background-image: url({{ asset('img/tablet-touch.jpg') }});--}}
              background: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url({{ asset('img/tablet-touch.jpg') }});
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }
</style>
