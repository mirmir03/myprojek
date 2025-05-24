<div class="box box-primary">
    <!-- form start -->
    <?= form_open($this->uri->uri_string(),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form','class'=>'form-horizontal')) ?>
    	<div class="box-body">
			<div class="form-group <?= form_error('nm_supplier') ? ' has-error' : ''; ?>">
			    <label for="nm_supplier" class="col-sm-2 control-label"><?= lang('supplier_name') ?></label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="nm_supplier" name="nm_supplier" maxlength="45" value="<?php echo set_value('nm_supplier', isset($data->nm_supplier) ? $data->nm_supplier : ''); ?>" required autofocus>
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('alamat') ? ' has-error' : ''; ?>">
			    <label for="alamat" class="col-sm-2 control-label"><?= lang('supplier_addr') ?></label>
			    <div class="col-sm-4">
			    	<textarea class="form-control" id="alamat" name="alamat" maxlength="255" required=""><?php echo set_value('alamat', isset($data->alamat) ? $data->alamat : ''); ?></textarea>
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('telp1') ? ' has-error' : ''; ?>">
			    <label for="telp1" class="col-sm-2 control-label"><?= lang('supplier_telp1') ?></label>
			    <div class="col-sm-2">
			    	<input type="text" class="form-control" id="telp1" name="telp1" maxlength="15" value="<?php echo set_value('telp1', isset($data->telp1) ? $data->telp1 : ''); ?>" required="">
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('telp2') ? ' has-error' : ''; ?>">
			    <label for="telp2" class="col-sm-2 control-label"><?= lang('supplier_telp2') ?></label>
			    <div class="col-sm-2">
			    	<input type="text" class="form-control" id="telp2" name="telp2" maxlength="15" value="<?php echo set_value('telp2', isset($data->telp2) ? $data->telp2 : ''); ?>">
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('email') ? ' has-error' : ''; ?>">
			    <label for="email" class="col-sm-2 control-label"><?= lang('supplier_email') ?></label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="email" name="email" maxlength="45" value="<?php echo set_value('email', isset($data->email) ? $data->email : ''); ?>">
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('cp') ? ' has-error' : ''; ?>">
			    <label for="cp" class="col-sm-2 control-label"><?= lang('supplier_cp') ?></label>
			    <div class="col-sm-3">
			    	<input type="text" class="form-control" id="cp" name="cp" maxlength="45" value="<?php echo set_value('cp', isset($data->cp) ? $data->cp : ''); ?>" required="">
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('ket') ? ' has-error' : ''; ?>">
			    <label for="ket" class="col-sm-2 control-label"><?= lang('supplier_desc') ?></label>
			    <div class="col-sm-4">
			    	<textarea class="form-control" id="ket" name="ket" maxlength="255"><?php echo set_value('ket', isset($data->ket) ? $data->ket : ''); ?></textarea>
			    </div>
		  	</div>
		  	<div class="form-group <?= form_error('st_aktif') ? ' has-error' : ''; ?>">
			    <label for="st_aktif" class="col-sm-2 control-label"><?= lang('supplier_status') ?></label>
			    <div class="col-sm-2">
			    	<select id="st_aktif" name="st_aktif" class="form-control">
			    		<option value="1" <?= set_select('st_aktif', 1, isset($data->st_aktif) && $data->st_aktif == 1) ?>><?= lang('supplier_active') ?></option>
			    		<option value="0" <?= set_select('st_aktif', 0, isset($data->st_aktif) && $data->st_aktif == 0) ?>><?= lang('supplier_inactive') ?></option>
			    	</select>
			    </div>
		  	</div>
		  	<div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      	<button type="submit" name="save" class="btn btn-primary"><?= lang('supplier_btn_save') ?></button>
			    	<?php
	                	echo lang('bf_or') . ' ' . anchor('supplier', lang('supplier_btn_cancel'));
	                ?>
			    </div>
		  	</div>
	  </div>
	<?= form_close() ?>
</div><!-- /.box -->