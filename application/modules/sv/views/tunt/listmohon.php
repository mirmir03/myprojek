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
	  	        <a href="<?= module_url('ccc/mohon') ?>" class="btn btn-primary d-flex align-items-center" title="<?= lang('ccc_btn_new') ?>"><i class="ti ti-users text-white me-1 fs-5"></i><?= lang('ccc_btn_new') ?></a>
                <?php endif; ?>
                </div>
              </div>
            </div>
</div>		
<div class="card">
	
        <div class="card-header">Senarai Permohonan Tuntutan  Benchfee
		
		</div>
		
	<div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="50">No.</th>
						<th>No Matrik</th>
						<th>Nama Pelajar</th>
						<th>Tujuan</th>
						<th>Tarikh Hantar</th>
						
						
						
<th><?=lang('cov_bank_kod')?> (RM)</th>


<th>Lihat</th>

                        
                    </tr>
                </thead>
                <tbody>
                 
                  
					<tr>
					    <td>1</td>
						<td>P4545</td>
						<td>AZRUL AZHAN BIN AHMAD ALI</td>
						<td>MENGAMBIL SAMPLE CORAL DI PULAU BIDONG</td>
						<td>12/10/2024</td>
						
						<td>100.00</td>
						<td><a class="btn btn-flat btn-primary">Lihat</a></td>
                    </tr>
					<tr>
					    <td>2</td>
						<td>P4545</td>
						<td>AZRUL AZHAN BIN AHMAD ALI</td>
						<td>SAMPLING DI TASIK KENYIR</td>
						<td>12/10/2024</td>
						
						<td>100.00</td>
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