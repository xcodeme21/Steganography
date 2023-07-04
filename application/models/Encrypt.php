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
}
?>
