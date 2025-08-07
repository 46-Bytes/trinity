<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#tinymce', // Your textarea selector
        plugins: 'code table lists',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
        valid_elements: '*[*]',
        entity_encoding: "raw",
        forced_root_block: '',
        forced_root_block_attrs: { "class": "paragraph" },  // Add custom paragraph handling if needed
        br_in_pre: false,  // Control how <br> elements are handled
    });
</script>
