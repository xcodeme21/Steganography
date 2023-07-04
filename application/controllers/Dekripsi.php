<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Dekripsi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Form Dekripsi dan Ekstrak';

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

        // Mendapatkan file gambar
        $imageFile = $_FILES['image']['tmp_name'];

        // Membaca gambar dan mendekripsi pesan yang disisipkan
        $decryptedMessage = $this->decryptAndExtract($imageFile, $privateKey);

        // Mengkonversi pesan yang telah didekripsi menjadi array data
        $data = $this->convertToArray($decryptedMessage);

        // Membuat file CSV dari data
        $csvFile = $this->createCSV($data);

        // Menyimpan file CSV
        $imageName = $_FILES['image']['name'];
        $outputFile = APPPATH . 'uploads/decrypt/' . pathinfo($imageName, PATHINFO_FILENAME) . '.csv';
        write_file($outputFile, $csvFile);

        // Mengirim file CSV ke browser untuk diunduh
        $this->downloadFile($outputFile);
    }

    private function decryptAndExtract($imageFile, $privateKey) {
        // Mendapatkan konten gambar
        $image = imagecreatefromstring(file_get_contents($imageFile));

        // Mendapatkan pesan yang disisipkan dari gambar menggunakan metode steganografi
        $hiddenMessage = $this->extractMessageFromImage($image);

        // Mendekripsi pesan yang disisipkan menggunakan kunci privat RSA
        $decryptedMessage = $this->decryptWithRSA($hiddenMessage, $privateKey);

        // Membebaskan memori yang digunakan oleh objek gambar
        imagedestroy($image);

        return $decryptedMessage;
    }

    private function extractMessageFromImage($image) {
        // Mendapatkan pesan binary dari bit LSB (Least Significant Bit) setiap byte gambar
        $binaryMessage = '';

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        for ($y = 0; $y < $imageHeight; $y++) {
            for ($x = 0; $x < $imageWidth; $x++) {
                // Mendapatkan warna pixel
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;

                // Mendapatkan bit LSB dari komponen warna merah
                $bit = $r & 1;

                // Mengonversi bit menjadi karakter
                $binaryMessage .= $bit;

                // Berhenti jika sudah mencapai akhir pesan
                if (substr($binaryMessage, -8) === '00000000') {
                    break 2;
                }
            }
        }

        // Mengonversi pesan binary menjadi string
        $message = '';
        $messageLength = strlen($binaryMessage);

        for ($i = 0; $i < $messageLength; $i += 8) {
            $byte = substr($binaryMessage, $i, 8);
            $char = chr(bindec($byte));
            $message .= $char;
        }

        return $message;
    }

    private function decryptWithRSA($messageToDecrypt, $privateKey) {
        // Mendapatkan nilai d dan n dari private key
        $privateKey = str_replace(['(', ')'], '', $privateKey);
        list($d, $n) = explode(',', $privateKey);

        // Mendekripsi pesan menggunakan kunci privat RSA
        $decryptedMessage = '';

        $messageLength = strlen($messageToDecrypt);
        for ($i = 0; $i < $messageLength; $i++) {
            $char = $messageToDecrypt[$i];
            $ascii = ord($char);

            // Mendekripsi karakter menggunakan formula c = m^d % n
            $decryptedAscii = bcpowmod($ascii, $d, $n);
            $decryptedChar = chr($decryptedAscii);

            $decryptedMessage .= $decryptedChar;
        }

        return $decryptedMessage;
    }

    private function convertToArray($decryptedMessage) {
        // Memecah pesan menjadi array data
        $data = explode("\n", $decryptedMessage);
        $dataArray = [];

        foreach ($data as $row) {
            $rowData = explode(',', $row);
            $dataArray[] = $rowData;
        }

        return $dataArray;
    }

    private function createCSV($data) {
        // Membuat file CSV dari data
        $csv = '';
        foreach ($data as $rowData) {
            $csv .= implode(',', $rowData) . "\r\n";
        }
        return $csv;
    }

    private function downloadFile($filePath) {
        // Mengirim file ke browser untuk diunduh
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "File tidak ditemukan.";
        }
    }
}
