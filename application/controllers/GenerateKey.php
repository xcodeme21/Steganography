<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateKey extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Form Membuat Kunci';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('generatekey');
        $this->load->view('templates/footer');
    }

	public function generate()
	{
		$p = $this->input->post('inputp');
		$q = $this->input->post('inpuip');

		$n = $p * $q;
		$m = ($p - 1) * ($q - 1);

		// Calculate e
		$e = $this->calculateE($m);

		// Calculate d
		$d = $this->calculateD($e, $m);

		$privateKey = "($d, $n)";
		$publicKey = "($e, $n)";

		$result = array(
			"n" => $n,
			"m" => $m,
			"e" => $e,
			"d" => $d,
			"privateKey" => $privateKey,
			"publicKey" => $publicKey
		);

		$this->session->set_flashdata('pesan', 'Your message here');
		
		$this->output->set_content_type('application/json');

		$this->output->set_output(json_encode($result));
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
