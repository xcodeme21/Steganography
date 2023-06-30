<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Enkripsi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
    }

    public function index() {
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
        $this->form_validation->set_rules('pwdfile', 'Public Key', 'required|callback_check_public_key_format');

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

            // Menyimpan file excel yang diunggah ke lokasi tujuan tanpa enkripsi
            move_uploaded_file($excelFile['tmp_name'], $encryptedExcelPath);

            // Mendapatkan data file gambar yang diunggah
            $gambarFile = $_FILES['gambar_file'];

            // Menentukan path untuk menyimpan file gambar
            $gambarPath = APPPATH . 'uploads/encrypt/' . $gambarFile['name'];

            // Menyimpan file gambar yang diunggah ke lokasi tujuan tanpa enkripsi
            move_uploaded_file($gambarFile['tmp_name'], $gambarPath);

            // Set pesan flash
            $this->session->set_flashdata('success', 'File berhasil dienkripsi dan disisipkan.');

            // Encrypt the image file
            $this->encryptGambarFile($gambarPath, $publicKey, $gambarPath);

            // Update the Excel file with the image name and path
            $this->updateImagePathInExcel($encryptedExcelPath, $gambarFile['name'], $gambarPath);

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
        // Load the Excel file using PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);

        // Save the Excel file as encrypted
        $spreadsheet->getSecurity()->setLockWindows(true);
        $spreadsheet->getSecurity()->setLockStructure(true);
        $spreadsheet->getSecurity()->setWorkbookPassword($publicKey);
        $spreadsheet->save($encryptedFilePath);
    }

    private function encryptGambarFile($filePath, $publicKey, $encryptedFilePath) {
        // Read the image file content
        $content = file_get_contents($filePath);

        // Generate a random IV
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

        // Encrypt the content using openssl_encrypt()
        $encryptedContent = openssl_encrypt($content, 'AES-256-CBC', $publicKey, OPENSSL_RAW_DATA, $iv);

        // Save the encrypted file to the destination path
        file_put_contents($encryptedFilePath, $encryptedContent);
    }

   private function updateImagePathInExcel($excelFilePath, $imageName, $imagePath) {
		// Load the Excel file using PhpSpreadsheet
		$spreadsheet = IOFactory::load($excelFilePath);
		$worksheet = $spreadsheet->getActiveSheet();

		// Set the headers in cells A1 and B1
		$worksheet->setCellValue('A1', 'Nama File');
		$worksheet->setCellValue('B1', 'Path File');

		// Count the number of existing rows in the worksheet
		$lastRow = $worksheet->getHighestRow();
		$newRow = $lastRow + 1;

		// Insert the image name and path into the worksheet
		$worksheet->setCellValue('A' . $newRow, $imageName);
		$worksheet->setCellValue('B' . $newRow, $imagePath);

		// Save the modified Excel file
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save($excelFilePath);
	}

}
