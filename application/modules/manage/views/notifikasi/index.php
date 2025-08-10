<?php 
$ENABLE_ADD = TRUE;
$ENABLE_MANAGE = TRUE;
$ENABLE_DELETE = TRUE;
$ENABLE_EDIT = TRUE;
echo "Bilangan Data " . count($data);
?>

<style>
    /* Container for the card */
    .card.shadow-sm {
    height: 100%;
    display: flex;
    flex-direction: column;
    margin: 1rem;
}

.card-body {
    flex: 1;
    overflow-y: auto; /* Only this part scrolls */
}

    /* Prevent body scrolling */
    body.modal-open {
        overflow: hidden;
    }

    /* DataTables styling improvements */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
    }

    .tooltip-inner {
        max-width: 300px;
        text-align: left;
    }

    /* Fixed width for status buttons - maintain original colors */
    .toggle-status-btn {
        min-width: 100px !important;
        width: 100px !important;
    }

    /* Table row hover effects */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Amount styling */
    .amount-cell {
        font-weight: 600;
        text-align: right;
    }

    /* ID styling similar to invoice numbers */
    .id-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .id-link:hover {
        text-decoration: underline;
    }

    /* Round notification badge with sharp red color */
    .badge {
        border-radius: 50% !important;
        width: 24px !important;
        height: 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        padding: 0 !important;
        min-width: 24px !important;
    }

    /* Sharp red color for danger badge */
    .badge.bg-danger {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        border: 2px solid #ffffff !important;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3) !important;
    }

    /* Hover effect for badge */
    .badge.bg-danger:hover {
        background-color: #b91c1c !important;
        transform: scale(1.1);
        transition: all 0.2s ease;
    }

    /* PDF file icon styling */
    .pdf-icon {
        color: #blue;
        font-size: 1.2rem;
        transition: transform 0.2s ease;
    }

    .pdf-icon:hover {
        transform: scale(1.1);
    }

    .pdf-link {
        text-decoration: none;
    }

    .pdf-link:hover {
        text-decoration: none;
    }

    /* Style DataTables sorting arrows */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after {
        opacity: 0.5;
    }
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_desc:after {
        opacity: 1;
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

    /* Responsive table improvements */
    @media (max-width: 768px) {
        .table-responsive table {
            font-size: 0.875rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Smaller badge on mobile */
        .badge {
            width: 20px !important;
            height: 20px !important;
            font-size: 10px !important;
            min-width: 20px !important;
        }

        html, body {
    height: 100%;
    overflow: hidden; /* This disables body scrolling */
}
/* Pulse animation */
.pulse-animation {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

    }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark">Senarai Notifikasi Pesanan</h5>
            <div>
                <?php if ($ENABLE_ADD): ?>
                    <a class="btn btn-primary" href="<?php echo module_url("notifikasi/tambah") ?>">
                        <i class="ti ti-plus"></i> Tambah Notifikasi
                    </a>
                <?php endif; ?>
                <?php if ($ENABLE_DELETE): ?>
                    <button type="button" class="btn btn-danger ms-2 delete-multiple" style="display: none;">
                        <i class="ti ti-trash"></i> Padam Semua
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= form_open($this->uri->uri_string(), ['id' => 'frm_menu', 'name' => 'frm_menu']) ?>

        <div>
            <table class="table table-hover w-100" id="notifikasi-table">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">Bil</th>
                        <th>Nama Pembekal</th>
                        <th>No Telefon</th>
                        <th>No Pesanan</th>
                        <th>Tarikh Pesanan</th>
                        <th>Tamat Pesanan</th>
                        <th class="text-end">Jumlah Harga</th>
                        <th class="text-center">Fail PDF</th>
                        <th class="text-center">Status</th>
                        <?php if ($ENABLE_EDIT || $ENABLE_DELETE): ?>
                            <th width="150" class="text-center">Tindakan</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="text-center"></td>
                        
                        <td><?= htmlspecialchars($row->T06_NAMA_PEMBEKAL) ?></td>
                        <td><?= htmlspecialchars($row->T06_NOMBOR_TELEFON) ?></td>
                        <td><?= htmlspecialchars($row->T06_NOMBOR_PESANAN) ?></td>
                        <td><?= date('d/m/Y', strtotime($row->T06_TARIKH_PESANAN)) ?></td>
                        <td><?= date('d/m/Y', strtotime($row->T06_TAMAT_PESANAN)) ?></td>
                        <td class="amount-cell">RM <?= number_format($row->T06_JUMLAH_HARGA, 2) ?></td>
                        <td class="text-center">
                            <?php if (!empty($row->T06_PDF_FILE)): ?>
                                <a href="<?= base_url('uploads/pdf/' . basename($row->T06_PDF_FILE)); ?>" 
                                   target="_blank"
                                   class="pdf-link"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="<?= basename($row->T06_PDF_FILE) ?>">
                                    <i class="ti ti-file-text pdf-icon"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="top" 
                                      title="Tiada file">
                                    <i class="ti ti-file-off"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button"
                                class="btn btn-sm <?= $row->T06_STATUS == 'Received' ? 'btn-success' : 'btn-warning' ?> toggle-status-btn"
                                data-id="<?= $row->T06_ID_NOTIFIKASI ?>">
                                <?= $row->T06_STATUS == 'Received' ? 'Diterima' : 'Belum Terima' ?>
                            </button>
                        </td>
                        <?php if ($ENABLE_EDIT || $ENABLE_DELETE): ?>
                        <td class="text-center">
                            <?php if ($ENABLE_EDIT): ?>
                                <a class="btn btn-sm btn-warning edit-btn"
                                   href="<?= module_url("notifikasi/edit/" . $row->T06_ID_NOTIFIKASI) ?>"
                                   title="Edit Notifikasi">
                                   <i class="ti ti-edit"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($ENABLE_DELETE): ?>
                                <a class="btn btn-sm btn-danger delete-btn ms-1"
                                   href="javascript:void(0);"
                                   data-id="<?= $row->T06_ID_NOTIFIKASI ?>"
                                   data-name="<?= htmlspecialchars($row->T06_NAMA_PEMBEKAL) ?>"
                                   title="Padam Notifikasi">
                                   <i class="ti ti-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with enhanced features
    var table = $('#notifikasi-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        "order": [[4, 'desc']], // Sort by Tarikh Pesanan column by default
        "columnDefs": [
            { "orderable": false, "targets": [-1] }, // Only action column is not sortable
            { "type": "date-eu", "targets": [4, 5] }, // Date columns in dd/mm/yyyy format
            { "type": "num-fmt", "targets": [6] }, // For currency column
            { "className": "text-center", "targets": [0, 7, 8, 9] },
            { "className": "text-end", "targets": [6] }, // Right align amount
            { 
                "render": function(data, type, row) {
                    if (type === 'sort') {
                        // Convert dd/mm/yyyy to sortable format
                        var parts = data.split('/');
                        return parts[2] + parts[1] + parts[0];
                    }
                    return data;
                },
                "targets": [4, 5] // Apply to both date columns
            }
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
            // Re-number the rows
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });

    // Enhanced delete function with better UX
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');

        // Show confirmation with better styling
       if (confirm(`Adakah anda pasti mahu memadam notifikasi untuk "${name}"?\n\nTindakan ini tidak boleh dibatalkan.`)) {

            // Show loading state
            $(this).prop('disabled', true).html('<i class="ti ti-loader"></i>');
            
            const deleteUrl = '<?= base_url("manage/notifikasi/delete/") ?>' + id;
            const form = $('<form>', {
                method: 'POST',
                action: deleteUrl
            });

            form.append($('<input>', {
                type: 'hidden',
                name: '<?= $this->security->get_csrf_token_name(); ?>',
                value: '<?= $this->security->get_csrf_hash(); ?>'
            }));

            $('body').append(form);
            form.submit();
        }
    });

    // Enhanced toggle status function with improved error handling
$(document).on('click', '.toggle-status-btn', function () {
    const button = $(this);
    const id = button.data('id');

    // Add loading state
    button.prop('disabled', true).text('Tunggu...');

    $.ajax({
        url: '<?= base_url("manage/notifikasi/mark_received_ajax/") ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Update button text and style
                button.removeClass('btn-success btn-warning');

                if (response.new_status === 'Received') {
                    button.addClass('btn-success').text('Diterima');
                } else {
                    button.addClass('btn-warning').text('Belum Terima');
                }

                // Optionally update sidebar count
                console.log("Unreceived count:", response.count);
            } else {
                alert(response.message || 'Ralat tidak diketahui.');
            }
        },
        error: function () {
            alert('Ralat pelayan semasa mengemaskini status.');
        },
        complete: function () {
            button.prop('disabled', false);
        }
    });
});


