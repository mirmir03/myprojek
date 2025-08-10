<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="notes-container">
    <div class="notes-header">
        <h3>Nota Analisis Reject</h3>
        <div class="period-info">
            <?php 
            $period_text = [];
            if (!empty($selected_month)) {
                $months = ['', 'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 
                         'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
                $period_text[] = $months[$selected_month];
            }
            if (!empty($selected_year)) {
                $period_text[] = $selected_year;
            }
            echo !empty($period_text) ? implode(' ', $period_text) : 'Semua Tempoh';
            ?>
        </div>
    </div>

    <!-- Current Note Section -->
    <div class="current-note-section">
        <div class="note-form">
            <textarea id="noteText" placeholder="Masukkan nota analisis untuk tempoh ini..."><?= isset($current_note->T06_NOTE_TEXT) ? $current_note->T06_NOTE_TEXT : '' ?></textarea>
            <div class="note-actions">
                <button type="button" class="btn btn-primary" onclick="saveNote()">
                    <?= isset($current_note) ? 'Kemaskini Nota' : 'Simpan Nota' ?>
                </button>
                <?php if (isset($current_note)): ?>
                    <button type="button" class="btn btn-danger" onclick="deleteNote()">Padam Nota</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Notes Section -->
<div class="recent-notes-section">
    <h4>Nota Terkini</h4>
    <?php if (!empty($all_notes)): ?>
        <div class="notes-list">
            <?php foreach ($all_notes as $note): ?>
                <div class="note-item">
                    <div class="note-period">
                        <strong><?= $note->T06_MONTH_SELECTED ?> <?= $note->T06_YEAR_SELECTED ?></strong>
                        <?php if (!empty($note->T06_UPDATED_AT)): ?>
                            <small><?= date('d/m/Y H:i', strtotime($note->T06_UPDATED_AT)) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="note-content"><?= nl2br($note->T06_NOTE_TEXT ?? '') ?></div>
                    <div class="note-author">Oleh: <?= $note->T06_CREATED_BY ?? 'System' ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-notes">Tiada nota terkini</div>
    <?php endif; ?>
</div>

<script>
    function saveNote() {
        const noteText = document.getElementById('noteText').value.trim();
        const month = '<?= $selected_month ?? "" ?>';
        const year = '<?= $selected_year ?? "" ?>';
        
        if (noteText === '') {
            alert('Sila masukkan kandungan nota');
            return;
        }
        
        fetch('<?= module_url("reject/save_note") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `month=${month}&year=${year}&note_text=${encodeURIComponent(noteText)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Nota berjaya disimpan');
                location.reload(); // Refresh to show updated note
            } else {
                alert('Gagal menyimpan nota: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat berlaku semasa menyimpan nota');
        });
    }
    
    function deleteNote() {
        if (!confirm('Adakah anda pasti ingin memadam nota ini?')) {
            return;
        }
        
        const month = '<?= $selected_month ?? "" ?>';
        const year = '<?= $selected_year ?? "" ?>';
        
        fetch('<?= module_url("reject/delete_note_by_period") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `month=${month}&year=${year}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Nota berjaya dipadam');
                location.reload();
            } else {
                alert('Gagal memadam nota: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat berlaku semasa memadam nota');
        });
    }
</script>

<style>
    .notes-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    
    .notes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .period-info {
        background: #f8f9fa;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .note-form textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
        resize: vertical;
    }
    
    .note-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #007bff;
        color: white;
    }
    
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    
    .recent-notes-section {
        margin-top: 30px;
    }
    
    .notes-list {
        margin-top: 15px;
    }
    
    .note-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
        border-left: 3px solid #007bff;
    }
    
    .note-period {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .note-period small {
        color: #6c757d;
    }
    
    .note-content {
        margin: 10px 0;
        white-space: pre-line;
    }
    
    .note-author {
        font-size: 12px;
        color: #6c757d;
        text-align: right;
    }
    
    .no-notes {
        text-align: center;
        padding: 20px;
        color: #6c757d;
        font-style: italic;
        background: #f8f9fa;
        border-radius: 4px;
    }
</style>