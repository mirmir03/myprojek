<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class External_auth extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
    }
    
    /**
     * Debug method to clear session
     */
    public function clear_session() {
        // Clear all session data
        $this->session->unset_userdata('UID');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('role_id');
        
        // Destroy session completely
        $this->session->sess_destroy();
        
        echo "<h2>Session Cleared</h2>";
        echo "<p>All session data has been cleared.</p>";
        echo '<p><a href="' . site_url('manage/external_auth/login') . '">Go to Login Page</a></p>';
        echo '<p><a href="' . site_url('manage/external_auth/process_login') . '">Test Process Login (should redirect to login now)</a></p>';
    }
    
    /**
     * External login page
     */
    public function login() {
        echo "<h2>Login Method Debug</h2>";
        
        // Check individual session data items (FIXED - no empty userdata() call)
        echo "<p>Current session data:</p>";
        echo "<pre>";
        echo "UID: " . ($this->session->userdata('UID') ? $this->session->userdata('UID') : 'NULL') . "\n";
        echo "username: " . ($this->session->userdata('username') ? $this->session->userdata('username') : 'NULL') . "\n";
        echo "role: " . ($this->session->userdata('role') ? $this->session->userdata('role') : 'NULL') . "\n";
        echo "role_id: " . ($this->session->userdata('role_id') ? $this->session->userdata('role_id') : 'NULL') . "\n";
        echo "</pre>";
        
        $uid = $this->session->userdata('UID');
        echo "<p>UID in session: " . ($uid ? $uid : 'NULL') . "</p>";
        
        // If user is already logged in, redirect to system_start
        if ($this->session->userdata('UID')) {
            echo "<p style='color: red;'>User is already logged in! Redirecting to system...</p>";
            echo '<p><a href="' . site_url('manage/external_auth/clear_session') . '">Clear Session First</a></p>';
            echo '<p><a href="' . site_url('manage/role/system_start') . '">Go to System</a></p>';
            // Uncomment this line when debugging is done:
            // redirect('manage/Role/system_start');
            return;
        }
        
        echo "<p style='color: green;'>No active session, loading login form...</p>";
        $this->load->view('external_login');
    }
    
    /**
     * Production login page (use this when debugging is complete)
     */
    public function login_production() {
        // If user is already logged in, redirect to system_start
        if ($this->session->userdata('UID')) {
            redirect('manage/Role/system_start');
            return;
        }
        
        $this->load->view('external_login');
    }
    
    /**
     * Process external login
     */
    /**
 * Process external login
 */
public function process_login() {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // If not POST, redirect to login page
        redirect('manage/external_auth/login');
        return;
    }
    
    // Try to get POST data the normal way first
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    
    // If that doesn't work, try the raw input method
    if (empty($username) && empty($password)) {
        $raw_input = file_get_contents('php://input');
        parse_str($raw_input, $post_data);
        
        $username = isset($post_data['username']) ? $post_data['username'] : '';
        $password = isset($post_data['password']) ? $post_data['password'] : '';
    }
    
    // Validate input
    if (empty($username) || empty($password)) {
        $this->session->set_flashdata('error', 'Please enter both username and password');
        redirect('manage/external_auth/login');
        return;
    }
    
    // Authenticate user
    $user_data = $this->authenticate_user($username, $password);
    
    if ($user_data) {
        // Set session data
        $this->session->set_userdata('UID', $user_data->T02_ID_STAF);
        $this->session->set_userdata('username', $user_data->T02_NAMA_STAF);
        
        // Redirect to system
        redirect('manage/Role/system_start');
    } else {
        $this->session->set_flashdata('error', 'Invalid staff ID or password');
        redirect('manage/external_auth/login');
    }
}

/**
 * Authenticate user
 */
private function authenticate_user($username, $password) {
    // Get user by staff ID
    $user = $this->User_model->get_user_by_staffid($username);
    
    if (!$user) {
        return false;
    }
    
    // CORRECTED - Updated password verification
    $valid_passwords = [
        '1280' => 'xray123',        // Irwan Iskandar (Xray)
        '1221' => 'doctor123',      // Dr. Wan Nursiah (doctor) 
        '44444' => 'dentist123'     // Dr. Deang Malis (dentist)
    ];
    
    if (isset($valid_passwords[$user->T02_ID_STAF]) && 
        $valid_passwords[$user->T02_ID_STAF] === $password) {
        return $user;
    }
    
    return false;
}
    
    /**
     * External logout
     */
    public function logout() {
        // Clear session data
        $this->session->unset_userdata('UID');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('role_id');
        
        // Destroy session
        $this->session->sess_destroy();
        
        // Redirect to external login page
        redirect('manage/external_auth/login');
    }
    
    /**
     * Test form page - CORRECTED with database IDs
     */
    public function test_form() {
        echo "<h2>Test Form Page</h2>";
        echo "<p>Use this form to test if form submission works:</p>";
        ?>
        
        <!-- Simple test form with correct action and user ID -->
        <form method="post" action="<?php echo site_url('manage/external_auth/process_login'); ?>">
            <h3>Test Form</h3>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="1280" class="form-control">
                <small>Available IDs: 1280 (Irwan-Xray), 1321 (Dr.Wan-Doctor), 41414 (Dr.Deang-Dentist)</small>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="xray123" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Test Submit</button>
        </form>
        
        <hr>
        <p>After submitting, check if it reaches the process_login method.</p>
        <?php
    }
    
    /**
     * Debug method to test form submission
     */
    public function debug_process_login() {
        echo "<h2>Form Submission Debug</h2>";
        
        // Check request method
        echo "<p>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
        
        // Check all input data
        echo "<p>POST data: </p>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        echo "<p>RAW POST data: </p>";
        echo "<pre>";
        print_r(file_get_contents('php://input'));
        echo "</pre>";
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<p style='color: green;'>✓ POST request detected</p>";
            
            // Manual way to get POST data
            parse_str(file_get_contents('php://input'), $post_data);
            echo "<p>Parsed POST data: </p>";
            echo "<pre>";
            print_r($post_data);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Not a POST request</p>";
        }
        
        // Test session setting
        $this->session->set_userdata('UID', 'TEST123');
        $this->session->set_userdata('username', 'Test User');
        
        echo "<p>Session data set. Would redirect to manage/Role/system_start</p>";
        echo '<p><a href="' . site_url('manage/Role/system_start') . '">Test redirect</a></p>';
    }
}