<div class="card">
    <div class="card-header">
        <h5>Kemaskini Dosimetri untuk <?= $month ?> <?= $year ?></h5>
    </div>
    <div class="card-body">
        <!-- Debug Info -->
        <div class="alert alert-info">
            <strong>Debug Information:</strong><br>
            Staff: <?= $dosimetri->T04_NAMA_PENGGUNA ?><br>
            Current SETARA1: <?= $dosimetri->T04_DOS_SETARA1 ?><br>
            Current SETARA2: <?= $dosimetri->T04_DOS_SETARA2 ?><br>
        </div>

        <form action="<?= module_url('dosimetristaf/update_month') ?>" method="post">
            <input type="hidden" name="id" value="<?= $dosimetri->T04_ID_DOS_STAF ?>">
            <input type="hidden" name="month" value="<?= isset($month_number) ? $month_number : '01' ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            
            <div class="mb-3">
                <label class="form-label">Dos Setara 1 (Terkini: <?= $dosimetri->T04_DOS_SETARA1 ?>)</label>
                <input type="number" step="0.01" class="form-control" name="dos_setara1" 
                       value="<?= $dosimetri->T04_DOS_SETARA1 ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Dos Setara 2 (Terkini: <?= $dosimetri->T04_DOS_SETARA2 ?>)</label>
                <input type="number" step="0.01" class="form-control" name="dos_setara2" 
                       value="<?= $dosimetri->T04_DOS_SETARA2 ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= module_url('dosimetristaf/listdos_staff') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>