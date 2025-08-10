<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Side Notes Panel -->
<div class="notes-header mb-3">
    <h4 class="page-title">Pengurusan Nota Reject</h4>
    
    <!-- Month-Year Filter -->
    <div class="month-year-filter mb-3 d-flex align-items-center">
    <label for="select_month_year" class="mr-2 mb-0">Pilih Bulan & Tahun:</label>
    <select id="select_month_year" class="form-control" onchange="loadNoteByMonthYear()">

        <option disabled selected>-- Pilih --</option>
        <?php foreach ($notes as $note): ?>
            <?php
                $month = $note->T06_MONTH_SELECTED;
                $year = $note->T06_YEAR_SELECTED;
                $monthName = DateTime::createFromFormat('!m', $month)->format('F');
            ?>
            <option value="<?= $month ?>-<?= $year ?>">
                <?= $monthName . ' ' . $year ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


    <div id="note_result" class="mt-3">
        <!-- Loaded note will appear here -->
    </div>
</div>

<!-- Notes List -->
<div class="notes-container">

    <!-- Header + Add Note Button -->
    <div class="note-item">
        <div class="note-status side-notes d-flex justify-content-between align-items-center">
            <span class="status-text">Side Notes</span>
            <div class="action-icons">
                <button id="toggleNoteInput" class="btn-icon" title="Tambah Nota">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Slide-down Input Form -->
        <div id="noteInputForm" style="display: none; padding: 15px;">
            <input type="month" id="inputMonth" class="form-control mb-2" value="<?= date('Y-m') ?>">
            <textarea id="inputNoteText" class="form-control mb-2" rows="4" placeholder="Tulis nota anda..."></textarea>
            <button id="saveNoteBtn" class="btn btn-success w-100">Simpan</button>
        </div>
    </div>

    <!-- Saved Notes -->
    <?php if (!empty($year_notes)): ?>
        <?php foreach ($year_notes as $note): ?>
            <?php
                $note_text = is_object($note->T06_NOTE_TEXT) ? $note->T06_NOTE_TEXT->load() : $note->T06_NOTE_TEXT;
                $updated_at = !empty($note->T06_UPDATED_AT) ? date('d/m/Y H:i', strtotime($note->T06_UPDATED_AT)) : 'Tarikh tiada';
                $note_id = isset($note->T06_ID) ? $note->T06_ID : (isset($note->T06_NOTE_ID) ? $note->T06_NOTE_ID : null);
            ?>
            
            <div class="note-item">
                <div class="note-status side-notes d-flex justify-content-between align-items-center">
                    <span class="status-text">Side Notes</span>
                    <div class="action-icons">
                        <?php if (!empty($note_id)): ?>
                            <button class="btn-icon edit-note" data-id="<?= $note_id ?>" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn-icon delete-note" data-id="<?= $note_id ?>" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="note-content">
                    <div class="note-text">
                        <?= nl2br(htmlspecialchars($note_text)) ?>
                    </div>
                    <div class="note-meta">
                        <small class="text-muted">Dikemaskini: <?= $updated_at ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
</div>

<style>
    /* Style the dropdown button */
#select_month_year {
    background-color: #808080;
    color: white;
    font-size: 15px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 220px;
    max-width: 100%;
    cursor: pointer;
    box-shadow: none;
}

.month-year-filter {
    margin-bottom: 20px;
    flex-wrap: wrap;
}

@media (max-width: 576px) {
    .month-year-filter {
        flex-direction: column;
        align-items: flex-start;
    }

    #select_month_year {
        width: 100%;
        margin-top: 8px;
    }
}


