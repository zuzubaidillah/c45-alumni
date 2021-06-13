<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_model extends CI_Model
{

    public function get_kolom_tabel($sql)
    {
        $query = $this->db->query($sql);

        if ($query->num_rows() == 0) {
            return 0;
        } else {
            return $query->result();
        }
    }
    public function get_sql($sql)
    {
        $this->db->query($sql);
        return 1;
    }

    public function count_kolom_tabel($sql)
    {
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    public function hapusTabel($sql)
    {
        $query = $this->db->query($sql);

        return 0;
    }

    public function hapusdata($tabel, $data)
    {
        // $this->db->error();
        $query = $this->db->delete($tabel, $data);

        if ($query == TRUE) {
            return 1;
        } else {
            return 0;
        }
    }

    public function tambahdata($tabel, $data)
    {
        $query = $this->db->insert($tabel, $data);

        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    public function editdata($tabel, $data, $where, $id)
    {
        $this->db->where($where, $id);
        $query = $this->db->update($tabel, $data);

        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }
}
