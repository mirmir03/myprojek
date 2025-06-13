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
                    <a class="btn btn-primary" href="<?php echo module_url("reject/form_add"); ?>">
                        <i class="ti ti-plus"></i> Tambah Reject Baru
                    </a>
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
                        <td><?php echo ++$i; ?></td>
                        <td><?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?></td>
                        <td><?php echo htmlspecialchars($row->T06_TARIKH); ?></td>
                        
                        <?php if ($ENABLE_MANAGE): ?>
                        <td>
                            <a class="btn btn-sm btn-warning" href="<?php echo module_url("reject/form_edit/" . $row->T06_ID_REJECT); ?>">
                                <i class="ti ti-edit"></i> Edit
                            </a>
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

<script>
$(document).ready(function() {
    // Initialize DataTable with pagination
    var table = $('#reject-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "order": [[0, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": -1 }, // Delete
            <?php if ($ENABLE_MANAGE): ?>
            { "orderable": false, "targets": -2 }, // Edit
            <?php endif; ?>
            { "orderable": false, "targets": 0 } // No column (row numbering)
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
        }
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