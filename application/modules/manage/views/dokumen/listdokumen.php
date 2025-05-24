<?php
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;

    echo "Bilangan Data " . $data->num_rows();
?>
<div class="widget-content searchable-container list">
    <div class="card card-body">
        <div class="row">
            <div class="col-md-4 col-xl-3">
                <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Search ..">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                <div class="action-btn show-btn">
                    <a href="javascript:void(0)" class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center ">
                        <i class="ti ti-trash text-danger me-1 fs-5"></i> Delete All Row
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Senarai Dokumen Di Muat Naik <?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>  
        <a class="btn btn-primary float-end" href="<?php echo module_url("dokumen/form_add") ?>">Tambah Dokumen Baru</a>
    </div>
    <div class="card-body ">
        <table class="table table-hover table-striped" id="table">
        <thead>
            <thead>
                <tr>
                    <th>Bil</th> 
                    <th>Tahun</th>        
                    <th>Reject Analysis</th>
                    <th>CME Certification</th>
                    <th>Audit Image</th>
                    <th>Laporan QC</th>
                    <th>Edit</th>        
                    <th>Delete</th>                        
                </tr>
            </thead>
            <!-- Rest of your table code remains the same -->
<tbody>
    <?php $i = 0; foreach ($data->result() as $row) { ?>
    <tr>
        <td><?php echo ++$i; ?></td>
        <td><?php echo $row->T02_TARIKH; ?></td>
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
                <a href="<?= base_url($row->T02_DOKUMEN_CME_CERTIFICATION); ?>" target="_blank">
                    <?php echo basename($row->T02_DOKUMEN_CME_CERTIFICATION); ?>
                </a>
            <?php else: ?>
                <span class="text-muted">No file</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!empty($row->T02_AUDIT_IMAGE)): ?>
                <a href="<?= base_url($row->T02_AUDIT_IMAGE); ?>" target="_blank">
                    <?php echo basename($row->T02_AUDIT_IMAGE); ?>
                </a>
            <?php else: ?>
                <span class="text-muted">No file</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if (!empty($row->T02_DOKUMEN_LAPORANQC)): ?>
                <a href="<?= base_url($row->T02_DOKUMEN_LAPORANQC); ?>" target="_blank">
                    <?php echo basename($row->T02_DOKUMEN_LAPORANQC); ?>
                </a>
            <?php else: ?>
                <span class="text-muted">No file</span>
            <?php endif; ?>
        </td>
        <td>
            <a class="btn btn-flat btn-warning" href="<?php echo module_url("dokumen/form_edit/" . $row->T02_ID_DOKUMEN); ?>">Kemaskini</a>
        </td>
        <!-- Update the delete button HTML -->
<td>
    <a class="btn btn-flat btn-danger" 
       href="<?php echo module_url("dokumen/delete/" . $row->T02_ID_DOKUMEN); ?>" 
       onclick="return confirmDelete(this, event)">
       Padam
    </a>
</td>
    </tr>
    <?php } ?>
</tbody>


        </table>
    </div>
</div>

<!-- Add this JavaScript function at the bottom of your script section -->
<script>
function confirmDelete(link, event) {
    event.preventDefault();
    if (confirm('Adakah anda pasti mahu memadam rekod ini?')) {
        window.location.href = link.href;
    }
    return false;
}

// Keep your existing search functionality
document.getElementById('input-search').addEventListener('keyup', function () {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#table tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchText));
        row.style.display = match ? '' : 'none';
    });
});
</script>