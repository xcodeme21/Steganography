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
			// Validation failed, display error message
			$this->session->set_flashdata('error', validation_errors());
			redirect('dekripsi');
		} else {
			// Validation succeeded, continue with image upload and decryption

			// Configuration for file upload
			$config['upload_path'] = APPPATH . 'uploads/'; // Specify the folder for storing the image
			$config['allowed_types'] = 'gif|jpg|jpeg|png'; // Specify the allowed file types
			$config['max_size'] = 2048; // Specify the maximum file size (in kilobytes)

			// Load Upload library and initialize the configuration
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('image')) {
				// If the upload process fails, display error message
				$error = $this->upload->display_errors();
				$this->session->set_flashdata('error', $error);
				redirect('dekripsi');
			} else {
				// If the upload process succeeds, get the uploaded file information
				$uploadData = $this->upload->data();

				// Get the file path of the uploaded image
				$filePath = $uploadData['full_path'];

				// Perform decryption using the private key
				$privateKey = $this->input->post('private_key');
				$decryptedFilePath = $this->decryptImage($filePath, $privateKey, $uploadData["full_name"]);

				if ($decryptedFilePath) {
					$this->session->set_flashdata('success', "Image successfully decrypted. Decrypted file path: " . $decryptedFilePath);
				} else {
					$this->session->set_flashdata('error', "Failed to decrypt the image.");
				}

				// Redirect back to the decryption form
				redirect('dekripsi');
			}
		}
	}

	private function decryptImage($filePath, $privateKey, $fullName) {
		// Parse the private key values
		$privateKey = trim($privateKey, '()');
		list($d, $n) = explode(',', $privateKey);

		// Read the stego image data
		$imageData = file_get_contents($filePath);

		// Decrypt the stego image using RSA decryption algorithm
		$decryptedData = ''; // Initialize the variable to store the decrypted image data

		// Perform RSA decryption on each pixel/byte of the stego image
		for ($i = 0; $i < strlen($imageData); $i++) {
			$byte = ord($imageData[$i]); // Get the ASCII value of the byte

			// Perform RSA decryption on the byte using the private key
			$decryptedByte = bcpowmod($byte, $d, $n); // Use appropriate RSA decryption function

			$decryptedData .= chr($decryptedByte); // Append the decrypted byte to the decrypted data
		}

		// Save the decrypted image data to a new file
		$decryptedFilePath = APPPATH . 'uploads/'.$fullName; // Replace with the actual path and filename of the decrypted image
		file_put_contents($decryptedFilePath, $decryptedData);

		if (file_exists($decryptedFilePath)) {
			return $decryptedFilePath;
		} else {
			return false;
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
