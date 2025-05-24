<div class="card">
    <div class="card-header">
        <h3 class="card-title">Butiran Pesakit</h3>
        <a href="<?php echo site_url('manage/doctor'); ?>" class="btn btn-secondary float-end">Kembali ke senarai nama</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>No Rujukan:</th>
                        <td><?php echo $patient->T01_NO_RUJUKAN; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Pesakit:</th>
                        <td><?php echo $patient->T01_NAMA_PESAKIT; ?></td>
                    </tr>
                    <tr>
                        <th>Jantina:</th>
                        <td><?php echo $patient->T01_JANTINA; ?></td>
                    </tr>
                    <tr>
                        <th>Kategori:</th>
                        <td><?php echo $patient->T01_KATEGORI; ?></td>
                    </tr>
                    <tr>
                        <th>Bahagian Utama:</th>
                        <td><?php echo $patient->T01_BAHAGIAN_UTAMA; ?></td>
                    </tr>
                    <tr>
                        <th>Sub Bahagian:</th>
                        <td><?php echo $patient->T01_SUB_BAHAGIAN; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Komen Doktor</h4>
                    </div>
                    <div class="card-body">
                    <?php echo form_open(site_url('manage/doctor/add_comment')); ?>
    <input type="hidden" name="patient_id" value="<?= $patient->T01_ID_PESAKIT ?>">
    <textarea name="doctor_comment" class="form-control" rows="5">
        <?= htmlspecialchars($patient->T01_DOCTOR_COMMENT ?? '') ?>
    </textarea>
    <button type="submit" class="btn btn-primary">Simpan Komen</button>
<?php echo form_close(); ?>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>