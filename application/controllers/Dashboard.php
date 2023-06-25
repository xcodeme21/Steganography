<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Dashboard';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('session_messages');
        $this->load->view('dashboard');
        $this->load->view('templates/footer');
    }

	public function sessionValidate() {
		if (!$this->session->userdata('nama')) {
			$this->session->set_flashdata('error', "Session habis");
			redirect('/login'); // Replace '/redirect-path' with the actual URL where you want to redirect the user
		}
	}
}
