<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sidenotes - Graf Analisis Reject</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= module_url('reject/graph') ?>">Graf Reject</a></li>
                        <li class="breadcrumb-item active">Sidenotes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter Info Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> 
                        Filter Semasa
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Bulan:</strong> 
                            <?php 
                            if (!empty($selected_month)) {
                                $months = ['', 'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 
                                         'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
                                echo $months[$selected_month];
                            } else {
                                echo "Semua Bulan";
                            }
                            ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Tahun:</strong> 
                            <?= !empty($selected_year) ? $selected_year : "Semua Tahun" ?>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= module_url('reject/graph') ?>?month=<?= $selected_month ?>&year=<?= $selected_year ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-chart-bar"></i> Lihat Graf
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Sidenotes Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note"></i> 
                        Senarai Sidenotes
                    </h3>
                    <div class="card-tools">
                        <a href="<?= module_url('sidenote/form_add') ?>?month=<?= $selected_month ?>&year=<?= $selected_year ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Sidenote
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (!empty($sidenotes)): ?>
                        <div class="row">
                            <?php foreach ($sidenotes as $note): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-sticky-note text-primary"></i>
                                                <?= htmlspecialchars($note->T07_TITLE) ?>
                                            </h5>
                                            <div class="card-tools">
                                                <div class="btn-group">
                                                    <a href="<?= module_url('sidenote/form_edit/' . $note->T07_ID_SIDENOTE) ?>" 
                                                       class="btn btn-tool" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= module_url('sidenote/delete/' . $note->T07_ID_SIDENOTE) ?>" 
                                                       class="btn btn-tool text-danger" 
                                                       title="Padam"
                                                       onclick="return confirm('Adakah anda pasti ingin memadam sidenote ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <?= nl2br(htmlspecialchars(substr($note->T07_CONTENT, 0, 150))) ?>
                                                <?= strlen($note->T07_CONTENT) > 150 ? '...' : '' ?>
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i>
                                                    <?= date('d/m/Y H:i', strtotime($note->T07_CREATED_DATE)) ?>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i>
                                                    <?= htmlspecialchars($note->T07_CREATED_BY) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Tiada Sidenotes</h4>
                            <p class="text-muted">
                                Belum ada sidenotes untuk filter ini. 
                                <a href="<?= module_url('sidenote/form_add') ?>?month=<?= $selected_month ?>&year=<?= $selected_year ?>">
                                    Tambah sidenote pertama
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= count($sidenotes) ?></h3>
                            <p>Total Sidenotes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= !empty($selected_month) ? 'Bulan ' . $selected_month : 'Semua' ?></h3>
                            <p>Filter Bulan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= !empty($selected_year) ? $selected_year : 'Semua' ?></h3>
                            <p>Filter Tahun</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><i class="fas fa-chart-bar"></i></h3>
                            <p>Graf Analisis</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="<?= module_url('reject/graph') ?>?month=<?= $selected_month ?>&year=<?= $selected_year ?>" 
                           class="small-box-footer">
                            Lihat Graf <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.small-box {
    margin-bottom: 20px;
}

.card-text {
    min-height: 60px;
}

.btn-tool {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.alert {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .col-md-6.col-lg-4 {
        margin-bottom: 1rem;
    }
    
    .card-tools {
        margin-top: 0.5rem;
    }
}
</style>