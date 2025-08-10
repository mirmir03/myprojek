<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Senarai Pesanan</h4>
                <a href="<?= module_url('pesanan/form_add') ?>" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Tambah Pesanan
                </a>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pesananTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nombor Pesanan</th>
                                <th width="20%">Nama Pembekal</th>
                                <th width="12%">Nombor Telefon</th>
                                <th width="12%">Tarikh Pesanan</th>
                                <th width="12%">Tamat Pesanan</th>
                                <th width="10%">Jumlah (RM)</th>
                                <th width="8%">PDF</th>
                                <th width="6%">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($data as $pesanan): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($pesanan->T06_NOMBOR_PESANAN) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($pesanan->T06_NAMA_PEMBEKAL) ?></td>
                                        <td><?= htmlspecialchars($pesanan->T06_NOMBOR_TELEFON) ?></td>
                                        <td>
                                            <?php 
                                            $date = new DateTime($pesanan->T06_TARIKH_PESANAN);
                                            echo $date->format('d/m/Y');
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $due_date = new DateTime($pesanan->T06_TAMAT_PESANAN);
                                            $today = new DateTime();
                                            $diff = $today->diff($due_date);
                                            $days_remaining = $due_date >= $today ? $diff->days : -$diff->days;
                                            
                                            $badge_class = 'badge ';
                                            if ($days_remaining < 0) {
                                                $badge_class .= 'bg-danger';
                                                $status_text = 'Overdue';
                                            } elseif ($days_remaining <= 3) {
                                                $badge_class .= 'bg-warning';
                                                $status_text = $days_remaining . ' days left';
                                            } else {
                                                $badge_class .= 'bg-success';
                                                $status_text = $days_remaining . ' days left';
                                            }
                                            ?>
                                            <div>
                                                <?= $due_date->format('d/m/Y') ?>
                                                <br>
                                                <span class="<?= $badge_class ?>"><?= $status_text ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>RM <?= number_format($pesanan->T06_JUMLAH_HARGA, 2) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($pesanan->T06_PDF_FILE)): ?>
                                                <a href="<?= base_url('uploads/pesanan/' . $pesanan->T06_PDF_FILE) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View PDF">
                                                    <i class="ti ti-file-text"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= module_url('pesanan/form_edit/' . $pesanan->T06_ID_NOTIFIKASI) ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete(<?= $pesanan->T06_ID_NOTIFIKASI ?>)" 
                                                        title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tiada data pesanan dijumpai</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Adakah anda pasti untuk memadam pesanan ini? Tindakan ini tidak boleh dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Ya, Padam</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const deleteUrl = '<?= module_url('pesanan/delete/') ?>' + id;
    document.getElementById('confirmDeleteBtn').setAttribute('href', deleteUrl);
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Initialize DataTable if available
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#pesananTable').DataTable({
            "pageLength": 25,
            "order": [[ 4, "desc" ]], // Order by Tarikh Pesanan descending
            "columnDefs": [
                { "orderable": false, "targets": [7, 8] } // Disable ordering for PDF and Action columns
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/ms.json"
            }
        });
    }
});
</script>