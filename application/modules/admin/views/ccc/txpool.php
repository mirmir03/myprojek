<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;
?>
<div class="row">
            <div class="col-lg-4">
              <div class="card w-100">
                <div class="card-body">
                  <h4 class="card-title">Peratus Baki</h4>
                  <h6 class="card-subtitle fw-normal">24 new support ticket request generate</h6>
                  <div id="sales-of-the-month" class="mt-4 mx-auto"></div>
                  <ul class="list-unstyled mb-0 hstack justify-content-center gap-3 pt-4">
                    <li>
                      <h6 class="text-muted mb-0 hstack gap-2 fw-bold">
                        <span class="text-bg-info round-10 rounded-circle"></span>Item A
                      </h6>
                    </li>
                    <li>
                      <h6 class="text-muted mb-0 hstack gap-2 fw-bold">
                        <span class="text-bg-danger round-10 rounded-circle"></span>Item B
                      </h6>
                    </li>
                    <li>
                      <h6 class="text-muted mb-0 hstack gap-2 fw-bold">
                        <span class="text-bg-warning round-10 rounded-circle"></span>Item C
                      </h6>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-6 col-lg-6 col-xl-7">
                          <h4 class="card-title mb-7">Amaun Pool Tabung Tak Aktif</h4>
                          <div>
                            <h2 class="fs-9 fw-light">RM 30,528</h2>
                           
                          </div>
                        </div>
                        <div class="col-6 col-lg-6 col-xl-5">
                          <div id="sales-prediction"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col-6 col-lg-6 col-xl-7">
                          <h4 class="card-title mb-7">Baki</h4>
                          <div>
                            <h2 class="fs-9 fw-light">RM 4,316</h2>
                         
                          </div>
                        </div>
                        <div class="col-6 col-lg-6 col-xl-5">
                          <div id="sales-difference"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card w-100">
                <div class="card-body">
                  <div class="d-flex flex-row">
                    <div class="">
                      <img src="../assets/images/profile/user-6.jpg" alt="user" class="rounded-circle"
                        width="96" />
                    </div>
                    <div class="ps-9">
                      <h3 class="fs-6">Mini Statistik BenchFee FSKM</h3>
                      
                    </div>
                  </div>
                  <div class="row mt-4 pt-3 pb-0">
                    <div class="col text-center border-end">
                      <h2 class="">14</h2>
                      <h6 class="text-muted fw-bold mb-0">Aktif</h6>
                    </div>
                    <div class="col text-center border-end">
                      <h2 class="">54</h2>
                      <h6 class="text-muted fw-bold mb-0">Tak Aktif</h6>
                    </div>
                    <div class="col text-center">
                      <h2 class="">145</h2>
                      <h6 class="text-muted fw-bold mb-0">Permohonon</h6>
                    </div>
                  </div>
                </div>
                <div class="card-body border-top">
                  <p class="text-center text-muted mb-9 fs-3" data-simplebar style="height: 62px">
                    FAKULTI SAINS KOMPUTER DAN MATEMATIK (FSKM)
                  </p>
                  <ul class="list-unstyled hstack justify-content-center gap-9 mb-0">
                    <li>
                      <a class="text-muted" href="javascript:void(0)"><i class="ti ti-brand-facebook fs-6"></i></a>
                    </li>
                    <li>
                      <a class="text-muted" href="javascript:void(0)"><i class="ti ti-brand-twitter fs-6"></i></a>
                    </li>
                    <li>
                      <a class="text-muted" href="javascript:void(0)"><i class="ti ti-brand-linkedin fs-6"></i></a>
                    </li>
                    <li>
                      <a class="text-muted" href="javascript:void(0)"><i class="ti ti-brand-youtube fs-6"></i></a>
                    </li>
                    <li>
                      <a class="text-muted" href="javascript:void(0)"><i class="ti ti-world fs-6"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- Row -->
<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>	
<div class="widget-content searchable-container list">
            <div class="card card-body">
              <div class="row">
                <div class="col-md-4 col-xl-3">
                 
                    
                  
                </div>
                <div
                  class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                  <div class="action-btn show-btn">
                    <a href="javascript:void(0)"
                      class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center ">
                      <i class="ti ti-trash text-danger me-1 fs-5"></i> Delete All Row
                    </a>
                  </div>
                  
				  <?php if ($ENABLE_ADD) : ?>
	  	        <a href="<?= module_url('ccc/addtx') ?>" class="btn btn-primary d-flex align-items-center" title="<?= lang('ccc_btn_new') ?>"><i class="ti ti-users text-white me-1 fs-5"></i><?= lang('ccc_btn_new') ?></a>
                <?php endif; ?>
                </div>
              </div>
            </div>
</div>		
<div class="card">
	
        <div class="card-header">Senarai Permohonan
		
		</div>
		
	<div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="50">No.</th>
						<th>Tujuan</th>
						<th>Status Kelulusan</th>
						<th>Pemohon</th>
						<th>Tarikh Hantar</th>
						<th>Kelulusan</th>
						<th>Tarikh Lulus</th>
						
						
						<th>Muat Turun</th>
						
						
						
<th><?=lang('cov_bank_kod')?> (RM)</th>


<th>Lihat</th>

                        
                    </tr>
                </thead>
                <tbody>
                 
                  
					<tr>
					    <td>1</td>
						<td>PERBELANJAAN BENGKEL SAMPLING DI TASIK KENYIR</td>
						<td>KELULUSAN KETUA PTJ</td>
						<td>PUAN MANIZA MAIDIN</td>
						<td>14/10/2024</td>	
						<td>PROF TS DR MUHAMMAD SUZURI HITAM</td>
						
						<td>14/10/2024</td>
						<td><i class="fs-5 ti ti-download"></i></td>
						<td>1,200.00</td>
						<td><button class="btn btn-flat btn-primary">Lihat</button></td>
                    </tr>
					<tr>
					    <td>2</td>
						<td>PEMBELIAN KEPERLUAN STUDENT RESEARCH DAY 2024</td>
						<td>LULUS</td>
						<td>PUAN MANIZA MAIDIN</td>
						<td>14/10/2024</td>	
						<td>PROF TS DR MUHAMMAD SUZURI HITAM</td>
						
						<td>14/10/2024</td>
						<td><i class="fs-5 ti ti-download"></i></td>
						<td>2,000.00</td>
						<td><button class="btn btn-flat btn-primary">Lihat</button></td>
                    </tr>
                </tbody>
	  </table>
	  
	  <?php if(!$ENABLE_DELETE) { ?>
		<input type="button" name="delete1" class="btn btn-danger" id="delete-me" value="Hapus" onclick="confirm_delete(this.form) ">
		<input type="hidden" name="delete" id="isdelete">
	  <?php } ?>
	  
	</div><!-- /.box-body -->
	<div class="box-footer clearfix">
		<?php
		// echo $this->pagination->create_links(); 
		?>
	</div>
	
	
	
</div><!-- /.box --> <?php form_close(); ?>

<script>
function confirm_delete(myform)
{
	if (confirm('<?= (lang('ccc_delete_confirm')); ?>'))
	{
		$("#isdelete").val(1);
		myform.submit();
	}
	
	return false;
}
</script>