<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = FALSE;
?>
<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>	
<div class="widget-content searchable-container list">
            <div class="card card-body">
              <div class="row">
                <div class="col-md-4 col-xl-3">
                 
                    <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="<?php echo isset($search) ? $search : ''; ?>"
                      placeholder="Search Nama/Matrik..." />
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                  
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
	  	        <a href="<?= module_url('ccc/create') ?>" class="btn btn-primary d-flex align-items-center" title="<?= lang('ccc_btn_new') ?>"><i class="ti ti-users text-white me-1 fs-5"></i><?= lang('ccc_btn_new') ?></a>
                <?php endif; ?>
                </div>
              </div>
            </div>
</div>		
<div class="card">
	
        <div class="card-header">Senarai Bench Fee Yang Telah Selesai Agihan di SKP</div>
		
	<?php if (isset($results) && is_array($results) && count($results)) : ?>
	<div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="column-check" style="width: 30px;"><input class="check-all" type="checkbox" /></th>
                        <th width="50">#</th>
                        <th>No Batch Agihan</th>
						<th><?=lang('cov_matrik')?></th>
						<th><?=lang('cov_nama')?></th>
<th><?=lang('cov_hp')?></th>
<th><?=lang('cov_bank_kod')?> (RM)</th>

                        <?php if(!$ENABLE_MANAGE) : ?>
                        <th width="25">#</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $record) : ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="<?= $record->COV_ID ?>" /></td>
                        <td><?= $numb; ?></td>
                        <td><?= rand(40000, 60000); ?></td>
						<td>UK45678</td>
						<td>AHMAD ALI BIN HUSAIAN </td>
						<td>SESI 1 (2024/2025)</td>
						<td>1,200.00</td>

                                               
                        <?php if(!$ENABLE_MANAGE) : ?>
                        <td style="padding-right:20px"><a class="text-black" href="<?= site_url('gen/ccc/edit/' . $record->COV_ID); ?>" data-toggle="tooltip" data-placement="left" title="Detail"><i class="fa fa-edit"></i></a></td>
                        <?php endif; ?> 
						
                    </tr>
                    <?php $numb++; endforeach; ?>
                </tbody>
	  </table>
	  
	  <?php if($ENABLE_MANAGE) { ?>
		<input type="button" name="delete1" class="btn btn-danger" id="sa-success" value="<?php echo lang('ccc_btn_delete') ?>" onclick="confirm_delete(this.form) ">
		<input type="hidden" name="delete" id="isdelete">
	  <?php } ?>
	  
	</div><!-- /.box-body -->
	<div class="box-footer clearfix">
		<?php
		echo $this->pagination->create_links(); 
		?>
	</div>
	<?php else: ?>
    <div class="callout callout-info">
        <p><i class="fa fa-warning"></i> &nbsp; <?= lang('nm_controller}_no_records_found') ?></p>
    </div>
    <?php 
	endif;
	
	?>
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