<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;
?>
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
	  	        <a href="<?= module_url('ccc/addkeny') ?>" class="btn btn-primary d-flex align-items-center" title="<?= lang('ccc_btn_new') ?>"><i class="ti ti-users text-white me-1 fs-5"></i><?= lang('ccc_btn_new') ?></a>
                <?php endif; ?>
                </div>
              </div>
            </div>
</div>		
<div class="card">
	
        <div class="card-header">Senarai Kenyataan</div>
		
	<div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="50"><input type="checkbox"></th>
                        <th width="50">#</th>
						<th>Jenis</th>
						<th>Rujukan/No. Resit</th>
						<th>Jumlah KM</th>
						
						<th>Muat turun</th>
<th><?=lang('cov_bank_kod')?> (RM)</th>

<th>Transaksi</th>

                        
                    </tr>
                </thead>
                <tbody>
                 
                    <tr>
						<td><input type="checkbox"></td>
					    <td>1</td>
						<td>PERBATUAN</td>
						<td> - </td>
						<td>50</td>
						<td>-</td>
						<td>200.00</td>
						<td><button class="btn btn-flat btn-primary">Kemaskini</button></td>

                                               
                        
						
                    </tr>
					<tr>
						<td><input type="checkbox"></td>
					    <td>2</td>
						<td>PENGINAPAN</td>
						<td>223232</td>
						<td>SESI 1 (2024/2025)</td>
						<td><button type="button"
                      class="btn mb-1 btn-secondary rounded-circle round-40 btn-sm d-inline-flex align-items-center justify-content-center">
                      <i class="fs-5 ti ti-download"></i>
                    </button></td>
					<td>100.00</td>
						<td><button class="btn btn-flat btn-primary">Kemaskini</button></td>

                                               
                        
						
                    </tr>
					<tr>
						<td><input type="checkbox"></td>
					    <td>3</td>
						<td>PENGANGKUTAN</td>
						<td>223232</td>
						<td>SESI 1 (2024/2025)</td>
						<td><button type="button"
                      class="btn mb-1 btn-secondary rounded-circle round-40 btn-sm d-inline-flex align-items-center justify-content-center">
                      <i class="fs-5 ti ti-download"></i>
                    </button></td>
					<td>60.00</td>
						<td><button class="btn btn-flat btn-primary">Kemaskini</button></td>

                                               
                        
						
                    </tr>
                </tbody>
	  </table>
	  
	  <?php if($ENABLE_DELETE) { ?>
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