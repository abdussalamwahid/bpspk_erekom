<?php

use App\Models\DataIkan;
use App\Services\Contracts\Template;
use App\Services\Letter;
use App\Models\Permohonan;
use App\Models\Rekomendasi;

require_once("config.php");
$SCRIPT_FOOT = "
<script>
$(document).ready(function(){
	$('nav li.nav3').addClass('nav-active');
});
</script>
<script src=\"custom-3.js\"></script>
";

$idpengajuan = base64_decode($_GET['data']);
if (!ctype_digit($idpengajuan)) {
	exit();
}

if ($_GET['token'] != md5($idpengajuan . U_ID . 'confirm')) {
	exit();
}

$sql->get_row('tb_permohonan', array('idp' => $idpengajuan, 'status' => 4), '*');
if ($sql->num_rows > 0) {
	$p = $sql->result;
	$arr_alatangkut = array(
		'udara' => "Pesawat Udara",
		'laut' => "Kapal Laut",
		'darat' => "Kendaraan Darat"
	);
	$arr_jns_tujuan = array(
		"perdagangan" => "Perdagangan",
		"souvenir" => "Souvenir"
	); ?>
	<section role="main" class="content-body">
		<header class="page-header">
			<h2>Pengesahan Permohonan Rekomendasi</h2>

			<div class="right-wrapper pull-right">
				<ol class="breadcrumbs">
					<li>
						<a href="<?php echo c_MODULE; ?>">
							<i class="fa fa-home"></i>
						</a>
					</li>
					<li><span>Pengesahan Permohonan Rekomendasi</span></li>
				</ol>
				<a class="sidebar-right-toggle"><i class="fa fa-chevron-left"></i></a>
			</div>
		</header>
		<div class="row">
			<div class="col-md-7">
				<?php
					$b = $sql->run("SELECT u.nama_lengkap,u.email, b.* FROM tb_userpublic u JOIN tb_biodata b ON (b.ref_iduser=u.iduser) WHERE u.iduser='" . $p['ref_iduser'] . "' LIMIT 1");
					$bio = $b->fetch(); ?>
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Profil Pemohon</h2>
					</header>
					<div class="panel-body">
						<table class="table table-hover">
							<tr>
								<td width="30%">Nama Lengkap</td>
								<td><?php echo $bio['nama_lengkap']; ?></td>
							</tr>
							<tr>
								<td>Tempat, Tanggal Lahir</td>
								<td><?php echo $bio['tmp_lahir'] . ", " . tanggalIndo($bio['tgl_lahir'], 'j F Y'); ?></td>
							</tr>
							<tr>
								<td>No Identitas</td>
								<td><?php echo $bio['no_ktp']; ?></td>
							</tr>
							<tr>
								<td>Alamat Gudang 1</td>
								<td><?php echo $bio['gudang_1']; ?></td>
							</tr>
							<tr>
								<td>Alamat Gudang 2</td>
								<td><?php echo $bio['gudang_2']; ?></td>
							</tr>
							<tr>
								<td>Alamat Gudang 3</td>
								<td><?php echo $bio['gudang_3']; ?></td>
							</tr>
							<tr>
								<td>No Telepon</td>
								<td><?php echo $bio['no_telp']; ?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td><?php echo $bio['email']; ?></td>
							</tr>
							<tr>
								<td>NPWP</td>
								<td><?php echo $bio['npwp']; ?></td>
							</tr>
							<tr>
								<td>Nama Perusahaan</td>
								<td><?php echo $bio['nm_perusahaan']; ?></td>
							</tr>
							<tr>
								<td>SIUP</td>
								<td><?php echo $bio['siup']; ?></td>
							</tr>
							<tr>
								<td>NIB</td>
								<td><?php echo $bio['nib']; ?></td>
							</tr>
							<tr>
								<td>SIPJI</td>
								<td><?php echo $bio['sipji']; ?></td>
							</tr>
							<tr>
								<td>Izin Usaha Lainnya</td>
								<td><?php echo $bio['izin_lain']; ?></td>
							</tr>
						</table>
					</div>
				</section>
			</div>
			<div class="col-md-5">
				<section class="panel">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Berkas Pemohon</h2>
					</header>
					<div class="panel-body">
						<table class="table">
							<tr>
								<td>KTP<br />
									<?php
										$n = $sql->run("SELECT nama_file FROM tb_berkas WHERE ref_iduser='" . $p['ref_iduser'] . "' AND jenis_berkas='4' ORDER BY revisi DESC, date_upload DESC LIMIT 1");
										if ($n->rowCount() > 0) {
											$img_npwp = $n->fetch();
											echo '<img width="50%" href="' . BERKAS . $img_npwp['nama_file'] . '" src="' . BERKAS . $img_npwp['nama_file'] . '" class="img-prev">';
										} else {
											echo '<p class="text-alert alert-warning">KTP Belum diupload</p>';
										} ?></td>
							</tr>
							<tr>
								<td>NPWP<br />
									<?php
										$n = $sql->run("SELECT nama_file FROM tb_berkas WHERE ref_iduser='" . $p['ref_iduser'] . "' AND jenis_berkas='2' ORDER BY revisi DESC, date_upload DESC LIMIT 1");
										if ($n->rowCount() > 0) {
											$img_npwp = $n->fetch();
											echo '<img width="50%" href="' . BERKAS . $img_npwp['nama_file'] . '" src="' . BERKAS . $img_npwp['nama_file'] . '" class="img-prev">';
										} else {
											echo '<p class="text-alert alert-warning">NPWP Belum diupload</p>';
										} ?></td>
							</tr>
							<tr>
								<td>SIUP<br />
									<?php
										$sql->order_by = "revisi DESC, date_upload DESC, idb DESC";
										$sql->get_row('tb_berkas', array('ref_iduser' => $p['ref_iduser'], 'jenis_berkas' => 3), array('nama_file'));
										$img_siup = $sql->result;
										if ($img_siup['nama_file'] == '') {
											echo '<p class="text-alert alert-warning">SIUP Belum diupload</p>';
										} else {
											echo '<img width="50%" href="' . BERKAS . $img_siup['nama_file'] . '" src="' . BERKAS . $img_siup['nama_file'] . '" class="img-prev">';
										} ?></td>
							</tr>
							<tr>
								<td>NIB<br/>
								<?php
								$n=$sql->run("SELECT nama_file FROM tb_berkas WHERE ref_iduser='".$p['ref_iduser']."' AND jenis_berkas='5' ORDER BY revisi DESC, date_upload DESC LIMIT 1");
								if($n->rowCount()>0){
									$img_nib=$n->fetch();
									echo '<img width="50%" href="'.BERKAS.$img_nib['nama_file'].'" src="'.BERKAS.$img_nib['nama_file'].'" class="img-prev">';
								}else{
									echo '<p class="text-alert alert-warning">NIB Belum diupload</p>';
								}
								?></td>
							</tr>
							<tr>
								<td>SIPJI<br/>
								<?php
								$n=$sql->run("SELECT nama_file FROM tb_berkas WHERE ref_iduser='".$p['ref_iduser']."' AND jenis_berkas='6' ORDER BY revisi DESC, date_upload DESC LIMIT 1");
								if($n->rowCount()>0){
									$img_sipji=$n->fetch();
									echo '<img width="50%" href="'.BERKAS.$img_sipji['nama_file'].'" src="'.BERKAS.$img_sipji['nama_file'].'" class="img-prev">';
								}else{
									echo '<p class="text-alert alert-warning">SIPJI Belum diupload</p>';
								}
								?></td>
							</tr>	
							<tr>
								<td>TTD<br/>
								<?php
								$t=$sql->run("SELECT nama_file FROM tb_berkas WHERE ref_iduser='".$p['ref_iduser']."' AND jenis_berkas='1' ORDER BY revisi DESC, date_upload DESC LIMIT 1");
								if($t->rowCount()>0){
									$img_npwp=$t->fetch();
									echo '<img width="50%" href="'.BERKAS.$img_npwp['nama_file'].'" src="'.BERKAS.$img_npwp['nama_file'].'" class="img-prev">';
								}else{
									echo '<p class="text-alert alert-warning">Tandatangan Belum diupload</p>';
								}
								?></td>
							</tr>

							<?php 
								$permohonanStoragePath = "/pengajuan/berkas/";
								$permohonan = Permohonan::find(
									base64_decode($_GET['data'])
								);
							
							?>

							<tr>
								<td>
									<div>
										Invoice
									</div>

									<a href="<?= $permohonanStoragePath . $permohonan->file_invoice ?>">
										<img
											style="
												width: 200px;
												height: auto;
												object-fit: cover;
											" 
											src="<?= $permohonanStoragePath . $permohonan->file_invoice ?>"
											>
									</a>
								</td>
							</tr>

							<tr>
								<td>
									<div>
										Packing List
									</div>

									<a href="<?= $permohonanStoragePath . $permohonan->file_packing_list ?>">
										<img
											style="
												width: 200px;
												height: auto;
												object-fit: cover;
											" 
											src="<?= $permohonanStoragePath . $permohonan->file_packing_list ?>"
											>
									</a>
								</td>
							</tr>

							<?php if($permohonan->file_pra_bap): ?>
							<tr>
								<td>
									<div>
										Pra-BAP
									</div>

									<a href="<?= $permohonanStoragePath . $permohonan->file_pra_bap ?>">
										<img
											style="
												width: 200px;
												height: auto;
												object-fit: cover;
											" 
											src="<?= $permohonanStoragePath . $permohonan->file_pra_bap ?>"
											>
									</a>
								</td>
							</tr>
							<?php endif ?>

						</table>
					</div>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Data Permohonan & Barang</h2>
					</header>
					<div class="panel-body">
						<table class="table table-hover">
							<tr>
								<td width="20%">Diajukan Pada</td>
								<td><?php echo tanggalIndo($p['tgl_pengajuan'], 'j F Y H:i'); ?></td>
							</tr>
							<tr>
								<td>Dikirim Ke</td>
								<td><?php echo $p['tujuan']; ?></td>
							</tr>
							<tr>
								<td>Penerima</td>
								<td><?php echo $p['penerima']; ?></td>
							</tr>
							<tr>
								<td>Untuk</td>
								<td><?php echo $arr_jns_tujuan[$p['jenis_tujuan']]; ?></td>
							</tr>
							<tr>
								<td>Alat Angkut</td>
								<td><?php echo $arr_alatangkut[$p['jenis_angkutan']]; ?></td>
							</tr>
							<tr>
								<td>Alamat Pemeriksaan</td>
								<td><?php echo $p['alamat_gudang']; ?></td>
							</tr>
							<tr>
								<td>Keterangan Tambahan</td>
								<td><?php echo $p['ket_tambahan']; ?></td>
							</tr>
						</table>
						<hr />
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama Barang</th>
									<th>Kuantitas</th>
									<th>Jumlah Berat</th>
									<th>Asal Komoditas</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql->order_by = "";
									$sql->get_all('tb_barang', array('ref_idphn' => $idpengajuan), '*');
									if ($sql->num_rows > 0) {
										$no = 0;
										foreach ($sql->result as $b) {
											$no++;
											echo '<tr>
										<td>' . $no . '</td>
										<td>' . $b['nm_barang'] . '</td>
										<td>' . $b['kuantitas'] . ' <em>Colly</em></td>
										<td>' . $b['jlh'] . ' Kg</td>
										<td>' . $b['asal_komoditas'] . '</td>
									</tr>';
										}
									} ?>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Hasil Pemeriksaan</h2>
					</header>
					<div class="panel-body">
						<table class="table">
							<?php
								$sql->get_row('tb_pemeriksaan', array('ref_idp' => $idpengajuan), array('tgl_periksa'));
								$tgl = $sql->result; ?>
							<tr>
								<td style="width:20%">Tanggal Pemeriksaan</td>
								<td>: <?php echo tanggalIndo($tgl['tgl_periksa'], 'j F Y'); ?></td>
							</tr>
							<?php
								$pt = $sql->run("SELECT op.nm_lengkap, op.nip FROM tb_petugas_lap pl JOIN op_pegawai op ON (pl.ref_idpeg=op.idp) WHERE pl.ref_idp='$idpengajuan'");
								if ($pt->rowCount() > 0) {
									$no = 0;
									foreach ($pt->fetchAll() as $ptgs) {
										$no++;
										echo '<tr>
									<td style="width:20%">Petugas Pemeriksa ' . $no . '</td>
									<td>: ' . $ptgs['nm_lengkap'] . ' (' . $ptgs['nip'] . ')</td>
								</tr>';
									}
								} ?>

						</table>
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Jenis Produk</th>
									<th>Jenis Ikan</th>
									<th>Panjang<br>Sampel(Cm)</th>
									<th>Lebar<br>Sampel(Cm)</th>
									<th>Berat<br>Sampel(Kg)</th>
									<th>Berat Total(Kg)</th>
									<th>Jlh Kemasan </th>
									<th>Keterangan</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$tb = $sql->query("SELECT 
										th.*,
										rdi.nama_ikan,
										rjs.jenis_sampel AS jns_produk,
										satuan_barang.nama AS nama_satuan_barang
										
										FROM tb_hsl_periksa th 
										LEFT JOIN ref_data_ikan rdi ON (rdi.id_ikan=th.ref_idikan) 
										LEFT JOIN ref_jns_sampel rjs ON (rjs.id_ref=th.ref_jns_sampel)
										LEFT JOIN satuan_barang ON (th.id_satuan_barang = satuan_barang.id)
								
								WHERE th.ref_idp='$idpengajuan'
								");
								
									if ($tb->rowCount() > 0) {
									$no = 1;
									foreach ($tb->fetchAll() as $dtrow) {
										
										echo
								'<tr>
									<td>' .  $no . '</td>
									<td>' . $dtrow['produk'] . ' ' . $dtrow['kondisi_produk'] . ' ' . $dtrow['jenis_produk'] . '</td>
									<td>' . $dtrow['nama_ikan'] . '</td>
									<td>' . $dtrow['pjg'] . '' . (($dtrow['pjg2'] != '0.00') ? " / " . $dtrow['pjg2'] : "") . '</td>
									<td>' . $dtrow['lbr'] . '' . (($dtrow['lbr2'] != '0.00') ? " / " . $dtrow['lbr2'] : "") . '</td>
									<td>' . $dtrow['berat'] . '' . (($dtrow['berat2'] != '0.00') ? " / " . $dtrow['berat2'] : "") . '</td>
									<td>' . $dtrow['tot_berat'] . '</td>
									<td>' . $dtrow['kuantitas'] . ' ' . $dtrow['nama_satuan_barang'] . '</td>
									<td>' . $dtrow['ket'] . '</td>
								</tr>';
										$no++;
										
									}
									} else {
									echo '<tr><td colspan="11" class="text-center">Data Belum Diisi.</td></tr>';
									}
									?>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Foto Dokumentasi Pemeriksaan</h2>
					</header>
					<div class="panel-body">
						<table class="table">
							<?php
								$sql->get_all('tb_dokumentasi', array('ref_idp' => $idpengajuan), array('nm_file', 'ket_foto', 'id_dok'));
								if ($sql->num_rows > 0) {
									$nog = 0;
									echo '<tr>';
									foreach ($sql->result as $gbr) {
										$nog++;
										echo '<td><img width="100%" src="' . ADM_FOTO . $gbr['nm_file'] . '" href="' . ADM_FOTO . $gbr['nm_file'] . '" class="img-prev"><p>' . $gbr['ket_foto'] . '</p></td>';
										if ($nog > 1) {
											if ($nog % 2 != 0) {
												echo '</tr><tr>';
											}
										}
									}
									echo '</tr>';
								} ?>
						</table>
					</div>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Berita Acara Pemeriksaan</h2>
					</header>
					<div class="panel-body">
						<?php
							//load hasil data BAP
							$bap = $sql->run("SELECT bap.*,
						p1.nm_lengkap nmp1, p1.nip nip1, p1.jabatan jbtn1, p1.ttd ttd1, 
						p2.nm_lengkap nmp2, p2.nip nip2, p2.jabatan jbtn2, p2.ttd ttd2 FROM tb_bap bap 
						LEFT JOIN op_pegawai p1 ON(p1.idp=bap.ptgs1)
						LEFT JOIN op_pegawai p2 ON(p2.idp=bap.ptgs2)
						WHERE bap.ref_idp='$idpengajuan' LIMIT 1");
							$row = $bap->fetch();
							$hari = tanggalIndo($row['tgl_surat'], 'l');
							$tgl = tanggalIndo($row['tgl_surat'], 'j');
							$bln = tanggalIndo($row['tgl_surat'], 'F');
							$thn = tanggalIndo($row['tgl_surat'], 'Y');

							//load list pengajuan
							$sql->get_all('tb_barang', array('ref_idphn' => $row['ref_idp']), 'nm_barang');
							$barang = array();
							foreach ($sql->result as $brg) {
								$barang[] = $brg['nm_barang'];
							}
							$list_brg = implode(', ', $barang);

							//load info pemohon
							$sql->get_row('tb_permohonan', array('idp' => $row['ref_idp']), 'ref_iduser');
							$p = $sql->result;
							$idpemohon = $p['ref_iduser'];
							$u = $sql->run("SELECT u.nama_lengkap,b.gudang_1,
						(SELECT nama_file FROM tb_berkas WHERE jenis_berkas='1' AND ref_iduser='" . $idpemohon . "' ORDER BY revisi DESC, date_upload DESC LIMIT 1) nama_file 
						FROM tb_userpublic u 
						JOIN tb_biodata b ON(u.iduser=b.ref_iduser)
						WHERE u.iduser='$idpemohon' LIMIT 1");
							$pemohon = $u->fetch();

							//load data petugas pemeriksa
							$pt = $sql->run("SELECT p.nm_lengkap,p.nip,p.jabatan,p.ttd FROM tb_petugas_lap pl LEFT JOIN op_pegawai p ON(pl.ref_idpeg=p.idp) WHERE pl.ref_idp='" . $row['ref_idp'] . "' AND p.status='2'"); ?>

						<?= container(Letter::class)->getHeaderContentHTML(ADM_IMAGES) ?>

						<table style="width:100%">
							<tr>
								<td colspan="3" style="text-align: center;">
									<h5><strong><u>BERITA ACARA PEMERIKSAAN BARANG MASUK</u></strong></h5>
									Nomor : <?php echo $row['no_surat']; ?>
									<br /><br />
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<p>Pada Hari Ini <strong><?php echo ucwords($hari); ?></strong> tanggal <strong><?php echo ucwords(terbilang($tgl)); ?></strong> Bulan <strong><?php echo ucwords($bln); ?></strong> Tahun <strong><?php echo ucwords(terbilang($thn)); ?> (<?php echo $thn; ?>)</strong> bertempat di <strong><?php echo $row['lokasi']; ?></strong>, kami yang bertanda tangan dibawah ini :</p>
								</td>
							</tr>
							<tr>
								<td>1.</td>
								<td>Nama</td>
								<td>: <?php echo $row['nmp1']; ?></td>
							</tr>
							<tr>
								<td></td>
								<td>NIP</td>
								<td>: <?php echo $row['nip1']; ?></td>
							</tr>
							<tr>
								<td></td>
								<td>Jabatan</td>
								<td>: <?php echo $row['jbtn1']; ?></td>
							</tr>
							<tr>
								<td>2.</td>
								<td>Nama</td>
								<td>: <?php echo $row['nmp2']; ?></td>
							</tr>
							<tr>
								<td></td>
								<td>NIP</td>
								<td>: <?php echo $row['nip2']; ?></td>
							</tr>
							<tr>
								<td></td>
								<td>Jabatan</td>
								<td>: <?php echo $row['jbtn2']; ?></td>
							</tr>
							<tr>
								<td colspan="3">
									<p>Menerangkan Bahwa telah melakukan pemeriksaan barang masuk berupa sampel <?php echo $list_brg; ?> milik:</p>
								</td>
							</tr>
							<tr>
								<td colspan="2" width="20%">Nama</td>
								<td>: <?php echo $pemohon['nama_lengkap']; ?></td>
							</tr>
							<tr>
								<td colspan="2" width="20%">Alamat</td>
								<td>: <?php echo $pemohon['gudang_1']; ?></td>
							</tr>
							<tr>
								<td colspan="3">
									<br />
									<p><?php echo $row['redaksi']; ?></p>
									<p>Demikian Berita Acara Pemeriksaan ini dibuat dengan sebenar-benarnya, untuk dapat dipergunakan sebagaimana mestinya.</p>
								</td>
							</tr>
						</table>
						<table width="100%">
							<tr>
								<td colspan="2" style="text-align: center;">
									<br>
									<?php echo tanggalIndo($row['tgl_surat'], 'j F Y'); ?>,<br>
									Tim Pemeriksa
								</td>
							</tr>
							<tr>
								<td width="50%" style="text-align: center;">
									<br>
									<a href="#"><img height="100px" src="<?php echo ADM_IMAGES . $row['ttd1']; ?>"></a>
									<p><?php echo $row['nmp1']; ?></p>
								</td>
								<td width="50%" style="text-align: center;">
									<br>
									<a href="#"><img height="100px" src="<?php echo ADM_IMAGES . $row['ttd2']; ?>"></a>
									<p><?php echo $row['nmp2']; ?></p>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;">
									<br>
									Perwakilan Perusahaan/ Pengirim
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;">
									<br>
									<a href="#"><img height="100px" src="<?php echo BERKAS . $pemohon['nama_file']; ?>"></a>
									<p><?php echo $pemohon['nama_lengkap']; ?></p>
								</td>
							</tr>
						</table>
					</div>
				</section>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<section class="panel panel-featured panel-featured-primary">
					<header class="panel-heading">
						<div class="panel-actions"><a href="#" class="fa fa-caret-down"></a></div>
						<h2 class="panel-title">Draft Surat Rekomendasi</h2>
					</header>
					<div class="panel-body">
						<?php
							//load data surat rekomendasi
							$rek = $sql->run("SELECT tr.*, tp.tgl_pengajuan, tp.tujuan, tp.jenis_angkutan, tu.nama_lengkap, tb.no_surat nobap, tb.tgl_surat tglbap, op.nm_lengkap penandatgn, op.jabatan, op.ttd,ou.lvl 
					FROM tb_rekomendasi tr
					JOIN tb_permohonan tp ON (tr.ref_idp=tp.idp)
					JOIN tb_userpublic tu ON (tu.iduser=tr.ref_iduser)
					JOIN tb_bap tb ON (tp.idp=tb.ref_idp)
					JOIN op_pegawai op ON(tr.pnttd=op.nip)
					JOIN op_user ou ON(ou.ref_idpeg=op.idp)
					WHERE tr.ref_idp='" . $idpengajuan . "' LIMIT 1");

							$row = $rek->fetch(); ?>

						<?= container(Letter::class)->getHeaderContentHTML(ADM_IMAGES) ?>

						<br />
						<table style="width:100%">
							<tr>
								<td>Nomor</td>
								<td>: <?php echo $row['no_surat']; ?></td>
								<td style="text-align:right"><?php echo tanggalIndo($row['tgl_surat'], 'j F Y'); ?></td>
							</tr>
							<tr>
								<td>Perihal</td>
								<td>: <?php echo $row['perihal']; ?></td>
								<td></td>
							</tr>
						</table>

						<table style="width:100%">
							<tr>
								<td>
									<br>Kepada
									<br>Yth. <?php echo $row['nama_lengkap']; ?>
									<br>di -
									<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tempat
									<br>
									<br></td>
								<td style="text-align:right"></td>
							</tr>
						</table>

						<table style="width:100%">
							<tr>
								<td>
									<?=
											container(Letter::class)->getOpeningText(
												tanggalIndo($row['tgl_pengajuan'], 'j F Y'),
												$row['tujuan'],
												$row['jenis_angkutan'],
												$row['nobap'],
												tanggalIndo($row['tglbap'], 'j F Y')
											)
										?>
								</td>
							</tr>
						</table>

						<?php
							$dt = $sql->run("SELECT 
							thp.*, 
							
							rdi.nama_latin, 
							rdi.dilindungi,
							satuan_barang.nama AS nama_satuan_barang
							
							
							FROM tb_rek_hsl_periksa thp 
								LEFT JOIN ref_data_ikan rdi ON (rdi.id_ikan=thp.ref_idikan) 
								LEFT JOIN satuan_barang ON (thp.id_satuan_barang = satuan_barang.id)
								

							WHERE thp.ref_idrek='" . $row['idrek'] . "' 
							ORDER BY thp.ref_jns ASC"
							);
						?> 

						
							

						<?= container(Template::class)->render("letter/table", [
							"records" => $dt->fetchAll(),
							"rekomendasi" => Rekomendasi::find($row['idrek']) ?? new Rekomendasi(),	
							"tujuan" => $row["tujuan"] ?? null,
                            "jenis_angkutan" => $row["jenis_angkutan"] ?? null
						]) ?>

						<table style="width:100%">
							<tr>
								<td><br>
									<p><?php echo $row['redaksi']; ?></p>
								</td>
							</tr>
							<tr>
								<td>
									<p>Demikian kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</p>
								</td>
							</tr>
						</table>
						<?php
							$tmb = $sql->run("SELECT rbk.nama FROM tb_rekomendasi tr JOIN ref_balai_karantina rbk ON(rbk.idbk=tr.ref_bk) WHERE tr.ref_idp='" . $idpengajuan . "' LIMIT 1");
							$karantina = $tmb->fetch(); ?>
						<table style="width:100%">
							<tr>
								<td width="60%"></td>
								<td width="60%" style="text-align:center">
									<?php echo (($row['lvl'] == 90) ? "Kepala Loka" : "Plh. Kepala Loka"); ?>
									<p><a href="#"><img height="100px" src="<?php echo ADM_IMAGES . $row['ttd']; ?>"></a></p>
									<?php echo $row['penandatgn']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									Tembusan:
									<ol>
										<li>Direktur Jenderal PRL</li>
										<li>Direktur Konservasi Keanekaragaman dan Hayati Laut</li>
										<li>Kepala <?php echo $karantina['nama']; ?></li>
									</ol>
								</td>
							</tr>
						</table>
						<hr>
						<?php
							$v = $sql->run("SELECT tsp.tgl_verifikasi, op.nm_lengkap, op.nip FROM tb_stat_pengesahan tsp JOIN op_pegawai op ON(tsp.verifikator=op.nip) WHERE tsp.ref_idp='$idpengajuan' AND tsp.status='1' LIMIT 1");
							if ($v->rowCount() > 0) {
								$vf = $v->fetch();
								echo '<p class="alert alert-danger">Telah Diperiksa dan diverifikasi oleh : ' . $vf['nm_lengkap'] . ' (' . $vf['nip'] . ') Pada tanggal ' . tanggalIndo($vf['tgl_verifikasi'], 'j F Y H:i') . '</p>';
							} ?>
					</div>
					<footer class="panel-footer">
						<form method="post" name="pengesahan" id="pengesahan">
							<input type="hidden" name="a" value="pengesahan">
							<input type="hidden" name="idp" id="idp" value="<?php echo base64_encode($idpengajuan); ?>">
							<input type="hidden" name="token" id="token" value="<?php echo md5($idpengajuan . U_ID . 'pengesahan'); ?>">

							<div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="sesuai_bap_pemeriksaan" value="yes">
										Sudah sesuai dengan Surat Permohonan dan Berita Acara Pemeriksaan.
									</label>
								</div>
							</div>

							<div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="sesuai_data" value="yes">
										Nama barang, jumlah, dan berat sudah sesuai.
									</label>
								</div>
							</div>

							<div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="sesuai_redaksi" value="yes">
										Redaksi sudah benar.
									</label>
								</div>
							</div>

							<button type="submit" class="btn btn-sm btn-primary" id="btn_pengesahan">Terima & Sahkan Surat Ini</button>
							<button type="button" class="btn btn-sm btn-danger" id="btn_tolak">Tolak untuk diperbaiki</button>
						</form>
						<span id="actloading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Loading...</span>
					</footer>
				</section>
			</div>
		</div>
	</section>
	<div id="TolakModal" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
		<section class="panel">
			<header class="panel-heading">
				<h2 class="panel-title">Kirim Pesan Kepada Verifiaktor </h2>
			</header>
			<div class="panel-body">
				<p>Kirim Pesan Kepada Verifikator</p>
				<textarea class="form-control" rows="5" name="msg2verifiaktor" id="msg2verifiaktor"></textarea>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-primary modal-confirm">Kirim</button>
						<button class="btn btn-default modal-dismiss">Cancel</button>
					</div>
				</div>
			</footer>
		</section>
	</div>
<?php
}
include(AdminFooter);
?>

<script>
	window.onload = function() {
		$('#pengesahan').validate({
			ignore: [],
			errorClass: 'error',
			rules: {
				sesuai_bap_pemeriksaan: { required: true },
				sesuai_data: { required: true },
				sesuai_redaksi: { required: true },
			},
			messages: {
				sesuai_bap_pemeriksaan: { required: 'Harus dicentang.' },
				sesuai_data: { required: 'Harus dicentang.' },
				sesuai_redaksi: { required: 'Harus dicentang.' },
			},
			errorPlacement: function(error, element) {
				error.insertAfter(element);
			},
			highlight: function(element, validClass) {
				$(element).parent().addClass('has-error');
			},
			unhighlight: function(element, validClass) {
				$(element).parent().removeClass('has-error');
			},
			submitHandler: function(form) {
				var stack_bar_bottom = {
					'dir1': 'up',
					'dir2': 'right',
					'spacing1': 0,
					'spacing2': 0
				};
				var formData = new FormData(document.getElementById('pengesahan'));
				$.ajax({
					url: "ajax.php",
					dataType: 'json',
					type: 'post',
					cache: false,
					data: formData,
					mimeType: 'multipart/form-data',
					contentType: false,
					processData: false,
					beforeSend: function() {
						$('#btn_simpan').prop('disabled', true);
						$('#actloading').show();
					},
					success: function(json) {
						if (json.stat) {
							var notice = new PNotify({
								title: 'Notification',
								text: json.msg,
								type: 'success',
								addclass: 'stack-bar-bottom',
								stack: stack_bar_bottom,
								width: '60%',
								delay: 1000,
								after_close: function() {
									window.location.href = "./list-persetujuan.php";
								}
							});
						} else {
							var notice = new PNotify({
								title: 'Notification',
								text: json.msg,
								type: 'warning',
								addclass: 'stack-bar-bottom',
								stack: stack_bar_bottom,
								width: '60%',
								delay: 2500
							});
						}

						$('#btn_simpan').prop('disabled', false);
						$('#actloading').hide();
					}
				});
			}
		})
	}
</script>