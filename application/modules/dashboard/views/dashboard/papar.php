<?php 

?>

<?= form_open(site_url('gen/ccc/hapus/'),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form','class'=>'form-horizontal')) ?>
<input type="hidden" value="<?=$id?>" name="id" >
<div class="box box-primary">
    <!-- form start -->
   <div class="box-header with-border">
              <h3 class="box-title">Title Here</h3>
			   <button class="btn btn-primary pull-right"  onclick="return confirm('<?= (lang('ccc_delete_confirm')); ?>')"><?= lang('ccc_btn_delete') ?></button>
	</div>
   <div class="box-body">
			
			<div class="form-group <?= form_error('cov_matrik') ? ' has-error' : ''; ?>">
			    <label for="cov_matrik" class="col-sm-3 control-label"><?=lang("cov_matrik")?></label>
			    
				<div class="col-sm-8 form-control-static" >
						
							<?=$data->COV_MATRIK?>
							
						</div>
		  	</div>
			<div class="form-group <?= form_error('cov_hp') ? ' has-error' : ''; ?>">
			    <label for="cov_hp" class="col-sm-3 control-label"><?=lang("cov_hp")?></label>
			    
				<div class="col-sm-8 form-control-static" >
						
							<?=$data->COV_HP?>
							
						</div>
		  	</div>
			<div class="form-group <?= form_error('cov_bank_kod') ? ' has-error' : ''; ?>">
			    <label for="cov_bank_kod" class="col-sm-3 control-label"><?=lang("cov_bank_kod")?></label>
			    
				<div class="col-sm-8 form-control-static" >
						
							<?=$data->COV_BANK_KOD?>
							
						</div>
		  	</div>
			
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<a class="btn btn-primary" href="<?=site_url('gen/ccc/edit/'.$id)?>"><?= lang('ccc_btn_edit') ?></a>
				
			</div>
		</div>		
	</div>
	
</div>


 </div>
	
<?= form_close() ?>
