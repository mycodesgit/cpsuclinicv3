<script>
    $(document).ready(function () {
        $('#medicine-dropdown').select2({
            placeholder: '--Select--',
            ajax: {
                url: '/get-medicines',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.medicine
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });
</script>