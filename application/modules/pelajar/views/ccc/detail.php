<?php 
$numb=1;

$jenis = [
	"Gas","Radas","Bahan-bahan kimia yang lain","Bekalan bahan-bahan penyelidikan yang lain","Perkhidmatan Saintifik &amp; Penyelidikan (Analisis data, Perkhidmatan makmal)","Bayaran perkhidmatan percetakan yang lain (banner/sijil/poster)",
];

?>
<div class="col-lg-12">
  <div class="form-with-tabs">
           <hr/>
            <div class="card">
				<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0 text-center">BORANG PEMOHONAN BAYARAN BENCHFEES</h5>
	
	</div>
			<p></p>
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
					
                  <div>
                    <div class="mb-3 mb-0 text-end">
                      <a  class="btn btn-info " href="<?=site_url("admin/ccc/tunt")?>">
                            <i class="ti ti-plus fs-4"></i>
                            Tambah 
                          </a>
                      
                    </div>
                 
                </div>
				
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
						<td><a type="button" class="btn btn-warning btn-sm" href="<?=site_url("admin/ccc/edittunt")?>"><i class="ti ti-edit me-1 fs-4"></i> Kemaskini </a></td>

                                               
                      
						
                    </tr>
                    <?php $numb++; }; ?>
                
	
                                               
                      
						
                    </tr>
				</tbody>
				
				</table>
	  <div class="p-3 border-top">
                    <div class="d-flex flex-wrap gap-6 align-items-center">
                     
                        <div class="text-center">
                          <div class="btn-group">
                          <button type="button" class="btn btn-danger"><i class="ti ti-trash me-1 fs-4"></i> Hapus </button>
                        </div> 
                      </div>
                    
                        
                     
                    
					</div>
					
				 </div>
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
                    <?php for($i=1;$i<=3;$i++) { ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="" /></td>
                        <td><?= $i; ?></td>
                        <td>OUT CAMPUS APPLICATION APPROVAL <?= $i; ?></td>
						<td>
							
                      
							<div>
                    <div class="mb-3 mb-0 text-end">
                      <a  class="btn btn-info " href="<?=site_url("admin/ccc/kenyataan")?>">
                            <i class="ti ti-plus fs-4"></i>
                            Tambah 
                          </a>
                      
                    </div>
                 
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
									<th>#</th>
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
									<td>
										<a type="button" class="btn btn-warning btn-sm" href="<?=site_url("admin/ccc/edittunt")?>"><i class="ti ti-edit me-1 fs-4"></i> Kemaskini </a>
										<a type="button" class="btn btn-danger btn-sm" href="<?=site_url("admin/ccc/edittunt")?>"><i class="ti ti-trash me-1 fs-4"></i> Hapus </a>
									</td>
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
		</div> 
	</div>
 </div>
</div>
