<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Data Alumni</h1> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Klasifikasi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <?php
                    ?>
                    <!-- <form action="https://project-c45.herokuapp.com/predict" method="POST"> -->
                    <div class="form-group">
                        <label for="exampleInputEmail1">Kegiatan Masyarakat?</label>
                        <select class="form-control" name="kegiatan_masyarakat" id="kegiatan_masyarakat">
                            <?php
                            if ($dtkegiatanmasyarakat == null) {
                                echo '<option value="">Data Tidak Ditemukan</option>';
                            } else {
                                foreach ($dtkegiatanmasyarakat as $km) {
                            ?>
                                    <option value="<?= $km->nama_nilai; ?>"><?= $km->nama_nilai; ?></option>
                            <?php }
                            } ?>
                        </select>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Pekerjaan?</label>
                        <select class="form-control" name="pekerjaan" id="pekerjaan">
                            <?php
                            if ($dtpekerjaan == null) {
                                echo '<option value="">Data Tidak Ditemukan</option>';
                            } else {
                                foreach ($dtpekerjaan as $km) {
                            ?>
                                    <option value="<?= $km->nama_nilai; ?>"><?= $km->nama_nilai; ?></option>
                            <?php }
                            } ?>
                        </select>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Menikah?</label>
                        <select class="form-control" name="menikah" id="menikah">
                            <?php
                            if ($dtmenikah == null) {
                                echo '<option value="">Data Tidak Ditemukan</option>';
                            } else {
                                foreach ($dtmenikah as $km) {
                            ?>
                                    <option value="<?= $km->nama_nilai; ?>"><?= $km->nama_nilai; ?></option>
                            <?php }
                            } ?>
                        </select>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Pendidikan FOrmal?</label>
                        <select class="form-control" name="pendidikan_formal" id="pendidikan_formal">
                            <?php
                            if ($dtpendidikan_formal == null) {
                                echo '<option value="">Data Tidak Ditemukan</option>';
                            } else {
                                foreach ($dtpendidikan_formal as $km) {
                            ?>
                                    <option value="<?= $km->nama_nilai; ?>"><?= $km->nama_nilai; ?></option>
                            <?php }
                            } ?>
                        </select>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                    </div>
                    <!-- <button type="submit" class="btn btn-primary">Cek Data...</button> -->
                    <button onclick="cekKlasifikasi()" class="btn btn-primary">Cek Data...</button>
                    <!-- </form> -->
                </div>
                <div class="col-6">
                    <label for="aa">HASIL</label>
                    <div class="alert alert-success" role="alert">
                        A simple success alert—check it out!
                    </div>
                    <div class="alert alert-danger" role="alert">
                        A simple danger alert—check it out!
                    </div>
                    <div class="alert alert-warning" role="alert">
                        A simple warning alert—check it out!
                    </div>
                    <div class="alert alert-info" role="alert">
                        A simple info alert—check it out!
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>
<script>
    function cekKlasifikasi() {
        var km = $('#kegiatan_masyarakat').val()
        var pe = $('#pekerjaan').val()
        var me = $('#menikah').val()
        var pf = $('#pendidikan_formal').val()

        console.log(km, pe, me, pf) // ansor belum belum mts sederajat

        // $.ajax({
        //     type: "get",
        //     url: a,
        //     dataType: "JSON",
        //     success: function(response) {
        //         console.log(response);
        //     },
        //     error: function(a, b, c) {
        //         console.log(a, b, c);
        //     }
        // });
        var a = "https://project-c45.herokuapp.com/predict"
        $.ajax({
            url: a,
            type: 'POST',
            data: {
                kegiatan_masyarakat: 1,
                pekerjaan: 1,
                menikah: 1,
                pendidikan_formal: 1
            },
            dataType: "JSON",
            cache: false,
            success: function(data) {
                console.log(data);
            },
            error: function() {
                alert("Cannot get data");
            }
        });
        // $.post("https://project-c45.herokuapp.com/predict", {kegiatan_masyarakat: 1,pekerjaan: 1,menikah: 1,pendidikan_formal: 1},
        //     function (data, textStatus, jqXHR) {
        //         console.log(data,textStatus,jqXHR);
        //     },
        //     "JSON"
        // );
    }
</script>