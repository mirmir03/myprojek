<?php
class Role_model extends CI_Model
{
    /**
     * Get staff/user information by UID from X-ray staff table
     * Returns user object if found and active, NULL otherwise
     */
    public function get_staff_by_uid($uid)
    {
        if (empty($uid)) return null;
        
        // Debug logging
        log_message('debug', "Looking for staff with UID: " . $uid);
        
        // Try exact match first (as string)
        $this->db->where('T02_ID_STAF', $uid);
        $this->db->where('T02_IS_ACTIVE', 'Y');
        $query = $this->db->get('EV_T02_STAF_XRAY');
        
        if ($query->num_rows() > 0) {
            $result = $query->row();
            log_message('debug', "Found staff: " . json_encode($result));
            return $result;
        }
        
        // Try as integer if string failed
        if (is_numeric($uid)) {
            $this->db->where('T02_ID_STAF', intval($uid));
            $this->db->where('T02_IS_ACTIVE', 'Y');
            $query = $this->db->get('EV_T02_STAF_XRAY');
            
            if ($query->num_rows() > 0) {
                $result = $query->row();
                log_message('debug', "Found staff (as int): " . json_encode($result));
                return $result;
            }
        }
        
        log_message('error', "No staff found for UID: " . $uid);
        return null;
    }

    /**
     * Get user role information for session management
     * Returns array format for compatibility with Landing.php
     */
    public function get_user_role()
    {
        // Get UID from session
        $uid = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;
        
        if (!$uid) {
            return false;
        }
        
        // Query the database for user role
        $this->db->where('T02_ID_STAF', $uid);
        $this->db->where('T02_IS_ACTIVE', 'Y');
        $query = $this->db->get('EV_T02_STAF_XRAY');
        
        if ($query->num_rows() > 0) {
            return $query->row_array(); // Return as array for compatibility with Landing.php
        }
        return false;
    }

    /**
     * Get all active X-ray staff
     */
    public function get_all_active_staff()
    {
        $this->db->where('T02_IS_ACTIVE', 'Y');
        $this->db->order_by('T02_NAMA_STAF', 'ASC');
        $query = $this->db->get('EV_T02_STAF_XRAY');
        return $query->result();
    }

    /**
     * Get staff by role/position (Juru Xray, Doktor, etc.)
     */
    public function get_staff_by_role($role_id)
    {
        if (empty($role_id)) {
            return array();
        }
        
        $this->db->where('T0_ID', $role_id);
        $this->db->where('T02_IS_ACTIVE', 'Y');
        $this->db->order_by('T02_NAMA_STAF', 'ASC');
        $query = $this->db->get('EV_T02_STAF_XRAY');
        return $query->result();
    }

    /**
     * Check if user has specific permission based on role
     * Enhanced with more granular permissions for X-ray system
     */
    public function has_permission($uid, $permission)
    {
        $user = $this->get_staff_by_uid($uid);
        
        if (!$user) {
            return false;
        }

        // Define permissions for X-ray staff roles
        $role_permissions = [
            '1' => [ // Juru Xray
                'patient_create', 'patient_read', 'patient_update', 'patient_delete',
                'xray_create', 'xray_read', 'xray_update', 'xray_delete',
                'report_create', 'report_read', 'report_update',
                'equipment_manage', 'schedule_manage'
            ],
            '2' => [ // Doktor
                'patient_read', 'patient_update',
                'xray_read', 'xray_approve', 'xray_reject', 'xray_review',
                'report_read', 'report_approve', 'report_create', 'report_update',
                'diagnosis_create', 'diagnosis_update', 'diagnosis_read',
                'prescription_create', 'prescription_update'
            ],
            '3' => [ // Doktor Gigi
                'patient_read', 'patient_update',
                'xray_read', 'xray_approve', 'xray_reject', 'xray_review',
                'report_read', 'report_approve', 'report_create', 'report_update',
                'diagnosis_create', 'diagnosis_update', 'diagnosis_read',
                'dental_treatment_create', 'dental_treatment_update'
            ]
        ];

        $user_role_id = isset($user->T0_ID) ? $user->T0_ID : 'UNKNOWN';
        
        if (!isset($role_permissions[$user_role_id])) {
            return false;
        }

        return in_array($permission, $role_permissions[$user_role_id]);
    }

    /**
     * Get user role ID
     */
    public function get_user_role_id($uid)
    {
        $user = $this->get_staff_by_uid($uid);
        return $user ? (isset($user->T0_ID) ? $user->T0_ID : 'UNKNOWN') : 'UNKNOWN';
    }

    /**
     * Get user role description based on role ID
     */
    public function get_role_description($role_id)
    {
        $role_descriptions = [
            '1' => 'xray',
            '2' => 'doctor',
            '3' => 'dentist'
        ];
        
        return isset($role_descriptions[$role_id]) ? $role_descriptions[$role_id] : 'unknown';
    }

    /**
     * Get user role name/position from database
     */
    public function get_user_role_name($uid)
    {
        $user = $this->get_staff_by_uid($uid);
        return $user ? (isset($user->T02_ROLE) ? $user->T02_ROLE : 'UNKNOWN') : 'UNKNOWN';
    }

    /**
     * Check if user is Juru Xray (role ID = 1)
     */
    public function is_juru_xray($uid)
    {
        return $this->get_user_role_id($uid) === '1';
    }

