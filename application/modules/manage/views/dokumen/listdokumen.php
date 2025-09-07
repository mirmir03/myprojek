<?php
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;

    echo "Bilangan Data " . $data->num_rows();
?>

<style>
    /* Prevent any background movement or shifting */
    body, html {
        overflow-x: hidden;
        position: relative;
    }
    
    .main-wrapper {
        position: relative;
        overflow-x: hidden;
    }
    
    .card {
        position: relative;
        transform: none !important;
        transition: none !important;
        will-change: auto !important;
    }
    
    .card-body {
        position: relative;
        transform: none !important;
        transition: none !important;
    }
    
    /* Prevent table and container movement */
    .table-responsive {
        position: relative;
        transform: none !important;
        will-change: auto !important;
    }
    
    .table {
        position: relative;
        transform: none !important;
        will-change: auto !important;
    }
    
    /* DataTables styling improvements - prevent movement */
    .dataTables_wrapper {
        position: relative;
        transform: none !important;
    }
    
    .dataTables_wrapper .dataTables_length select {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transform: none !important;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        margin-left: 0.5rem;
        transform: none !important;
    }
    
    /* Prevent tooltip movement issues */
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
        position: relative !important;
        transform: none !important;
    }
    
    /* Stable hover effects without movement */
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
        transform: none !important;
        transition: background-color 0.15s ease-in-out;
    }
    
    .btn:hover {
        transform: none !important;
        transition: all 0.15s ease-in-out;
    }
    
    /* Prevent any scrollbar-related movement */
    .widget-content {
        overflow-x: visible;
        position: relative;
    }
    
    /* Fix search container positioning */
    .product-search {
        position: relative;
        transform: none !important;
    }
    
    /* Ensure pagination doesn't cause movement */
    .pagination {
        transform: none !important;
        will-change: auto !important;
    }
    
    .pagination .page-link {
        transform: none !important;
        transition: all 0.15s ease-in-out;
    }
    
    /* Prevent accordion-like movements */
    .accordion-item {
        transform: none !important;
    }
    
    .accordion-button {
        transform: none !important;
        transition: all 0.15s ease-in-out;
    }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<div class="widget-content searchable-container list">
    <div class="card card-body">
        <div class="row">
            <div class="col-md-4 col-xl-3">
                <!-- Keep the original search input for visual consistency -->
                <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Cari ..">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                <?php if ($ENABLE_DELETE): ?>
                <div class="action-btn show-btn">
                    <a href="javascript:void(0)" class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center ">
                        <i class="ti ti-trash text-danger me-1 fs-5"></i> Padam Semua
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Senarai Dokumen Di Muat Naik 
        <?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>  
        <?php if ($ENABLE_ADD): ?>
            <a class="btn btn-primary float-end" href="<?php echo module_url("dokumen/form_add") ?>"><i class="ti ti-plus"></i>Tambah Dokumen Baru</a>
        <?php endif; ?>
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

        <div class="table-responsive">
            <table class="table table-hover table-striped" id="dokumen-table">
                <thead class="table-light">
                    <tr>
                        <th>Bil</th> 
                        <th>Tahun</th>        
                        <th>Reject Analysis</th>
                        <th>CME Certification</th>
                        <th>Audit Image</th>
                        <th>Laporan QC</th>
                        <?php if ($ENABLE_MANAGE): ?>
                            <th>Edit</th>
                        <?php endif; ?>
                        <?php if ($ENABLE_DELETE): ?>
                            <th>Delete</th>
                        <?php endif; ?>                        
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sort data by date descending (latest first)
                    $data_array = $data->result();
                    usort($data_array, function($a, $b) {
                        return strtotime($b->T02_TARIKH) - strtotime($a->T02_TARIKH);
                    });
                    
                    $total_records = count($data_array);
                    $i = $total_records; // Start numbering from highest
                    foreach ($data_array as $row) { ?>
                    <tr>
                        <td><?php echo $i--; ?></td>
                        <td><?php echo htmlspecialchars($row->T02_TARIKH); ?></td>
                        <td>
                            <?php if (!empty($row->T02_DOKUMEN_REJECT_ANALYSIS)): ?>
                                <a href="<?= base_url('www-uploads/' . basename($row->T02_DOKUMEN_REJECT_ANALYSIS)); ?>" target="_blank">
                                    <?php echo basename($row->T02_DOKUMEN_REJECT_ANALYSIS); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row->T02_DOKUMEN_CME_CERTIFICATION)): ?>
                                <a href="<?= base_url('www-uploads/' . basename($row->T02_DOKUMEN_CME_CERTIFICATION)); ?>" target="_blank">
                                    <?php echo basename($row->T02_DOKUMEN_CME_CERTIFICATION); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row->T02_AUDIT_IMAGE)): ?>
                                <a href="<?= base_url('www-uploads/' . basename($row->T02_AUDIT_IMAGE)); ?>" target="_blank">
                                    <?php echo basename($row->T02_AUDIT_IMAGE); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row->T02_DOKUMEN_LAPORANQC)): ?>
                                <a href="<?= base_url('www-uploads/' . basename($row->T02_DOKUMEN_LAPORANQC)); ?>" target="_blank">
                                    <?php echo basename($row->T02_DOKUMEN_LAPORANQC); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                        
                        <?php if ($ENABLE_MANAGE): ?>
                        <td>
                            <a class="btn btn-sm btn-warning" href="<?php echo module_url("dokumen/form_edit/" . $row->T02_ID_DOKUMEN); ?>">
                                <i class="ti ti-edit"></i> Kemaskini
                            </a>
                        </td>
                        <?php endif; ?>

                        <?php if ($ENABLE_DELETE): ?>
                        <td>
                            <a class="btn btn-sm btn-danger delete-btn" 
                               href="javascript:void(0);" 
                               data-id="<?php echo $row->T02_ID_DOKUMEN; ?>" 
                               data-year="<?php echo htmlspecialchars($row->T02_TARIKH); ?>">
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
    // Disable any CSS animations that might cause movement
    $('*').css({
        'transform': 'none',
        'transition': 'none'
    });
    
    // Re-enable only safe transitions after a brief delay
    setTimeout(function() {
        $('*').css('transition', '');
        $('.table-hover tbody tr, .btn, .pagination .page-link').css({
            'transition': 'all 0.15s ease-in-out',
            'transform': 'none'
        });
    }, 100);

    // Initialize DataTable with stable settings - NO MOVEMENT
    var table = $('#dokumen-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "ordering": true,
        "order": [[1, 'desc']], // Default sort by year column descending
        "searching": false, // Disable DataTables search to prevent conflicts
        "columnDefs": [
            { "orderable": false, "targets": [-1, -2] }, // Disable sorting for edit and delete columns
            { "className": "no-transform", "targets": "_all" } // Prevent transforms on all columns
        ],
        "language": {
            "lengthMenu": "Papar _MENU_ rekod",
            "info": "Paparan _START_ hingga _END_ daripada _TOTAL_ rekod",
            "infoEmpty": "Tiada rekod",
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
            // Ensure no transforms are applied after redraw
            $('#dokumen-table').css('transform', 'none');
            $('#dokumen-table tbody tr').css('transform', 'none');
        }
    });

    // Keep your original search functionality with no movement
    $('#input-search').on('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#dokumen-table tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const match = Array.from(cells).some(cell => 
                cell.textContent.toLowerCase().includes(searchText)
            );
            row.style.display = match ? '' : 'none';
            // Ensure no transform is applied during search
            row.style.transform = 'none';
        });
    });

    // Delete button functionality with no movement
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const dokumenId = $(this).data('id');
        const dokumenYear = $(this).data('year');
        
        if (confirm(`Adakah anda pasti mahu memadam dokumen tahun "${dokumenYear}"?`)) {
            // Redirect to delete URL
            const deleteUrl = '<?php echo base_url("manage/dokumen/delete/"); ?>' + dokumenId;
            
            // Create a form to submit the request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            form.style.transform = 'none';
            
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

    // Initialize tooltips with stable positioning
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                container: 'body',
                trigger: 'hover',
                placement: 'top',
                template: '<div class="tooltip" style="transform: none !important;"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
            });
        });
    }
    
    // Prevent any mouse tracking or movement effects
    $(document).off('mousemove.unwanted mouseenter.unwanted mouseleave.unwanted');
    
    // Override any existing transform styles that might cause movement
    setInterval(function() {
        $('.card, .card-body, .table-responsive, .table').each(function() {
            if ($(this).css('transform') !== 'none') {
                $(this).css('transform', 'none');
            }
        });
    }, 1000);
});

// Prevent document-level movement effects
document.addEventListener('mousemove', function(e) {
    e.stopPropagation();
}, true);

document.addEventListener('mouseenter', function(e) {
    if (e.target.closest('.card, .table-responsive, .table')) {
        e.stopPropagation();
    }
}, true);
</script>