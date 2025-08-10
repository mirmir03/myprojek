<?php
// SESSION ROLE DATA
$userRoleId = isset($_SESSION['userrole_id']) ? $_SESSION['userrole_id'] : null;
$userRoleName = isset($_SESSION['userrole']) ? strtolower(trim($_SESSION['userrole'])) : null;

// Alternative fallback to role_id if userrole_id not set
if (!$userRoleId && isset($_SESSION['role_id'])) {
    $userRoleId = $_SESSION['role_id'];
}

// Alternative fallback to role if userrole not set
if (!$userRoleName && isset($_SESSION['role'])) {
    $userRoleName = strtolower(trim($_SESSION['role']));
}

$CI =& get_instance();
$CI->load->database();
$CI->load->model("notifikasi_model");

// This is enough, uses the model and is clean:
$unreceived_count = $CI->notifikasi_model->count_sidebar_notifikasi();

// DEBUG: Add this temporarily at the top of menu.php
echo "<script>console.log('DEBUG: UserRoleId = " . $userRoleId . "');</script>";
echo "<script>console.log('DEBUG: Unreceived count from PHP = " . $unreceived_count . "');</script>";

// Test the count function directly
$debug_count = $CI->notifikasi_model->count_sidebar_notifikasi();
echo "<script>console.log('DEBUG: Direct count call = " . $debug_count . "');</script>";

// Test basic unreceived count
$basic_count = $CI->notifikasi_model->get_unreceived_count();
echo "<script>console.log('DEBUG: Basic unreceived count = " . $basic_count . "');</script>";

// Get role display names
$role_display_names = [
    '1' => 'X-Ray Technician',
    '2' => 'Medical Doctor', 
    '3' => 'Dentist'
];

$role_short_names = [
    '1' => 'xray',
    '2' => 'doctor',
    '3' => 'dentist'
];

$current_role_display = isset($role_display_names[$userRoleId]) ? $role_display_names[$userRoleId] : 'Unknown Role';
$current_role_short = isset($role_short_names[$userRoleId]) ? $role_short_names[$userRoleId] : 'unknown';

// DEBUG INFO (optional: remove/comment before production)
// echo "<!-- DEBUG: UserRoleId = $userRoleId, UserRoleName = $userRoleName -->";
?>

<ul id="sidebarnav">
    <?php if (!empty($_SESSION['UID'])): ?>
        <!-- User Info Section -->
        <li class="sidebar-item">
            <div style="padding: 8px 16px; font-size: 14px; color: #888; border-bottom: 1px solid #eee;">
                <?php 
                // Display user info using session data
                $displayId = $_SESSION['UID'] ?? 'N/A';
                $displayName = $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User';
                echo "<strong>" . $displayId . " - " . $displayName . "</strong><br>";
                echo "<small style='color: #666;'>" . $current_role_display . "</small>";
                ?>
            </div>
        </li>

        <!-- Back to MyNemo -->
        <li class="sidebar-item">
            <a href="https://mynemov3.umt.edu.my/mynemov3/mainpage/main" class="sidebar-link">
                <i class="ti ti-corner-up-left-double"></i>
                <span class="hide-menu">Kembali ke MyNemo</span>
            </a>
        </li>

        <!-- ROLE-SPECIFIC MENU ITEMS -->
        <?php if ($userRoleId == '1'): // Juru Xray ?>
            
            <!-- Register New Patient -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/pesakit/listpesakit" class="sidebar-link">
                    <iconify-icon icon="solar:user-plus-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Tab Pendaftaran</span>
                </a>
            </li>

            <!-- Manage Dokumen -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/dokumen/listdokumen" class="sidebar-link">
                    <iconify-icon icon="solar:document-text-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Dokumen Perlesenan</span>
                </a>
            </li>

            <!-- Manage Reject -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/reject/listreject" class="sidebar-link">
                    <iconify-icon icon="solar:document-text-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Analisis <i>Reject</i></span>
                </a>
            </li>

           <li class="sidebar-item">
    <a href="<?= base_url() ?>manage/notifikasi/index" class="sidebar-link">
        <iconify-icon icon="solar:settings-linear" class="aside-icon"></iconify-icon>
        <span class="hide-menu">
            Pesanan Item Xray
            <span id="notificationBadge" class="badge bg-danger ms-2" style="<?= $unreceived_count > 0 ? '' : 'display: none;' ?>">
                <?= $unreceived_count ?>
            </span>
        </span>
    </a>
