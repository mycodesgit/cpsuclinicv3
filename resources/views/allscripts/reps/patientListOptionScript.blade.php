<script>
    // Generic visit search for both consult and refer select boxes
    function visitSearch(selectId, routeNameTemplate) {
        const selectedValue = document.getElementById(selectId).value;
        if (selectedValue) {
            $('#' + selectId).prop('disabled', true);
            const url = routeNameTemplate.replace(':id', selectedValue);

            setTimeout(() => {
                window.location.href = url;
            }, 100);
        }
    }
</script>

<script>
    $(document).ready(function () {
        let currentPage = 1;
        let isMoreData = true;
        let isLoading = false;

        function loadPatients() {
            if (!isMoreData || isLoading) return;

            isLoading = true;

            $.ajax({
                url: "{{ route('patientListOption') }}",
                type: 'GET',
                dataType: 'json',
                data: { page: currentPage },
                success: function (response) {
                    const $selects = $('#mySelect, #mySelectrefer');
                    const options = response.data.map(patient =>
                        `<option value="${patient.id}">${patient.fname} ${patient.mname ? patient.mname.charAt(0) + '.' : ''} ${patient.lname}</option>`
                    ).join('');
                    
                    $selects.append(options);

                    isMoreData = response.pagination?.more ?? false;
                    if (isMoreData) {
                        currentPage++;
                        setTimeout(loadPatients, 200);
                    }

                    isLoading = false;
                },
                error: function () {
                    console.error('Failed to fetch patient list.');
                    isLoading = false;
                }
            });
        }

        loadPatients();
    });
</script>

<script>
    $(document).ready(function() {
        $('.student-report').change(function() {
            var selectedId = $(this).val();
            if (selectedId) {
                var url = '{{ route("reportPatientDataShow", ":id") }}';
                url = url.replace(':id', selectedId);
                window.location.href = url;
            }
        });
    });
</script>