<?php
// Enable these if you need permission checks
// $ENABLE_ADD = has_permission('reminder.Add');
// $ENABLE_MANAGE = has_permission('reminder.Manage');
// $ENABLE_DELETE = has_permission('reminder.Delete');
$ENABLE_ADD = TRUE;
$ENABLE_MANAGE = TRUE;
$ENABLE_DELETE = TRUE;

echo "Jumlah Rekod: " . $data->num_rows();
?>

<style>
    .status-badge {
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
</style>

<div class="widget-content searchable-container list">
    <div class="card card-body">
        <div class="row">
            <div class="col-md-4 col-xl-3">
                <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Cari ...">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                <?php if ($ENABLE_DELETE): ?>
                <div class="action-btn show-btn">
                    <a href="javascript:void(0)" class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center">
                        <i class="ti ti-trash text-danger me-1 fs-5"></i> Padam Pilihan
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Senarai Peringatan Pembekal
        <?= form_open($this->uri->uri_string(), array('id' => 'frm_reminder', 'name' => 'frm_reminder')) ?>  
        <?php if ($ENABLE_ADD): ?>
            <a class="btn btn-primary float-end" href="<?php echo module_url("reminder/addForm") ?>">
                <i class="ti ti-plus me-1"></i> Tambah Peringatan Baru
            </a>  
        <?php endif; ?>
    </div>
   
    <div class="card-body">
        <table class="table table-hover table-striped" id="table">
            <thead>
                <tr>
                    <th>Bil</th>        
                    <th>Nombor Pesanan</th>
                    <th>Nama Pembekal</th>
                    <th>Nombor Telefon</th>
                    <th>Tarikh Pesanan</th>
                    <th>Tarikh Tamat</th>
                    <th>Jumlah Harga (RM)</th>
                    <th>Status</th>
                    <th>Kemaskini</th>        
                    <th>Padam</th>                        
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 0; 
                // Use processed result if available, otherwise use original result
                $results = isset($data->processed_result) ? $data->processed_result : $data->result();
                foreach ($results as $row) { 
                ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo htmlspecialchars($row->T06_NOMBOR_PESANAN); ?></td>
                    <td><?php echo htmlspecialchars($row->T06_NAMA_PEMBEKAL); ?></td>
                    <td><?php echo htmlspecialchars($row->T06_NOMBOR_TELEFON); ?></td>
                    <td><?php echo htmlspecialchars($row->T06_TARIKH_PESANAN); ?></td>
                    <td><?php echo htmlspecialchars($row->T06_TARIKH_TAMAT); ?></td>
                    <td><?php echo number_format($row->T06_JUMLAH_HARGA, 2); ?></td>
                    <td>
                        <?php if (!empty($row->T06_PROSES_TARIKH)): ?>
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Diproses pada <?php echo htmlspecialchars($row->T06_PROSES_TARIKH); ?>">
                                <i class="ti ti-check"></i> Selesai
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Belum Diproses</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (empty($row->T06_PROSES_TARIKH) && $ENABLE_MANAGE): ?>
                            <a class="btn btn-flat btn-warning" href="<?php echo module_url("reminder/editForm/" . $row->T06_ID_NOTIFIKASI); ?>">
                                <i class="ti ti-edit me-1"></i> Kemaskini
                            </a>
                        <?php else: ?>
                            <button class="btn btn-flat btn-secondary" disabled>
                                <i class="ti ti-edit me-1"></i> Kemaskini
                            </button>
                            <small class="text-muted d-block"><?php echo !empty($row->T06_PROSES_TARIKH) ? 'Tidak boleh edit (telah diproses)' : 'Tiada kebenaran'; ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (empty($row->T06_PROSES_TARIKH) && $ENABLE_DELETE): ?>
                            <a class="btn btn-flat btn-danger delete-btn" 
                               href="javascript:void(0);" 
                               data-id="<?php echo $row->T06_ID_NOTIFIKASI; ?>" 
                               data-name="<?php echo htmlspecialchars($row->T06_NAMA_PEMBEKAL); ?>">
                               <i class="ti ti-trash me-1"></i> Padam
                            </a>
                        <?php else: ?>
                            <button class="btn btn-flat btn-secondary" disabled>
                                <i class="ti ti-trash me-1"></i> Padam
                            </button>
                            <small class="text-muted d-block"><?php echo !empty($row->T06_PROSES_TARIKH) ? 'Tidak boleh padam (telah diproses)' : 'Tiada kebenaran'; ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <!-- Report Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="<?php echo module_url('reminder/graphs'); ?>" class="btn btn-success report-btn">
                    <i class="ti ti-file-report me-1"></i> Laporan Graf
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Delete button functionality
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const reminderId = $(this).data('id');
        const supplierName = $(this).data('name');
        
        if (confirm(`Adakah anda pasti mahu memadam peringatan untuk "${supplierName}"?`)) {
            const deleteUrl = '<?php echo module_url("reminder/delete/"); ?>' + reminderId;
            
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

    // Search functionality
    document.getElementById('input-search').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#table tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const match = Array.from(cells).some(cell => 
                cell.textContent.toLowerCase().includes(searchText)
            );
            row.style.display = match ? '' : 'none';
        });
    });
});
</script>