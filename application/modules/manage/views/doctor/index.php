<div class="card">
    <div class="card-header">
        <h3 class="card-title">Senarai Pesakit X-ray Berdaftar</h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="input-search" class="form-control" placeholder="Cari...">
            </div>
        </div>
        
        <table class="table table-hover table-striped" id="doctor-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Rujukan</th>
                    <th>Nama Pesakit</th>
                    <th>Jantina</th>
                    <th>Kategori</th>
                    <th>Bahagian Utama</th>
                    <th>Sub Bahagian</th>
                    <th>Status Komen</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; foreach ($data->result() as $patient): ?>
                <tr>
                    <td><?php echo ++$i; ?></td>
                    <td><?php echo $patient->T01_NO_RUJUKAN; ?></td>
                    <td><?php echo $patient->T01_NAMA_PESAKIT; ?></td>
                    <td><?php echo $patient->T01_JANTINA; ?></td>
                    <td><?php echo $patient->T01_KATEGORI; ?></td>
                    <td><?php echo $patient->T01_BAHAGIAN_UTAMA; ?></td>
                    <td><?php echo $patient->T01_SUB_BAHAGIAN; ?></td>
                    <td>
                        <?php if (empty($patient->T01_DOCTOR_COMMENT)): ?>
                            <span class="badge bg-warning">Tiada Komen</span>
                        <?php else: ?>
                            <span class="badge bg-success">Ada Komen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                    <!-- To this: -->
                    <a href="<?php echo module_url('doctor/view_patient/' . $patient->T01_ID_PESAKIT); ?>" class="btn btn-primary btn-sm">Lihat & Komen</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('input-search').addEventListener('keyup', function () {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#doctor-table tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchText));
        row.style.display = match ? '' : 'none';
    });
});
</script>