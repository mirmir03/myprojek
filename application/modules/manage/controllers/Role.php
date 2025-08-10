<?php
class Role extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Role_model');
    }

    /**
     * System entry point - validates user and redirects based on role
     */
    public function system_start()
    {    
        // Check if session UID exists
        if (!isset($_SESSION["UID"]) || empty($_SESSION["UID"])) {
            show_error("Session expired or UID not found", 403);
            return;
        }
        
        $uid = $_SESSION["UID"];
        
        // Get user data by UID
        $userdata = $this->Role_model->get_staff_by_uid($uid);
        
        if (!$userdata) {
            // Add debugging information
            log_message('error', "User lookup failed for UID: " . $uid);
            show_error("User not found or access denied. UID: " . $uid, 403);
            return;
        }
        
        // Debug log the user data
        log_message('debug', "User found: " . json_encode($userdata));
        
        // Set session variables using the correct field mapping
        $_SESSION['UID'] = $userdata->T02_ID_STAF;
        $_SESSION['username'] = $userdata->T02_NAMA_STAF;
        $_SESSION['user_name'] = $userdata->T02_NAMA_STAF; // For menu display
        
        // Map role ID to role name for consistency
        $role_mapping = $this->get_role_mapping();
        $role_id = isset($userdata->T0_ID) ? $userdata->T0_ID : 'unknown';
        
        if (!isset($role_mapping[$role_id])) {
            show_error("Role not recognized. Role ID: " . $role_id, 403);
            return;
        }
        
        // Set role session variables for menu
        $_SESSION['role'] = $role_mapping[$role_id];
        $_SESSION['role_id'] = $role_id;
        $_SESSION['userrole_id'] = $role_id; // For menu compatibility
        $_SESSION['userrole'] = $role_mapping[$role_id]; // For menu compatibility
        
        // Set role flags for easier checking
        $_SESSION['is_juru_xray'] = ($role_id == '1');
        $_SESSION['is_doktor'] = ($role_id == '2');
        $_SESSION['is_doktor_gigi'] = ($role_id == '3');
        
        // Redirect based on user role
        switch ($_SESSION['role']) {
            case 'xray':
                redirect('manage/pesakit/listpesakit');
                break;
            case 'doctor':
                redirect('manage/doctor/');
                break;
            case 'dentist':
                redirect('manage/dosimetristaf/graph');
                break;
            default:
                show_error("Role not configured for redirection: " . $_SESSION['role'], 403);
        }
    }

    /**
     * Get role mapping from role ID to role name
     */
    private function get_role_mapping()
    {
        return [
            '1' => 'xray',     // Juru Xray
            '2' => 'doctor',   // Doktor
            '3' => 'dentist'   // Doktor Gigi
        ];
    }

    /**
     * Get display name for role based on role ID
     */
    private function get_role_display_name($role_id)
    {
        $role_map = [
            '1' => 'X-Ray Technician',
            '2' => 'Medical Doctor',
            '3' => 'Dentist'
        ];
        
        return isset($role_map[$role_id]) ? $role_map[$role_id] : 'Unknown Role';
    }

    /**
     * Welcome page with role-specific content
     */
    public function welcome()
    {
        // Check if user is logged in
        if (!isset($_SESSION["UID"]) || empty($_SESSION["UID"])) {
            redirect('auth/login');
            return;
        }

        $uid = $_SESSION["UID"];
        $userdata = $this->Role_model->get_staff_by_uid($uid);
        
        if (!$userdata) {
            show_error("User not found", 403);
            return;
        }

        // Prepare data for view
        $data = [
            'user' => $userdata,
            'role_display' => $this->get_role_display_name($userdata->T0_ID),
            'permissions' => $this->get_user_permissions($uid)
        ];

        $this->template->set('data', $data);
        $this->template->title("Welcome - " . $userdata->T02_NAMA_STAF);
        $this->template->render();
    }

    /**
     * Check if current user has specific permission
     */
    public function check_permission($permission)
    {
        if (!isset($_SESSION["UID"])) {
            return false;
        }

        return $this->Role_model->has_permission($_SESSION["UID"], $permission);
    }

    /**
     * Get all permissions for current user based on role ID
     */
    private function get_user_permissions($uid)
    {
        $user = $this->Role_model->get_staff_by_uid($uid);
        if (!$user) {
            return [];
        }

        $role_id = isset($user->T02_ID) ? $user->T0_ID : 'unknown';
        
        // Define role-based permissions using role ID
        $role_permissions = [
            '1' => [ // Juru Xray
                'xray_create', 'xray_read', 'xray_update', 'xray_delete',
                'schedule_create', 'schedule_read', 'schedule_update', 'schedule_delete',
                'patient_create', 'patient_read', 'patient_update', 'patient_delete',
                'equipment_read', 'equipment_update',
                'report_create', 'report_read'
            ],
            '2' => [ // Doktor
                'patient_read', 'patient_update',
                'xray_read', 'xray_update', 'xray_approve',
                'diagnosis_create', 'diagnosis_read', 'diagnosis_update', 'diagnosis_delete',
                'prescription_create', 'prescription_read', 'prescription_update',
                'report_read', 'report_create',
                'schedule_read'
            ],
            '3' => [ // Doktor Gigi
                'patient_read', 'patient_update',
                'xray_read', 'xray_update', 'xray_approve',
                'dental_diagnosis_create', 'dental_diagnosis_read', 'dental_diagnosis_update', 'dental_diagnosis_delete',
                'dental_treatment_create', 'dental_treatment_read', 'dental_treatment_update',
                'report_read', 'report_create',
                'schedule_read'
            ]
        ];

        return isset($role_permissions[$role_id]) ? $role_permissions[$role_id] : [];
    }

    /**
     * Role-specific dashboard redirects
     */
    public function dashboard()
    {
        if (!isset($_SESSION["UID"]) || !isset($_SESSION["role"])) {
            redirect('auth/login');
            return;
        }

        $role = $_SESSION["role"]; // Already lowercase from system_start()
        
        switch ($role) {
            case 'xray':
                redirect('manage/pesakit/listpesakit');
                break;
            case 'doctor':
                redirect('manage/doctor/');
                break;
            case 'dentist':
                redirect('manage/dosimetristaf/graph');
                break;
            default:
                show_error("Invalid role: " . $role, 403);
        }
    }

    /**
     * User management functions (if needed for admin)
     */
    public function manage_users()
    {
        // Check if user has admin permissions
        if (!$this->check_permission('user_manage')) {
            show_error("Access denied", 403);
            return;
        }

        $data['users'] = $this->Role_model->get_all_active_staff();
        $data['roles'] = $this->get_role_mapping();
        
        $this->template->set('data', $data);
        $this->template->title("Manage Users");
        $this->template->render();
    }

    /**
     * Get users by role (AJAX endpoint)
     */
    public function get_users_by_role()
    {
        $role_id = $this->input->post('role_id');
        
        if (empty($role_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Role ID not specified']);
            return;
        }

        $users = $this->Role_model->get_staff_by_role($role_id);
        echo json_encode(['status' => 'success', 'data' => $users]);
    }

    /**
     * Update user role (AJAX endpoint)
     */
    public function update_user_role()
    {
        // Check permissions
        if (!$this->check_permission('user_manage')) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $uid = $this->input->post('uid');
        $role_id = $this->input->post('role_id');

        if (empty($uid) || empty($role_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
            return;
        }

        $result = $this->Role_model->update_staff_role($uid, $role_id);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Role updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update role']);
        }
    }

    /**
     * Deactivate user (AJAX endpoint)
     */
    public function deactivate_user()
    {
        // Check permissions
        if (!$this->check_permission('user_manage')) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $uid = $this->input->post('uid');

        if (empty($uid)) {
            echo json_encode(['status' => 'error', 'message' => 'User ID not specified']);
            return;
        }

        $result = $this->Role_model->deactivate_staff($uid);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'User deactivated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to deactivate user']);
        }
    }

    /**
     * Get current user info (AJAX endpoint)
     */
    public function get_current_user()
    {
        if (!isset($_SESSION["UID"])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $uid = $_SESSION["UID"];
        $userdata = $this->Role_model->get_staff_by_uid($uid);
        
        if ($userdata) {
            $response = [
                'status' => 'success',
                'data' => [
                    'id' => $userdata->T02_ID_STAF,
                    'name' => $userdata->T02_NAMA_STAF,
                    'role_id' => $userdata->T0_ID,
                    'role' => $_SESSION['role'] ?? 'unknown',
                    'role_display' => $this->get_role_display_name($userdata->T0_ID),
                    'is_active' => $userdata->T02_IS_ACTIVE
                ]
            ];
        } else {
            $response = ['status' => 'error', 'message' => 'User not found'];
        }

        echo json_encode($response);
    }
}