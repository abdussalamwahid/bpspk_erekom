<?php
//@include_once ("../../engine/render.php");
//cek 
/*
1. Cek Fitur Kusioner Aktif atau tidak pada tabel web_setting
2. cek jumlah pengajuan
3. jika kurang dari 6 skip kuisioner
4. jika sama sama dgn 6 tampilkan kuisioner
5. jika lebih dari 6, cek pengisian kuisioner terakhir.. 
6. cari jumlah pengajuan dalam rentang waktu kuisioner terakhir smpi sekarang.. + 1 
7. klo jumlahny udah sampai 6.. tampilkan kuisioner
*/
$sql->get_row('web_setting',array('sid'=>2,'ws_key'=>'kuisioner_stat','ws_value'=>'yes'));
if($sql->num_rows>0){
	$jlh_permohonan=$sql->get_count('tb_permohonan',array('ref_iduser'=>U_ID));
	$nextjlh=$jlh_permohonan+1;
	if($nextjlh==6){
		
		$sql->order_by=" q_answered DESC ";
		$sql->limit=" 1";
		$sql->get_row('tb_kuisioner_s',array('ref_idpemohon'=>U_ID));
		if($sql->num_rows>0){
			$r=$sql->result;
			$lastkuisionerdate=$r['q_answered'];

			$q=$sql->run("SELECT COUNT(idp) jlh FROM tb_permohonan WHERE ref_iduser='".U_ID."' AND tgl_pengajuan> '".$lastkuisionerdate."' ");
			$r=$q->fetch();
			if(($r['jlh']+1)>5){
				header('location:?kuisioner');
			}
		}else{
		    header('location:?kuisioner');
		}
	}else if($nextjlh>6){
		$sql->order_by=" q_answered DESC ";
		$sql->limit=" 1";
		$sql->get_row('tb_kuisioner_s',array('ref_idpemohon'=>U_ID));
		if($sql->num_rows>0){
			$r=$sql->result;
			$lastkuisionerdate=$r['q_answered'];

			$q=$sql->run("SELECT COUNT(idp) jlh FROM tb_permohonan WHERE ref_iduser='".U_ID."' AND tgl_pengajuan> '".$lastkuisionerdate."' ");
			$r=$q->fetch();
			if(($r['jlh']+1)>5){
				header('location:?kuisioner');
			}
		}
	}
}


//$ITEM_HEAD = "bootstrap.css, font-awesome.css, magnific-popup.css, datepicker3.css, theme.css, default.css, theme-custom.css, modernizr.js";
//$ITEM_FOOT = "jquery.js, jquery.browser.mobile.js, bootstrap.js, nanoscroller.js, bootstrap-datepicker.js, magnific-popup.js, jquery.placeholder.js, pnotify.custom.js, theme.js, theme.custom.js, theme.init.js, jquery.validate.js, jquery.validate.msg.id.js";

//@include(c_THEMES."meta.php");