    /**
     * Check if user is Doktor (role ID = 2)
     */
    public function is_doktor($uid)
    {
        return $this->get_user_role_id($uid) === '2';
    }

    /**
     * Check if user is Doktor Gigi (role ID = 3)
     */
    public function is_doktor_gigi($uid)
    {
        return $this->get_user_role_id($uid) === '3';
    }

    /**
     * Check if user is any type of doctor (role ID = 2 or 3)
     */
    public function is_any_doctor($uid)
    {
        $role_id = $this->get_user_role_id($uid);
        return in_array($role_id, ['2', '3']);
    }

    /**
     * Get permissions for a specific role ID
     */
    public function get_role_permissions($role_id)
    {
        $role_permissions = [
            '1' => [ // Juru Xray
                'patient_create', 'patient_read', 'patient_update', 'patient_delete',
                'xray_create', 'xray_read', 'xray_update', 'xray_delete',
                'report_create', 'report_read', 'report_update',
                'equipment_manage', 'schedule_manage'
            ],
            '2' => [ // Doktor
                'patient_read', 'patient_update',
                'xray_read', 'xray_approve', 'xray_reject', 'xray_review',
                'report_read', 'report_approve', 'report_create', 'report_update',
                'diagnosis_create', 'diagnosis_update', 'diagnosis_read',
                'prescription_create', 'prescription_update'
            ],
            '3' => [ // Doktor Gigi
                'patient_read', 'patient_update',
                'xray_read', 'xray_approve', 'xray_reject', 'xray_review',
                'report_read', 'report_approve', 'report_create', 'report_update',
                'diagnosis_create', 'diagnosis_update', 'diagnosis_read',
                'dental_treatment_create', 'dental_treatment_update'
            ]
        ];

        return isset($role_permissions[$role_id]) ? $role_permissions[$role_id] : [];
    }

    /**
     * Get user's menu access level based on role
     */
    public function get_menu_access_level($uid)
    {
        $role_id = $this->get_user_role_id($uid);
        
        $access_levels = [
            '1' => 'juru_xray',    // Full patient and X-ray management
            '2' => 'doktor',       // Review and approve X-rays, diagnosis
            '3' => 'doktor_gigi'   // Dental-specific review and treatment
        ];
        
        return isset($access_levels[$role_id]) ? $access_levels[$role_id] : 'none';
    }

    /**
     * Soft delete staff (set inactive)
     */
    public function deactivate_staff($uid)
    {
        if (empty($uid)) {
            return false;
        }
        
        $data = ['T02_IS_ACTIVE' => 'N'];
        $this->db->where('T02_ID_STAF', $uid);
        return $this->db->update('EV_T02_STAF_XRAY', $data);
    }

    /**
     * Reactivate staff
     */
    public function activate_staff($uid)
    {
        if (empty($uid)) {
            return false;
        }
        
        $data = ['T02_IS_ACTIVE' => 'Y'];
        $this->db->where('T02_ID_STAF', $uid);
        return $this->db->update('EV_T02_STAF_XRAY', $data);
    }

    /**
     * Update staff role/position
     */
    public function update_staff_role($uid, $role_id)
    {
        if (empty($uid) || empty($role_id)) {
            return false;
        }
        
        // Validate role ID
        if (!in_array($role_id, ['1', '2', '3'])) {
            return false;
        }
        
        $data = ['T0_ID' => $role_id];
        $this->db->where('T02_ID_STAF', $uid);
        return $this->db->update('EV_T02_STAF_XRAY', $data);
    }

    /**
     * Validate user credentials and return user data
     */
    public function authenticate_user($uid)
    {
        if (empty($uid)) {
            return false;
        }
        
        $user = $this->get_staff_by_uid($uid);
        
        if (!$user) {
            return false;
        }
        
        // Return formatted user data for session
        return [
            'uid' => $user->T02_ID_STAF,
            'name' => $user->T02_NAMA_STAF,
            'role_id' => $user->T0_ID,
            'role_name' => $user->T02_ROLE,
            'role_description' => $this->get_role_description($user->T0_ID),
            'is_active' => $user->T02_IS_ACTIVE,
            'menu_access' => $this->get_menu_access_level($user->T02_ID_STAF)
        ];
    }

    /**
     * Get staff statistics by role
     */
    public function get_staff_statistics()
    {
        $this->db->select('T0_ID, COUNT(*) as count');
        $this->db->where('T02_IS_ACTIVE', 'Y');
        $this->db->group_by('T0_ID');
        $query = $this->db->get('EV_T02_STAF_XRAY');
        
        $stats = [];
        foreach ($query->result() as $row) {
            $stats[$row->T02_ID] = [
                'count' => $row->count,
                'description' => $this->get_role_description($row->T0_ID)
            ];
        }
        
        return $stats;
    }

    /**
     * Check if user can access specific module
     */
    public function can_access_module($uid, $module)
    {
        $role_id = $this->get_user_role_id($uid);
        
        $module_access = [
            'patient_management' => ['1'],           // Only Juru Xray
            'xray_management' => ['1'],              // Only Juru Xray
            'medical_review' => ['2'],               // Only Doktor
            'dental_review' => ['3'],                // Only Doktor Gigi
            'diagnosis' => ['2', '3'],               // Both doctors
            'reports' => ['1', '2', '3']             // All roles
        ];
        
        return isset($module_access[$module]) && in_array($role_id, $module_access[$module]);
    }
}