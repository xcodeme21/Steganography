<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('auth');
	}

    public function index()
    {
        $data['title'] = 'Signup';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('register');
		$this->load->model('auth');
    }

	public function sessionValidate() {
		if ($this->session->userdata('nama')) {
			redirect('/dashboard');
		}
	}
 
	public function process()
	{
		$this->form_validation->set_rules('email', 'email','trim|required|min_length[1]|max_length[255]|is_unique[tbl_users.email]');
		$this->form_validation->set_rules('password', 'password','trim|required|min_length[6]|max_length[255]');
		$this->form_validation->set_rules('nama', 'nama','trim|required|min_length[1]|max_length[255]');
		if ($this->form_validation->run()==true)
	   	{
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$nama = $this->input->post('nama');
			$this->auth->register($email,$password,$nama);
			$this->session->set_flashdata('success','Proses Pendaftaran User Berhasil');
			redirect('login');
		}
		else
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect('register');
		}
	}
}
