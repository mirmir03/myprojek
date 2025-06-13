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
</style>

<div class="widget-content searchable-container list">
    <div class="card card-body">
        <div class="row">
            <div class="col-md-4 col-xl-3">
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
        <?php if ($ENABLE_ADD): ?>
            <a class="btn btn-primary float-end" href="<?php echo module_url("pesakit/form_add") ?>">Tambah Pesakit Baru</a>  
        <?php endif; ?>
                        <!-- Graph Button -->
<a class="btn btn-success me-2" href="<?php echo module_url("pesakit/patient_graph"); ?>">
    <i class="ti ti-chart-bar"></i> Jana Graf
</a>
    </div>
   
    <div class="card-body">
        <table class="table table-hover table-striped" id="table">
            <thead>
                <tr>
                    <th>No Xray</th>        
                    <th>No Rujukan</th>
                    <th>Nama Pesakit</th>
                    <th>Jantina</th>
                    <th>Kategori</th>
                    <th>Bahagian Utama</th>
                    <th>Sub Bahagian</th>
                    <th>Doctor Comment</th>
                    <th>Edit</th>        
                    <th>Delete</th>                        
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; foreach ($data->result() as $row) { ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo htmlspecialchars($row->T01_NO_RUJUKAN); ?></td>
                    <td><?php echo htmlspecialchars($row->T01_NAMA_PESAKIT); ?></td>
                    <td><?php echo htmlspecialchars($row->T01_JANTINA); ?></td>
                    <td><?php echo htmlspecialchars($row->T01_KATEGORI); ?></td>
                    <td><?php echo htmlspecialchars($row->T01_BAHAGIAN_UTAMA); ?></td>
                    <td><?php echo htmlspecialchars($row->T01_SUB_BAHAGIAN); ?></td>
                    <td>
                        <?php if (!empty($row->T01_DOCTOR_COMMENT)): ?>
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($row->T01_DOCTOR_COMMENT); ?>">
                                <i class="ti ti-message-circle"></i> Ada Komen
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tiada Komen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (empty($row->T01_DOCTOR_COMMENT) && $ENABLE_MANAGE): ?>
                            <a class="btn btn-flat btn-warning" href="<?php echo module_url("pesakit/form_edit/" . $row->T01_ID_PESAKIT); ?>">Kemaskini</a>
                        <?php else: ?>
                            <button class="btn btn-flat btn-secondary" disabled>Kemaskini</button>
                            <small class="text-muted d-block"><?php echo !empty($row->T01_DOCTOR_COMMENT) ? 'Tidak boleh edit (ada komen)' : 'Tiada kebenaran'; ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (empty($row->T01_DOCTOR_COMMENT) && $ENABLE_DELETE): ?>
                            <a class="btn btn-flat btn-danger delete-btn" 
                               href="javascript:void(0);" 
                               data-id="<?php echo $row->T01_ID_PESAKIT; ?>" 
                               data-name="<?php echo htmlspecialchars($row->T01_NAMA_PESAKIT); ?>">
                               Padam
                            </a>
                        <?php else: ?>
                            <button class="btn btn-flat btn-secondary" disabled>Padam</button>
                            <small class="text-muted d-block"><?php echo !empty($row->T01_DOCTOR_COMMENT) ? 'Tidak boleh padam (ada komen)' : 'Tiada kebenaran'; ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
   
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
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

</script>







