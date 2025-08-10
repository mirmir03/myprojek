<style>
.comment-badge {
    cursor: pointer;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5rem;
}

.comment-modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.comment-modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 0;
    border-radius: 0.5rem;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.2s ease-in-out;
}

.comment-modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0.5rem 0.5rem 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-modal-body {
    padding: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
}

.comment-close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    background: none;
    padding: 0;
}

.comment-close:hover,
.comment-close:focus {
    color: #000;
}

.comment-text {
    font-size: 14px;
    line-height: 1.6;
    color: #333;
    white-space: pre-wrap;
}

.patient-info {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
    border-left: 4px solid #0d6efd;
}

.patient-info h6 {
    margin-bottom: 0.5rem;
    color: #0d6efd;
    font-weight: 600;
}

.patient-info p {
    margin: 0.25rem 0;
    font-size: 14px;
    color: #6c757d;
}

.btn-delete-comment {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Main Content Starts -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Senarai Pesakit X-ray Berdaftar</h3>
        <?php echo "Bilangan Data: " . $data->num_rows(); ?>
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

        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="input-search" class="form-control" placeholder="Cari...">
            </div>
        </div>
        
        <div class="table-responsive">         
            <table class="table table-hover table-striped" id="doctor-table">
                <thead class="table-light">
                    <tr>
                        <th width="80">No</th>
                        <th>No Rujukan</th>
                        <th>Nama Pesakit</th>
                        <th>Jantina</th>
                        <th>Kategori</th>
                        <th>Bahagian Utama</th>
                        <th>Sub Bahagian</th>
                        <th width="120">Status Komen</th>
                        <th width="150">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $data_array = $data->result();
                    usort($data_array, function($a, $b) {
                        $dateA = !empty($a->TARIKH) ? new DateTime($a->TARIKH) : new DateTime('@0');
                        $dateB = !empty($b->TARIKH) ? new DateTime($b->TARIKH) : new DateTime('@0');
                        return $dateB <=> $dateA;
                    });

                    $i = count($data_array);
                    foreach ($data_array as $patient): ?>
                    <tr>
                        <td><?= $i-- ?></td>
                        <td><?= htmlspecialchars($patient->T01_NO_RUJUKAN) ?></td>
                        <td><?= htmlspecialchars($patient->T01_NAMA_PESAKIT) ?></td>
                        <td><?= htmlspecialchars($patient->T01_JANTINA) ?></td>
                        <td><?= htmlspecialchars($patient->T01_KATEGORI) ?></td>
                        <td><?= htmlspecialchars($patient->T01_BAHAGIAN_UTAMA) ?></td>
                        <td><?= htmlspecialchars($patient->T01_SUB_BAHAGIAN) ?></td>
                        <td>
                            <?php if (empty($patient->T01_DOCTOR_COMMENT)): ?>
                                <span class="badge bg-warning">
                                    <i class="ti ti-alert-circle"></i> Tiada Komen
                                </span>
                            <?php else: ?>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success comment-badge me-2"
                                          onclick="showCommentModal(
                                              '<?= htmlspecialchars($patient->T01_NAMA_PESAKIT, ENT_QUOTES) ?>',
                                              '<?= htmlspecialchars($patient->T01_NO_RUJUKAN, ENT_QUOTES) ?>',
                                              '<?= htmlspecialchars($patient->T01_DOCTOR_COMMENT, ENT_QUOTES) ?>')">
                                        <i class="ti ti-message-circle"></i> Ada Komen
                                    </span>
                                    <button class="btn btn-danger btn-sm btn-delete-comment"
                                            data-patient-id="<?= $patient->T01_ID_PESAKIT ?>"
                                            title="Padam Komen">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= module_url('doctor/view_patient/' . $patient->T01_ID_PESAKIT); ?>" 
                               class="btn btn-primary btn-sm">
                               <i class="ti ti-eye"></i> Lihat & Komen
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="comment-modal">
    <div class="comment-modal-content">
        <div class="comment-modal-header">
            <h5 class="modal-title">
                <i class="ti ti-message-circle me-2"></i>Komen Doktor
            </h5>
            <button type="button" class="comment-close" onclick="closeCommentModal()">&times;</button>
        </div>
        <div class="comment-modal-body">
            <div class="patient-info">
                <h6>Maklumat Pesakit</h6>
                <p><strong>Nama:</strong> <span id="modalPatientName"></span></p>
                <p><strong>No Rujukan:</strong> <span id="modalPatientRef"></span></p>
            </div>
            <div>
                <h6 class="mb-3"><i class="ti ti-message-dots me-2"></i>Komen:</h6>
                <div class="comment-text" id="modalCommentText"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-delete-comment', function() {
    const patientId = $(this).data('patient-id');
    if (confirm('Adakah anda pasti ingin memadam komen ini?')) {
        const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url: '<?= site_url("manage/doctor/delete_comment"); ?>',
            method: 'POST',
            dataType: 'json',
            data: { patient_id: patientId, [csrfName]: csrfHash },
            success: function(response) {
                if (response.status === 'success') {
                    alert('Komen berjaya dipadam');
                    location.reload();
                } else {
                    alert('Gagal memadam komen: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                alert('Ralat teknikal: ' + xhr.status + ' - ' + xhr.statusText);
            }
        });
    }
});

$(document).ready(function() {
    $('#doctor-table').DataTable({
        pageLength: 10,
        ordering: false,
        searching: false,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            lengthMenu: "Papar _MENU_ rekod",
            info: "Paparan _START_ hingga _END_ daripada _TOTAL_ rekod",
            infoEmpty: "Tiada rekod",
            zeroRecords: "Tiada padanan ditemui",
            paginate: {
                first: "Pertama", last: "Terakhir", next: "Seterusnya", previous: "Sebelumnya"
            },
            emptyTable: "Tiada data tersedia dalam jadual",
            loadingRecords: "Memuatkan...", processing: "Memproses..."
        }
    });

    $('#input-search').on('keyup', function() {
        const searchText = this.value.toLowerCase();
        $('#doctor-table tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchText));
        });
    });

    window.onclick = function(event) {
        if (event.target == document.getElementById('commentModal')) closeCommentModal();
    };

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeCommentModal();
    });
});

function showCommentModal(patientName, patientRef, comment) {
    $('#modalPatientName').text(patientName);
    $('#modalPatientRef').text(patientRef);
    $('#modalCommentText').text(comment);
    $('#commentModal').show();
    setTimeout(() => {
        $('.comment-modal-content').css({ transform: 'scale(1)', opacity: '1' });
    }, 10);
}

function closeCommentModal() {
    const content = $('.comment-modal-content');
    content.css({ transform: 'scale(0.8)', opacity: '0' });
    setTimeout(() => $('#commentModal').hide(), 150);
}
</script>
