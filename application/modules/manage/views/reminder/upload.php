<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF Pesanan - Reminder</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<style>
.upload-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 0;
    margin-bottom: 30px;
}

.upload-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.upload-area {
    border: 3px dashed #dee2e6;
    border-radius: 10px;
    padding: 60px 20px;
    text-align: center;
    transition: all 0.3s ease;
    background: #fafafa;
}

.upload-area:hover {
    border-color: #667eea;
    background: #f8f9ff;
}

.upload-area.drag-over {
    border-color: #667eea;
    background: #f0f4ff;
    transform: scale(1.02);
}

.upload-icon {
    font-size: 4rem;
    color: #667eea;
    margin-bottom: 20px;
}

.file-input {
    display: none;
}

.btn-upload {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-upload:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-back {
    background: #6c757d;
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-back:hover {
    color: white;
    background: #5a6268;
    transform: translateY(-2px);
}

.file-info {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    display: none;
}

.progress-container {
    margin-top: 20px;
    display: none;
}

.requirements {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.requirements h5 {
    color: #856404;
    margin-bottom: 15px;
}

.requirements ul {
    color: #856404;
    margin-bottom: 0;
}
</style>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light">
    <div class="container">
        <ol class="breadcrumb bg-light mb-0 py-3">
            <li class="breadcrumb-item">
                <a href="<?= base_url() ?>">Utama</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= module_url('reminder/list') ?>">Senarai Pesanan</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Upload PDF</li>
        </ol>
    </div>
</nav>

<!-- Header -->
<div class="upload-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-0">
                    <i class="fas fa-upload mr-3"></i>
                    Upload PDF Pesanan
                </h1>
                <p class="mb-0 mt-2 opacity-75">Muat naik fail PDF pesanan untuk diproses secara automatik</p>
            </div>
            <div class="col-md-4 text-right">
                <a href="<?= module_url('reminder/list') ?>" class="btn btn-light btn-back">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Senarai
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
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

    <!-- Requirements -->
    <div class="requirements">
        <h5><i class="fas fa-info-circle mr-2"></i>Keperluan Fail PDF</h5>
        <ul class="mb-0">
            <li>Fail mestilah dalam format PDF sahaja</li>
            <li>Saiz maksimum: 5MB</li>
            <li>PDF mestilah mengandungi maklumat pesanan yang lengkap</li>
            <li>Pastikan PDF boleh dibaca dan tidak dilindungi kata laluan</li>
            <li>Sistem akan mengekstrak maklumat seperti nombor pesanan, nama pembekal, tarikh, dan jumlah harga</li>
        </ul>
    </div>

    <!-- Upload Form -->
    <div class="upload-container">
        <form id="uploadForm" action="<?= module_url('reminder/process_upload') ?>" method="post" enctype="multipart/form-data">
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h4 class="mb-3">Seret dan lepas fail PDF di sini</h4>
                <p class="text-muted mb-4">atau klik butang di bawah untuk memilih fail</p>
                
                <input type="file" id="pdfFile" name="pdf_file" class="file-input" accept=".pdf" required>
                
                <button type="button" class="btn btn-upload" onclick="document.getElementById('pdfFile').click()">
                    <i class="fas fa-folder-open mr-2"></i>
                    Pilih Fail PDF
                </button>
            </div>

            <!-- File Info -->
            <div class="file-info" id="fileInfo">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">
                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                            <span id="fileName"></span>
                        </h6>
                        <small class="text-muted">
                            Saiz: <span id="fileSize"></span> | 
                            Jenis: <span id="fileType"></span>
                        </small>
                    </div>
                    <div class="col-md-4 text-right">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearFile()">
                            <i class="fas fa-times mr-1"></i>
                            Buang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container" id="progressContainer">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" id="uploadProgress">
                        0%
                    </div>
                </div>
                <small class="text-muted mt-2">Sedang memproses fail PDF...</small>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-upload btn-lg" id="submitBtn" disabled>
                    <i class="fas fa-upload mr-2"></i>
                    Muat Naik dan Proses
                </button>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle mr-2"></i>
                        Bagaimana ia berfungsi?
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li>Pilih fail PDF pesanan anda</li>
                        <li>Sistem akan membaca dan mengekstrak maklumat secara automatik</li>
                        <li>Data yang diekstrak akan disimpan ke dalam pangkalan data</li>
                        <li>Anda akan diarahkan ke senarai pesanan selepas berjaya</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Pastikan PDF mempunyai kualiti yang baik</li>
                        <li>Elakkan PDF yang telah diimbas dengan kualiti rendah</li>
                        <li>Semak semula data selepas upload jika perlu</li>
                        <li>Hubungi pentadbir jika menghadapi masalah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const uploadArea = $('#uploadArea');
    const fileInput = $('#pdfFile');
    const fileInfo = $('#fileInfo');
    const submitBtn = $('#submitBtn');
    const uploadForm = $('#uploadForm');
    const progressContainer = $('#progressContainer');

    // Drag and drop functionality
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        uploadArea.addClass('drag-over');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        uploadArea.removeClass('drag-over');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        uploadArea.removeClass('drag-over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type === 'application/pdf') {
                fileInput[0].files = files;
                displayFileInfo(file);
            } else {
                alert('Sila pilih fail PDF sahaja.');
            }
        }
    });

    // File input change
    fileInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            displayFileInfo(file);
        }
    });

    // Display file information
    function displayFileInfo(file) {
        const fileSize = formatFileSize(file.size);
        const fileName = file.name;
        const fileType = file.type;

        $('#fileName').text(fileName);
        $('#fileSize').text(fileSize);
        $('#fileType').text(fileType);

        fileInfo.slideDown();
        submitBtn.prop('disabled', false);

        // Validate file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('Saiz fail melebihi had maksimum 5MB. Sila pilih fail yang lebih kecil.');
            clearFile();
            return;
        }
    }

    // Clear file
    window.clearFile = function() {
        fileInput.val('');
        fileInfo.slideUp();
        submitBtn.prop('disabled', true);
        uploadArea.removeClass('drag-over');
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission with progress
    uploadForm.on('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput[0].files.length) {
            alert('Sila pilih fail PDF terlebih dahulu.');
            return;
        }

        // Show progress
        progressContainer.slideDown();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Sedang Memproses...');

        // Simulate progress (since we can't track actual upload progress easily in PHP)
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            
            $('#uploadProgress').css('width', progress + '%').text(Math.round(progress) + '%');
        }, 500);

        // Create form data
        const formData = new FormData(this);

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                clearInterval(progressInterval);
                $('#uploadProgress').css('width', '100%').text('100%');
                
                setTimeout(function() {
                    // Redirect will be handled by the controller
                    window.location.href = '<?= module_url("reminder/list") ?>';
                }, 1000);
            },
            error: function() {
                clearInterval(progressInterval);
                alert('Ralat berlaku semasa memuat naik fail. Sila cuba lagi.');
                submitBtn.prop('disabled', false).html('<i class="fas fa-upload mr-2"></i>Muat Naik dan Proses');
                progressContainer.slideUp();
            }
        });
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

</body>
</html>