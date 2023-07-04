<?php 
class Encrypt extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
    }

    function create($fileName, $desc)
    {
        $data = array(
            'nama_file' => $fileName,
            'keterangan' => $desc
        );
        $this->db->insert('tbl_enkrip', $data);
    }

	function checkData($fileName)
    {
        $data = $this->check_data_by_file_name($fileName);
        if ($data) {
            return true;
        }
        return false;
    }

	function check_data_by_file_name($fileName)
    {
        $query = $this->db->get_where('tbl_enkrip', array('nama_file' => $fileName));
        return $query->row();
    }
}
?>
