<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/PhpSpreadsheet/IOFactory.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Enkripsi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Form Enkripsi dan Sisip';

        $this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('session_messages');
        $this->load->view('enkripsi');
        $this->load->view('templates/footer');
    }

    public function sessionValidate() {
        if (!$this->session->userdata('nama')) {
            $this->session->set_flashdata('error', "Session habis");
            redirect('/login'); // Ganti '/login' dengan URL sebenarnya tempat Anda ingin mengarahkan pengguna
        }
    }

    public function process() {
        // Set aturan validasi
        $this->form_validation->set_rules('excel_file', 'File Excel', 'required');
		$this->form_validation->set_rules('pwdfile', 'Public Key', 'required|callback_check_public_key_format');
		$this->form_validation->set_rules('gambar_file', 'File Gambar', 'callback_check_image_format|required');

        // Jalankan validasi
        if ($this->form_validation->run() == FALSE) {
            // Validasi gagal, tampilkan pesan error atau kembali ke halaman sebelumnya
            $this->session->set_flashdata('error', validation_errors());
            redirect('enkripsi');
        } else {
            // Validasi sukses, proses file
            // Mendapatkan data file excel yang diunggah
            $excelFile = $_FILES['excel_file'];
            $publicKey = $this->input->post('pwdfile');

            // Menentukan path untuk menyimpan file excel yang telah dienkripsi
            $encryptedExcelPath = APPPATH . 'uploads/encrypt/' . $excelFile['name'];

            // Mengenkripsi file excel
            $this->encryptExcelFile($excelFile['tmp_name'], $publicKey, $encryptedExcelPath);

            // Mendapatkan data file gambar yang diunggah
            $gambarFile = $_FILES['gambar_file'];

            // Menentukan path untuk menyimpan file gambar yang telah dienkripsi
            $encryptedGambarPath = APPPATH . 'uploads/encrypt/' . $gambarFile['name'];

            // Mengenkripsi file gambar
            $this->encryptGambarFile($gambarFile['tmp_name'], $publicKey, $encryptedGambarPath);

            // Sisipkan file yang telah dienkripsi ke dalam file excel
            $this->sisipkanFileKeExcel($encryptedExcelPath, $encryptedGambarPath);

            // Set pesan flash
            $this->session->set_flashdata('success', 'File berhasil dienkripsi dan disisipkan.');

            // Redirect atau tampilkan pesan berhasil, sesuai kebutuhan
            redirect('enkripsi');
        }
    }

    public function check_public_key_format($publicKey) {
        // Validasi format public key (e,n)
        if (!preg_match('/^\(\d+,\d+\)$/', $publicKey)) {
            $this->form_validation->set_message('check_public_key_format', 'Format Public Key tidak valid. Gunakan format (e,n).');
            return false;
        }
        return true;
    }

    public function check_image_format($imageFile) {
        // Validasi format file gambar (gif|jpg|jpeg|png)
        $allowedFormats = array('gif', 'jpg', 'jpeg', 'png');
        $imageInfo = getimagesize($imageFile['tmp_name']);
        $imageType = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));

        if (!in_array($imageType, $allowedFormats) || $imageInfo === false) {
            $this->form_validation->set_message('check_image_format', 'Format file gambar tidak valid. Gunakan format GIF, JPG, JPEG, atau PNG.');
            return false;
        }
        return true;
    }

    private function encryptExcelFile($filePath, $publicKey, $encryptedFilePath) {
        // Membaca konten file excel
        $content = file_get_contents($filePath);

        // Proses enkripsi menggunakan openssl_encrypt()
        $encryptedContent = openssl_encrypt($content, 'AES-256-CBC', $publicKey, OPENSSL_RAW_DATA);

        // Menyimpan file yang dienkripsi ke lokasi tujuan
        file_put_contents($encryptedFilePath, $encryptedContent);
    }

    private function encryptGambarFile($filePath, $publicKey, $encryptedFilePath) {
        // Membaca konten file gambar
        $content = file_get_contents($filePath);

        // Proses enkripsi menggunakan openssl_encrypt()
        $encryptedContent = openssl_encrypt($content, 'AES-256-CBC', $publicKey, OPENSSL_RAW_DATA);

        // Menyimpan file yang dienkripsi ke lokasi tujuan
        file_put_contents($encryptedFilePath, $encryptedContent);
    }

    private function sisipkanFileKeExcel($excelFilePath, $gambarFilePath) {
        // Buka file excel menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($excelFilePath);

        // Sisipkan informasi file ke dalam file excel
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'Nama File');
        $worksheet->setCellValue('B1', 'Path File');

        // Simpan gambar ke dalam file excel
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Nama Gambar');
        $drawing->setDescription('Deskripsi Gambar');
        $drawing->setPath($gambarFilePath);
        $drawing->setCoordinates('A2');
        $drawing->setWorksheet($worksheet);

        // Simpan file excel yang telah diubah
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($excelFilePath);
    }
}
?>