// Function to update sidebar badge
function updateSidebarBadge(count) {
    const sidebarLink = $('.sidebar-link:contains("Pesanan Item Xray")');
    let badge = sidebarLink.find('.badge');

    if (count > 0) {
        if (badge.length) {
            badge.text(count).show();
        } else {
            // Create new badge
            const newBadge = $('<span class="badge bg-danger ms-2">' + count + '</span>');
            sidebarLink.append(newBadge);
            
            // Add animation
            newBadge.css('transform', 'scale(0)')
                   .animate({}, 0)
                   .css('transform', 'scale(1.2)')
                   .animate({}, 150, function() {
                       $(this).css('transform', 'scale(1)');
                   });
        }
    } else {
        if (badge.length) {
            badge.fadeOut(200, function() {
                $(this).remove();
            });
        }
    }
}

// Enhanced toast notification function
function showToast(message, type = 'info') {
    const toastClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const toast = $(`
        <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(toast);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.alert('close');
    }, 4000);
}
});
// File: assets/js/notification-polling.js
$(document).ready(function() {
    // Function to update notification count
    function updateNotificationCount() {
        $.ajax({
            url: module_url + 'notifikasi/get_notification_count',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update the badge count
                    $('.notification-badge').text(response.count);
                    
                    // Optional: Add visual effect if count changed
                    if (response.count > 0) {
                        $('.notification-badge').addClass('pulse');
                    } else {
                        $('.notification-badge').removeClass('pulse');
                    }
                }
            },
            complete: function() {
                // Schedule the next poll (every 30 seconds)
                setTimeout(updateNotificationCount, 30000);
            }
        });
    }

    // Initial call
    updateNotificationCount();
});
// assets/js/notification-polling.js
document.addEventListener('DOMContentLoaded', function() {
    const notificationBadge = document.getElementById('notificationBadge');
    const baseUrl = window.location.origin;
    
    function updateNotificationCount() {
        fetch(`${baseUrl}/manage/notifikasi/get_notification_count`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    if (data.count > 0) {
                        notificationBadge.textContent = data.count;
                        notificationBadge.style.display = 'inline-block';
                        
                        // Add pulse animation (optional)
                        notificationBadge.classList.add('pulse-animation');
                        setTimeout(() => {
                            notificationBadge.classList.remove('pulse-animation');
                        }, 1500);
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error fetching notification count:', error))
            .finally(() => {
                // Poll every 30 seconds
                setTimeout(updateNotificationCount, 30000);
            });
    }

    // Initial call
    updateNotificationCount();
});
</script>