<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Data Alumni</h1> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Alumni</h6>
        </div>
        <div class="card-body">
            <a href="<?=base_url('index.php/tbl_alumni/proseshasil');?>" class="btn btn-success">Proses Hasil -></a>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" data-lengthMenu="100" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Id</th>
                            <th>kegiatan_masyarakat</th>
                            <th>pekerjaan</th>
                            <th>menikah</th>
                            <th>pendidikan_formal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start = 0;
                        foreach ($tbl_alumni_data as $tbl_alumni) {
                        ?>
                            <tr>
                                <td width="10px"><?php echo ++$start ?></td>
                                <td><?php echo $tbl_alumni->id_data_alumni ?></td>
                                <td><?php echo $tbl_alumni->kegiatan_masyarakat ?></td>
                                <td><?php echo $tbl_alumni->pekerjaan ?></td>
                                <td><?php echo $tbl_alumni->menikah ?></td>
                                <td><?php echo $tbl_alumni->pendidikan_formal ?></td>
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
