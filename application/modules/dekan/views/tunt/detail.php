<?php 
$numb=1;

$jenis = [
	"Gas","Radas","Bahan-bahan kimia yang lain","Bekalan bahan-bahan penyelidikan yang lain","Perkhidmatan Saintifik &amp; Penyelidikan (Analisis data, Perkhidmatan makmal)","Bayaran perkhidmatan percetakan yang lain (banner/sijil/poster)",
];

?>
<div class="col-lg-12">
  <div class="card">
	<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0">BORANG PEMOHONAN BAYARAN BENCHFEES</h5>
	
	</div>
	<div class="card-body">
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">Name</label>
		<div class="col-sm-9">
		  NOORHAZREEN BINTI AMRI
		</div>
	  </div>
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Matrik</label>
		<div class="col-sm-9">
		  S62000
		</div>
	  </div>
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">No K/P</label>
		<div class="col-sm-9">
		  910204085986
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Phone
		  no</label>
		<div class="col-sm-9">
		  <div class="input-group">
			0129876552
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tujuan</label>
		<div class="col-sm-9">
		  <div class="input-group">
			MENGAMBIL SAMPLE CORAL DI PULAU BIDONG
		  </div>
		</div>
	  </div>
	  
	 
	  </div>
	</div>
  </div>
<div class="col-lg-12">
  <div class="form-with-tabs">
           
            <div class="card">
				<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0">MAKLUMAT TUNTUTAN BAYARAN BENCHFEES</h5>
	
	</div>
              <ul class="nav nav-pills user-profile-tab border-bottom" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-6 fw-bold"
                    id="pills-personal-info-tab" data-bs-toggle="pill" data-bs-target="#pills-personal-info"
                    type="button" role="tab" aria-controls="pills-personal-info" aria-selected="true">Perbelanjaan
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-6 fw-bold"
                    id="pills-account-details-tab" data-bs-toggle="pill" data-bs-target="#pills-account-details"
                    type="button" role="tab" aria-controls="pills-account-details" aria-selected="false"> Pengangkutan dan penginapan </button>
                </li>
                
              </ul>
              <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-personal-info" role="tabpanel"
                    aria-labelledby="pills-personal-info-tab" tabindex="0">
					
                  
				
				<table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="column-check" style="width: 30px;"><input class="check-all" type="checkbox" /></th>
                        <th width="50">#</th>
                        <th>Jenis Tuntutan</th>
						<th>Rujukan/No.Resit</th>
						<th>Tarikh</th>

<th><?=lang('cov_bank_kod')?> (RM)</th>
<th>Catatan</th>

                        
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=0;$i<5;$i++) { ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="" /></td>
                        <td><?= $numb; ?></td>
                        <td><?= $jenis[rand(1,count($jenis)-1)]; ?></td>
						<td><?= rand(10000,99999); ?></td>
						<td>13/12/2024 </td>
						
						<td><?=rand(100,999)?>.00</td>
						<td>LOREM IPSUM OKPSSAWER</td>
						
                                               
                      
						
                    </tr>
                    <?php $numb++; }; ?>
                
	
                                               
                      
						
                   
				</tbody>
				
				</table>
	  
					
				
              </div>
							
					
					
					
                  <div class="tab-pane fade" id="pills-account-details" role="tabpanel"
                    aria-labelledby="pills-account-details-tab" tabindex="0">
                    <div class="row">
                      <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="column-check" style="width: 30px;"><input class="check-all" type="checkbox" /></th>
                        <th width="50">#</th>
                        <th>Keluar Kampus</th>
						<th>Keterangan</th>
						<th></th>
						
                        
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1;$i<=1;$i++) { ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="" /></td>
                        <td><?= $i; ?></td>
                        <td>OUT CAMPUS APPLICATION APPROVAL <?= $i; ?></td>
						<td>
							
                      
							<div>
                   
                 
                </div>
                    
							<table class="table table-hover">
								<tr>
									<th>Masa Bertolak</th>
									<th>Masa Sampai</th>
									<th>Tujuan/Tempat</th>
									<th>Penginapan</th>
									<th>Pengangkutan Awam</th>
									<th>Perbatuan (KM)</th>
									<th>Jumlah</th>
									
								</tr>
								<?php for($j=1;$j<=3;$j++) { ?>
								<tr>
									<td>01/10/2024 10.00</td>
									<td>01/10/2024 12.00</td>
									<td>KENYIR</td>
									<td>60.00 <br><button type="button"
                      class="btn mb-1 btn-secondary rounded-circle round-40 btn-sm d-inline-flex align-items-center justify-content-center">
                      <i class="fs-5 ti ti-download"></i>
                    </button></td>
									<td>10.00<br><button type="button"
                      class="btn mb-1 btn-secondary rounded-circle round-40 btn-sm d-inline-flex align-items-center justify-content-center">
                      <i class="fs-5 ti ti-download"></i>
                    </button></td>
									<td>50</td>
									<td>90.00</td>
									
								</tr>
								<?php }; ?>
							</table>
						</td>
						

                                               
                      
						
                    </tr>
                    <?php $numb++; }; ?>
                
	
                                               
                      
						
                    </tr>
				</tbody>
				
				</table>
                    </div>
                 
                  
		</div> 
	</div>


<div class="col-lg-12">
  <div class="card">
	<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0">SOKONGAN</h5>
	
	</div>
	<div class="card-body">
		<div class="mb-4 row align-items-center">
			<label for="startDate" class="form-label fw-semibold col-sm-2 col-form-label text-end">Semakan Oleh</label>
			<div class="col-sm-10">
			  <div class="input-group">
				PUAN MANIZA MAIDIN (07/11/2024)
			  </div>
			</div>
		</div> 
		<div class="mb-4 row align-items-center">
			<label for="startDate" class="form-label fw-semibold col-sm-2 col-form-label text-end">Pengesahan Oleh</label>
			<div class="col-sm-10">
			  <div class="input-group">
				PROFESOR MADYA DR MUSTAFA BIN MAN (08/11/2024)
			  </div>
			</div>
		</div>
		<div class="mb-4 row align-items-center">
			<label for="startDate" class="form-label fw-semibold col-sm-2 col-form-label text-end">Catatan</label>
			<div class="col-sm-10">
			  <div class="input-group">
				<textarea type="text" class="form-control" id="exampleInputText6" placeholder="Alasan" rows"4"> </textarea>
			  </div>
			</div>
		</div>
		<div class="row">
		  <div class="col-sm-3"><a class="btn btn-warning" href="<?=module_url("ccc/listmohon")?>"><< Pulang Semula Ke Pemohon</a></div>
		  <div class="col-sm-9 text-end">
			<div class="d-flex align-items-center gap-6 text-end">
			  <button class="btn bg-danger-subtle text-danger">Tidak Lulus</button>
			  <a class="btn btn-primary" href="<?=module_url("ccc/listmohon")?>">Lulus >></a>
			  
			</div>
		  </div>
		</div>	 
	  </div>
	</div>
  </div>
  
  