</li>
            <!-- X-Ray Management -->
            <li class="sidebar-item">
                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <iconify-icon icon="solar:medical-kit-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Dosimetri</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                    <li class="sidebar-item">
                        <a href="<?= base_url() ?>manage/dosimetripesakit/listdospesakit" class="sidebar-link">
                            <div class="round-16 d-flex align-items-center justify-content-center">
                                <i class="ti ti-circle"></i>
                            </div>
                            <span class="hide-menu">Pesakit</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url() ?>manage/dosimetristaf/listdos_staff" class="sidebar-link">
                            <div class="round-16 d-flex align-items-center justify-content-center">
                                <i class="ti ti-circle"></i>
                            </div>
                            <span class="hide-menu">Staf</span>
                        </a>
                    </li>
                </ul>
            </li>

        <?php elseif ($userRoleId == '2'): // Doktor ?>
            
            <!-- Doctor Dashboard -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/doctor/index" class="sidebar-link">
                    <iconify-icon icon="solar:home-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu"><i>Dashboard</i> Doktor</span>
                </a>
            </li>

            <!-- Doctor Dashboard -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/reject/graph" class="sidebar-link">
                    <iconify-icon icon="solar:home-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Analisis <i> Reject</i></span>
                </a>
            </li>

            <!-- Doctor Dashboard -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/dokumen/listdokumen" class="sidebar-link">
                    <iconify-icon icon="solar:home-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Dokumen Perlesenan </i></span>
                </a>
            </li>

            <!-- Doctor Dashboard -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/dosimetristaf/listdos_staff" class="sidebar-link">
                    <iconify-icon icon="solar:home-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Dosimetri Staff</i></span>
                </a>
            </li>

        <?php elseif ($userRoleId == '3'): // Doktor Gigi ?>
            
            <!-- Dentist Dashboard -->
            <li class="sidebar-item">
                <a href="<?= base_url() ?>manage/dosimetristaf/listdos_staff" class="sidebar-link">
                    <iconify-icon icon="solar:home-linear" class="aside-icon"></iconify-icon>
                    <span class="hide-menu">Dashboard Doktor Gigi</span>
                </a>
            </li>

        <?php else: ?>
            <!-- Unknown Role - Show limited access -->
            <li class="sidebar-item">
                <div style="padding: 16px; color: #f44336; text-align: center;">
                    <i class="ti ti-alert-circle" style="font-size: 24px;"></i><br>
                    <small>Peranan tidak dikenali<br>Sila hubungi pentadbir sistem</small>
                </div>
            </li>
        <?php endif; ?>

        <!-- Settings (if user has admin permissions) -->
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <iconify-icon icon="solar:settings-linear" class="aside-icon"></iconify-icon>
                <span class="hide-menu">Tetapan Sistem</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                    <a href="<?= base_url() ?>admin/users" class="sidebar-link">
                        <div class="round-16 d-flex align-items-center justify-content-center">
                            <i class="ti ti-circle"></i>
                        </div>
                        <span class="hide-menu">Pengurusan Pengguna</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= base_url() ?>admin/roles" class="sidebar-link">
                        <div class="round-16 d-flex align-items-center justify-content-center">
                            <i class="ti ti-circle"></i>
                        </div>
                        <span class="hide-menu">Pengurusan Peranan</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Logout -->
        <li class="sidebar-item">
            <a href="<?= base_url() ?>auth/logout" class="sidebar-link" onclick="return confirm('Adakah anda pasti untuk log keluar?')">
                <iconify-icon icon="solar:logout-linear" class="aside-icon"></iconify-icon>
                <span class="hide-menu">Log Keluar</span>
            </a>
        </li>

    <?php else: ?>
        <!-- Not logged in -->
        <li class="sidebar-item">
            <div style="padding: 16px; text-align: center; color: #666;">
                <i class="ti ti-lock" style="font-size: 24px;"></i><br>
                <small>Sila log masuk untuk akses</small>
            </div>
        </li>
        <li class="sidebar-item">
            <a href="<?= base_url() ?>auth/login" class="sidebar-link">
                <iconify-icon icon="solar:login-linear" class="aside-icon"></iconify-icon>
                <span class="hide-menu">Log Masuk</span>
            </a>
        </li>
    <?php endif; ?>
