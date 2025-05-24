<div class="box box-primary">
    <!-- form start -->
    <?= form_open($this->uri->uri_string(),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form','class'=>'form-horizontal')) ?>
    	<div class="box-body">
			<div class="form-group <?= form_error('nama_menu') ? ' has-error' : ''; ?>">
			    <label for="nama_menu" class="col-sm-2 control-label"><?= lang('sys_name') ?></label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="nama_menu" name="nama_menu" maxlength="45" value="<?php echo set_value('nama_menu', isset($data->NAMA_MENU) ? $data->NAMA_MENU : ''); ?>" required autofocus>
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('url') ? ' has-error' : ''; ?>">
			    <label for="url" class="col-sm-2 control-label"><?= lang('sys_addr') ?></label>
			    <div class="col-sm-4">
			    	<textarea class="form-control" id="url" name="url" maxlength="255" required=""><?php echo set_value('url', isset($data->URL) ? $data->URL : ''); ?></textarea>
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('sorting') ? ' has-error' : ''; ?>">
			    <label for="sorting" class="col-sm-2 control-label"><?= lang('sys_telp1') ?></label>
			    <div class="col-sm-2">
			    	<input type="text" class="form-control" id="sorting" name="sorting" maxlength="15" value="<?php echo set_value('sorting', isset($data->SORTING) ? $data->SORTING : ''); ?>" required="">
			    </div>
		  	</div>
		  	
		  	<div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      	<button type="submit" name="save" class="btn btn-primary"><?= lang('sys_btn_save') ?></button>
			    	<?php
	                	echo lang('bf_or') . ' ' . anchor('supplier/ora/testoracle', lang('sys_btn_cancel'));
	                ?>
			    </div>
		  	</div>
	  </div>
	<?= form_close() ?>
</div><!-- /.box -->