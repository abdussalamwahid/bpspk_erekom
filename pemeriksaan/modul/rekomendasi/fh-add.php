<?php

use App\Constants\ProductClassification;
use App\Models\SatuanBarang;

require_once("config.php");
$SCRIPT_FOOT = "
<script>
$(document).ready(function(){
	$('ul li.nav-hasil').addClass('active');
	var ik;
	var pr;

	$('.jns_ikan').change(function(){
		ik=$('.jns_ikan').val();
		if(pr!='' &&ik!='' && pr!=undefined){
			get_ket(ik,pr);
		}
	});

	$('.jns_produk').change(function(){
		pr=$('.jns_produk').val();
		if(ik!='' && pr!='' && ik!=undefined){
			get_ket(ik,pr);
		}
	});
});
function get_ket(ik,pr){
	$.ajax({
		url:'ajax.php',
		dataType:'html',
		type:'post',
		data:'a=getciri&ik='+ik+'&pr='+pr,
		beforeSend:function(){
		},
		success: function(html){
			$('.ket').html(html);
		}
	});
}
</script>
<script src=\"hasil-pemeriksaan.js\"></script>
";

$idpengajuan=U_IDP;
$idperiksa=base64_decode($_GET['p']);
if($idperiksa==""){
	header('location:./input-hasil.php');
}
$sql->get_row('tb_pemeriksaan',array('id_periksa'=>$idperiksa));
if($sql->num_rows>0){
if(ctype_digit($idpengajuan)){
?>
<body class="hold-transition skin-blue sidebar-mini">

<div class="content-wrapper">
	<section class="content-header">
		<h1>
		Hasil Pemeriksaan
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo c_URL;?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="./input-hasil.php">Hasil Pemeriksaan</a></li>
			<li class="active">Input Hasil Pemeriksaan</li>
		</ol>
	</section>
	<section class="content">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Input Hasil Pemeriksaan</h3>
			</div>
			<div class="box-body">
				<form class="form-horizontal" action="" method="POST" name="formhasilperiksa" id="formhasilperiksa">
                    <input type="hidden" name="a" value="add-hsl-periksa">
					<input type="hidden" name="idp" value="<?php echo base64_encode($idpengajuan);?>" >
					<input type="hidden" name="idpr" value="<?php echo base64_encode($idperiksa);?>" >
					<div class="form-group">
						<label class="col-sm-3 control-label">Jenis Ikan</label>
						<div class="col-sm-9">
							<select class="form-control jns_ikan" name="jenis_ikan">
								<option value="">-Pilih-</option>
								<?php
								$sql->get_all('ref_data_ikan');
								if($sql->num_rows>0){
									foreach($sql->result as $r){
									echo '<option value="'.$r['id_ikan'].'">'.$r['nama_ikan'].' ('.$r['nama_latin'].')</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Asal Komoditas</label>
						<div class="col-md-4 ak_div">
							<select name="asal_komoditas_opt" class="form-control asal_komoditas">
								<option value="">-Pilih-</option>
								<?php
								$ak=$sql->run("SELECT DISTINCT(asal_komoditas) ak FROM tb_hsl_periksa where ref_idp='$idpengajuan'");
								if($ak->rowCount()>0){
									foreach($ak->fetchAll() as $rak){
										echo '<option value="'.$rak['ak'].'">'.$rak['ak'].'</option>';
									}
								}
								?>
								<option value="lainnya">Lainnya</option>
							</select>
							<input type="text" style="display:none" name="asal_komoditas" class="form-control custom_ak">
						</div>
					</div>
					
					
					<div class="form-group">
						<label class="col-sm-3 control-label"> Jumlah Kemasan </label>
						<div class="col-md-2">
							<input type="number" step="any" name="kemasan" class="form-control">
						</div>
					</div>

					<?php 
						$satuan_barangs = SatuanBarang::all();
					?>

					<div class="form-group">
						<label class="col-sm-3 control-label"> Satuan Kemasan </label>
						<div class="col-md-2">
							<select 
								class="form-control"
								name="id_satuan_barang"
								id="id_satuan_barang"
								>
								<?php foreach($satuan_barangs as $satuan_barang): ?>
								<option value="<?= $satuan_barang->id ?>">
									<?= $satuan_barang->nama ?>
								</option>
								<?php endforeach ?>
							</select>
						</div>
					</div>

					<script>
						window.onload = function() {
							

							new Vue({
								el: "#app",

								props: {
								},

								data: {

									product_classification: JSON.parse('<?= json_encode(ProductClassification::get()) ?>'),
								
									product_type: null,
									product_condition: null,
									product_category: null,
								},

								watch: {
									product_type: function() {
										this.product_condition = null
										this.product_category = null
									},
									
									product_condition: function() {
										this.product_category = null
									},
								},

								computed: {
									product_condition_options() {
										if (!this.product_type) {
											return []
										}

										return this.product_classification[this.product_type].items
									},

									product_category_options() {
										if (!this.product_condition) {
											return []
										}

										return this.product_classification[this.product_type].items
											[this.product_condition].items
									}

								}
							})


						}
					</script>

					<div id="app">
						<div class="form-group">
							<label 
								class="control-label col-sm-3"
								for="product_type">
								Produk:
							</label>

							<div class="col-md-6">
								<select 
									class="form-control"
									name="product_type"
									id="product_type"
									v-model="product_type"
									>

									<option 
										v-for="(product_type_data, product_type_name) in product_classification"
										>
										{{ product_type_name }}
									</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label 
								class="control-label col-sm-3"
								for="product_condition">
								Kondisi:
							</label>

							<div class="col-md-6">
								<select 
									class="form-control"
									name="product_condition"
									id="product_condition"
									v-model="product_condition"
									>

									<option 
										v-for="(product_condition_data, product_condition_name) in product_condition_options"
										>
										{{ product_condition_name }}
									</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label 
								class="control-label col-sm-3"
								for="product_category">
								Jenis Produk:
							</label>

							<div class="col-md-6">
								<select 
									class="form-control"
									name="product_category"
									id="product_category"
									v-model="product_category"
									>

									<option 
										v-for="(product_category_data, product_category_name) in product_category_options"
										>
										{{ product_category_name }}
									</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-md-4">-- Sampel Terkecil --</label>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Panjang Sampel (Cm)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="pjg" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Lebar Sampel (Cm)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="lbr" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Berat Sampel(Kg)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="berat" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4">-- Sampel Terbesar --</label>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Panjang Sampel (Cm)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="pjg2" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Lebar Sampel (Cm)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="lbr2" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Berat Sampel(Kg)</label>
						<div class="col-md-3">
							<input type="number" step="any" name="berat2" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Berat Total (Kg)</label>
						<div class="col-md-4">
							<input type="number" step="any" name="berat_tot" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Keterangan</label>
						<div class="col-md-5">
							<textarea class="form-control ket" rows="5" name="ket"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-md-5">
							<button type="submit" class="btn btn-sm btn-primary btn-flat" id="btn_save">Tambah Hasil</button>
							<a href="./input-hasil.php" class="btn btn-sm btn-danger btn-flat">Kembali</a>
							<span id="actloadingmd" style="display:none"><i class="fa fa-spin fa-spinner"></i> Menyimpan....</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-md-6">
						<p>catatan : <span class="text-alert alert-danger">Angka Desimal Menggunakan . <strong>(titik) cth: 90.2Kg</strong></span></p>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>

</div>
</body>
<?php
}
}
include(AdminFooter);
?>
