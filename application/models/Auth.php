<?php 
class Auth extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
    }

    function register($email, $password, $nama)
    {
        $data_user = array(
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nama' => $nama
        );
        $this->db->insert('tbl_users', $data_user);
    }

    function login_user($email, $password)
    {
        $query = $this->db->get_where('tbl_users', array('email' => $email));
        if ($query->num_rows() > 0) {
            $data_user = $query->row();
            if (password_verify($password, $data_user->password)) {
                $this->session->set_userdata('email', $email);
                $this->session->set_userdata('nama', $data_user->nama);
                $this->session->set_userdata('is_login', TRUE);
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    function cek_login()
    {
        if (empty($this->session->userdata('is_login'))) {
            redirect('login');
        }
    }

    function get_user_by_email($email)
    {
        $query = $this->db->get_where('tbl_users', array('email' => $email));
        return $query->row();
    }

    function updatePassword($email, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->where('email', $email);
        $this->db->update('tbl_users', array('password' => $hashedPassword));
    }

    function checkCredentials($email, $password)
    {
        $user = $this->get_user_by_email($email);
        if ($user) {
            // User found, check if the old password matches
            if (password_verify($password, $user->password)) {
                return true;
            }
        }
        return false;
    }
}
?>
