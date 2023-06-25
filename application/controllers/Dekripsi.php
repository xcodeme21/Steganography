<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dekripsi extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Form Dekripsi dan Ekstraksi';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dekripsi');
        $this->load->view('templates/footer');
    }

}