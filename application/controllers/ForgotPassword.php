<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ForgotPassword extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
		$this->load->model('auth');
    }

    public function index() {
        $data['title'] = 'Forgot Password';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('forgotpassword');
    }

	public function sessionValidate() {
		if ($this->session->userdata('nama')) {
			redirect('/dashboard');
		}
	}

    public function process() {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('old_password', 'Old Password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            // Form validation failed
            $this->session->set_flashdata('error', validation_errors());
            redirect('forgotpassword');
        } else {
            // Form validation succeeded
            $email = $this->input->post('email');
            $oldPassword = $this->input->post('old_password');
            $newPassword = $this->input->post('new_password');

            // Check if email and old password are valid in the database
            $isValid = $this->auth->checkCredentials($email, $oldPassword);

            if ($isValid) {
                // Update the password in the database
                $this->auth->updatePassword($email, $newPassword);

                // Set success flashdata message
                $this->session->set_flashdata('success', 'Password updated successfully.');
                redirect('login');
            } else {
                // Set error flashdata message
                $this->session->set_flashdata('error', 'Invalid email or old password.');
                redirect('forgotpassword');
            }
        }
    }
}
?>