$SCRIPT_FOOT="
<script>
$(document).ready(function(){
	$('nav li.nav1').addClass('nav-active');
	$('#btn_add_brg').click(function(){
		var tr    = $('tr.row_clone:first');
	    var clone = tr.clone();
	    clone.find(':text').val('');
	    clone.find('input').prop('disabled', false);
	    clone.show();
	    $('tr.row_clone:last').after(clone);

	    clone.find('.del_thisrow').on('click', function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
		});
	});
	
	$('#form_pengajuan').validate({
		ignore: [],
		errorClass: 'error',
		rules:{
			nm_penerima:{required:true},
			alamat_penerima:{required:true},
			alamat_gudang:{required:true},
			sop:{required:true},
			persetujuan2:{required:true},
			alat_angkut:{required:true}
		},
		messages:{
			nm_penerima:{required:'Nama Penerima Harap Diisi.'},
			alamat_penerima:{required:'Alamat Penerima Harap Diisi.'},
			alamat_gudang:{required:'Alamat Gudang Harap Diisi.'},
			sop:{required:'Harus Dicentang.'},
			persetujuan2:{required:'Harus Dicentang.'},
			alat_angkut:{required:'Silakan Pilih Jenis Alat Angkut.'}
		},
		errorPlacement: function (error, element) {
	        error.insertAfter(element);
	    },
	    highlight: function (element, validClass) {
	        $(element).parent().addClass('has-error');
	    },
	    unhighlight: function (element, validClass) {
	        $(element).parent().removeClass('has-error');
	    },
		submitHandler: function(form) {
			var stack_bar_bottom = {'dir1': 'up', 'dir2': 'right', 'spacing1': 0, 'spacing2': 0};
			$.ajax({
				url:'".c_STATIC."pengajuan/modul/pengajuan/ajax.php',
				dataType:'json',
				type:'post',
				cache:false,
				data:$('#form_pengajuan').serialize(),
				beforeSend:function(){
					$('#btn_submit').prop('disabled', true);
					$('#actloading').show();	
				},
				success:function(json){	
					if(json.stat){
						var notice = new PNotify({
							title: 'Notification',
							text: json.msg,
							type: 'success',
							addclass: 'stack-bar-bottom',
							stack: stack_bar_bottom,
							width: '60%',
							delay:1000,
							after_close:function(){
								location.reload();
							}
						});
					}else{
						var notice = new PNotify({
							title: 'Notification',
							text: json.msg,
							type: 'warning',
							addclass: 'stack-bar-bottom',
							stack: stack_bar_bottom,
							width: '60%',
							delay:2500
						});
					}
					$('#btn_submit').prop('disabled', false);
					$('#actloading').hide();
				}
			});
		return false;
		}
	});
});
</script>";
?>
<section role="main" class="content-body">
	<header class="page-header">
		<h2>Pengajuan Rekomendasi</h2>
		<div class="right-wrapper pull-right">
			<ol class="breadcrumbs">
				<li><a href="<?php echo c_DOMAIN;?>"><i class="fa fa-home"></i></a></li>
				<li><span>Pengajuan Rekomendasi</span></li>
			</ol>
			<a class="sidebar-right-toggle"><i class="fa fa-chevron-left"></i></a>
		</div>
	</header>
	<form id="form_pengajuan" method="post">
		<input type="hidden" name="a" value="pr">
		<div class="row">
			<?php
			if(container(App\Services\Auth::class)->isVerified()){
				$sql->get_row('tb_biodata',array('ref_iduser'=>U_ID),array('idbio','alamat'));
				if($sql->num_rows>0){
					$bio=$sql->result;
					$sql->get_row('tb_berkas',array('ref_iduser'=>U_ID,'jenis_berkas'=>1),'idb');
					if($sql->num_rows>0){
						?>
						<div class="col-md-12">
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
									</div>
									<h2 class="panel-title">Pengajuan Rekomendasi</h2>
								</header>
								<div class="panel-body">
									<h4>Tujuan Pengiriman</h4>
									<div class="form-group">
										<label class="control-label col-md-3">Nama Penerima</label>
										<div class="col-md-4">
											<input type="text" name="nm_penerima" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Alamat Penerima</label>
										<div class="col-md-5">
											<textarea class="form-control" name="alamat_penerima"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Untuk</label>
										<div class="col-md-3">
											<select class="form-control" name="jenis_tujuan">
												<option value=''>-Pilih-</option>
												<option value='perdagangan'>Perdagangan</option>
												<option value='souvenir'>Souvenir</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Alat Angkut</label>
										<div class="col-md-3">
											<select class="form-control" name="alat_angkut">
												<option value=''>-Pilih-</option>
												<option value='udara'>Pesawat Udara</option>
												<option value='laut'>Kapal Laut</option>
												<option value='darat'>Kendaraan Darat</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Lokasi Pemeriksaan Sampel</label>
										<div class="col-md-5">
											<select class="form-control" name="alamat_gudang">
												<option value="">-- Pilih Lokasi Pemeriksan --</option>
												<option value="<?php echo $bio['alamat'];?>"><?php echo $bio['alamat'];?></option>
												<option value="Kantor LPSPL Serang">Kantor LPSPL Serang</option>
												<option value="Kantor LPSPL Serang">Kantor Satker Balikpapan, LPSPL Serang</option>
												<option value="Kantor LPSPL Serang">Kantor Satker Banjarmasin, LPSPL Serang</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Keterangan</label>
										<div class="col-md-5">
											<textarea class="form-control" name="ket"></textarea>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Persetujuan</label>
										<div class="checkbox col-md-5">
											<label>
												<input type="checkbox" name="sop" value="yes">
												Saya Menyetujui <a href="#prosedur" data-toggle="modal" data-target="#prosedurpelayanan">Standar Prosedur Pelayanan LPSPL Serang</a>
											</label>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3"></label>
										<div class="checkbox col-md-5">
											<label>
												<input type="checkbox" name="persetujuan2" value="yes">
												Dengan ini saya menyetujui kewenangan operator pelayanan untuk menyimpan pendaftaran serta menjaga kerahasiaanya, serta semua dokumen pendaftaran yang berhubungan dengan proses permohonan, tujuan pengiriman, sampai terbitnya surat e-rekomendasi akan dijamin kerahasiaannya.
											</label>
										</div>
									</div>
									<hr>
									<h4>Data Barang</h4>
									<table class="table table-bordered" id="tblbarang">
										<thead>
											<tr>
												<th class="text-center" >Nama Barang</th>
												<th class="text-center" width="15%">Kemasan<br>(Colly)</th>
												<th class="text-center" width="15%">Jumlah/<br>Berat (Kg)</th>
												<th class="text-center" width="20%">Asal Komoditas</th>
												<th class="text-center" width="5%">Aksi</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input type="text" name="nm_brg[]" class="form-control"></td>
												<td><input type="text" name="kuantitas[]" class="form-control"></td>
												<td><input type="text" name="jlh[]" class="form-control"></td>
												<td><input type="text" name="asal_komoditas[]" class="form-control"></td>
												<td></td>
											</tr>
											<tr class="row_clone" style="display:none">
												<td><input type="text" disabled name="nm_brg[]" class="form-control"></td>
												<td><input type="text" disabled name="kuantitas[]" class="form-control"></td>
												<td><input type="text" disabled name="jlh[]" class="form-control"></td>
												<td><input type="text" disabled name="asal_komoditas[]" class="form-control"></td>
												<td><a href="#" class="btn btn-sm btn-danger del_thisrow" title="Hapus Baris Ini">X</a></td>
											</tr>
										</tbody>
										<tfoot>
											<tr id="addrow">
												<td colspan="5">
													<p>catatan : <span class="text-alert alert-danger">Angka Desimal Menggunakan . <strong>(titik) cth: 90.2Kg</strong></span></p>
													<a href="#addrow" id="btn_add_brg" class="btn btn-sm btn-default">Tambah Barang (+)</a>
													</td>
											</tr>
										</tfoot>
									</table>
									
								</div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-md-12"><button type="submit" id="btn_submit" class="btn btn-primary">Kirim Permohonan Rekomendasi</button></div>
										<span id="actloading" style="display:none"><i class="fa fa-spin fa-spinner"></i> Loading...</span>
									</div>
								</div>
							</section>
						</div>
						<?php
					}else{
						?>
						<div class="col-md-12">
							<div class="alert alert-danger">
								<strong>Maaf,</strong> Silakan Upload Tandatangan Anda Sebelum Mengajukan Permohonan Rekomendasi. Klik <a href="?biodata"><strong>Di Sini</strong></a> Untuk Melengkapi Biodata Anda.
							</div>
						</div>
						<?php
					}
				}else{
					?>
					<div class="col-md-12">
						<div class="alert alert-danger">
							<strong>Maaf,</strong> Anda Harus Melakukan Melengkapi Biodata Anda untuk menggunakan fasilitas ini. Klik <a href="?biodata"><strong>Di Sini</strong></a> Untuk Melengkapi Biodata Anda.
						</div>
					</div>
					<?php
				}
			}else{
				?>
				<div class="col-md-12">
					<div class="alert alert-danger">
						<strong>Maaf,</strong> Anda Harus Melakukan Verifikasi Akun Terlebih Dahulu untuk menggunakan fasilitas ini. Klik <a href="?verifikasi"><strong>Di Sini</strong></a> Untuk Melakukan Verifikasi Akun.
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</form>
</section>
<div id="prosedurpelayanan" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
          <!-- <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Modal Header</h4>
          </div> -->
          <div class="modal-body">
            <?php
            $sql->get_row('tb_maklumat',array('id'=>1),array('isi_maklumat'));
            if($sql->num_rows>0){
              $rr=$sql->result;
              echo $rr['isi_maklumat'];
            }
            ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
          </div>
        </div>

      </div>
    </div>
<?php
include(AdminFooter);
?>
