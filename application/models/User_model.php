<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function does_email_exist1($email) {
        // Query the database to check if the email exists
        $query = $this->db->get_where('employee', array('em_email' => $email));

        // Check if the email exists in the database
        if ($query->num_rows() > 0) {
            // Generate a new password
            $new_password = $this->generate_password();

            // Update the password in the database for the user with the provided email
            $this->update_password($email, $new_password);

            // Send an email with the new password
            $this->send_reset_email($email, $new_password);

            // Return a success message
            return "A new password has been sent to your email.";
        } else {
            return "Email does not exist!";
        }
    }

    private function generate_password($length = 10) {
        // Generate a random password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    private function update_password($email, $password) {
        // Update the password in the database for the user with the provided email
        $this->db->where('em_email', $email);
        $this->db->update('employee', array('em_password' => sha1($password)));
    }

    private function send_reset_email($email, $password) {
        $message = "Your new password is: $password";

        $this->load->library('email');
        $this->email->from('your-email@gmail.com', 'Your Name');
        $this->email->to($email);
        $this->email->subject('Password Reset Request');
        $this->email->message($message);

        $this->email->send();
    }
}
