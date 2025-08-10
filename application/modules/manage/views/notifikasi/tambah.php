<h4>Tambah Notifikasi Pesanan</h4>
<form method="post" action="<?= module_url('notifikasi/simpan') ?>" enctype="multipart/form-data">
    <div class="form-group">
        <label>Nama Pembekal</label>
        <input type="text" name="nama_pembekal" class="form-control" required>
    </div>
    <div class="form-group">
    <label>No Telefon</label>
    <input type="text" name="nombor_telefon" class="form-control"
           pattern="^01[0-9]-\d{7,8}$" placeholder="Contoh: 011-61363591" required>
    <small class="form-text text-muted">Format: 011-12345678</small>
</div>

    <div class="form-group">
        <label>No PO</label>
        <input type="text" name="nombor_pesanan" class="form-control" required>
    </div>
    <div class="form-group">
    <label>Tarikh Pesanan</label>
    <input type="date" name="tarikh_pesanan" id="tarikh_pesanan" class="form-control" required>
</div>
<div class="form-group">
    <label>Tarikh Tamat</label>
    <input type="date" name="tamat_pesanan" id="tamat_pesanan" class="form-control" required>
</div>

    <div class="form-group">
        <label>Jumlah Harga (RM)</label>
        <input type="number" name="jumlah_harga" class="form-control" step="0.01">
    </div>
    <div class="form-group">
    <label>PDF File</label>
    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
</div>

    <button type="submit" class="btn btn-success">Simpan</button>
<a href="<?= module_url('notifikasi/index') ?>" class="btn btn-secondary">Batal</a>

<script>
document.getElementById('tarikh_pesanan').addEventListener('change', function () {
    const pesananDate = new Date(this.value);
    
    if (!isNaN(pesananDate.getTime())) {
        // Add 14 days to Tarikh Pesanan
        pesananDate.setDate(pesananDate.getDate() + 2);

        // Format to yyyy-mm-dd for HTML date input
        const minDate = pesananDate.toISOString().split('T')[0];
        
        // Set as the min for Tarikh Tamat
        const tamatInput = document.getElementById('tamat_pesanan');
        tamatInput.min = minDate;
        tamatInput.value = ''; // Clear previously selected tamat date
    }
});
</script>



</form>
