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
</script>