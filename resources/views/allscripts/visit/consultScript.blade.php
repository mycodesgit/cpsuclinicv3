<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };
    $(document).ready(function() {
        $('#adPVisit').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: pvisitCreateRoute,
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        //console.log(response);
                        $(document).trigger('pvisitAdded');
                        $('#addPatientModal').modal('hide');
                    } else {
                        toastr.error(response.message);
                        console.log(response);
                    }
                },
                error: function(xhr, status, error, message) {
                    var errorMessage = xhr.responseText ? JSON.parse(xhr.responseText).message : 'An error occurred';
                    toastr.error(errorMessage);
                }
            });
        });

        var studentId = window.location.pathname.split('/').pop();
        var dataTable = $('#consultationTable').DataTable({
            "ajax": {
                "url": "{{ route('getconsultPatientVisitSearch', ['id' => ':id']) }}".replace(':id', studentId),
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                { data: 'date',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return moment(data).format('MMMM D, YYYY');
                        } else {
                            return data;
                        }
                    }
                 },
                { data: 'time' },
                { data: 'medicines' },
                { data: 'complaintname' },
                { data: 'treatment' },
                {
                    data: 'vid',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var buttons = `
                                <a href="#" 
                                class="btn btn-sm btn-primary btn-consultaionedit mr-1"
                                data-id="${row.vid}"
                                data-chiefcomplaint="${row.complaint_ids}"
                                data-medicines="${row.medicine_ids}"
                                data-qtys="${row.qtys}"
                                data-treatment="${row.treatment}">
                                <i class="fas fa-pen"></i>
                                </a>`;
                            return buttons;
                        } else {
                            return data;
                        }
                    }
                },
            ],
            "createdRow": function (row, data, index) {
                $(row).attr('id', 'tr-' + data.id); 
            }
        });
        $(document).on('pvisitAdded', function() {
            dataTable.ajax.reload();
        });
    });

    $(document).on('click', '.btn-consultaionedit', function() {
        var id = $(this).data('id');
        var chiefcomplaint = $(this).data('chiefcomplaint') ? $(this).data('chiefcomplaint').toString().split(',') : [];
        var medicineIds = $(this).data('medicines') ? $(this).data('medicines').toString().split(',') : [];
        var qtys = $(this).data('qtys') ? $(this).data('qtys').toString().split(',') : [];

        $('#editConsultationId').val(id);
        $('#editConsultChief').val(chiefcomplaint).trigger('change');
        $('textarea[name="treatment"]').val($(this).data('treatment'));
        $('#dynamic-fields').html('');

        for (var i = 0; i < medicineIds.length; i++) {
            var $row = $($('#medicine-row-template').html());
            $row.find('select[name="medicine[]"]').val(medicineIds[i]);
            $row.find('input[name="qty[]"]').val(qtys[i] || '');
            $('#dynamic-fields').append($row);
            $row.find('select[name="medicine[]"]').select2();
        }
        $('#editConsultationModal').modal('show');
    });


</script>

