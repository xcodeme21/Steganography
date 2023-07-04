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
        $message_to_hide = $this->convertToRSA($data, $publicKey);
        $binary_message = $this->toBin($message_to_hide);
        $message_length = strlen($binary_message);

        // Melanjutkan proses enkripsi dan penyisipan
        $src = $_FILES['gambar_file']['tmp_name'];
        $srcFileType = exif_imagetype($src);

        // Determine the imagecreatefrom function based on the file type
        $imagecreatefromFunction = '';
		switch ($srcFileType) {
			case IMAGETYPE_JPEG:
				$imagecreatefromFunction = 'imagecreatefromjpeg';
				break;
			case IMAGETYPE_PNG:
				$imagecreatefromFunction = 'imagecreatefrompng';
				break;
			case IMAGETYPE_GIF:
				$imagecreatefromFunction = 'imagecreatefromgif';
				break;
			// Add additional cases for other supported image types as needed
			default:
				echo "The uploaded file is not a valid image.";
				return;
		}


        // Determine the output file path and name
        $outputPath = APPPATH . 'uploads/encrypt/';
        $outputFilename = uniqid() . '.png';
        $outputFile = $outputPath . $outputFilename;

        // Create the image resource
        $im = call_user_func($imagecreatefromFunction, $src);

        $imageWidth = imagesx($im);
        $imageHeight = imagesy($im);

        for ($x = 0; $x < $message_length; $x++) {
			$y = $x;

			// Check if coordinates are within image bounds
			if ($x >= $imageWidth) {
				$y += floor($x / $imageWidth);
				$x = $x % $imageWidth;
			}

			// Check if coordinates are within image bounds
			if ($x >= $imageWidth || $y >= $imageHeight) {
				echo "Coordinates are out of bounds";
				break;
			}

			$rgb = imagecolorat($im, $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			$newR = $r;
			$newG = $g;

			// Replace the least significant bit of blue component with the encrypted message bit
			$newB = ($b & 0xFE) | $binary_message[$x];

			$new_color = imagecolorallocate($im, $newR, $newG, $newB);
			imagesetpixel($im, $x, $y, $new_color);
		}


        // Save the encrypted image
        imagepng($im, $outputFile);
        imagedestroy($im);

        // Download the encrypted image
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: Binary');
        header('Content-disposition: attachment; filename="' . $outputFilename . '"');
        readfile($outputFile);

        // Remove the temporary file
        unlink($outputFile);
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
        // Mengonversi data menjadi string terformat
        $message = "";
        foreach ($data as $row) {
            foreach ($row as $cell) {
                if (!empty($cell)) {
                    $message .= $cell . ", ";
                }
            }
        }
        $message = rtrim($message, ", "); // Menghapus koma dan spasi terakhir
        $rsaChipper = $message;

        return $rsaChipper;
    }

    private function toBin($str) {
        $str = (string) $str;
        $l = strlen($str);
        $result = '';
        while ($l--) {
            $result = str_pad(decbin(ord($str[$l])), 8, "0", STR_PAD_LEFT) . $result;
        }
        return $result;
    }

    private function toString($binary) {
        return pack('H*', base_convert($binary, 2, 16));
    }
}
