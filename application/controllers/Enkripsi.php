<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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
        // Mendapatkan data dari formulir
        $publicKey = $this->input->post('pwdfile');
        
        // Validasi format pwdfile (misalnya: (1234,1378))
        if (!preg_match('/^\(\d+,\d+\)$/', $publicKey)) {
            echo "Format pwdfile tidak valid. Harap masukkan format yang benar, misalnya: (1234,1378)";
            return;
        }
        
        // Memuat file Excel
        $excelFile = $_FILES['excel_file']['tmp_name'];
        
        // Menggunakan library PhpSpreadsheet untuk membaca file Excel
       	$reader = IOFactory::createReaderForFile($excelFile);
		$spreadsheet = $reader->load($excelFile);

		// Mendapatkan data dari file Excel
		$data = $this->getDataFromExcel($spreadsheet);

		// Mengonversi data menjadi RSA chipper
		$messageToHide = $this->convertToRSA($data, $publicKey);

        
        // Melanjutkan proses enkripsi dan penyisipan
        $gambarFile = $_FILES['gambar_file']['tmp_name'];
        $outputFile = APPPATH . 'uploads/encrypt/' . $_FILES['gambar_file']['name'];
        
        // Mengenkripsi pesan ke dalam gambar
        $this->encryptAndEmbed($gambarFile, $outputFile, $messageToHide);
        
        $this->session->set_flashdata('success', "Enkripsi dan penyisipan berhasil!");
        redirect('enkripsi');
    }
    
    private function getDataFromExcel($spreadsheet) {
        $data = array();
        
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $data[] = $rowData[0];
        }
        
        return $data;
    }
    
    private function convertToRSA($data, $publicKey) {
        // mengonversi data menjadi RSA chipper
        $message = serialize($data); // Mengubah data menjadi string terformat
        $rsaChipper = $message . " (encrypted with RSA)";
        
        return $rsaChipper;
    }
    
    private function encryptAndEmbed($sourceFile, $outputFile, $messageToHide) {
        // Mengambil konten gambar asli
        $imageContents = file_get_contents($sourceFile);

        // Menyisipkan pesan ke dalam gambar menggunakan metode steganografi
        $imageWithMessage = $this->hideMessageInImage($imageContents, $messageToHide);

        // Menyimpan gambar dengan pesan yang telah disisipkan
        file_put_contents($outputFile, $imageWithMessage);
    }

    private function hideMessageInImage($imageContents, $messageToHide) {
        // Mendeklarasikan variabel untuk menyimpan gambar hasil penyisipan pesan
        $imageWithMessage = '';

        // Konversi pesan menjadi bitstream
        $messageBits = $this->stringToBits($messageToHide);

        // Menyisipkan pesan ke dalam bit LSB (Least Significant Bit) dari setiap byte gambar
        $imageLength = strlen($imageContents);
        $messageLength = strlen($messageBits);
        $messageIndex = 0;

        for ($i = 0; $i < $imageLength; $i++) {
            // Mendapatkan byte gambar
            $byte = ord($imageContents[$i]);

            // Menyisipkan bit pesan ke dalam bit LSB byte gambar
            if ($messageIndex < $messageLength) {
                $bit = $messageBits[$messageIndex];
                $byte = ($byte & 0xFE) | $bit;
                $messageIndex++;
            }

            // Menyimpan byte yang telah dimodifikasi ke dalam gambar hasil penyisipan pesan
            $imageWithMessage .= chr($byte);
        }

        return $imageWithMessage;
    }

    private function stringToBits($string) {
        $bits = '';

        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; $i++) {
            $char = $string[$i];
            $ascii = ord($char);
            $bits .= sprintf("%08b", $ascii);
        }

        return $bits;
    }
}
