<?php 

?>

<?= form_open($this->uri->uri_string(),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form','class'=>'form-horizontal')) ?>

<div class="box box-primary">
    <!-- form start -->
   <div class="box-header with-border">
              <h3 class="box-title">Title Here</h3>
	</div>
   <div class="box-body">
			
			<div class="form-group <?= form_error('cov_matrik') ? ' has-error' : ''; ?>">
			    <label for="cov_matrik" class="col-sm-3 control-label"><?=lang("cov_matrik")?></label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="cov_matrik" name="cov_matrik" maxlength="45" value="<?php echo set_value('cov_matrik', isset($data->COV_MATRIK) ? $data->COV_MATRIK : ''); ?>" placeholder="<?=lang("cov_matrik")?>">
					
				</div>
		  	</div>
			<div class="form-group <?= form_error('cov_hp') ? ' has-error' : ''; ?>">
			    <label for="cov_hp" class="col-sm-3 control-label"><?=lang("cov_hp")?></label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="cov_hp" name="cov_hp" maxlength="45" value="<?php echo set_value('cov_hp', isset($data->COV_HP) ? $data->COV_HP : ''); ?>"  placeholder="<?=lang("cov_hp")?>">
					
				</div>
		  	</div>
			<div class="form-group <?= form_error('cov_bank_kod') ? ' has-error' : ''; ?>">
			    <label for="cov_bank_kod" class="col-sm-3 control-label"><?=lang("cov_bank_kod")?></label>
			    <div class="col-sm-8">
			    	<input type="text" class="form-control" id="cov_bank_kod" name="cov_bank_kod" maxlength="45" value="<?php echo set_value('cov_bank_kod', isset($data->COV_BANK_KOD) ? $data->COV_BANK_KOD : ''); ?>" required placeholder="<?=lang("cov_bank_kod")?>">
					
				</div>
		  	</div>
			
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" name="save" class="btn btn-primary"><?= lang('ccc_btn_save') ?></button>
				<?php
					echo lang('bf_or') . ' ' . anchor('gen/ccc', lang('ccc_btn_cancel'));
				?>
			</div>
		</div>		
	</div>
	
</div>


 </div>
	
<?= form_close() ?>
