<script>
    $(document).ready(function() {

        $('#studentdata').DataTable({
            "ajax": {
                "url": studentsReadRoute,
            },
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                // { data: 'id'},
                { 
                    data: null,
                    render: function(data, type, row) {
                        // Format: Lastname, Firstname M.
                        let lname = row.lname || '';
                        let fname = row.fname || '';
                        let mname = row.mname ? row.mname.charAt(0) + '.' : '';
                        return `${lname}, ${fname} ${mname}`.trim();
                    }
                }, 
                { data: 'stdntid' },   
                { data: 'sex' },   
                { data: 'c_status' },   
                { data: 'studCourse' },   
                { 
                    data: 'pexam_remarks', 
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge badge-success">Fit for enrollment</span>';
                        } else if (data == 2) {
                            return '<span class="badge badge-danger">Not fit for enrollment</span>';
                        } else if (data == 3) {
                            return `<span class="badge badge-warning" data-toggle="tooltip" title="${row.pend_reason}">Pending</span>`;
                        } else {
                            return '<span class="badge badge-info">NO Remarks</span>';
                        }
                    }
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        var encryptedId = row.encrypted_id;
                        var moreInfoUrl = "{{ route('studentsMoreInfo', [ 'id' => ':id' ]) }}".replace(':id', data);
                        var fileReadUrl = "{{ route('fileRead', [ 'id' => ':id' ]) }}".replace(':id', data);
                        var reportsReadUrl = "{{ route('reportPatientDataShow', ':id') }}".replace(':id', data);
                        
                        return `
                            <div class="btn-group">
                                <a href="${moreInfoUrl}" class="mr-1 btn btn-info btn-sm text-light" title="More Info">
                                    <i class="fas fa-exclamation-circle"></i> 
                                </a>

                                <a href="${fileReadUrl}" class="mr-1 btn btn-success btn-sm" title="File Info">
                                    <i class="fas fa-file"></i> 
                                </a>

                                <a href="${reportsReadUrl}" class="mr-1 btn btn-warning btn-sm" title="Pre-Entrance Health Examination Report">
                                    <i class="fas fa-file-pdf"></i> 
                                </a>

                                <a href="#" class="mr-1 btn btn-primary btn-sm btn-studhisview" 
                                    data-id="${row.stdntid}" 
                                    data-fname="${row.fname}" 
                                    data-lname="${row.lname}" 
                                    title="Student History">
                                        <i class="fas fa-eye"></i>
                                </a>
                                
                                <button class="mr-1 btn btn-danger btn-sm patient-delete" data-id="${data}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            "createdRow": function (row, data, dataIndex) {
                $(row).attr('id', 'tr-' + data.id);
            }
        });
    });

    $(document).on('click', '.patient-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to recover this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('patients.Delete', ':id') }}".replace(':id', id),
                    success: function(response) {
                        $("#tr-" + id).delay(1000).fadeOut();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Successfully Deleted!',
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        if (response.success) {
                            toastr.success(response.message);
                            console.log(response);
                        }
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('#studentdata').on('click', '.btn-studhisview', function() {
            var studentId = $(this).data('id');
            var studentName = $(this).data('fname') + ' ' + $(this).data('lname');

            $('#viewStudHisId').val(studentId);
            $('#studentName').text(studentName);

            $.ajax({
                url: studenhistoryClickReadRoute,
                method: 'GET',
                data: { stdntid: studentId },
                success: function(response) {
                    var historyTable = $('#enrollmentHistoryTable');
                    historyTable.empty();

                    if (response.data.length > 0) {
                        response.data.forEach(function(history) {
                            var semesterText;
                            switch(history.semester) {
                                case 1:
                                    semesterText = '<span class="badge badge-info">1st Sem</span>';
                                    break;
                                case 2:
                                    semesterText = '<span class="badge badge-info">2nd Sem</span>';
                                    break;
                                case 3:
                                    semesterText = '<span class="badge badge-secondary">Summer</span>';
                                    break;
                                default:
                                    semesterText = 'Unknown Semester';
                                    break;
                            }
                            var row = '<tr>' +
                                '<td>' + history.studentID + '</td>' +
                                '<td>' + history.schlyear + '</td>' +
                                '<td>' + semesterText + '</td>' +
                                '<td>' + history.progAcronym + '</td>' +
                                '<td>' + history.studYear + '</td>' +
                                '<td>' + history.studSec + '</td>' +
                                '</tr>';
                            historyTable.append(row);
                        });
                    } else {
                        historyTable.append('<tr><td colspan="5" class="text-center">No enrollment history found.</td></tr>');
                    }

                    $('#viewStudHisModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred while fetching the enrollment history.');
                }
            });
        });
    });
</script>