/* Add a custom dropdown arrow */
#select_month_year::after {
    content: "▼"; /* Custom dropdown arrow */
    font-size: 14px;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: white; /* Arrow color */
}

    .note-item {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid #e0e0e0;
    }

    .note-status {
        background: #28a745;
        color: white;
        padding: 12px 15px;
        font-weight: 500;
        font-size: 14px;
    }

    .note-status.side-notes {
        background: #28a745;
    }

    .status-text {
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    .action-icons {
        display: flex;
        gap: 5px;
    }

    .btn-icon {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-icon:hover {
        background: rgba(255,255,255,0.3);
    }

    .note-content {
        padding: 15px;
    }

    .note-text {
        color: #333;
        line-height: 1.5;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .note-meta {
        border-top: 1px solid #f0f0f0;
        padding-top: 8px;
        margin-top: 10px;
    }

    .empty-state {
        padding: 40px 20px;
        color: #999;
        font-style: italic;
    }

    #noteInputForm {
        animation: slideDown 0.3s ease-out;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 300px;
        }
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .notes-header {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .page-title {
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
    }
</style>

<script>
$(document).ready(function () {
    // Toggle input form
    $('#toggleNoteInput').click(function () {
        $('#noteInputForm').slideToggle();
    });

    // Save note
    $('#saveNoteBtn').click(function () {
        const monthYear = $('#inputMonth').val();
        const noteText = $('#inputNoteText').val().trim();

        if (!monthYear || !noteText) {
            alert('Sila isi semua ruangan');
            return;
        }

        const [year, month] = monthYear.split('-');

        $.post('<?= module_url("reject/save_note") ?>', {
            year: year,
            month: month,
            note_text: noteText
        }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Gagal simpan nota');
            }
        }, 'json');
    });

    // Edit note
    $('.edit-note').click(function () {
        const noteId = $(this).data('id');
        
        // Get note data via AJAX
        $.get('<?= module_url("reject/get_note_by_id") ?>', { id: noteId }, function(response) {
            if (response.success && response.note) {
                const note = response.note;
                $('#editNoteId').val(noteId);
                $('#editMonth').val(note.T06_YEAR_SELECTED + '-' + note.T06_MONTH_SELECTED.padStart(2, '0'));
                $('#editNoteText').val(note.T06_NOTE_TEXT);
                $('#editNoteModal').modal('show');
            } else {
                alert('Gagal ambil data nota');
            }
        }, 'json');
    });

    // Update note
    $('#updateNoteBtn').click(function () {
        const noteId = $('#editNoteId').val();
        const monthYear = $('#editMonth').val();
        const noteText = $('#editNoteText').val().trim();

        if (!monthYear || !noteText) {
            alert('Sila isi semua ruangan');
            return;
        }

        const [year, month] = monthYear.split('-');

        $.post('<?= module_url("reject/update_note") ?>', {
            id: noteId,
            year: year,
            month: month,
            note_text: noteText
        }, function (response) {
            if (response.success) {
                $('#editNoteModal').modal('hide');
                location.reload();
            } else {
                alert(response.message || 'Gagal kemaskini nota');
            }
        }, 'json');
    });

    // Delete note - using delegated event handler
$(document).on('click', '.delete-note', function() {
    if (!confirm('Padam nota ini?')) return;

    const noteId = $(this).data('id');
    const noteElement = $(this).closest('.note-item');
    
    $.ajax({
        url: '<?= module_url("reject/delete_note") ?>',
        type: 'POST',
        data: { id: noteId },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                noteElement.fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                alert('Gagal padam: ' + (res.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            let errorMsg = 'Ralat sistem: ';
            try {
                const res = JSON.parse(xhr.responseText);
                errorMsg += res.message || xhr.statusText;
            } catch (e) {
                errorMsg += xhr.statusText;
            }
            alert(errorMsg);
        }
    });
});


    // View all notes
    $('.view-all-notes').click(function () {
        window.location.href = '<?= module_url("reject/manage_notes") ?>';
    });

    // Manage notes
    $('.manage-notes').click(function () {
        $.get('<?= module_url("reject/get_all_notes") ?>', function(response) {
            if (response.success) {
                if (response.notes.length > 0) {
                    alert('Terdapat ' + response.notes.length + ' nota dalam sistem. Akan diarahkan ke halaman pengurusan.');
                    window.location.href = '<?= module_url("reject/manage_notes") ?>';
                } else {
                    alert('Tiada nota dalam sistem. Sila tambah nota baru terlebih dahulu.');
                }
            }
        }, 'json');
    });
});

function loadNoteByMonthYear() {
    const selected = document.getElementById("select_month_year").value;
    const [month, year] = selected.split("-");

    fetch("<?= base_url('manage/reject/get_note') ?>?month=" + month + "&year=" + year)
        .then(response => response.json())
        .then(data => {
            let html = "";

            if (data.success && data.note) {
                const note = data.note.T06_NOTE_TEXT || "-";
                const updatedAt = data.note.T06_UPDATED_AT
                    ? new Date(data.note.T06_UPDATED_AT).toLocaleString()
                    : "Tiada Rekod Kemaskini";

                html = `
                <div class="note-item">
                    <div class="note-status side-notes d-flex justify-content-between align-items-center">
                        <span class="status-text">Side Notes</span>
                        <div class="action-icons">
                            <button class="btn-icon view-all-notes" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn-icon delete-note" data-id="${data.note.T06_NOTE_ID || data.note.T06_ID}" title="Delete">
                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="note-content">
                        <p>${note.replace(/\n/g, "<br>")}</p>
                        <p><strong>Dikemaskini:</strong> ${updatedAt}</p>
                    </div>
                </div>`;
            } else {
                html = `
                <div class="note-item">
                    <div class="note-status side-notes d-flex justify-content-between align-items-center">
                        <span class="status-text">Side Notes</span>
                        <div class="action-icons">
                            <button class="btn-icon view-all-notes" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn-icon manage-notes" title="Urus Nota">
                                <i class="fa fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div class="note-content">
                        <div class="empty-state text-center text-muted">
                            <i class="fa fa-sticky-note-o fa-2x mb-3"></i>
                            <p>Tiada nota dijumpai untuk tempoh ini</p>
                            <small>Klik butang + di atas untuk menambah nota baru</small>
                        </div>
                    </div>
                </div>`;
            }

            document.getElementById("note_result").innerHTML = html;
        })
        .catch(err => {
            console.error("Gagal ambil nota:", err);
            document.getElementById("note_result").innerHTML = `
                <div class="note-item">
                    <div class="note-status side-notes d-flex justify-content-between align-items-center">
                        <span class="status-text">Side Notes</span>
                        <div class="action-icons">
                            <button class="btn-icon view-all-notes" title="Lihat Semua Nota">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn-icon manage-notes" title="Urus Nota">
                                <i class="fa fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div class="note-content">
                        <div class="empty-state text-center text-muted">
                            <i class="fa fa-exclamation-triangle fa-2x mb-3 text-danger"></i>
                            <p>Ralat semasa memuatkan nota.</p>
                        </div>
                    </div>
                </div>`;
        });
}
</script>