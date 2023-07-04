<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dekripsi extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('form_validation');
		$this->load->model('encrypt');
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
        $src = $_FILES['image']['tmp_name'];
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

        // Create the image resource
        $im = call_user_func($imagecreatefromFunction, $src);

		$real_message = '';
		for($x=0;$x<200;$x++){
			$y = $x;
			$rgb = imagecolorat($im,$x,$y);
			$r = ($rgb >>16) & 0xFF;
			$g = ($rgb >>8) & 0xFF;
			$b = $rgb & 0xFF;
			
			$blue = $this->toBin($b);
			$real_message .= $blue[strlen($blue)-1];
		}
		$real_message = $this->toString($real_message);

		$fileName = $_FILES['image']['name'];
		$checkData=$this->encrypt->checkData($fileName);
		if($checkData) {
			$data=$this->encrypt->check_data_by_file_name($fileName);
			$string=$data->keterangan;

			// Pisahkan string menjadi array berdasarkan koma
			$data = explode(", ", $string);

			// Buat objek Spreadsheet
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			if (count($data) > 1) {
				// Tulis data ke dalam setiap baris di file Excel jika ada koma
				foreach ($data as $key => $value) {
					$column = 'A';  // Tulis pada kolom A
					$row = $key + 1; // Mulai dari baris pertama
					$sheet->setCellValue($column . $row, $value);
				}
			} else {
				// Tulis data pada baris pertama jika tidak ada koma
				$column = 'A';  // Tulis pada kolom A
				$row = 1; // Baris pertama
				$sheet->setCellValue($column . $row, $string);
			}

			// Generate nama file yang unik
			$filename = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

			// Simpan file Excel
			$writer = new Xlsx($spreadsheet);
			$writer->save(APPPATH . 'uploads/decrypt/' . $filename);

			// Set header dan kirim file sebagai respons download
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . $filename . '"');
			readfile(APPPATH . 'uploads/decrypt/' . $filename);

			// Hapus file setelah dikirim
			//unlink(APPPATH . 'uploads/' . $filename);
		} else {
			$this->session->set_flashdata('error','Dekripsi gagal !');
			return "/dekripsi";
		}
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
