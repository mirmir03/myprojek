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

<div class="widget-content searchable-container list">
    <div class="card card-body">
        <div class="row">
            <div class="col-md-4 col-xl-3">
                <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Cari ..">
                <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
            </div>
            <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                <?php if ($ENABLE_ADD): ?>
                <a class="btn btn-primary float-end" href="<?php echo module_url("reject/form_add") ?>">Tambah Reject Baru</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Senarai Reject
    </div>
   
    <div class="card-body">
        <table class="table table-hover table-striped" id="table">
            <thead>
                <tr>
                    <th>Bil</th>
                    <th>Tarikh</th>
                    <th>Jenis Reject</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; foreach ($data->result() as $row) { ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo htmlspecialchars($row->T06_TARIKH); ?></td>
                    <td><?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <?php if ($ENABLE_MANAGE): ?>
                                <a class="btn btn-sm btn-warning" href="<?php echo module_url("reject/form_edit/" . $row->T06_ID_REJECT); ?>">
                                    <i class="ti ti-edit"></i> Edit
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($ENABLE_DELETE): ?>
                                <a class="btn btn-sm btn-danger delete-btn" 
                                   href="javascript:void(0);" 
                                   data-id="<?php echo $row->T06_ID_REJECT; ?>" 
                                   data-name="<?php echo htmlspecialchars($row->T06_JENIS_REJECT); ?>">
                                   <i class="ti ti-trash"></i> Padam
                                </a>
                            <?php endif; ?>
                        </div>
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
        const rejectId = $(this).data('id');
        const rejectName = $(this).data('name');
        
        if (confirm(`Adakah anda pasti mahu memadam reject "${rejectName}"?`)) {
            const deleteUrl = '<?php echo base_url("manage/reject/delete/"); ?>' + rejectId;
            
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