<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Sidenote</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= module_url('reject/graph') ?>">Graf Reject</a></li>
                        <li class="breadcrumb-item"><a href="<?= module_url('sidenote/index') ?>">Sidenotes</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <!-- Main Form -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus"></i> 
                                Tambah Sidenote Baru
                            </h3>
                        </div>