<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Pesanan Baru</h4>
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

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="addPesananTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pdf-upload-tab" data-bs-toggle="tab" data-bs-target="#pdf-upload" type="button" role="tab">
                            <i class="ti ti-file-upload"></i> Upload PDF
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="manual-entry-tab" data-bs-toggle="tab" data-bs-target="#manual-entry" type="button" role="tab">
                            <i class="ti ti-edit"></i> Manual Entry
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="addPesananTabContent">
                    <!-- PDF Upload Tab -->
                    <div class="tab-pane fade show active" id="pdf-upload" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="<?= module_url('pesanan/upload_pdf') ?>" method="post" enctype="multipart/form-data" id="pdfUploadForm">
                                    <div class="mb-3">
                                        <label for="pdf_file" class="form-label">
                                            <i class="ti ti-file-pdf"></i> Pilih Fail PDF Pesanan
                                        </label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="pdf_file" 
                                               name="pdf_file" 
                                               accept=".pdf" 
                                               required>
                                        <div class="form-text">
                                            Saiz maksimum: 10MB. Hanya fail PDF dibenarkan.
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                                            <i class="ti ti-upload"></i> Upload & Extract Data
                                        </button>
                                        <a href="<?= module_url('pesanan/listpesanan') ?>" class="btn btn-secondary">
                                            <i class="ti ti-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="ti ti-info-circle"></i> Maklumat Upload PDF
                                        </h6>
                                        <ul class="list-unstyled small">
                                            <li>• Sistem akan mengekstrak data secara automatik</li>
                                            <li>• Data yang diekstrak:
                                                <ul>
                                                    <li>- Nama Pembekal</li>
                                                    <li>- Nombor Telefon</li>
                                                    <li>- Nombor Pesanan</li>
                                                    <li>- Tarikh Pesanan</li>
                                                    <li>- Tarikh Tamat</li>
                                                    <li>- Jumlah Harga</li>
                                                </ul>
                                            </li>
                                            <li>• Anda boleh mengedit data selepas upload</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Entry Tab -->
                    <div class="tab-pane fade" id="manual-entry" role="tabpanel">
                        <form action="<?= module_url('pesanan/add_manual') ?>" method="post" id="manualEntryForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="T06_NAMA_PEMBEKAL" class="form-label">
                                            Nama Pembekal <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="T06_NAMA_PEMBEKAL" 
                                               name="T06_NAMA_PEMBEKAL" 
                                               required
                                               placeholder="Masukkan nama pembekal">
                                    </div>

                                    <div class="mb-3">
                                        <label for="T06_NOMBOR_TELEFON" class="form-label">
                                            Nombor Telefon
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="T06_NOMBOR_TELEFON" 
                                               name="T06_NOMBOR_TELEFON"
                                               placeholder="Contoh: 03-1234567">
                                    </div>

                                    <div class="mb-3">
                                        <label for="T06_NOMBOR_PESANAN" class="form-label"></label>