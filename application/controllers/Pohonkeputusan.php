<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pohonkeputusan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = "Dashboard";
        $data['judul'] = "Dashboard";
        $data['side'] = "Dashboard";
        $data['acdashboard'] = true;

        echo "<h2>Pohon Keputusan Menggunakan Algoritma C45</h2>";
		echo "<font face='Courier New' size='2'>";
		echo "<a style='color:red; margin-bottom:3px;' href='http://localhost/c45-alumni/'>Hitung Lagi</a><br>
			<input style='float:right; margin-top:-27px; margin-bottom:3px;' type=button value='Hapus Pohon Keputusan' onclick=\"window.location.href='hapus-pohon.html';\"><hr><br>";
		echo $this->generatePohonC45('0', 0);
		echo "</font>";


        // $this->template->load('template', 'tbl_alumni/pohonkeputusan', $data);
    }

    function generatePohonC45($idparent, $spasi)
    {
        $result = "SELECT * FROM pohon_keputusan_c45 WHERE id_parent= '$idparent'";
        $row = $this->M_model->get_kolom_tabel($result);
        if ($row !== 0) {
            foreach ($row as $krow) {
                for ($i = 1; $i <= $spasi; $i++) {
                    echo "|&nbsp;&nbsp;";
                }

                if ($krow->keputusan === 'berhasil') {
                    $keputusan = "<font color=green>$krow->keputusan</font>";
                } elseif ($krow->keputusan === 'belum berhasil') {
                    $keputusan = "<font color=red>$krow->keputusan</font>";
                } elseif ($krow->keputusan === '?') {
                    $keputusan = "<font color=blue>$krow->keputusan</font>";
                } else {
                    $keputusan = "<b>$krow->keputusan</b>";
                }
                echo "<font color=red>$krow->atribut</font> = $krow->nilai_atribut (berhasil = $krow->jml_berhasil, belum berhasil = $krow->jml_belum_berhasil) : <b>$keputusan</b><br>";

                /*panggil dirinya sendiri*/
                $this->generatePohonC45($krow->id, $spasi + 1);
            }
            // return $hasil_pohonkeputusan;
        }
    }
}
