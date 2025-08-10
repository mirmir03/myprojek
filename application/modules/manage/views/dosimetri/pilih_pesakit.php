<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pilih Pesakit</h5>
            <div>
                <a href="<?= module_url('dosimetriPesakit/listdospesakit') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali ke Senarai Dosimetri
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover table-striped" id="pesakit-table">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No. Kad Pengenalan</th>
                        <th>Nama Pesakit</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($pesakit as $row): ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><?= htmlspecialchars($row->T01_NO_RUJUKAN) ?></td>
                        <td><?= htmlspecialchars($row->T01_NAMA_PESAKIT) ?></td>
                        <td>
                            <a href="<?= module_url('dosimetriPesakit/form_add/' . $row->T01_ID_PESAKIT) ?>" class="btn btn-sm btn-primary">
                                <i class="ti ti-plus"></i> Tambah Rekod Dosimetri
                            </a>
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
    $('#pesakit-table').DataTable({
        "pageLength": 10,
        "ordering": false,
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
            }
        }
    });
});
</script>