</ul>

<!-- JavaScript for menu functionality and notification system -->
<script>
// Handle notification badge updates with real-time polling
function updateNotificationCount() {
    // Only run for X-Ray Technicians (role 1)
    <?php if ($userRoleId == '1'): ?>
    fetch('<?= base_url('manage/notifikasi/get_notification_count') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSidebarBadge(data.count);
            }
        })
        .catch(error => console.error('Notification update failed:', error));
    <?php endif; ?>
}

// Function to update sidebar badge with smooth animations
function updateSidebarBadge(count) {
    const pesananLink = document.querySelector('a[href*="notifikasi/index"] .hide-menu');
    let badge = pesananLink?.querySelector('.badge');

    if (count > 0) {
        if (badge) {
            // Update existing badge
            const oldCount = parseInt(badge.textContent) || 0;
            badge.textContent = count;
            
            // Add pulse animation if count increased
            if (count > oldCount) {
                badge.style.animation = 'pulse 0.6s ease-in-out';
                setTimeout(() => {
                    badge.style.animation = '';
                }, 600);
            }
        } else {
            // Create new badge
            const newBadge = document.createElement('span');
            newBadge.className = 'badge bg-danger ms-2';
            newBadge.textContent = count;
            newBadge.style.transform = 'scale(0)';
            newBadge.style.transition = 'transform 0.3s ease-out';
            
            if (pesananLink) {
                pesananLink.appendChild(newBadge);
                
                // Animate in
                setTimeout(() => {
                    newBadge.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        newBadge.style.transform = 'scale(1)';
                    }, 150);
                }, 50);
            }
        }
    } else {
        // Remove badge if count is 0
        if (badge) {
            badge.style.transform = 'scale(0)';
            badge.style.transition = 'transform 0.3s ease-in';
            setTimeout(() => {
                badge.remove();
            }, 300);
        }
    }
}

// Enhanced toast notification with better styling
function showToast(message, type = 'info') {
    // Check if jQuery is loaded for this function
    if (typeof $ !== 'undefined') {
        const toastClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const icon = type === 'success' ? 'ti-check-circle' :
                     type === 'error' ? 'ti-alert-circle' :
                     type === 'warning' ? 'ti-alert-triangle' : 'ti-info-circle';
        
        const toast = $(`
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
                 role="alert">
                <i class="ti ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.alert('close');
        }, 4000);
    } else {
        // Fallback to simple alert if jQuery not loaded
        alert(message);
    }
}

// Add CSS for pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); box-shadow: 0 0 10px rgba(220, 38, 38, 0.7); }
        100% { transform: scale(1); }
    }
    
    .badge.bg-danger {
        animation-fill-mode: both;
    }
`;
document.head.appendChild(style);

// Initialize and start polling for X-Ray Technicians
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($userRoleId == '1'): ?>
        // Initial count update
        updateNotificationCount();
        
        // Poll every 30 seconds for updates
        setInterval(updateNotificationCount, 30000);
        
        // Also update when page gains focus (user returns to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateNotificationCount();
            }
        });
    <?php endif; ?>

    // Add active class to current menu item based on URL
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.sidebar-link[href]');
    
    menuLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentPath || currentPath.includes(href.replace('<?= base_url() ?>', '')))) {
            link.closest('.sidebar-item').classList.add('selected');
            
            // If it's a submenu item, expand parent
            const parentSubmenu = link.closest('.collapse');
            if (parentSubmenu) {
                parentSubmenu.classList.add('show');
                const parentToggle = parentSubmenu.previousElementSibling;
                if (parentToggle) {
                    parentToggle.setAttribute('aria-expanded', 'true');
                    parentToggle.classList.remove('collapsed');
                }
            }
        }
    });
});

// Global function accessible from other pages to update badge
window.updateNotificationBadge = updateSidebarBadge;
window.showNotificationToast = showToast;
</script>