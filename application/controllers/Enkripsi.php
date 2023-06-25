<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enkripsi extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Form Enkripsi dan Sisip';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('enkripsi');
        $this->load->view('templates/footer');
    }


}