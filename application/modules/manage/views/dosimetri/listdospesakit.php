<?php
$ENABLE_ADD = true;
$ENABLE_DELETE = true;
?>

<style>
    .dose-high { background-color: #ffcccc; }
    .dose-medium { background-color: #fff3cd; }
    .table-responsive { overflow-x: auto; }
    th { white-space: nowrap; }
</style>

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Senarai Dosimetri Pesakit</h5>
            <div>
                <a href="<?= module_url('pesakit/listpesakit') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali ke Senarai Pesakit
                </a>
                <?php if ($ENABLE_ADD): ?>
                <a href="<?= module_url('dosimetri/form_add_pesakit') ?>" class="btn btn-sm btn-primary ms-2">
                    <i class="ti ti-plus"></i> Tambah Rekod
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="dosimetri-table">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Xray</th>
                        <th>Nama Pesakit</th>
                        <th>Voltan Tiub (kV)</th>
                        <th>Arus-Masa (mAs)</th>
                        <th>Masa Pendedahan (ms)</th>
                        <th>Jarak Sumber-Gambar (cm)</th>
                        <th>Jarak Sumber-Kulit (cm)</th>
                        <th>Saiz Medan Kolimasi</th>
                        <th>Grid</th>
                        <th>DAP (μGy·m²)</th>
                        <th>Indeks Pendedahan</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($data->result() as $row): 
                        $patient = $this->db->where("T01_ID_PESAKIT", $row->T01_ID_PESAKIT)
                                         ->get("EV_T01_PESAKIT")
                                         ->row();
                        
                        // Determine dose level
                        $row_class = '';
                        if ($row->T03_DOSE_AREA_PRODUCT > 1000) {
                            $row_class = 'dose-high';
                        } elseif ($row->T03_DOSE_AREA_PRODUCT > 500) {
                            $row_class = 'dose-medium';
                        }
                    ?>
                    <tr class="<?= $row_class ?>">
                        <td><?= ++$i ?></td>
                        <td><?= htmlspecialchars($patient->T01_NO_RUJUKAN ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($patient->T01_NAMA_PESAKIT ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row->T03_TUBE_VOLTAGE) ?></td>
                        <td><?= htmlspecialchars($row->T03_CURRENT_TIME_PRODUCT) ?></td>
                        <td><?= htmlspecialchars($row->T03_EXPOSURE_TIME) ?></td>
                        <td><?= htmlspecialchars($row->T03_SOURCE_IMAGE_DISTANCE) ?></td>
                        <td><?= htmlspecialchars($row->T03_SOURCE_SKIN_DISTANCE) ?></td>
                        <td><?= htmlspecialchars($row->T03_COLLIMATION_FIELD_SIZE) ?></td>
                        <td><?= htmlspecialchars($row->T03_GRID) ?></td>
                        <td><?= htmlspecialchars($row->T03_DOSE_AREA_PRODUCT) ?></td>
                        <td><?= htmlspecialchars($row->T03_EXPOSURE_INDEX) ?></td>
                        <td>
                            <?php if ($ENABLE_DELETE): ?>
                            <button class="btn btn-sm btn-danger delete-btn" 
                                    data-id="<?= $row->T03_ID_DOS_PESAKIT ?>"
                                    data-name="<?= htmlspecialchars($patient->T01_NAMA_PESAKIT ?? 'N/A') ?>">
                                <i class="ti ti-trash"></i> Padam
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Delete confirmation
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        if(confirm(`Padam rekod dosimetri untuk ${name}?`)) {
            window.location = '<?= module_url("dosimetri/delete/") ?>' + id;
        }
    });
});
</script>