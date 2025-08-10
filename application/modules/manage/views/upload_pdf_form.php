<h2>Upload PDF File</h2>

<?php echo form_open_multipart(module_url('pdf/upload')); ?>

    <p><input type="file" name="pdf_file" required></p>
    <p><button type="submit">Upload & Extract</button></p>

<?php echo form_close(); ?>

<?php if (!empty($info)): ?>
    <h3>Extracted Info:</h3>
    <ul>
        <li><strong>Invoice:</strong> <?= $info['invoice'] ?? 'N/A' ?></li>
        <li><strong>Date:</strong> <?= $info['date'] ?? 'N/A' ?></li>
        <li><strong>Customer:</strong> <?= $info['customer'] ?? 'N/A' ?></li>
    </ul>
<?php endif; ?>
