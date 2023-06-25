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

        $this->load->view('templates/header', $data);
        $this->load->view('login');
    }
	
 
	public function process()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		if($this->auth->login_user($email,$password))
		{
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
		redirect('login');
	}
}
