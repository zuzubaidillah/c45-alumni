<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Data Alumni</h1> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">HASIL Perhitungan Keberhasilan</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" data-lengthMenu="100" cellspacing="0">
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
                    <tbody>
                        <?php
                        $start = 0;
                        foreach ($tbl_alumni_data as $tbl_alumni) {
                            $pekerjaan = $tbl_alumni->pekerjaan;
                            // dekalarasi nilai pada data pekerjaan
                            if ($pekerjaan=='sudah') {
                                $bobot_pe = 7; //sudah
                            }else{
                                $bobot_pe = 0; //belum
                            }
                            $menikah = $tbl_alumni->menikah;
                            // dekalarasi nilai pada data menikah
                            if ($menikah=='sudah') {
                                $bobot_me = 6; //sudah
                            }else{
                                $bobot_me = 3; //belum
                            }
                        ?>
                            <tr>
                                <td width="10px"><?php echo ++$start ?></td>
                                <td><?php echo $tbl_alumni->id_data_alumni ?></td>
                                <td><?php echo $tbl_alumni->kegiatan_masyarakat ?></td>
                                <td><?php echo $tbl_alumni->bobot_km ?></td>
                                <td><?php echo $tbl_alumni->pekerjaan ?></td>
                                <td><?php echo $bobot_pe ?></td>
                                <td><?php echo $tbl_alumni->menikah ?></td>
                                <td><?php echo $bobot_me ?></td>
                                <td><?php echo $tbl_alumni->pendidikan_formal ?></td>
                                <td><?php echo $tbl_alumni->bobot_pf ?></td>
                                <td><?php echo $tbl_alumni->jumlahBobot ?></td>
                                <td><?php echo $tbl_alumni->hasil ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
