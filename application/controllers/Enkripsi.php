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
        $this->session->set_flashdata('to_encrypt_image', base_url('application/uploads/encrypt/' . $_FILES['gambar_file']['name']));

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
        $rsaChipper = $message;

        return $rsaChipper;
    }

    private function convertToBinary($messageToHide) {
        $binaryMessage = '';

        $messageLength = strlen($messageToHide);
        for ($i = 0; $i < $messageLength; $i++) {
            $char = $messageToHide[$i];
            $ascii = ord($char);
            $binaryMessage .= sprintf("%08b", $ascii);
        }

        return $binaryMessage;
    }

    private function encryptAndEmbed($sourceFile, $outputFile, $messageToHide) {
        // Mengambil konten gambar asli
        $image = imagecreatefromstring(file_get_contents($sourceFile));

        // Mengonversi RSA chipper menjadi binary
        $binaryMessage = $this->convertToBinary($messageToHide);

        // Menyisipkan pesan binary ke dalam gambar menggunakan metode steganografi
        $this->hideMessageInImage($image, $binaryMessage);

        // Menyimpan gambar dengan pesan yang telah disisipkan
        if (!imagepng($image, $outputFile)) {
            echo "Gagal menyimpan gambar dengan pesan yang disisipkan.";
            return;
        }

        // Membebaskan memori yang digunakan oleh objek gambar
        imagedestroy($image);
    }

    private function hideMessageInImage($image, $messageToHide) {
        // Konversi pesan menjadi binary
        $messageBits = $messageToHide;

        // Menyisipkan pesan binary ke dalam bit LSB (Least Significant Bit) dari setiap byte gambar
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $messageLength = strlen($messageBits);
        $messageIndex = 0;

        for ($y = 0; $y < $imageHeight; $y++) {
            for ($x = 0; $x < $imageWidth; $x++) {
                // Mendapatkan warna pixel
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Menyisipkan bit pesan ke dalam bit LSB komponen warna
                if ($messageIndex < $messageLength) {
                    $bit = $messageBits[$messageIndex];
                    $r = ($r & 0xFE) | $bit;
                    $messageIndex++;
                }

                // Menetapkan warna pixel yang telah dimodifikasi
                $modifiedRgb = ($r << 16) | ($g << 8) | $b;
                imagesetpixel($image, $x, $y, $modifiedRgb);

                // Berhenti jika sudah mencapai akhir pesan
                if ($messageIndex >= $messageLength) {
                    break 2;
                }
            }
        }
    }
}
