<?php
require_once("config.php");
$SCRIPT_FOOT = "
<script>
$(document).ready(function(){
	$('ul li.nav-ba').addClass('active');
});
</script>
<script src=\"bap.js\"></script>
";

$idpengajuan=U_IDP;
$permohonan = Permohonan::find($idpengajuan);

dump($permohonan->hasil_periksa()->count());

if (ctype_digit($idpengajuan)) {
    ?>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    Berita Acara Pemeriksaan
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo c_URL; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Berita Acara Pemeriksaan</li>
                </ol>
            </section>

            <section class="content">
            <?php if($permohonan->hasil_periksa()->count() === 0): ?>
                <?php
                    $found = $sql->get_count('tb_bap', array('ref_idp' => $idpengajuan));
                    if ($found > 0) {
                        include("bap-edit.php");
                    } else {
                        include("bap-add.php");
                    }
                    ?>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>
                    BAP Tidak Teridentifikasi hanya dapat diisi jika hasil pemeriksaan masih kosong.
                </div>
            <?php endif ?>
            </section>
        </div>
    </body>
<?php
}
include(AdminFooter);
?>
