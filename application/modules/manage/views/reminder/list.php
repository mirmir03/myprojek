<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Pesanan - Reminder</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>

<style>
.reminder-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 0;
    margin-bottom: 30px;
}

.stats-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
}

.stats-label {
    color: #666;
    font-size: 0.9rem;
    margin-top: 5px;
}

.table-container {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-overdue {
    background-color: #f8d7da;
    color: #721c24;
}

.status-due-soon {
    background-color: #fff3cd;
    color: #856404;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.search-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.btn-custom {
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}
</style>

<div class="reminder-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">
                    <i class="fas fa-bell mr-3"></i>
                    Senarai Pesanan - Reminder
                </h1>
                <p class="mb-0 mt-2 opacity-75">Pengurusan dan pemantauan pesanan</p>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= module_url('reminder/upload') ?>" class="btn btn-light btn-custom">
                    <i class="fas fa-upload mr-2"></i>
                    Upload PDF Pesanan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4" id="stats-container">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-number text-primary" id="total-count">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stats-label">Jumlah Pesanan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-number text-warning" id="due-month-count">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stats-label">Tamat Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-number text-danger" id="overdue-count">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stats-label">Sudah Lewat</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-number text-success" id="recent-count">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="stats-label">Baru Minggu Ini</div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-container">
        <form method="GET" action="<?= current_url() ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Carian:</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Nama pembekal, nombor pesanan, kod pembekal..."
                               value="<?= $this->input->get('search') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="all" <?= ($this->input->get('status') == 'all') ? 'selected' : '' ?>>Semua</option>
                            <option value="overdue" <?= ($this->input->get('status') == 'overdue') ? 'selected' : '' ?>>Sudah Lewat</option>
                            <option value="due_soon" <?= ($this->input->get('status') == 'due_soon') ? 'selected' : '' ?>>Tamat Tidak Lama</option>
                            <option value="future" <?= ($this->input->get('status') == 'future') ? 'selected' : '' ?>>Masa Hadapan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year">Tahun:</label>
                        <select class="form-control" id="year" name="year">
                            <?php 
                            $current_year = date('Y');
                            for($i = $current_year; $i >= $current_year - 5; $i--): 
                            ?>
                                <option value="<?= $i ?>" <?= ($this->input->get('year') == $i || (!$this->input->get('year') && $i == $current_year)) ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary-custom btn-custom">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Flash Messages -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Data Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reminderTable">
                <thead>
                    <tr>
                        <th width="5%">Bil</th>
                        <th width="15%">ID Notifikasi</th>
                        <th width="20%">Nama Pembekal</th>
                        <th width="12%">Nombor Pesanan</th>
                        <th width="10%">Tarikh Pesanan</th>
                        <th width="10%">Tamat Pesanan</th>
                        <th width="10%">Jumlah (RM)</th>
                        <th width="8%">Status</th>
                        <th width="10%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data->num_rows() > 0): ?>
                        <?php $no = 1; foreach($data->result() as $row): ?>
                            <?php
                            // Determine status based on due date
                            $status_class = 'status-active';
                            $status_text = 'Aktif';
                            
                            if(!empty($row->T06_TAMAT_PESANAN)) {
                                $due_date = DateTime::createFromFormat('d-M-y', $row->T06_TAMAT_PESANAN);
                                $today = new DateTime();
                                $next_week = new DateTime('+7 days');
                                
                                if($due_date && $due_date < $today) {
                                    $status_class = 'status-overdue';
                                    $status_text = 'Lewat';
                                } elseif($due_date && $due_date <= $next_week) {
                                    $status_class = 'status-due-soon';
                                    $status_text = 'Tamat Tidak Lama';
                                }
                            }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row->T06_ID_NOTIFIKASI) ?></strong>
                                    <?php if(!empty($row->T06_KOD_PEMBEKAL)): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($row->T06_KOD_PEMBEKAL) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row->T06_NAMA_PEMBEKAL) ?></strong>
                                    <?php if(!empty($row->T06_NOMBOR_TELEFON)): ?>
                                        <br><small class="text-muted">
                                            <i class="fas fa-phone fa-sm"></i> <?= htmlspecialchars($row->T06_NOMBOR_TELEFON) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row->T06_NOMBOR_PESANAN) ?>
                                    <?php if(!empty($row->T06_NO_ASET)): ?>
                                        <br><small class="text-muted">Aset: <?= htmlspecialchars($row->T06_NO_ASET) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row->T06_TARIKH_PESANAN) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row->T06_TAMAT_PESANAN) ?></strong>
                                </td>
                                <td class="text-right">
                                    <strong>RM <?= number_format((float)$row->T06_JUMLAH_HARGA, 2) ?></strong>
                                </td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= $status_text ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= module_url('reminder/edit/' . $row->T06_ID_NOTIFIKASI) ?>" 
                                           class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('<?= $row->T06_ID_NOTIFIKASI ?>', '<?= htmlspecialchars($row->T06_NAMA_PEMBEKAL) ?>')" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                onclick="viewDetails('<?= $row->T06_ID_NOTIFIKASI ?>')" 
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>Tiada Data Dijumpai</h5>
                                    <p>Belum ada pesanan yang direkodkan atau tiada data yang sepadan dengan carian anda.</p>
                                    <a href="<?= module_url('reminder/upload') ?>" class="btn btn-primary-custom btn-custom">
                                        <i class="fas fa-plus mr-2"></i>Upload PDF Pesanan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (if needed) -->
    <?php if($data->num_rows() > 0): ?>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="text-muted">
                    Menunjukkan <?= $data->num_rows() ?> rekod
                </p>
            </div>
            <div class="col-md-6 text-right">
                <!-- Add pagination here if implemented -->
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>Detail Pesanan
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-content">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Memuatkan...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Pengesahan Hapus
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Adakah anda pasti untuk menghapuskan pesanan ini?</p>
                <div class="alert alert-warning">
                    <strong>Pembekal:</strong> <span id="delete-supplier-name"></span><br>
                    <strong>ID:</strong> <span id="delete-reminder-id"></span>
                </div>
                <p class="text-muted">Tindakan ini tidak boleh dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                    <i class="fas fa-trash mr-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load statistics
    loadStats();
    
    // Initialize DataTable if needed
    if ($('#reminderTable tbody tr').length > 10) {
        $('#reminderTable').DataTable({
            "pageLength": 25,
            "order": [[ 5, "asc" ]], // Sort by due date
            "columnDefs": [
                { "orderable": false, "targets": [8] } // Disable sorting for action column
            ]
        });
    }
});

