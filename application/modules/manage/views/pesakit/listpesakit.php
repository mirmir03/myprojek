<?php
$ENABLE_ADD = TRUE;
$ENABLE_MANAGE = TRUE;
$ENABLE_DELETE = TRUE;

echo "Bilangan Data " . $data->num_rows();
?>
<style>
    /* Fix background page */
    body {
        overflow-x: hidden;
        background-color: #f5f7f9; /* Static background color */
        position: relative;
        min-height: 100vh;
    }
    
    /* Container for the card */
    .card {
        margin: 1rem;
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Make page content scrollable */
    .card-body {
        overflow: visible;
        background-color: #ffffff;
    }

    /* DataTables pagination styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin-left: 2px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #6c757d !important;
        border-radius: 0.375rem;
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #000 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        color: #6c757d !important;
        border-color: #dee2e6;
        background-color: #fff;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .comment-badge {
        cursor: pointer;
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .tooltip-inner {
        max-width: 300px;
        text-align: left;
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

    /* Grey styling for disabled buttons */
    .btn-disabled-grey {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #ffffff !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }

    .btn-disabled-grey:hover,
    .btn-disabled-grey:focus,
    .btn-disabled-grey:active {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #ffffff !important;
        opacity: 0.7 !important;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Styling for disabled text */
    .disabled-text {
        color: #6c757d;
        font-size: 0.75rem;
        font-style: italic;
        margin-top: 2px;
    }

    /* Comment popup modal styling */
    .comment-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(2px);
    }

    .comment-modal.show {
        display: block;
    }

    .comment-modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border: none;
        border-radius: 0.5rem;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        transform: scale(0.7);
        opacity: 0;
        transition: all 0.3s ease;
        position: relative;
        animation: modalFadeIn 0.3s ease forwards;
    }

    .comment-modal.show .comment-modal-content {
        transform: scale(1);
        opacity: 1;
    }

    @keyframes modalFadeIn {
        from {
            transform: scale(0.7);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes modalFadeOut {
        from {
            transform: scale(1);
            opacity: 1;
        }
        to {
            transform: scale(0.7);
            opacity: 0;
        }
    }

    .comment-modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
        border-radius: 0.5rem 0.5rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .comment-modal-body {
        padding: 1.5rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .comment-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        background: none;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .comment-close:hover,
    .comment-close:focus {
        color: #000;
        text-decoration: none;
        transform: scale(1.1);
    }

    .comment-text {
        font-size: 14px;
        line-height: 1.6;
        color: #333;
        white-space: pre-wrap;
        word-wrap: break-word;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        border-left: 4px solid #28a745;
    }

    .patient-info {
        background-color: #e3f2fd;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        border-left: 4px solid #0d6efd;
    }

    .patient-info h6 {
        margin: 0 0 0.5rem 0;
        color: #0d6efd;
        font-weight: 600;
    }

    .patient-info p {
        margin: 0.25rem 0;
        font-size: 14px;
        color: #495057;
    }
    
    /* Static background container */
    .static-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f5f7f9;
        z-index: -1;
    }
    
    /* Main content wrapper */
    .content-wrapper {
        position: relative;
        z-index: 1;
    }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Static background element -->
<div class="static-background"></div>

<div class="content-wrapper">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-4 col-xl-3">
                    <!-- Keep the original search input for visual consistency, but DataTables will handle search -->
                    <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Cari ..">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
                <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <?php if ($ENABLE_DELETE): ?>
                    <div class="action-btn show-btn">
                        <a href="javascript:void(0)" class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center">
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
            Senarai Pesakit Xray Berdaftar 
            <?= form_open($this->uri->uri_string(), array('id' => 'frm_menu', 'name' => 'frm_menu')) ?>  
            <div class="float-end d-flex gap-2">
            <?php if ($ENABLE_ADD): ?>
                <a class="btn btn-primary float-end" href="<?php echo module_url("pesakit/form_add") ?>"><i class="ti ti-plus"></i>Tambah Pesakit Baru</a>  
            <?php endif; ?>
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

            <div>
                <table class="table table-hover table-striped w-100" id="pesakit-table">
                    <thead class="table-light">
                        <tr>
                            <th width="80">No Xray</th>  
                            <th>Tarikh</th>      
                            <th>No Pengenalan</th>
                            <th>Nama Pesakit</th>
                            <th>Jantina</th>
                            <th>Kategori</th>
                            <th>Bahagian Utama</th>
                            <th>Sub Bahagian</th>
                            <th width="120">Komen Doktor</th>
                            <?php if ($ENABLE_MANAGE): ?>
                                <th width="120">Kemaskini</th>
                            <?php endif; ?>
                            <?php if ($ENABLE_DELETE): ?>
                                <th width="120">Padam</th>
                            <?php endif; ?>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Sort data by date (assuming there's a date field - adjust field name as needed)
                        $data_array = $data->result();
                        
                        // Sort by date descending (latest first)
                        // Replace 'T01_TARIKH' with your actual date field name
                        usort($data_array, function($a, $b) {
            $timeA = !empty($a->T01_TARIKH) ? strtotime($a->T01_TARIKH) : 0;
            $timeB = !empty($b->T01_TARIKH) ? strtotime($b->T01_TARIKH) : 0;
            return $timeB - $timeA;
        });
                        
                        $total_records = count($data_array);
                        $i = $total_records; // Start numbering from highest
                        foreach ($data_array as $row) { ?>
                        <tr>
                            <td><?php echo $i--; ?></td>
                            <td><?php echo !empty($row->T01_TARIKH) ? date('d/m/Y', strtotime($row->T01_TARIKH)) : ''; ?></td>
                            <td><?php echo htmlspecialchars($row->T01_NO_RUJUKAN); ?></td>
                            <td><?php echo htmlspecialchars($row->T01_NAMA_PESAKIT); ?></td>
                            <td><?php echo htmlspecialchars($row->T01_JANTINA); ?></td>
                            <td><?php echo htmlspecialchars($row->T01_KATEGORI); ?></td>
                            <td><?php echo htmlspecialchars($row->T01_BAHAGIAN_UTAMA); ?></td>
                            <td><?php echo htmlspecialchars($row->T01_SUB_BAHAGIAN); ?></td>
                            <td>
                                <?php if (!empty($row->T01_DOCTOR_COMMENT)): ?>
                                    <span class="badge bg-success comment-badge" 
                                          style="cursor: pointer;" 
                                          onclick="showCommentModal(
                                              '<?php echo htmlspecialchars($row->T01_NAMA_PESAKIT, ENT_QUOTES); ?>',
                                              '<?php echo htmlspecialchars($row->T01_BAHAGIAN_UTAMA, ENT_QUOTES); ?>',
                                              '<?php echo htmlspecialchars($row->T01_SUB_BAHAGIAN, ENT_QUOTES); ?>',
                                              '<?php echo htmlspecialchars($row->T01_DOCTOR_COMMENT, ENT_QUOTES); ?>'
                                          )">
                                        <i class="ti ti-message-circle"></i> Ada Komen
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tiada Komen</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if ($ENABLE_MANAGE): ?>
                            <td>
                                <?php if (empty($row->T01_DOCTOR_COMMENT)): ?>
                                    <a class="btn btn-sm btn-warning" href="<?php echo module_url("pesakit/form_edit/" . $row->T01_ID_PESAKIT); ?>">
                                        <i class="ti ti-edit"></i> Kemaskini
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-disabled-grey" disabled>
                                        <i class="ti ti-edit"></i> Kemaskini
                                    </button>
                                    <div class="disabled-text">Tidak boleh edit (ada komen)</div>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>

                            <?php if ($ENABLE_DELETE): ?>
                            <td>
                                <?php if (empty($row->T01_DOCTOR_COMMENT)): ?>
                                    <a class="btn btn-sm btn-danger delete-btn" 
                                       href="javascript:void(0);" 
                                       data-id="<?php echo $row->T01_ID_PESAKIT; ?>" 
                                       data-name="<?php echo htmlspecialchars($row->T01_NAMA_PESAKIT); ?>">
                                       <i class="ti ti-trash"></i> Padam
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-disabled-grey" disabled>
                                        <i class="ti ti-trash"></i> Padam
                                    </button>
                                    <div class="disabled-text">Tidak boleh padam (ada komen)</div>
                                <?php endif; ?>
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
</div>

<!-- Comment Modal -->
<div id="commentModal" class="comment-modal">
    <div class="comment-modal-content">
        <div class="comment-modal-header">
            <h5 class="modal-title">
                <i class="ti ti-message-circle me-2"></i>Komen Doktor
            </h5>
            <button type="button" class="comment-close" onclick="closeCommentModal()">&times;</button>
        </div>
        <div class="comment-modal-body">
            <div class="patient-info">
                <h6>Maklumat Pesakit</h6>
                <p><strong>Nama:</strong> <span id="modalPatientName"></span></p>
                <p><strong>Bahagian Utama:</strong> <span id="modalPatientRef"></span></p>
                <p><strong>Sub Bahagian:</strong> <span id="modalPatientSub"></span></p>
            </div>
            <div>
                <h6 class="mb-3">
                    <i class="ti ti-message-dots me-2"></i>Komen:
                </h6>
                <div class="comment-text" id="modalCommentText"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Check if DataTable is already initialized
$.fn.dataTable.Api.register('isInitialized()', function() {
    return this.length > 0 && $(this).data('dataTable') !== undefined;
});

$(document).ready(function() {
    // Check if DataTable is already initialized before initializing
    if (!$.fn.DataTable.isDataTable('#pesakit-table')) {
        // Initialize DataTable with pagination
        var table = $('#pesakit-table').DataTable({
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            "ordering": true, // Enable sorting
            "order": [[1, 'desc']], // Default sort by date column (index 1) descending
            "searching": true, // Enable DataTables search
            "columnDefs": [
                { "type": "date-eu", "targets": 1 }, // For date column (dd/mm/yyyy format)
                { "orderable": false, "targets": [-1, -2] } // Disable sorting for action columns (last two columns)
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
            }
        });

        // Connect custom search to DataTables search
        $('#input-search').on('keyup', function() {
            table.search(this.value).draw();
        });
    } else {
        console.warn('DataTable is already initialized on #pesakit-table');
    }

    // Delete button functionality
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const patientId = $(this).data('id');
        const patientName = $(this).data('name');
        
        if (confirm(`Adakah anda pasti mahu memadam rekod "${patientName}"?`)) {
            // Simple relative URL - works with your current structure
            const deleteUrl = '<?php echo base_url("manage/pesakit/delete/"); ?>' + patientId;
            
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

    // Close modal when clicking outside of it
    $(document).on('click', '#commentModal', function(event) {
        if (event.target === this) {
            closeCommentModal();
        }
    });

    // Close modal with Escape key
    $(document).on('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCommentModal();
        }
    });

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Function to show comment modal - FIXED PARAMETERS
function showCommentModal(patientName, patientRef, patientSub, comment) {
    console.log('Opening modal with:', patientName, patientRef, patientSub, comment); // Debug log
    
    // Set the content
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('modalPatientRef').textContent = patientRef;
    document.getElementById('modalPatientSub').textContent = patientSub;
    document.getElementById('modalCommentText').textContent = comment;
    
    // Show the modal
    const modal = document.getElementById('commentModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    
    // Focus on close button for accessibility
    setTimeout(function() {
        document.querySelector('.comment-close').focus();
    }, 100);
}

// Function to close comment modal - IMPROVED
function closeCommentModal() {
    const modal = document.getElementById('commentModal');
    const content = document.querySelector('.comment-modal-content');
    
    // Add closing animation
    content.style.animation = 'modalFadeOut 0.3s ease forwards';
    
    setTimeout(function() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        content.style.animation = '';
        
        // Restore body scrolling
        document.body.style.overflow = '';
    }, 300);
}
</script>