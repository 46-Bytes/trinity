<style>
    .toastr-top-right {
        top: 7rem; /* Adjust the value as needed to lower it */
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toastr-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        const messages = @json(session()->all());

        if (messages.success) toastr.success(messages.success);
        if (messages.error) toastr.error(messages.error);
        if (messages.info) toastr.info(messages.info);
        if (messages.warning) toastr.warning(messages.warning);
    });
</script>
