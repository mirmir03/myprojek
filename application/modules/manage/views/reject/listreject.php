<?php
// Enable these if you need permission checks
// $ENABLE_ADD = has_permission('menu.Add');
// $ENABLE_MANAGE = has_permission('menu.Manage');
// $ENABLE_DELETE = has_permission('menu.Delete');
$ENABLE_ADD = TRUE;
$ENABLE_MANAGE = TRUE;
$ENABLE_DELETE = TRUE;

echo "Bilangan Data " . $data->num_rows();
?>

<style>
    .modal-dialog.draggable {
        position: fixed;
        margin: 0;
        width: auto;
    }
    
    .modal-dialog.draggable .modal-content {
        cursor: move;
    }
    
    .modal-header {
        cursor: move;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .select2-container {
        width: 100% !important;
    }
    
    /* DataTables styling improvements */
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        margin-left: 0.5rem;
    }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Senarai Reject</h5>
            <div>
                <?php if ($ENABLE_ADD): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRejectModal">
                        <i class="ti ti-plus"></i> Tambah Reject Baru
                    </button>
                <?php endif; ?>
                <!-- Graph Button -->
                <a class="btn btn-success me-2" href="<?php echo module_url("reject/graph"); ?>">
                    <i class="ti ti-chart-bar"></i> Jana Graf
                </a>
            </div>
        </div>
    </div>
   
    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= form_open($this->uri->uri_string(), array('id' => 'frm_menu', 'name' => 'frm_menu')) ?>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="reject-table">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>        
                        <th>Jenis Reject</th>
                        <th width="120">Tarikh</th>
                        <?php if ($ENABLE_MANAGE): ?>
                            <th width="100">Edit</th>
                        <?php endif; ?>
                        <?php if ($ENABLE_DELETE): ?>
                            <th width="100">Delete</th>
                        <?php endif; ?>                       
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($data->result() as $row) { ?>
                    <tr>
                        <td></td>
                        <td><?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?></td>
                        <td><?php echo htmlspecialchars($row->T06_TARIKH); ?></td>
                        
                        <?php if ($ENABLE_MANAGE): ?>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?php echo $row->T06_ID_REJECT; ?>"
                                    data-jenis="<?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?>"
                                    data-tarikh="<?php echo $row->T06_TARIKH; ?>">
                                <i class="ti ti-edit"></i> Kemaskini
                            </button>
                        </td>
                        <?php endif; ?>

                        <?php if ($ENABLE_DELETE): ?>
                        <td>
                            <a class="btn btn-sm btn-danger delete-btn" 
                               href="javascript:void(0);" 
                               data-id="<?php echo $row->T06_ID_REJECT; ?>" 
                               data-name="<?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?>">
                               <i class="ti ti-trash"></i> Padam
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<!-- Add Reject Modal -->
<div class="modal fade" id="addRejectModal" tabindex="-1" aria-labelledby="addRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRejectModalLabel">
                    <i class="ti ti-plus"></i> Tambah Reject Baharu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST" action="<?php echo module_url('reject/add'); ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Error Alert (hidden by default) -->
                    <div id="modalError" class="alert alert-danger d-none"></div>
                    
                    <!-- Tarikh -->
                    <div class="mb-3 row align-items-center">
                        <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label">Tarikh</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="tarikh" name="tarikh" required>
                        </div>
                    </div>

                    <!-- Jenis Reject -->
                    <div class="mb-3 row align-items-center">
                        <label for="jenis_reject" class="form-label fw-semibold col-sm-3 col-form-label">Jenis Reject</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="jenis_reject" name="jenis_reject" required>
                                <option value="">Sila Pilih</option>
                                <option value="OverExposure">OverExposure</option>
                                <option value="UnderExposure">UnderExposure</option>
                                <option value="Double Exposure">Double Exposure</option>
                                <option value="Wrong Technique">Wrong Technique</option>
                                <option value="Wrong Patient / Exam">Wrong Patient / Exam</option>
                                <option value="Wrong Marker">Wrong Marker</option>
                                <option value="Collimation Error">Collimation Error</option>
                                <option value="Patient Movement">Patient Movement</option>
                                <option value="Patient Related Artifact">Patient Related Artifact</option>
                                <option value="Equipment Fault">Equipment Fault</option>
                                <option value="Detector / imaging plate">Detector / imaging plate</option>
                                <option value="Image artifact">Image artifact</option>
                                <option value="Processing Fault">Processing Fault</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Reject Modal -->
<div class="modal fade" id="editRejectModal" tabindex="-1" aria-labelledby="editRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRejectModalLabel">
                    <i class="ti ti-edit"></i> Edit Reject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRejectForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Error Alert (hidden by default) -->
                    <div id="editModalError" class="alert alert-danger d-none"></div>
                    
                    <!-- Hidden ID field -->
                    <input type="hidden" id="edit_reject_id" name="reject_id">
                    
                    <!-- Tarikh -->
                    <div class="mb-3 row align-items-center">
                        <label for="edit_tarikh" class="form-label fw-semibold col-sm-3 col-form-label">Tarikh</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="edit_tarikh" name="tarikh" required>
                        </div>
                    </div>

                    <!-- Jenis Reject -->
                    <div class="mb-3 row align-items-center">
                        <label for="edit_jenis_reject" class="form-label fw-semibold col-sm-3 col-form-label">Jenis Reject</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_jenis_reject" name="jenis_reject" required>
                                <option value="" disabled>Sila Pilih</option>
                                <option value="OverExposure">OverExposure</option>
                                <option value="UnderExposure">UnderExposure</option>
                                <option value="Double Exposure">Double Exposure</option>
                                <option value="Wrong Technique">Wrong Technique</option>
                                <option value="Wrong Patient / Exam">Wrong Patient / Exam</option>
                                <option value="Wrong Marker">Wrong Marker</option>
                                <option value="Collimation Error">Collimation Error</option>
                                <option value="Patient Movement">Patient Movement</option>
                                <option value="Patient Related Artifact">Patient Related Artifact</option>
                                <option value="Equipment Fault">Equipment Fault</option>
                                <option value="Detector / imaging plate">Detector / imaging plate</option>
                                <option value="Image artifact">Image artifact</option>
                                <option value="Processing Fault">Processing Fault</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with pagination
    var table = $('#reject-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "order": [[2, 'desc']], // Sort by date column (index 2) in descending order
        "columnDefs": [
            { "orderable": false, "targets": -1 }, // Delete
            <?php if ($ENABLE_MANAGE): ?>
            { "orderable": false, "targets": -2 }, // Edit
            <?php endif; ?>
            { "orderable": false, "targets": 0 }, // No column (row numbering)
            { "type": "date", "targets": 2 } // Ensure date column is treated as date for proper sorting
        ],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Papar _MENU_ rekod",
            "info": "Paparan _START_ hingga _END_ daripada _TOTAL_ rekod",
            "infoEmpty": "Tiada rekod",
            "infoFiltered": "(ditapis daripada _MAX_ jumlah rekod)",
            "zeroRecords": "Tiada padanan ditemui",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Seterusnya",
                "previous": "Sebelumnya"
            },
            "emptyTable": "Tiada data tersedia dalam jadual",
            "loadingRecords": "Memuatkan...",
            "processing": "Memproses..."
        },
        "drawCallback": function(settings) {
            // Re-number the rows after each draw (sort, filter, pagination)
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });

    // Set today's date when modal opens
    $('#addRejectModal').on('show.bs.modal', function() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, "0");
        const dd = String(today.getDate()).padStart(2, "0");
        const todayStr = `${yyyy}-${mm}-${dd}`;
        
        // Set the initial value to today
        document.getElementById("tarikh").value = todayStr;
        
        // Clear any previous error messages
        $('#modalError').addClass('d-none');
        
        // Reset form
        document.getElementById('rejectForm').reset();
        document.getElementById("tarikh").value = todayStr; // Set again after reset
    });

    // Form validation before submit
    $('#rejectForm').on('submit', function(e) {
        const jenisReject = $('#jenis_reject').val();
        const tarikh = $('#tarikh').val();
        
        // Clear previous error
        $('#modalError').addClass('d-none');

        if (!jenisReject) {
            e.preventDefault();
            $('#modalError').removeClass('d-none').text('Sila pilih jenis reject.');
            $('#jenis_reject').focus();
            return false;
        }

        if (!tarikh) {
            e.preventDefault();
            $('#modalError').removeClass('d-none').text('Sila pilih tarikh.');
            $('#tarikh').focus();
            return false;
        }
        
        // If validation passes, show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ti ti-loader"></i> Menyimpan...');
    });

    // Handle Edit button click
    $(document).on('click', '.edit-btn', function(e) {
        e.preventDefault();
        
        const rejectId = $(this).data('id');
        const jenisReject = $(this).data('jenis');
        const tarikh = $(this).data('tarikh');
        
        // Populate the edit modal with current data
        $('#edit_reject_id').val(rejectId);
        $('#edit_jenis_reject').val(jenisReject);
        
        // Format date for input field (convert from database format to YYYY-MM-DD)
        if (tarikh) {
            const dateObj = new Date(tarikh);
            const formattedDate = dateObj.toISOString().split('T')[0];
            $('#edit_tarikh').val(formattedDate);
        }
        
        // Set form action URL
        $('#editRejectForm').attr('action', '<?php echo module_url("reject/save/"); ?>' + rejectId);
        
        // Clear any previous error messages
        $('#editModalError').addClass('d-none');
        
        // Show the modal
        $('#editRejectModal').modal('show');
    });

    // Edit form validation before submit
    $('#editRejectForm').on('submit', function(e) {
        const jenisReject = $('#edit_jenis_reject').val();
        const tarikh = $('#edit_tarikh').val();
        
        // Clear previous error
        $('#editModalError').addClass('d-none');

        if (!jenisReject) {
            e.preventDefault();
            $('#editModalError').removeClass('d-none').text('Sila pilih jenis reject.');
            $('#edit_jenis_reject').focus();
            return false;
        }

        if (!tarikh) {
            e.preventDefault();
            $('#editModalError').removeClass('d-none').text('Sila pilih tarikh.');
            $('#edit_tarikh').focus();
            return false;
        }
        
        // If validation passes, show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ti ti-loader"></i> Menyimpan...');
    });

    // Simple delete function - exactly like Pesakit controller
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const rejectId = $(this).data('id');
        const rejectName = $(this).data('name');
        
        if (confirm(`Adakah anda pasti mahu memadam rekod "${rejectName}"?`)) {
            // Simple relative URL - works with your current structure
            const deleteUrl = '<?php echo module_url("reject/delete/"); ?>' + rejectId;
            
            // Create a form to submit the request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            
            // Add CSRF token if needed
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '<?php echo $this->security->get_csrf_token_name(); ?>';
            csrfToken.value = '<?php echo $this->security->get_csrf_hash(); ?>';
            form.appendChild(csrfToken);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>