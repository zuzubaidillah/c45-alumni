<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function index()
    {
        echo "<a style='color:red; margin-bottom:3px;' href='http://localhost/c45-alumni/index.php/pohonkeputusan'>LIHAT POHON KEPUTUSAN</a><br>";
        $this->populateDb();
        $this->miningC45('', '');
        echo "<script>window.alert('Proses Mining Sukses !!!');</script>";
        // echo "<script>window.alert('Proses Mining Sukses !!!'); window.location='index.php/pohonkeputusan'</script>";
        
    }

    public function populateDb()
    {
        $sql = "TRUNCATE mining_c45";
        $this->M_model->get_sql($sql);
        $sql = "TRUNCATE iterasi_c45";
        $this->M_model->get_sql($sql);
        $sql = "TRUNCATE pohon_keputusan_c45";
        $this->M_model->get_sql($sql);
        $this->populateAtribut();
    }

    public function populateAtribut()
    {
        $sql = "TRUNCATE atribut";
        $this->M_model->get_sql($sql);
        $sql = "INSERT INTO `atribut` (`id`, `atribut`, `nilai_atribut`) VALUES
        (null, 'total', 'total'),
        (null, 'kegiatan_masyarakat', 'tidak ada'),
        (null, 'kegiatan_masyarakat', 'ipnu/ippnu'),
        (null, 'kegiatan_masyarakat', 'rantingnu'),
        (null, 'kegiatan_masyarakat', 'ansor'),
        (null, 'kegiatan_masyarakat', 'pacnu'),
        (null, 'kegiatan_masyarakat', 'mwcnu'),
        (null, 'kegiatan_masyarakat', 'pcnu'),
        (null, 'kegiatan_masyarakat', 'pbnu'),
        (null, 'kegiatan_masyarakat', 'takmir'),
        (null, 'pekerjaan', 'sudah'),
        (null, 'pekerjaan', 'belum'),
        (null, 'menikah', 'sudah'),
        (null, 'menikah', 'belum'),
        (null, 'pendidikan_formal', 'mts sederajat'),
        (null, 'pendidikan_formal', 'ma sederajat'),
        (null, 'pendidikan_formal', 'd1'),
        (null, 'pendidikan_formal', 'd2'),
        (null, 'pendidikan_formal', 'd3'),
        (null, 'pendidikan_formal', 's1'),
        (null, 'pendidikan_formal', 's2'),
        (null, 'pendidikan_formal', 's3'),
        (null, 'pendidikan_formal', 'prof')";

        $this->M_model->get_sql($sql);
    }

    public function miningC45($atribut, $nilai_atribut)
    {
        $this->perhitunganC45($atribut, $nilai_atribut);
        $this->insertAtributPohonKeputusan($atribut, $nilai_atribut);
        $this->getInfGainMax($atribut, $nilai_atribut);
        $this->replaceNull();
    }

    public function perhitunganC45($atribut, $nilai_atribut)
    {
        if (empty($atribut) and empty($nilai_atribut)) {
            //#2# Jika atribut yg diinputkan kosong, maka lakukan perhitungan awal
            $kondisiAtribut = ""; // set kondisi atribut kosong
        } else if (!empty($atribut) and !empty($nilai_atribut)) {
            // jika atribut tdk kosong, maka select kondisi atribut dari DB
            $sqlKondisiAtribut = "SELECT kondisi_atribut FROM pohon_keputusan_c45 WHERE atribut = '$atribut' AND nilai_atribut = '$nilai_atribut' order by id DESC LIMIT 1";
            $rowKondisiAtribut = $this->M_model->get_kolom_tabel($sqlKondisiAtribut);
            $kondisiAtribut = str_replace("~", "'", $rowKondisiAtribut[0]->kondisi_atribut); // replace string ~ menjadi '
        }

        // AMBIL SELURUH ATRIBUT
        $sqlAtribut = "SELECT distinct atribut FROM atribut";
        $rowGetAtribut = $this->M_model->get_kolom_tabel($sqlAtribut);
        if ($rowGetAtribut !== 0) {
            $norowGetAtribut = 0;
            foreach ($rowGetAtribut as $kAtribut) {
                $norowGetAtribut++;
                echo 'rowGetAtribut ' . $norowGetAtribut;
                echo "<br>";
                echo "<br>";
                $getAtribut = $kAtribut->atribut;
                if ($getAtribut === 'total') {

                    //#3# Jika atribut = total, maka hitung jumlah kasus total, jumlah kasus berhasil dan jumlah kasus tdk berhasil
                    // hitung jumlah kasus total
                    $sqlJumlahKasusTotal = "SELECT COUNT(*) as jumlah_total FROM data_survey WHERE status is not null $kondisiAtribut";
                    $rowJumlahKasusTotal = $this->M_model->get_kolom_tabel($sqlJumlahKasusTotal);
                    $getJumlahKasusTotal = $rowJumlahKasusTotal[0]->jumlah_total;

                    // hitung jumlah kasus berhasil
                    $sqlJumlahKasusberhasil = "SELECT COUNT(*) as jumlah_berhasil FROM data_survey WHERE status = 'berhasil' AND status is not null $kondisiAtribut";
                    $rowJumlahKasusberhasil = $this->M_model->get_kolom_tabel($sqlJumlahKasusberhasil);
                    $getJumlahKasusberhasil = $rowJumlahKasusberhasil[0]->jumlah_berhasil;

                    // hitung jumlah kasus tdk berhasil
                    $sqlJumlahKasusbelumberhasil = "SELECT COUNT(*) as jumlah_belum_berhasil FROM data_survey WHERE status = 'belum berhasil' AND status is not null $kondisiAtribut";
                    $rowJumlahKasusbelumberhasil = $this->M_model->get_kolom_tabel($sqlJumlahKasusbelumberhasil);
                    $getJumlahKasusbelumberhasil = $rowJumlahKasusbelumberhasil[0]->jumlah_belum_berhasil;

                    //#4# Insert jumlah kasus total, jumlah kasus berhasil dan jumlah kasus tdk berhasil ke DB
                    // insert ke database mining_c45
                    // $sql = "INSERT INTO mining_c45 VALUES (NULL, 'Total', 'Total', '$getJumlahKasusTotal', '$getJumlahKasusberhasil', '$getJumlahKasusbelumberhasil', '', '', '', '', '', '')";
                    $data = [
                        'atribut' => 'Total',
                        'nilai_atribut' => 'Total',
                        'jml_kasus_total' => $getJumlahKasusTotal,
                        'jml_berhasil' => $getJumlahKasusberhasil,
                        'jml_belum_berhasil' => $getJumlahKasusbelumberhasil,
                        'entropy' => '',
                        'inf_gain' => '',
                        'inf_gain_temp' => '',
                        'split_info' => '',
                        'split_info_temp' => '',
                        'gain_ratio' => ''
                    ];
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                    echo "<br>";
                    echo "<br>";
                    $this->M_model->tambahdata('mining_c45', $data);
                } else {

                    //#5# Jika atribut != total (atribut lainnya), maka hitung jumlah kasus total, jumlah kasus berhasil dan jumlah kasus tdk berhasil masing2 atribut
                    // ambil nilai atribut
                    $sqlNilaiAtribut = "SELECT nilai_atribut FROM atribut WHERE atribut = '$getAtribut' ORDER BY id";
                    $rowNilaiAtribut = $this->M_model->get_kolom_tabel($sqlNilaiAtribut);
                    if ($rowNilaiAtribut !== 0) {
                        $norowNilaiAtribut = 0;
                        foreach ($rowNilaiAtribut as $kNilaiAtribut) {
                            $norowNilaiAtribut++;
                            echo 'rowNilaiAtribut ' . $norowNilaiAtribut;
                            echo "<br>";
                            echo "<br>";
                            $getNilaiAtribut = $kNilaiAtribut->nilai_atribut;

                            // set kondisi dimana nilai_atribut = berdasakan masing2 atribut dan status data = data training
                            $kondisi = "$getAtribut = '$getNilaiAtribut' AND status is not null $kondisiAtribut";

                            // hitung jumlah kasus per atribut
                            $sqlJumlahKasusTotalAtribut = "SELECT COUNT(*) as jumlah_total FROM data_survey WHERE $kondisi";
                            $rowJumlahKasusTotalAtribut = $this->M_model->get_kolom_tabel($sqlJumlahKasusTotalAtribut);
                            $getJumlahKasusTotalAtribut = $rowJumlahKasusTotalAtribut[0]->jumlah_total;

                            // hitung jumlah kasus berhasil
                            $sqlJumlahKasusberhasilAtribut = "SELECT COUNT(*) as jumlah_berhasil FROM data_survey WHERE $kondisi AND status = 'berhasil'";
                            $rowJumlahKasusberhasilAtribut = $this->M_model->get_kolom_tabel($sqlJumlahKasusberhasilAtribut);
                            $getJumlahKasusberhasilAtribut = $rowJumlahKasusberhasilAtribut[0]->jumlah_berhasil;

                            // hitung jumlah kasus TDK berhasil
                            $sqlJumlahKasusbelumberhasilAtribut = "SELECT COUNT(*) as jumlah_belum_berhasil FROM data_survey WHERE $kondisi AND status = 'belum berhasil'";
                            $rowJumlahKasusbelumberhasilAtribut = $this->M_model->get_kolom_tabel($sqlJumlahKasusbelumberhasilAtribut);
                            $getJumlahKasusbelumberhasilAtribut = $rowJumlahKasusbelumberhasilAtribut[0]->jumlah_belum_berhasil;

                            //#6# Insert jumlah kasus total, jumlah kasus berhasil dan jumlah kasus tdk berhasil masing2 atribut ke DB
                            // insert ke database mining_c45
                            // "INSERT INTO mining_c45 VALUES (NULL, '$getAtribut', '$getNilaiAtribut', '$getJumlahKasusTotalAtribut', '$getJumlahKasusberhasilAtribut', '$getJumlahKasusbelumberhasilAtribut', '', '', '', '', '', '')";
                            $data = [
                                'atribut' => $getAtribut,
                                'nilai_atribut' => $getNilaiAtribut,
                                'jml_kasus_total' => $getJumlahKasusTotalAtribut,
                                'jml_berhasil' => $getJumlahKasusberhasilAtribut,
                                'jml_belum_berhasil' => $getJumlahKasusbelumberhasilAtribut,
                                'entropy' => '',
                                'inf_gain' => '',
                                'inf_gain_temp' => '',
                                'split_info' => '',
                                'split_info_temp' => '',
                                'gain_ratio' => ''
                            ];
                            echo "<pre>";
                            print_r($data);
                            echo "</pre>";
                            echo "<br>";
                            echo "<br>";
                            $this->M_model->tambahdata('mining_c45', $data);

                            //#7# Lakukan perhitungan entropy
                            // perhitungan entropy
                            $sqlEntropy = "SELECT id, jml_kasus_total, jml_berhasil, jml_belum_berhasil FROM mining_c45";
                            $rowEntropy = $this->M_model->get_kolom_tabel($sqlEntropy);
                            if ($rowEntropy !== 0) {
                                $norowEntropy = 0;
                                foreach ($rowEntropy as $kEntropy) {
                                    $norowEntropy++;
                                    echo 'rowEntropy ' . $norowEntropy;
                                    echo "<br>";
                                    echo "<br>";
                                    $getJumlahKasusTotalEntropy = $kEntropy->jml_kasus_total;
                                    $getJumlahKasusberhasilEntropy = $kEntropy->jml_berhasil;
                                    $getJumlahKasusbelumberhasilEntropy = $kEntropy->jml_belum_berhasil;
                                    $idEntropy = $kEntropy->id;

                                    // jika jml kasus = 0 maka entropy = 0
                                    if ($getJumlahKasusTotalEntropy == 0 or $getJumlahKasusberhasilEntropy == 0 or $getJumlahKasusbelumberhasilEntropy == 0) {
                                        $getEntropy = 0;
                                        // jika jml kasus berhasil = jml kasus tdk berhasil, maka entropy = 1
                                    } else if ($getJumlahKasusberhasilEntropy == $getJumlahKasusbelumberhasilEntropy) {
                                        $getEntropy = 1;
                                    } else { // jika jml kasus != 0, maka hitung rumus entropy:
                                        $perbandingan_berhasil = $getJumlahKasusberhasilEntropy / $getJumlahKasusTotalEntropy;
                                        $perbandingan_tidak_berhasil = $getJumlahKasusbelumberhasilEntropy / $getJumlahKasusTotalEntropy;

                                        $rumusEntropy = (- ($perbandingan_berhasil) * log($perbandingan_berhasil, 2)) + (- ($perbandingan_tidak_berhasil) * log($perbandingan_tidak_berhasil, 2));
                                        $getEntropy = round($rumusEntropy, 4); // 4 angka di belakang koma
                                    }

                                    //#8# Update nilai entropy
                                    // update nilai entropy
                                    // "UPDATE mining_c45 SET entropy = $getEntropy WHERE id = $idEntropy";
                                    $data = [
                                        'entropy' => $getEntropy
                                    ];
                                    echo "<pre>";
                                    print_r($data);
                                    echo "</pre>";
                                    echo "<br>";
                                    echo "<br>";
                                    $this->M_model->editdata('mining_c45', $data, 'id', $idEntropy);
                                }
                            }

                            //#9# Lakukan perhitungan information gain
                            // perhitungan information gain
                            // ambil nilai entropy dari total (jumlah kasus total)
                            $sqlJumlahKasusTotalInfGain = "SELECT jml_kasus_total, entropy FROM mining_c45 WHERE atribut = 'Total'";
                            $rowJumlahKasusTotalInfGain = $this->M_model->get_kolom_tabel($sqlJumlahKasusTotalInfGain);
                            $getJumlahKasusTotalInfGain = $rowJumlahKasusTotalInfGain[0]->jml_kasus_total;
                            // rumus information gain
                            $getInfGain = (- (($getJumlahKasusTotalEntropy / $getJumlahKasusTotalInfGain) * ($getEntropy)));

                            //#10# Update information gain tiap nilai atribut (temporary)
                            // update inf_gain_temp (utk mencari nilai masing2 atribut)
                            // "UPDATE mining_c45 SET inf_gain_temp = $getInfGain WHERE id = $idEntropy";
                            $data = [
                                'inf_gain_temp' => $getInfGain
                            ];
                            echo "<pre>";
                            print_r($data);
                            echo "</pre>";
                            echo "<br>";
                            echo "<br>";
                            $this->M_model->editdata('mining_c45', $data, 'id', $idEntropy);
                            $getEntropy = $rowJumlahKasusTotalInfGain[0]->entropy;

                            // jumlahkan masing2 inf_gain_temp atribut
                            $sqlAtributInfGain = "SELECT SUM(inf_gain_temp) as inf_gain FROM mining_c45 WHERE atribut = '$getAtribut'";
                            $rowAtributInfGain = $this->M_model->get_kolom_tabel($sqlAtributInfGain);
                            if ($rowAtributInfGain !== 0) {
                                $norowAtributInfGain = 0;
                                foreach ($rowAtributInfGain as $kAtributInfGain) {
                                    $norowAtributInfGain++;
                                    echo 'rowAtributInfGain ' . $norowAtributInfGain;
                                    echo "<br>";
                                    echo "<br>";
                                    $getAtributInfGain = $kAtributInfGain->inf_gain;

                                    // hitung inf gain
                                    $getInfGainFix = round(($getEntropy + $getAtributInfGain), 4);

                                    //#11# Looping perhitungan information gain, sehingga mendapatkan information gain tiap atribut. Update information gain
                                    // update inf_gain (fix)
                                    // "UPDATE mining_c45 SET inf_gain = $getInfGainFix WHERE atribut = '$getAtribut'";
                                    $data = [
                                        'inf_gain' => $getInfGainFix
                                    ];
                                    echo "<pre>";
                                    print_r($data);
                                    echo "</pre>";
                                    echo "<br>";
                                    echo "<br>";
                                    $this->M_model->editdata('mining_c45', $data, 'atribut', $getAtribut);
                                }
                            }

                            //#12# Lakukan perhitungan split info
                            // rumus split info
                            $getSplitInfo = (($getJumlahKasusTotalEntropy / $getJumlahKasusTotalInfGain) * (log(($getJumlahKasusTotalEntropy / $getJumlahKasusTotalInfGain), 2)));
                            if (is_nan($getSplitInfo)) {
                                $getSplitInfo = 0;
                            }

                            //#13# Update split info tiap nilai atribut (temporary)
                            // update split_info_temp (utk mencari nilai masing2 atribut)
                            // "UPDATE mining_c45 SET split_info_temp = $getSplitInfo WHERE id = $idEntropy";
                            $data = [
                                'split_info_temp' => $getSplitInfo
                            ];
                            echo "<pre>";
                            print_r($data);
                            echo "</pre>";
                            echo "<br>";
                            echo "<br>";
                            $this->M_model->editdata('mining_c45', $data, 'id', $idEntropy);

                            // jumlahkan masing2 split_info_temp dari tiap atribut
                            $sqlAtributSplitInfo = "SELECT SUM(split_info_temp) as split_info FROM mining_c45 WHERE atribut = '$getAtribut'";
                            $rowAtributSplitInfo = $this->M_model->get_kolom_tabel($sqlAtributSplitInfo);
                            if ($rowAtributSplitInfo !== 0) {
                                $norowAtributInfGain = 0;
                                foreach ($rowAtributSplitInfo as $kAtributSplitInfo) {
                                    $norowAtributInfGain++;
                                    echo 'rowAtributSplitInfo ' . $norowAtributInfGain;
                                    echo "<br>";
                                    echo "<br>";
                                    $getAtributSplitInfo = $kAtributSplitInfo->split_info;

                                    // split info fix (4 angka di belakang koma)
                                    $getSplitInfoFix = - (round($getAtributSplitInfo, 4));

                                    //#14# Looping perhitungan split info, sehingga mendapatkan information gain tiap atribut. Update information gain
                                    // update split info (fix)
                                    // "UPDATE mining_c45 SET split_info = $getSplitInfoFix WHERE atribut = '$getAtribut'";
                                    $data = [
                                        'split_info' => $getSplitInfoFix
                                    ];
                                    echo "<pre>";
                                    print_r($data);
                                    echo "</pre>";
                                    echo "<br>";
                                    echo "<br>";
                                    $this->M_model->editdata('mining_c45', $data, 'atribut', $getAtribut);
                                }
                            }
                        }
                    }


                    //#15# Lakukan perhitungan gain ratio
                    $sqlGainRatio = "SELECT id, inf_gain, split_info FROM mining_c45";
                    $rowGainRatio = $this->M_model->get_kolom_tabel($sqlGainRatio);
                    if ($rowGainRatio !== 0) {
                        $norowGainRatio = 0;
                        foreach ($rowGainRatio as $kGainRatio) {
                            $norowGainRatio++;
                            echo 'rowGainRatio ' . $norowGainRatio;
                            echo "<br>";
                            echo "<br>";
                            $idGainRatio = $kGainRatio->id;
                            // jika nilai inf gain == 0 dan split info == 0, maka gain ratio = 0
                            if ($kGainRatio->inf_gain == 0 and $kGainRatio->split_info == 0) {
                                $getGainRatio = 0;
                            } else {
                                // rumus gain ratio
                                $getGainRatio = round(($kGainRatio->inf_gain / $kGainRatio->split_info), 4);
                            }

                            //#16# Update gain ratio dari setiap atribut
                            // "UPDATE mining_c45 SET gain_ratio = $getGainRatio WHERE id = '$idGainRatio'";
                            $data = [
                                'gain_ratio' => $getGainRatio
                            ];
                            echo "<pre>";
                            print_r($data);
                            echo "</pre>";
                            echo "<br>";
                            echo "<br>";
                            $this->M_model->editdata('mining_c45', $data, 'id', $idGainRatio);
                        }
                    }
                }
            }
        }
    }

    //#17# Insert atribut dgn information gain max ke DB pohon keputusan
    public function insertAtributPohonKeputusan($atribut, $nilai_atribut)
    {
        // ambil nilai inf gain tertinggi dimana hanya 1 atribut saja yg dipilih
        $sqlInfGainMaxTemp = "SELECT distinct atribut, gain_ratio FROM mining_c45 WHERE gain_ratio in (SELECT max(gain_ratio) FROM `mining_c45`) LIMIT 1";
        $rowInfGainMaxTemp = $this->M_model->get_kolom_tabel($sqlInfGainMaxTemp);
        // hanya ambil atribut dimana jumlah kasus totalnya tidak kosong
        if ($rowInfGainMaxTemp[0]->gain_ratio > 0) {
            // ambil nilai atribut yang memiliki nilai inf gain max
            $getAtributrowInfGainMaxTemp = $rowInfGainMaxTemp[0]->atribut;
            $sqlInfGainMax = "SELECT * FROM mining_c45 WHERE atribut = '$getAtributrowInfGainMaxTemp'";
            $rowInfGainMax = $this->M_model->get_kolom_tabel($sqlInfGainMax);
            if ($rowInfGainMax !== 0) {
                $norowInfGainMax = 0;
                foreach ($rowInfGainMax as $kInfGainMax) {
                    $norowInfGainMax++;
                    echo 'rowInfGainMax ' . $norowInfGainMax;
                    echo "<br>";
                    echo "<br>";
                    if ($kInfGainMax->jml_berhasil == 0 and $kInfGainMax->jml_belum_berhasil == 0) {
                        $keputusan = 'Kosong'; // jika jml_berhasil = 0 dan jml_belum_berhasil = 0, maka keputusan Null
                    } else if ($kInfGainMax->jml_berhasil !== 0 and $kInfGainMax->jml_belum_berhasil == 0) {
                        $keputusan = 'berhasil'; // jika jml_berhasil != 0 dan jml_belum_berhasil = 0, maka keputusan berhasil
                    } else if ($kInfGainMax->jml_berhasil == 0 and $kInfGainMax->jml_belum_berhasil !== 0) {
                        $keputusan = 'belum berhasil'; // jika jml_berhasil = 0 dan jml_belum_berhasil != 0, maka keputusan Tidak berhasil
                    } else {
                        $keputusan = '?'; // jika jml_berhasil != 0 dan jml_belum_berhasil != 0, maka keputusan ?
                    }

                    if (empty($atribut) and empty($nilai_atribut)) {
                        //#18# Jika atribut yang diinput kosong (atribut awal) maka insert ke pohon keputusan id_parent = 0
                        // set kondisi atribut = AND atribut = nilai atribut
                        $kondisiAtribut = "AND $kInfGainMax->atribut = ~$kInfGainMax->nilai_atribut~";
                        // insert ke tabel pohon keputusan
                        // "INSERT INTO pohon_keputusan_c45 VALUES (NULL, '$kInfGainMax->atribut', '$kInfGainMax->nilai_atribut', 0, '$kInfGainMax->jml_berhasil', '$kInfGainMax->jml_belum_berhasil', '$keputusan', 'Belum', '$kondisiAtribut', 'Belum')";
                        $data = [
                            'atribut' => $kInfGainMax->atribut,
                            'nilai_atribut' => $kInfGainMax->nilai_atribut,
                            'id_parent' => 0,
                            'jml_berhasil' => $kInfGainMax->jml_berhasil,
                            'jml_belum_berhasil' => $kInfGainMax->jml_belum_berhasil,
                            'keputusan' => $keputusan,
                            'diproses' => 'Belum',
                            'kondisi_atribut' => $kondisiAtribut,
                            'looping_kondisi' => 'Belum'
                        ];
                        echo "<pre>";
                        print_r($data);
                        echo "</pre>";
                        echo "<br>";
                        echo "<br>";
                        $this->M_model->tambahdata('pohon_keputusan_c45', $data);
                    } else if (!empty($atribut) and !empty($nilai_atribut)) {
                        //#19# Jika atribut yang diinput tidak kosong maka insert ke pohon keputusan dimana id_parent diambil dari tabel pohon keputusan sebelumnya (where atribut = atribut yang diinput)
                        $sqlIdParent = "SELECT id, atribut, nilai_atribut, jml_berhasil, jml_belum_berhasil FROM pohon_keputusan_c45 WHERE atribut = '$atribut' AND nilai_atribut = '$nilai_atribut' order by id DESC LIMIT 1";
                        $rowIdParent = $this->M_model->get_kolom_tabel($sqlIdParent);
                        if ($rowIdParent !== 0) {
                            $norowIdParent = 0;
                            $perhitunganPessimisticChildIncrement = 0;
                            foreach ($rowIdParent as $kIdParent) {
                                $norowIdParent++;
                                echo 'rowIdParent ' . $norowIdParent;
                                echo "<br>";
                                echo "<br>";
                                // insert ke tabel pohon keputusan
                                // "INSERT INTO pohon_keputusan_c45 VALUES (NULL, '$kIdParent->atribut', '$kIdParent->nilai_atribut', $rowIdParent->id, '$kIdParent->jml_berhasil', '$kIdParent->jml_belum_berhasil', '$keputusan', 'Belum', '', 'Belum')";
                                $data = [
                                    'atribut' => $kIdParent->atribut,
                                    'nilai_atribut' => $kIdParent->nilai_atribut,
                                    'id_parent' => $kIdParent->id,
                                    'jml_berhasil' => $kIdParent->jml_berhasil,
                                    'jml_belum_berhasil' => $kIdParent->jml_belum_berhasil,
                                    'keputusan' => $keputusan,
                                    'diproses' => 'Belum',
                                    'kondisi_atribut' => '',
                                    'looping_kondisi' => 'Belum'
                                ];
                                echo "<pre>";
                                print_r($data);
                                echo "</pre>";
                                echo "<br>";
                                echo "<br>";
                                $this->M_model->tambahdata('pohon_keputusan_c45', $data);

                                //#PRE PRUNING (dokumentasi -> http://id3-c45.xp3.biz/dokumentasi/Decision-Tree.10.11.ppt)#
                                // hitung Pessimistic error rate parent dan child
                                $perhitunganParentPrePruning = $this->loopingPerhitunganPrePruning($kIdParent->jml_berhasil, $kIdParent->jml_belum_berhasil);
                                $perhitunganChildPrePruning = $this->loopingPerhitunganPrePruning($kIdParent->jml_berhasil, $kIdParent->jml_belum_berhasil);

                                // hitung average Pessimistic error rate child
                                $perhitunganPessimisticChild = (($kIdParent->jml_berhasil + $kIdParent->jml_belum_berhasil) / ($kIdParent->jml_berhasil + $kIdParent->jml_belum_berhasil)) * $perhitunganChildPrePruning;

                                // Increment average Pessimistic error rate child
                                $perhitunganPessimisticChildIncrement += $perhitunganPessimisticChild;
                                $perhitunganPessimisticChildIncrement = round($perhitunganPessimisticChildIncrement, 4);

                                // jika error rate pada child lebih besar dari error rate parent
                                if ($perhitunganPessimisticChildIncrement > $perhitunganParentPrePruning) {
                                    // hapus child (child tidak diinginkan)
                                    // "DELETE FROM pohon_keputusan_c45 WHERE id_parent = $kIdParent->id";
                                    $data = [
                                        'id_parent' => $kIdParent->id
                                    ];
                                    echo "<pre>";
                                    print_r($data);
                                    echo "</pre>";
                                    echo "<br>";
                                    echo "<br>";
                                    $this->M_model->hapusdata('pohon_keputusan_c45', $data);

                                    // jika jml kasus berhasil lbh besar, maka keputusan == berhasil
                                    if ($kIdParent->jml_berhasil > $kIdParent->jml_belum_berhasil) {
                                        $keputusanPrePruning = 'berhasil';
                                        // jika jml tdk kasus berhasil lbh besar, maka keputusan == tdk berhasil
                                    } else if ($kIdParent->jml_berhasil < $kIdParent->jml_belum_berhasil) {
                                        $keputusanPrePruning = 'belum berhasil';
                                    }
                                    // update keputusan parent
                                    // "UPDATE pohon_keputusan_c45 SET keputusan = '$keputusanPrePruning' where id = $kIdParent->id";
                                    $data = [
                                        'keputusan' => $keputusanPrePruning
                                    ];
                                    echo "<pre>";
                                    print_r($data);
                                    echo "</pre>";
                                    echo "<br>";
                                    echo "<br>";
                                    $this->M_model->editdata('pohon_keputusan_c45', $data, 'id', $kIdParent->id);
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->loopingKondisiAtribut();
    }

    //#20# Lakukan looping kondisi atribut untuk diproses pada fungsi perhitunganC45()
    function loopingKondisiAtribut()
    {
        // ambil semua id dan kondisi atribut
        $sqlLoopingKondisi = "SELECT id, kondisi_atribut FROM pohon_keputusan_c45";
        $rowLoopingKondisi = $this->M_model->get_kolom_tabel($sqlLoopingKondisi);
        if ($rowLoopingKondisi !== 0) {
            $norowLoopingKondisi = 0;
            foreach ($rowLoopingKondisi as $kLoopingKondisi) {
                $norowLoopingKondisi++;
                echo 'rowLoopingKondisi ' . $norowLoopingKondisi;
                echo "<br>";
                echo "<br>";
                // select semua data dimana id_parent = id awal
                $sqlUpdateKondisi = "SELECT * FROM pohon_keputusan_c45 WHERE id_parent = $kLoopingKondisi->id AND looping_kondisi = 'Belum'";
                $rowUpdateKondisi = $this->M_model->get_kolom_tabel($sqlUpdateKondisi);

                if ($rowUpdateKondisi !== 0) {
                    $norowLoopingKondisi = 0;
                    foreach ($rowUpdateKondisi as $kUpdateKondisi) {
                        $norowLoopingKondisi++;
                        echo 'rowUpdateKondisi ' . $norowLoopingKondisi;
                        echo "<br>";
                        echo "<br>";
                        // set kondisi: kondisi sebelumnya yg diselect berdasarkan id_parent ditambah 'AND atribut = nilai atribut'
                        $kondisiAtribut = "$kLoopingKondisi->kondisi_atribut AND $kUpdateKondisi->atribut = ~$kUpdateKondisi->nilai_atribut~";
                        // update kondisi atribut
                        // "UPDATE pohon_keputusan_c45 SET kondisi_atribut = '$kondisiAtribut', looping_kondisi = 'Sudah' WHERE id = $kUpdateKondisi->id";
                        $data = [
                            'kondisi_atribut' => $kondisiAtribut,
                            'looping_kondisi' => 'Sudah'
                        ];
                        echo "<pre>";
                        print_r($data);
                        echo "</pre>";
                        echo "<br>";
                        echo "<br>";
                        $this->M_model->editdata('pohon_keputusan_c45', $data, 'id', $kUpdateKondisi->id);
                    }
                }
            }
        }
        $this->insertIterasi();
    }

    //#21# Insert iterasi nilai perhitungan ke DB
    function insertIterasi()
    {
        $sqlInfGainMaxIterasi = "SELECT distinct atribut, gain_ratio FROM mining_c45 WHERE gain_ratio in (SELECT max(gain_ratio) FROM `mining_c45`) LIMIT 1";
        $rowInfGainMaxIterasi = $this->M_model->get_kolom_tabel($sqlInfGainMaxIterasi);
        // hanya ambil atribut dimana jumlah kasus totalnya tidak kosong
        if ($rowInfGainMaxIterasi[0]->gain_ratio > 0) {
            $kondisiAtribut = $rowInfGainMaxIterasi[0]->atribut;
            $iterasiKe = 1;
            $sqlInsertIterasiC45 = "SELECT * FROM mining_c45";
            $rowInsertIterasiC45 = $this->M_model->get_kolom_tabel($sqlInsertIterasiC45);
            if ($rowInsertIterasiC45 !== 0) {
                $norowInsertIterasiC45 = 0;
                foreach ($rowInsertIterasiC45 as $kInsertIterasiC45) {
                    $norowInsertIterasiC45++;
                    echo 'rowInsertIterasiC45 ' . $norowInsertIterasiC45;
                    echo "<br>";
                    echo "<br>";
                    // insert ke tabel iterasi
                    // "INSERT INTO iterasi_c45 VALUES (
                    //     NULL,
                    //     $iterasiKe,
                    //     '$kondisiAtribut',
                    //     '$rowInsertIterasiC45->atribut',
                    //     '$rowInsertIterasiC45->nilai_atribut',
                    //     '$rowInsertIterasiC45->jml_kasus_total',
                    //     '$rowInsertIterasiC45->jml_berhasil',
                    //     '$rowInsertIterasiC45->jml_belum_berhasil',
                    //     '$rowInsertIterasiC45->entropy',
                    //     '$rowInsertIterasiC45->inf_gain'
                    //     '$rowInsertIterasiC45->split_info',
                    //     '$rowInsertIterasiC45->gain_ratio')");
                    $data = [
                        'iterasi' => $iterasiKe,
                        'atribut_gain_ratio_max' => $kondisiAtribut,
                        'atribut' => $kInsertIterasiC45->atribut,
                        'nilai_atribut' => $kInsertIterasiC45->nilai_atribut,
                        'jml_kasus_total' => $kInsertIterasiC45->jml_kasus_total,
                        'jml_berhasil' => $kInsertIterasiC45->jml_berhasil,
                        'jml_belum_berhasil' => $kInsertIterasiC45->jml_belum_berhasil,
                        'entropy' => $kInsertIterasiC45->entropy,
                        'inf_gain' => $kInsertIterasiC45->inf_gain,
                        'split_info' => $kInsertIterasiC45->split_info,
                        'gain_ratio' => $kInsertIterasiC45->gain_ratio
                    ];
                    $iterasiKe++;
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                    echo "<br>";
                    echo "<br>";
                    $this->M_model->tambahdata('iterasi_c45', $data);
                }
            }
        }
    }

    // looping perhitungan Pessimistic error rate
    function loopingPerhitunganPrePruning($positif, $negatif)
    {
        $z = 1.645; // z = batas kepercayaan (confidence treshold)
        $n = $positif + $negatif; // n = total jml kasus
        $n = round($n, 4);
        // r = perbandingan child thd parent
        if ($positif < $negatif) {
            $r = $positif / ($n);
            $r = round($r, 4);
            return $this->perhitunganPrePruning($r, $z, $n);
        } elseif ($positif > $negatif) {
            $r = $negatif / ($n);
            $r = round($r, 4);
            return $this->perhitunganPrePruning($r, $z, $n);
        } elseif ($positif == $negatif) {
            $r = $negatif / ($n);
            $r = round($r, 4);
            return $this->perhitunganPrePruning($r, $z, $n);
        }
    }

    // rumus menghitung Pessimistic error rate
    function perhitunganPrePruning($r, $z, $n)
    {
        $rumus = ($r + (($z * $z) / (2 * $n)) + ($z * (sqrt(($r / $n) - (($r * $r) / $n) + (($z * $z) / (4 * ($n * $n))))))) / (1 + (($z * $z) / $n));
        $rumus = round($rumus, 4);
        return $rumus;
    }


    public function getInfGainMax($atribut, $nilai_atribut)
    {
        // select inf gain max
        $sqlInfGainMaxAtribut = "SELECT distinct atribut FROM mining_c45 WHERE gain_ratio in (SELECT max(gain_ratio) FROM `mining_c45`) LIMIT 1";
        $rowInfGainMaxAtribut = $this->M_model->get_kolom_tabel($sqlInfGainMaxAtribut);
        if ($rowInfGainMaxAtribut !== 0) {
            $norowInfGainMaxAtribut = 0;
            foreach ($rowInfGainMaxAtribut as $kInfGainMaxAtribut) {
                $norowInfGainMaxAtribut++;
                echo 'rowInfGainMaxAtribut ' . $norowInfGainMaxAtribut;
                echo "<br>";
                echo "<br>";
                $inf_gain_max_atribut = "$kInfGainMaxAtribut->atribut";
                if (empty($atribut) and empty($nilai_atribut)) {
                    // jika atribut kosong, proses atribut dgn inf gain max pada fungsi loopingMiningC45()
                    $this->loopingMiningC45($inf_gain_max_atribut);
                } else if (!empty($atribut) and !empty($nilai_atribut)) {
                    // jika atribut tdk kosong, maka update diproses = sudah pada tabel pohon_keputusan_c45
                    // "UPDATE pohon_keputusan_c45 SET diproses = 'Sudah' WHERE nilai_atribut = '$nilai_atribut'";
                    $data = [
                        'diproses' => 'Sudah'
                    ];
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";
                    echo "<br>";
                    echo "<br>";
                    $this->M_model->editdata('pohon_keputusan_c45', $data, 'nilai_atribut', $nilai_atribut);
                    // proses atribut dgn inf gain max pada fungsi $this->loopingMiningC45()
                    $this->loopingMiningC45($inf_gain_max_atribut);
                }
            }
        }
    }

    //#23# Looping proses mining dimana atribut dgn information gain max yang akan diproses pada fungsi miningC45()
    function loopingMiningC45($inf_gain_max_atribut)
    {
        $sqlBelumAdaKeputusanLagi = "SELECT * FROM pohon_keputusan_c45 WHERE keputusan = '?' and diproses = 'Belum' AND atribut = '$inf_gain_max_atribut'";
        $rowBelumAdaKeputusanLagi = $this->M_model->get_kolom_tabel($sqlBelumAdaKeputusanLagi);
        if ($rowBelumAdaKeputusanLagi !== 0) {
            $norowBelumAdaKeputusanLagi = 0;
            foreach ($rowBelumAdaKeputusanLagi as $kBelumAdaKeputusanLagi) {
                $norowBelumAdaKeputusanLagi++;
                echo 'rowBelumAdaKeputusanLagi ' . $norowBelumAdaKeputusanLagi;
                echo "<br>";
                echo "<br>";
                if ($kBelumAdaKeputusanLagi->id_parent == 0) {
                    $this->populateAtribut();
                }
                $atribut = "$kBelumAdaKeputusanLagi->atribut";
                $nilai_atribut = "$kBelumAdaKeputusanLagi->nilai_atribut";
                $kondisiAtribut = "AND $atribut = \'$nilai_atribut\'";
                $sql = "TRUNCATE mining_c45";
                $this->M_model->get_sql($sql);
                // "DELETE FROM atribut WHERE atribut = '$inf_gain_max_atribut'";
                $data = [
                    'atribut' => $inf_gain_max_atribut
                ];
                echo "<pre>";
                print_r($data);
                echo "</pre>";
                echo "<br>";
                echo "<br>";
                $this->M_model->hapusdata('atribut', $data);
                $this->miningC45($atribut, $nilai_atribut);
                $this->populateAtribut();
            }
        }
    }

    public function replaceNull()
    {
        $sqlReplaceNull = "SELECT id, id_parent FROM pohon_keputusan_c45 WHERE keputusan = 'Null'";
        $rowReplaceNull = $this->M_model->get_kolom_tabel($sqlReplaceNull);
        if ($rowReplaceNull !== 0) {
            $norowReplaceNull = 0;
            foreach ($rowReplaceNull as $kReplaceNull) {
                $norowReplaceNull++;
                echo 'rowReplaceNull ' . $norowReplaceNull;
                echo "<br>";
                echo "<br>";
                $sqlReplaceNullIdParent = "SELECT jml_berhasil, jml_belum_berhasil, keputusan FROM pohon_keputusan_c45 WHERE id = $kReplaceNull->id_parent";
                $rowReplaceNullIdParent = $this->M_model->get_kolom_tabel($sqlReplaceNullIdParent);
                if ($rowReplaceNullIdParent[0]->jml_berhasil > $rowReplaceNullIdParent[0]->jml_belum_berhasil) {
                    $keputusanNull = 'berhasil'; // jika jml_berhasil != 0 dan jml_belum_berhasil = 0, maka keputusan berhasil
                } else if ($rowReplaceNullIdParent[0]->jml_berhasil < $rowReplaceNullIdParent[0]->jml_belum_berhasil) {
                    $keputusanNull = 'belum berhasil'; // jika jml_berhasil = 0 dan jml_belum_berhasil != 0, maka keputusan Tidak berhasil
                }
                // "UPDATE pohon_keputusan_c45 SET keputusan = '$keputusanNull' WHERE id = $kReplaceNull->id";
                $data = [
                    'keputusan' => $keputusanNull
                ];
                echo "<pre>";
                print_r($data);
                echo "</pre>";
                echo "<br>";
                echo "<br>";
                $this->M_model->editdata('pohon_keputusan_c45', $data, 'id', $kReplaceNull->id);
            }
        }
    }
}
