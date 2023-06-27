<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dekripsi extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
	}


    public function index()
    {
        $data['title'] = 'Form Dekripsi dan Ekstraksi';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('session_messages');
        $this->load->view('dekripsi');
        $this->load->view('templates/footer');
    }

	public function sessionValidate() {
		if (!$this->session->userdata('nama')) {
			$this->session->set_flashdata('error', "Session habis");
			redirect('/login'); // Replace '/redirect-path' with the actual URL where you want to redirect the user
		}
	}

	public function process() {
		$this->load->library('form_validation');

		$this->form_validation->set_rules('private_key', 'Private Key', 'required|callback_validate_rsa_private_key');

		if ($this->form_validation->run() == FALSE) {
			// Validasi gagal, tampilkan pesan error
			$this->session->set_flashdata('error', validation_errors());
			redirect('dekripsi');
		} else {
			// Validasi berhasil, lanjutkan dengan proses upload gambar

			// Konfigurasi upload
			$config['upload_path'] = APPPATH . 'uploads/'; // Tentukan folder penyimpanan image
			$config['allowed_types'] = 'gif|jpg|jpeg|png'; // Tentukan jenis file yang diizinkan
			$config['max_size'] = 2048; // Tentukan ukuran maksimum file (dalam kilobita)

			// Load library Upload dan inisialisasi konfigurasi
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('image')) {
				// Jika proses upload gagal, tampilkan pesan error
				$error = $this->upload->display_errors();
				$this->session->set_flashdata('error', $error);
				redirect('dekripsi');
			} else {
				// Jika proses upload berhasil, ambil informasi file yang diupload
				$uploadData = $this->upload->data();

				// Dapatkan path file yang diupload
				$filePath = 'uploads/' . $uploadData['file_name'];

				$this->session->set_flashdata('success', "Image berhasil diupload. Path file: " . $filePath);
				redirect('dekripsi');
			}
		}
	}

	public function validate_rsa_private_key($str) {
		if (preg_match('/^\(\d+,\d+\)$/', $str) !== 1) {
			$this->form_validation->set_message('validate_rsa_private_key', 'The {field} field must be in the format (d,n).');
			return false;
		}
		return true;
	}

}
