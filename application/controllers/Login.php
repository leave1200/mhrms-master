<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model'); 
        $this->load->model('login_model');
        $this->load->model('dashboard_model');
    }

    public function index() {
        if ($this->session->userdata('user_login_access') == 1)
            redirect(base_url() . 'dashboard');
        $data = array();
        $this->load->view('login', $data);
    }

    public function forget_password1() {
        $this->load->view('backend/forget_password_view');
    }

    public function forgot_password2() {
        $email = $this->input->post('email');
        $message = $this->user_model->does_email_exist1($email);

        $response = array('message' => $message);
        echo json_encode($response);
        
    }

    public function Login_Auth() {    
        $response = array();
        $email = $this->input->post('email');
        $password = sha1($this->input->post('password'));
        $remember = $this->input->post('remember');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('email', 'User Email', 'trim|xss_clean|required|min_length[7]');
        $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            redirect(base_url() . 'login', $this->session->set_flashdata('feedback', 'User Email or Password is Invalid'), 'refresh');        
        } else {
            $login_status = $this->validate_login($email, $password);
            $response['login_status'] = $login_status;
            if ($login_status == 'success') {
                if ($remember) {
                    setcookie('email', $email, time() + (86400 * 30));
                    setcookie('password', $this->input->post('password'), time() + (86400 * 30));
                    redirect(base_url() . 'login', 'refresh');
                } else {
                    if (isset($_COOKIE['email'])) {
                        setcookie('email', '');
                    }
                    if (isset($_COOKIE['password'])) {
                        setcookie('password', '');
                    }                
                    redirect(base_url() . 'login', 'refresh');
                }
            } else {
                $this->session->set_flashdata('feedback', 'Incorrect email or password');
                redirect(base_url() . 'login', 'refresh');
            }
        }
    }

    function validate_login($email = '', $password = '') {
        $credential = array('em_email' => $email, 'em_password' => $password, 'status' => 'ACTIVE');
        $query = $this->login_model->getUserForLogin($credential);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->session->set_userdata('user_login_access', '1');
            $this->session->set_userdata('user_login_id', $row->em_id);
            $this->session->set_userdata('name', $row->first_name);
            $this->session->set_userdata('email', $row->em_email);
            $this->session->set_userdata('user_image', $row->em_image);
            $this->session->set_userdata('user_type', $row->em_role);
            return 'success';
        }
    }

    function logout() {
        $this->session->sess_destroy();
        $this->session->set_flashdata('feedback', 'logged_out');
        redirect(base_url(), 'refresh');
    }

    public function confirm_mail_send($email, $randcode) {
        $config = Array( 
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => 'mail.imojenpay.com', 
            'smtp_pass' => ''
        );       
        $from_email = "imojenpay@imojenpay.com"; 
        $to_email = $email; 
        $this->load->library('email', $config); 
        $this->email->from($from_email, 'Dotdev'); 
        $this->email->to($to_email);
        $this->email->subject('Confirm Your Account'); 
        $message = "Confirm Your Account. Click Here : ".base_url()."Confirm_Account?C=" . $randcode.'</br>'; 
        $this->email->message($message); 

        if ($this->email->send()) { 
            $this->session->set_flashdata('feedback', 'Kindly check your email To reset your password');
        } else {
            $this->session->set_flashdata("feedback", "Error in sending Email."); 
        }            
    }

    public function verification_confirm() {
        $verifycode = $this->input->get('C');
        $userinfo = $this->login_model->GetuserInfoBycode($verifycode);
        if ($userinfo) {
            $data = array('status' => 'ACTIVE', 'confirm_code' => 0);
            $this->login_model->UpdateStatus($verifycode, $data);
            if ($this->db->affected_rows()) {
                $this->session->set_flashdata('feedback', 'Your Account has been confirmed!! now login');
                $this->load->view('backend/login');
            }            
        } else {
            $this->session->set_flashdata('feedback', 'Sorry your account has not been verified');
            $this->load->view('backend/login');          
        }
    }

    public function forgotten_page() {
        $data = array();
        $data['settingsvalue'] = $this->dashboard_model->GetSettingsValue();
        $this->load->view('backend/forgot_password', $data);
    }

    public function forgot_password() {
        $email = $this->input->post('email');
        $checkemail = $this->login_model->Does_email_exists($email);
        if ($checkemail) {
            $randcode = md5(uniqid());
            $data = array('forgotten_code' => $randcode);
            $updatedata = $this->login_model->UpdateKey($data, $email);
            if ($this->db->affected_rows()) {
                $this->send_mail($email, $randcode);
                $this->session->set_flashdata('feedback', 'Kindly check your email To reset your password');
                redirect('Retriev');                
            } else {
                $this->session->set_flashdata('feedback', 'Please enter a valid email address!');
                redirect('Retriev');
            }
        } else {
            $this->session->set_flashdata('feedback', 'Please enter a valid email address!');
            redirect('Retriev');
        }
    }

    public function send_mail($email, $randcode) {
        $config = Array( 
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 25, 
            'smtp_user' => 'mail.imojenpay.com', 
            'smtp_pass' => ''
        );       
        $from_email = "imojenpay@imojenpay.com"; 
        $to_email = $email; 
        $this->load->library('email', $config); 
        $this->email->from($from_email, 'Dotdev'); 
        $this->email->to($to_email);
        $this->email->subject('Reset your password!!Dotdev'); 
        $message = "Your or someone request to reset your password. Click Here : ".base_url()."Reset_password?p=" . $randcode."<br />"; 
        $this->email->message($message); 

        if ($this->email->send()) { 
            $this->session->set_flashdata('feedback', 'Kindly check your email To reset your password');
        } else {
            $this->session->set_flashdata("feedback", "Error in sending Email."); 
        }    
    }

    public function Reset_View() {
        $this->load->helper('form');
        $reset_key = $this->input->get('p');
        if ($this->login_model->Does_Key_exists($reset_key)) {
            $data['key'] = $reset_key;
            $this->load->view('backend/reset_page', $data);
        } else {
            $this->session->set_flashdata('feedback', 'Please enter a valid email address!');
            redirect('Retriev');
        }
    }

    public function Reset_password_validation() {
        $password = $this->input->post('password');
        $confirm = $this->input->post('confirm');
        $key = $this->input->post('reset_key');
        $userinfo = $this->login_model->GetUserInfo($key);
        
        if ($password == $confirm) {
            if ($userinfo->password != sha1($password)) {
                $data = array('forgotten_code' => 0, 'password' => sha1($password));
                $update = $this->login_model->UpdatePassword($key, $data);
                if ($this->db->affected_rows()) {
                    $data['message'] = 'Successfully Updated your password!!';
                    $this->load->view('backend/login', $data);
                }
            } else {
                $this->session->set_flashdata('feedback', 'You entered your old password. Please enter a new password');
                redirect('Reset_password?p=' . $key);            
            }
        } else {
            $this->session->set_flashdata('feedback', 'Password does not match');
            redirect('Reset_password?p=' . $key);
        }
    }

    public function forgetPassword() {
        redirect(base_url() . 'forget_password');
    }
}
