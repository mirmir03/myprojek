<?php
class Auth extends Admin_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Role_model');
    }
    
    /**
     * Display login page
     */
    public function login() {
        // If user is already logged in, redirect to appropriate dashboard
        if ($this->session->userdata('UID')) {
            redirect('role/dashboard');
            return;
        }
        
        $this->load->view('auth/login');
    }
    
    /**
     * Process login authentication
     */
    public function process_login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        // Validate input
        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Please enter both username and password');
            redirect('auth/login');
            return;
        }
        
        // Authenticate user (pseudo-code - implement your actual authentication)
        $user_data = $this->authenticate_user($username, $password);
        
        if ($user_data) {
            // Set session data
            $this->session->set_userdata('UID', $user_data['staff_id']);
            $this->session->set_userdata('username', $user_data['name']);
            
            // Redirect to role system for further processing
            redirect('role/system_start');
        } else {
            $this->session->set_flashdata('error', 'Invalid staff ID or password');
            redirect('auth/login');
        }
    }
    
    /**
     * Authenticate user (placeholder - implement your actual authentication)
     */
    private function authenticate_user($username, $password) {
        // This is a placeholder - replace with your actual authentication logic
        // Example: Database authentication, LDAP, etc.
        
        // For demo purposes, we'll use a simple check
        $valid_users = [
            'xray001' => ['staff_id' => 'STF001', 'name' => 'Xray Technician', 'password' => 'xray123'],
            'doctor001' => ['staff_id' => 'DOC001', 'name' => 'Medical Doctor', 'password' => 'doctor123'],
            'dentist001' => ['staff_id' => 'DEN001', 'name' => 'Dental Doctor', 'password' => 'dentist123']
        ];
        
        if (isset($valid_users[$username]) && $valid_users[$username]['password'] === $password) {
            return [
                'staff_id' => $valid_users[$username]['staff_id'],
                'name' => $valid_users[$username]['name']
            ];
        }
        
        return false;
    }
    
    /**
     * Logout function
     */
    public function logout() {
        // Clear all session data
        $this->session->unset_userdata('UID');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('role_id');
        
        // Destroy session
        $this->session->sess_destroy();
        
        // Redirect to login page
        redirect('auth/login');
    }
}
?>