// Load statistics via AJAX
function loadStats() {
    $.ajax({
        url: '<?= module_url('reminder/get_stats') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#total-count').html(response.data.total);
                $('#due-month-count').html(response.data.due_this_month);
                $('#overdue-count').html(response.data.overdue);
                $('#recent-count').html(response.data.recent);
            }
        },
        error: function() {
            $('#stats-container .stats-number').html('<i class="fas fa-exclamation-triangle text-warning"></i>');
        }
    });
}

// View details function
function viewDetails(id) {
    $('#detailModal').modal('show');
    
    // Load reminder details via AJAX
    $.ajax({
        url: '<?= base_url() ?>reminder/get_reminder_details/' + id,
        type: 'GET',
        success: function(data) {
            $('#modal-content').html(data);
        },
        error: function() {
            $('#modal-content').html('<div class="alert alert-danger">Ralat memuatkan data</div>');
        }
    });
}

// Delete confirmation
function confirmDelete(id, supplierName) {
    $('#delete-reminder-id').text(id);
    $('#delete-supplier-name').text(supplierName);
    $('#deleteModal').modal('show');
    
    $('#confirm-delete-btn').off('click').on('click', function() {
        window.location.href = '<?= module_url('reminder/delete') ?>/' + id;
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>

</body>
</html>