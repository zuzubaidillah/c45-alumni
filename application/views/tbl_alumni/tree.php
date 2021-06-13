<style>
#tableKecil {
		font-size: 11px !important;
	}

	tr td {
		padding: 5px !important;
	}
</style>
<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Data Alumni</h1> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tree</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered" id="tableKecil" width="100%" data-lengthMenu="100" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Node</th>
                            <th>Kolom</th>
                            <th>Fild</th>
                            <th>Jumlah Kasus</th>
                            <th>Belum</th>
                            <th>Cukup</th>
                            <th>Berhasil</th>
                            <th>Entropy</th>
                            <th>Pengurangan Entropy</th>
                            <th>Gain</th>
                            <th>homogen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start = 0;
                        if ($data_node!==null) {
                        foreach ($data_node as $dtnode) {
                            $node = $dtnode->node;
                            $kolom = $dtnode->kolom;
                            $fild = $dtnode->fild;
                            $jumlah_kasus = $dtnode->jumlah_kasus;
                            $jumlah_belum = $dtnode->jumlah_belum;
                            $jumlah_cukup = $dtnode->jumlah_cukup;
                            $jumlah_berhasil = $dtnode->jumlah_berhasil;
                            $entropy_belum = $dtnode->entropy_belum;
                            $entropy_cukup = $dtnode->entropy_cukup;
                            $entropy_berhasil = $dtnode->entropy_berhasil;
                            $entropy = $dtnode->entropy;
                            $pengurangan_entropy = $dtnode->pengurangan_entropy;
                            $gain = $dtnode->gain;
                            $homogen = $dtnode->homogen;
                            $array_kolom = explode('~',$kolom);
                            $kolom = $array_kolom[1];
                            if ($array_kolom[0]=="total") {
                                $kolom = "<b>Total</b>";
                            }
                            if ($array_kolom[0]=="gain") {
                                $kolom = "<b>Nilai GAIN</b> ".$kolom;
                            }
                            if ($array_kolom[0]=="tertinggi") {
                                $kolom = "<b>GAIN Tertinggi ".$array_kolom[2]."</b> ";
                                $gain = "<b>".$dtnode->gain."</b>";
                            }
                            if ($homogen!==NULL) {
                                $array_homogen = explode('~', $homogen);
                                $homogen = $array_homogen[1];
                                if ($array_homogen[0]=="d") {
                                    $homogen = "".$homogen;
                                }else{
                                    $homogen = "".$homogen;
                                }
                            }
                        ?>
                            <tr>
                                <td width="10px"><?php echo ++$start ?></td>
                                <td><?php echo $node ?></td>
                                <td><?php echo $kolom ?></td>
                                <td><?php echo $fild ?></td>
                                <td><?php echo $jumlah_kasus ?></td>
                                <td><?php echo $jumlah_belum ?></td>
                                <td><?php echo $jumlah_cukup ?></td>
                                <td><?php echo $jumlah_berhasil ?></td>
                                <td><?php echo $entropy ?></td>
                                <td><?php echo $pengurangan_entropy ?></td>
                                <td><?php echo $gain ?></td>
                                <td><?php echo $homogen ?></td>
                            </tr>
                        <?php
                        }}
                        ?>
                    </tbody>
                </table>

                <hr>

                
                <table class="table table-bordered" id="" width="100%" data-lengthMenu="100" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Id</th>
                            <th>kegiatan_masyarakat</th>
                            <th>bobot</th>
                            <th>pekerjaan</th>
                            <th>bobot</th>
                            <th>menikah</th>
                            <th>bobot</th>
                            <th>pendidikan_formal</th>
                            <th>bobot</th>
                            <th>jumlah Keseluruhan</th>
                            <th>hasil</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>
