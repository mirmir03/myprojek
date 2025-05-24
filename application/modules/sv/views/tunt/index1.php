<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;
	
	
?>
<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>

<div class="card card-body">
	<div class="row">
		<div class="col-md-4 col-xl-3">

			<input type="text" class="form-control product-search ps-5" id="input-search" 
			  placeholder="Search Contacts..." />
			<i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
		
		</div>
		<div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
		  <div class="action-btn show-btn">
			<a href="javascript:void(0)"
			  class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center ">
			  <i class="ti ti-trash text-danger me-1 fs-5"></i> Delete All Row
			</a>
		  </div><?php if($ENABLE_DELETE) : ?>
		<input type="submit" name="delete" class="btn btn-danger" id="delete-me" value="<?php echo lang('ccc_btn_delete') ?>" onclick="return confirm('<?= (lang('ccc_delete_confirm')); ?>')">
		<?php endif; ?>
		  
		  
		  
		  <?php if ($ENABLE_ADD) { ?>
	  	        
				
		  <a href="<?= site_url($this->_module .'/ccc/create') ?>" id="btn-add-contact" class="btn btn-primary d-flex align-items-center">
			<i class="ti ti-users text-white me-1 fs-5"></i> <?= lang('ccc_btn_new') ?>
		  </a>
		  <?php }; ?>
		</div>
	</div>
</div>
<div class="card card-body">
	<?php if (isset($results) && is_array($results) && count($results)) : ?>
	<div class="table-responsive">
	<div class="table-responsive">
		<table class="table search-table align-middle text-nowrap">
		  <thead class="header-item">
			
			  <tr>
                        <th class="column-check" style="width: 30px;"><input class="check-all" type="checkbox" /></th>
                        <th width="50">#</th>
						<th><?=lang('cov_matrik')?></th>
<th><?=lang('cov_hp')?></th>
<th><?=lang('cov_bank_kod')?></th>

                        <?php if($ENABLE_MANAGE) : ?>
                        <th width="25">#</th>
                        <?php endif; ?>
                    </tr>
		  </thead>
		  <tbody>
                    <?php foreach ($results as $record) : ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="<?= $record->COV_ID ?>" /></td>
                        <td><?= $numb; ?></td>
						<td><?= $record->COV_MATRIK ?></td>
<td><?= $record->COV_HP ?></td>
<td><?= $record->COV_BANK_KOD ?></td>

                                               
                        <?php if($ENABLE_MANAGE) : ?>
                        <td style="padding-right:20px"><a class="text-black" href="<?= module_url('ccc/edit/' . $record->COV_ID); ?>" data-toggle="tooltip" data-placement="left" title="Detail"><i class="fa fa-edit"></i></a></td>
                        <?php endif; ?> 
						
                    </tr>
                    <?php $numb++; endforeach; ?>
                </tbody>
	  </table>
	</div>
</div>
<?php
	endif;
	
	?>


<div class="box box-primary">
	
        
	<?php if (isset($results) && is_array($results) && count($results)) : ?>
	<div class="box-body table-responsive no-padding">
            
                
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
</div><!-- /.box -->

<?php
	
	form_close(); 
?>