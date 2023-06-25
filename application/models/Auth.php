<?php 
class Auth extends CI_Model 
{
	
	public function __construct()
	{
        parent::__construct();
	}
 
	function register($email,$password,$nama)
	{
		$data_user = array(
			'email'=>$email,
			'password'=>password_hash($password,PASSWORD_DEFAULT),
			'nama'=>$nama
		);
		$this->db->insert('tbl_users',$data_user);
	}
}
?>
