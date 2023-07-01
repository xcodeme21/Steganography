<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
            $this->session->set_flashdata('error', "Session habis");
            redirect('/login'); // Ganti '/login' dengan URL sebenarnya tempat Anda ingin mengarahkan pengguna
        }
    }

    public function process() {
        // Mendapatkan data dari formulir
        $privateKey = $this->input->post('private_key');
        
        // Validasi format private_key (misalnya: (1234,1378))
        if (!preg_match('/^\(\d+,\d+\)$/', $privateKey)) {
            echo "Format private_key tidak valid. Harap masukkan format yang benar, misalnya: (1234,1378)";
            return;
        }
        
        // Memuat file gambar
        $imageFile = $_FILES['image']['tmp_name'];
        
        // Membaca file gambar menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($imageFile);
        
        // Mendapatkan pesan yang disisipkan dari gambar
        $hiddenMessage = $this->getHiddenMessage($spreadsheet);
        
        // Mendekripsi pesan menggunakan private_key
        $decryptedMessage = $this->decryptWithRSA($hiddenMessage, $privateKey);
        
        // Menyimpan pesan ke dalam file teks
        $outputFile = APPPATH . 'uploads/decrypt/output.txt';
        write_file($outputFile, $decryptedMessage); // Menggunakan File Helper untuk menulis file
        
        // Menampilkan pesan sukses atau error
        echo "Dekripsi dan ekstraksi berhasil! Pesan telah disimpan dalam file output.txt";
    }
    
    private function getHiddenMessage($spreadsheet) {
        $worksheet = $spreadsheet->getActiveSheet();
        $drawing = $worksheet->getDrawingCollection()[0];
        
        // Mendapatkan deskripsi/pesan yang disisipkan dalam gambar
        $hiddenMessage = $drawing->getDescription();
        
        return $hiddenMessage;
    }
    
    private function decryptWithRSA($encryptedMessage, $privateKey) {
        // Implementasikan logika Anda di sini untuk mendekripsi pesan menggunakan private_key RSA
        // ...
        // Misalnya, Anda dapat menggunakan library RSA yang tersedia atau mengimplementasikan algoritma RSA sendiri
        
        // Contoh sederhana untuk mendekripsi pesan dengan private_key RSA
        $decryptedMessage = $encryptedMessage . " (decrypted with RSA)";
        
        return $decryptedMessage;
    }
}
