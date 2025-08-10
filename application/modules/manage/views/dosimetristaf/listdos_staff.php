<?php
// Detect user role from session safely
$userRoleName = isset($_SESSION['userrole']) ? strtolower(trim($_SESSION['userrole'])) : null;
if (!$userRoleName && isset($_SESSION['role'])) {
    $userRoleName = strtolower(trim($_SESSION['role']));
}

// Initialize permission flags with default values
$ENABLE_ADD = true;
$ENABLE_MANAGE = true;
$ENABLE_DELETE = true;

// Restrict permissions based on user role
if (in_array($userRoleName, ['doctor', 'dentist'])) {
    $ENABLE_ADD = false;
    $ENABLE_MANAGE = false;
    $ENABLE_DELETE = false;
}

// Debug output (optional)
// echo "<!-- DEBUG: ENABLE_ADD = " . ($ENABLE_ADD ? 'true' : 'false') . " -->";
// echo "<!-- DEBUG: ENABLE_MANAGE = " . ($ENABLE_MANAGE ? 'true' : 'false') . " -->";
// echo "<!-- DEBUG: ENABLE_DELETE = " . ($ENABLE_DELETE ? 'true' : 'false') . " -->";
?>

<!-- DEBUG ROLE: <?= $userRoleName ?> -->
<!-- ENABLE_MANAGE: <?= $ENABLE_MANAGE ? 'true' : 'false' ?> -->
<!-- ENABLE_ADD: <?= $ENABLE_ADD ? 'true' : 'false' ?> -->
<!-- ENABLE_DELETE: <?= $ENABLE_DELETE ? 'true' : 'false' ?> -->

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
    
    /* Accordion styling */
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
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
    
    /* Highlight cells with values */
    .has-value {
        background-color: #f8f9fa;
        font-weight: 500;
    }
    
    /* Clickable cells */
    .clickable-cell {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .clickable-cell:hover {
        background-color: #e9ecef !important;
    }
</style>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Senarai Dosimetri Staf</h5>
            <div>
                <?php if ($ENABLE_ADD): ?>
                    <a href="<?php echo module_url("dosimetristaf/form_add"); ?>" class="btn btn-primary me-2">
                        <i class="ti ti-plus"></i> Tambah Data
                    </a>
                <?php endif; ?>
                <a class="btn btn-success" href="<?php echo module_url("dosimetristaf/graph"); ?>">
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

        <div class="accordion" id="dosAccordion">
<?php foreach ($staff_data as $staff_name => $year_data): ?>
    <?php $safeId = 'collapse_' . preg_replace('/[^a-z0-9]/i', '-', strtolower($staff_name)); ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_<?= $safeId ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#<?= $safeId ?>" aria-expanded="false" aria-controls="<?= $safeId ?>">
                <?= htmlspecialchars($staff_name) ?>
            </button>
        </h2>
        <div id="<?= $safeId ?>" class="accordion-collapse collapse" aria-labelledby="heading_<?= $safeId ?>">
            <div class="accordion-body p-0">

                <!-- Table for DOS_SETARA1 (AVE1) -->
                <div class="d-flex align-items-center ms-3 me-3">
                    <h5 class="mb-0 me-2">Dos Setara 1 (T04_DOS_AVE1)</h5>
                    <?php 
                    $firstId = null;
                    foreach ($year_data as $year => $data) {
                        if (!empty($data['id'])) {
                            $firstId = $data['id'];
                            break;
                        }
                    }
                    ?>
                    <?php if ($ENABLE_MANAGE && $firstId): ?>
                        <a href="<?= module_url('dosimetristaf/form_edit/' . $firstId); ?>" 
                           class="btn btn-sm btn-outline-secondary"
                           data-bs-toggle="tooltip"
                           title="Edit semua data tahun ini">
                            <i class="ti ti-edit"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>TAHUN</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAR</th>
                                <th>APR</th>
                                <th>MAY</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AUG</th>
                                <th>SEP</th>
                                <th>OCT</th>
                                <th>NOV</th>
                                <th>DEC</th>
                                <th class="bg-light">JUMLAH (AVE1)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($year_data as $year => $yearData): ?>
                                <tr>
                                    <td><?= $year ?></td>
                                    <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $month): ?>
                                        <?php
                                            $value = $yearData['ave1']['months'][$month] ?? null;
                                            $id = $yearData['id'] ?? null;
                                            $hasValue = $value !== null;
                                        ?>
                                        <td class="<?= $hasValue ? 'has-value clickable-cell' : 'text-muted' ?>"
                                            <?php if ($hasValue && $ENABLE_MANAGE && $id): ?>
                                                onclick="window.location.href='<?= module_url("dosimetristaf/form_edit_month/" . $id . "?month=" . $month . "&year=" . $year) ?>'"
                                                data-bs-toggle="tooltip"
                                                title="Klik untuk kemaskini data bulan <?= $month ?> tahun <?= $year ?>"
                                            <?php endif; ?>
                                        >
                                            <?= $hasValue ? number_format($this->dosimetristaf_model->_toDecimal($value), 2) : '-' ?>
                                        </td>
                                    <?php endforeach ?>
                                    <td class="fw-bold bg-light">
                                        <?= number_format($this->dosimetristaf_model->_toDecimal($yearData['ave1']['yearly_total']), 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table for DOS_SETARA2 (AVE2) -->
                <div class="table-responsive mt-4 mb-3">
                    <h5 class="ms-3">Dos Setara 2 (T04_DOS_AVE2)</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>TAHUN</th>
                                <th>JAN</th>
                                <th>FEB</th>
                                <th>MAR</th>
                                <th>APR</th>
                                <th>MAY</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AUG</th>
                                <th>SEP</th>
                                <th>OCT</th>
                                <th>NOV</th>
                                <th>DEC</th>
                                <th class="bg-light">JUMLAH (AVE2)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($year_data as $year => $yearData): ?>
                                <tr>
                                    <td><?= $year ?></td>
                                    <?php foreach (['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'] as $month): ?>
                                        <?php
                                            $value = $yearData['ave2']['months'][$month] ?? null;
                                            $id = $yearData['id'] ?? null;
                                            $hasValue = $value !== null;
                                        ?>
                                        <td class="<?= $hasValue ? 'has-value clickable-cell' : 'text-muted' ?>"
                                            <?php if ($hasValue && $ENABLE_MANAGE && $id): ?>
                                                onclick="window.location.href='<?= module_url("dosimetristaf/form_edit_month/" . $id . "?month=" . $month . "&year=" . $year) ?>'"
                                                data-bs-toggle="tooltip"
                                                title="Klik untuk kemaskini data bulan <?= $month ?> tahun <?= $year ?>"
                                            <?php endif; ?>
                                        >
                                            <?= $hasValue ? number_format($this->dosimetristaf_model->_toDecimal($value), 2) : '-' ?>
                                        </td>
                                    <?php endforeach ?>
                                    <td class="fw-bold bg-light">
                                        <?= number_format($this->dosimetristaf_model->_toDecimal($yearData['ave2']['yearly_total']), 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                        <div style="display:none;">
    ENABLE_MANAGE: <?= $ENABLE_MANAGE ? 'true' : 'false' ?><br>
    User Role: <?= $userRoleName ?><br>
    First ID: <?= $firstId ?>
</div>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>
</div>

<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Set today's date when modal opens
        $('#addDosimetriModal').on('show.bs.modal', function() {
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            document.getElementById("tarikh").value = todayStr;
            $('#modalError').addClass('d-none');
            document.getElementById('dosimetriForm').reset();
        });

        // Form validation before submit
        $('#dosimetriForm').on('submit', function(e) {
            const tarikh = $('#tarikh').val();
            
            $('#modalError').addClass('d-none');

            if (!tarikh) {
                e.preventDefault();
                $('#modalError').removeClass('d-none').text('Sila pilih tarikh.');
                $('#tarikh').focus();
                return false;
            }
            
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ti ti-loader"></i> Menyimpan...');
        });

        // Handle Edit button click
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            
            const dosimetriId = $(this).data('id');
            const dosSetara1 = $(this).data('dos_setara1');
            const dosAve1 = $(this).data('dos_ave1');
            const tarikh = $(this).data('tarikh');
            
            $('#edit_dosimetri_id').val(dosimetriId);
            $('#edit_dos_setara1').val(dosSetara1);
            $('#edit_dos_ave1').val(dosAve1);
            
            if (tarikh) {
                const dateObj = new Date(tarikh);
                const formattedDate = dateObj.toISOString().split('T')[0];
                $('#edit_tarikh').val(formattedDate);
            }
            
            $('#editDosimetriForm').attr('action', '<?php echo module_url("dosimetristaf/save/"); ?>' + dosimetriId);
            $('#editModalError').addClass('d-none');
            $('#editDosimetriModal').modal('show');
        });

        // Edit form validation before submit
        $('#editDosimetriForm').on('submit', function(e) {
            const tarikh = $('#edit_tarikh').val();
            
            $('#editModalError').addClass('d-none');

            if (!tarikh) {
                e.preventDefault();
                $('#editModalError').removeClass('d-none').text('Sila pilih tarikh.');
                $('#edit_tarikh').focus();
                return false;
            }
            
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ti ti-loader"></i> Menyimpan...');
        });

        // Delete button functionality
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const dosimetriId = $(this).data('id');
            const dosimetriName = $(this).data('name');
            
            if (confirm(`Adakah anda pasti mahu memadam rekod "${dosimetriName}"?`)) {
                const deleteUrl = '<?php echo module_url("dosimetristaf/delete/"); ?>' + dosimetriId;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                csrfToken.value = '<?php echo $this->security->get_csrf_hash(); ?>';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>