
 <?= form_open($this->uri->uri_string(),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form','class'=>'form-horizontal')) ?>
    	


<div class="box box-primary">
    <!-- form start -->
   <div class="box-header with-border">
              <h3 class="box-title">Maklumat Ibu Bapa/Penjaga</h3>
	</div>
   <div class="box-body">
			<div class="form-group <?= form_error('cov_nama_parent') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label">Nama Ibu Bapa/Penjaga</label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="cov_nama_parent" name="cov_nama_parent" maxlength="45" value="<?php echo set_value('cov_nama_parent', isset($data->COV_NAMA_PARENT) ? $data->COV_NAMA_PARENT : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
			<div class="form-group <?= form_error('cov_ic') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label">No K/P Ibu Bapa/Penjaga</label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="cov_ic" name="cov_ic" maxlength="45" value="<?php echo set_value('cov_ic', isset($data->COV_IC) ? $data->COV_IC : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
			<div class="form-group <?= form_error('cov_hp') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label"><?=lang("cov_hp")?></label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="cov_hp" name="cov_hp" maxlength="45" value="<?php echo set_value('cov_hp', isset($data->COV_IC) ? $data->COV_IC : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
		  	
	  </div>
	
</div>
<div class="box box-primary">
    <!-- form start -->
   <div class="box-header with-border">
              <h3 class="box-title">Maklumat Pelajar</h3>
	</div>
   <div class="box-body">
			
			<div class="form-group <?= form_error('cov_matrik') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label">No Matrik Pelajar</label>
			    <div class="col-sm-2">
			    	<input type="text" class="form-control" id="cov_matrik" name="cov_matrik" maxlength="45" value="<?php echo set_value('cov_matrik', isset($data->COV_MATRIK) ? $data->COV_MATRIK : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
			<div class="form-group <?= form_error('nama_menu') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label">Nama Pelajar</label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="nama_menu" name="nama_menu" maxlength="45" value="<?php echo set_value('cov_hp', isset($data->HP) ? $data->HP : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
			<div class="form-group <?= form_error('nama_menu') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-3 control-label">Program Pengajian</label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="nama_menu" name="nama_menu" maxlength="45" value="<?php echo set_value('cov_hp', isset($data->HP) ? $data->HP : ''); ?>" required autofocus>
					<div id="gps"></div>
				</div>
		  	</div>
			
		    
			<!--
		  	<div class="form-group <?= form_error('sorting') ? ' has-error' : ''; ?>">
			    <label for="sorting" class="col-sm-2 control-label"><?= lang('menu_telp1') ?></label>
			    <div class="col-sm-2">
			    	<input type="text" class="form-control" id="sorting" name="sorting" maxlength="15" value="<?php echo set_value('sorting', isset($data->SORTING) ? $data->SORTING : ''); ?>" required="">
			    </div>
		  	</div> -->
		  	
	  </div>
	
</div>
<div class="box box-primary">
    <!-- form start -->
   <div class="box-body">
   Saya dengan ini mengaku akan bertanggungjawab sepenuhnya dan akan
menanggung apa-apa jua risiko dan akan melepaskan UNIVERSITI MALAYSIA
TERENGGANU dari sebarang liabiliti atau tanggungan atas apa-apa jua sebab pun
apabila saya dengan rela hati membawa pulang anak/anak jagaan saya dalam
tempoh Perintah Kawalan Pergerakan 18 Mac 2020 – 31 Mac 2020.
Saya juga mengakui bahawa: 
<ol>
<li>Anak/anak jagaan saya tidak mempunyai sebarang simptom COVID-19
semasa saya mengambilnya untuk dibawa pulang;
</li>
<li>Akan bertanggungjawab sepenuhnya segala risiko yang mungkin dihadapi
apabila membawa anak/anak jagaan saya pulang;
</li>
<li>Saya dan ahli keluarga saya tidak mempunyai apa-apa simptom COVID-19;</li>
<li>Saya dan ahli keluarga saya tidak pernah melawat dalam tempoh masa dua
(2) bulan ini ke negara China, Jepun, Korea Selatan, Hong Kong, Taiwan,
Itali, Iran, Jerman, Sepanyol, Denmark atau Perancis;
</li>
<li>Saya dan ahli keluarga saya tidak terlibat dalam perhimpunan Jemaah Tabligh
di Masjid Sri Petaling, Minggu Penyelidikan dan Inovasi 2020 (MPI 2020) atau mana-mana perhimpunan atau program awam
yang telah diisytiharkan terkesan dengan COVID-19 oleh pihak Kerajaan.
</li>
</ol>   
   </div>
 </div>
			    	
<?= form_close() ?>
<script>
//getLocation();
var x = document.getElementById("gps");

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) { 
  x.value = + position.coords.latitude +  
  "," + position.coords.longitude;
}

$(document).ready(function() {
 // executes when HTML-Document is loaded and DOM is ready
 getLocation();
});
</script>