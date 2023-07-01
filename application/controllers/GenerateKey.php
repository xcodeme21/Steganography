<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateKey extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Form Membuat Kunci';

		$this->sessionValidate();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('session_messages');
        $this->load->view('generatekey');
        $this->load->view('templates/footer');
    }

	public function sessionValidate() {
		if (!$this->session->userdata('nama')) {
			$this->session->set_flashdata('error', "Session habis");
			redirect('/login'); // Replace '/redirect-path' with the actual URL where you want to redirect the user
		}
	}

	public function generate()
	{
		$p = $this->input->post('inputp');
		$q = $this->input->post('inpuip');

		if (!$this->isPrime($p) || !$this->isPrime($q)) {
			$this->session->set_flashdata('error', 'Hanya boleh bilangan prima!');
			redirect('generatekey');
		}

		$n = $p * $q;
		$m = ($p - 1) * ($q - 1);

		// Calculate e
		$e = $this->calculateE($m);

		// Calculate d
		$d = $this->calculateD($e, $m);

		$privateKey = "($d,$n)";
		$publicKey = "($e,$n)";

		// Set the 
		$this->session->set_flashdata('p', $p);
		$this->session->set_flashdata('q', $q);
		$this->session->set_flashdata('n', $n);
		$this->session->set_flashdata('m', $m);
		$this->session->set_flashdata('e', $e);
		$this->session->set_flashdata('d', $d);
		$this->session->set_flashdata('privateKey', $privateKey);
		$this->session->set_flashdata('publicKey', $publicKey);
		$this->session->set_flashdata('success', "Berhasil generate key");

		// Redirect to the desired page
		redirect('generatekey');
	}

	public function isPrime($number)
	{
		// 0 and 1 are not prime numbers
		if ($number <= 1) {
			return false;
		}

		// Check for divisibility from 2 to sqrt(number)
		for ($i = 2; $i * $i <= $number; $i++) {
			if ($number % $i == 0) {
				return false;
			}
		}

		return true;
	}

	
	// Function to calculate the greatest common divisor (GCD) using Euclidean algorithm
	private function gcd($a, $b)
    {
        if ($b == 0) {
            return $a;
        }
        return $this->gcd($b, $a % $b);
    }

	function calculateE($m)
	{
		for ($e = 2; $e < $m; $e++) {
			if ($this->gcd($e, $m) == 1) {
				return $e;
			}
		}
		return null; // No valid e found
	}


	// Function to calculate the value of d
	function calculateD($e, $m)
	{
		$d = 1;
		while (true) {
			if (($d * $e) % $m == 1) {
				return $d;
			}
			$d++;
		}
	}




}
