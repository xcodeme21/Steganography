<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dekripsi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
    }

    public function index() {
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
            $this->session->set_flashdata('error', 'Session habis');
            redirect('/login'); // Ganti '/login' dengan URL sebenarnya tempat Anda ingin mengarahkan pengguna
        }
    }

    public function process() {
        // Mendapatkan data dari formulir
        $privateKey = $this->input->post('private_key');

        // Validasi format private_key (misalnya: (1234,1378))
        if (!preg_match('/^\(\d+,\d+\)$/', $privateKey)) {
            $this->session->set_flashdata('error', 'Format private_key tidak valid. Harap masukkan format yang benar, misalnya: (1234,1378)');
            redirect('dekripsi');
            return;
        }

        // Memuat file gambar
        $imageFile = $_FILES['image']['tmp_name'];

        // Mendapatkan pesan yang disisipkan dari gambar
        $hiddenMessage = $this->getHiddenMessage($imageFile);

        // Menampilkan pesan sukses atau error
        if ($hiddenMessage !== false) {
            $this->session->set_flashdata('hidden_message', $hiddenMessage);
            $this->session->set_flashdata('success', 'Berhasil mengekstrak pesan tersembunyi dari gambar.');
            redirect('dekripsi');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengekstrak pesan tersembunyi dari gambar.');
            redirect('dekripsi');
        }
    }

    private function getHiddenMessage($imageFile) {
		// Load the image
		$image = imagecreatefromstring(file_get_contents($imageFile));

		// Get the image size
		$width = imagesx($image);
		$height = imagesy($image);

		$hiddenMessage = '';

		// Extract hidden message from the LSB of each pixel
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$rgb = imagecolorat($image, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				// Extract the least significant bit from each color component
				$redLSB = $r & 1;
				$greenLSB = $g & 1;
				$blueLSB = $b & 1;

				// Combine the LSBs to form the binary representation of the hidden message
				$binaryChar = $redLSB . $greenLSB . $blueLSB;

				// Append the binary character to the hidden message
				$hiddenMessage .= $binaryChar;

				// Break the loop if the end of the message is reached
				if (substr($hiddenMessage, -16) === '0000000000000000') {
					break 2;
				}
			}
		}

		return $hiddenMessage;
	}

}
