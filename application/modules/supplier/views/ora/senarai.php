<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;
?>
<div class="box box-primary">
	<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>
        <div class="box-header">
            <div class="form-group">
                <?php if (!$ENABLE_ADD) : ?>
	  	        <a href="<?= site_url('supplier/ora/create') ?>" class="btn btn-success" title="<?= lang('menu_btn_new') ?>"><?= lang('menu_btn_new') ?></a>
                <?php endif; ?>
                <div class="pull-right">
                    <div class="input-group">
                        <input type="text" name="table_search" value="<?php echo isset($search) ? $search : ''; ?>" class="form-control pull-right" placeholder="Search" autofocus />
                        <div class="input-group-btn">
                            <button class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<?php if (isset($results) && is_array($results) && count($results)) : ?>
	<div class="box-body table-responsive no-padding">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="column-check" style="width: 30px;"><input class="check-all" type="checkbox" /></th>
                        <th width="50">#</th>
                        <th><?= lang('cov_matrik') ?></th>
                        <th><?= lang('cov_nama_pelajar') ?></th>
                        <th><?= lang('cov_nama_parent') ?></th>
                        <th><?= lang('cov_ic') ?></th>
                        <th><?= lang('cov_hp') ?></th>
                        <?php if($ENABLE_MANAGE) : ?>
                        <th width="25"></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $record) : ?>
                    <tr>
                        <td class="column-check"><input type="checkbox" name="checked[]" value="<?= $record->ID ?>" /></td>
                        <td><?= $numb; ?></td>
                        <td><?= $record->COV_MATRIK ?></td>
                        <td><?= $record->COV_NAMA_PELAJAR ?></td>
                        <td><?= $record->COV_NAMA_PARENT ?></td>
                        <td><?= $record->COV_IC ?></td>
                        <td><?= $record->COV_HP ?></td>
                        
                        <?php if($ENABLE_MANAGE) : ?>
                        <td style="padding-right:20px"><a class="text-black" href="<?= site_url('supplier/ora/edit/' . $record->ID); ?>" data-toggle="tooltip" data-placement="left" title="Edit Data"><i class="fa fa-pencil"></i></a></td>
                        <?php endif; ?> 
						
                    </tr>
                    <?php $numb++; endforeach; ?>
                </tbody>
	  </table>
	</div><!-- /.box-body -->
	<div class="box-footer clearfix">
		<?php if(!$ENABLE_DELETE) : ?>
		<input type="submit" name="delete" class="btn btn-danger" id="delete-me" value="<?php echo lang('menu_btn_delete') ?>" onclick="return confirm('<?= (lang('menu_delete_confirm')); ?>')">
		<?php endif;
		echo $this->pagination->create_links(); 
		?>
	</div>
	<?php else: ?>
    <div class="callout callout-info">
        <p><i class="fa fa-warning"></i> &nbsp; <?= lang('menu_no_records_found') ?></p>
    </div>
    <?php
	endif;
	form_close(); 
	?>
</div><!-- /.box -->