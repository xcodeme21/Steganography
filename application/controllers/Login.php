<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('auth');
	}

    public function index()
    {
        $data['title'] = 'Login';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('login');
    }

	public function sessionValidate() {
		if ($this->session->userdata('nama')) {
			redirect('/dashboard');
		}
	}
	
 
	public function process()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		if($this->auth->login_user($email,$password))
		{
			$this->session->set_flashdata('success','Login berhasil');
			redirect('/dashboard');
		}
		else
		{
			$this->session->set_flashdata('error','Email & Password salah');
			redirect('login');
		}
	}
 
	public function logout()
	{
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('nama');
		$this->session->unset_userdata('is_login');
		$this->session->set_flashdata('success','Logout berhasil');
		redirect('login');
	}
}
