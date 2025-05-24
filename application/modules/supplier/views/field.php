
<?= form_open(site_url("supplier/sys/jana"),array('id'=>'frm_supplier','name'=>'frm_supplier','role'=>'form')) ?>
<input type="hidden" name="table" value="<?=$table?>">
<div class="box box-warning">
	<div class="box-header with-border">
	  <h3 class="box-title"><?=$table?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body table-responsive" id="table-basic">
	    Nama Controller: <input type="text" name="nm_controller"><br>
	    DB setting: <?= form_dropdown('dbcon', $dbs, '');?> <br>
	    <p>Seq name: <input type="text" name="nm_sequence"><br></p>
		<table  class="table table-hover table-striped">
                <thead>
                    <tr>
						<th>#</th>
                        <th class="column-check" style="width: 30px;"><input id="checkAll" class="check-all1" type="checkbox" /></th>
                        
						<th>Field</th>
						<th>Label</th>
						<th>PK</th>
						<th>Type</th>
						
						<th>Created On</th>
						<th>Created By</th>
						<th>Modified On</th>
						<th>Modified By</th>
						<th>Deleted</th>
						<th>Deleted On</th>
						<th>Deleted By</th>


                        
                    </tr>
                </thead>
                <tbody style=" cursor: move;">
                    <?php $i=0;foreach ($fields as $field) :
					//echo ($field->name == $pk) ? 'hoi<input type="hidden" name="pk" value="'.$field->name.'">' : "";
			
					?>
					<input type="hidden" name="max_<?=$field->name?>" value="<?=$field->max_length?>">
					<input type="hidden" name="type_<?=$field->name?>" value="<?=$field->type?>">
					
                    <tr>
						<td><?=++$i?>
						
						</td>
                        <td class="column-check">						
						<input type="checkbox" name="checked[]" value="<?=$field->name?>" /></td>
                       
						<td><?= $field->name ?></td>
						<td><input type="text" name="label_<?= $field->name ?>" value="<?=ucwords(strtolower(str_replace('_', ' ', $field->name)))?>"></td>
						<td><input data-toggle="tooltip" title="PK" type="radio" name="pk" value="<?= $field->name ?>" <?=($field->name == $pk) ? "checked" : ""?>></td>
						<td>
							<?php 
							$options = [
									'text'  => 'TextBox',
									'dropdown'    => 'Dropdown',
									'dropdown_negeri'    => 'Dropdown Negeri',
									'dropdown_yesno'    => 'Dropdown Ya/Tidak',
									'dropdown_bank'    => 'Dropdown Bank',
									'datepicker'  => 'Datepicker',
									'datetimepicker'  => 'Datetimepicker',
									'textarea' => 'Textarea',
							];

							$shirts_on_sale = ['small', 'large'];
							echo form_dropdown('jenis_'.$field->name, $options, 'text');
							?>
						</td>
						<td><input data-toggle="tooltip" title="created_on" type="radio" name="created_on" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="created_by" type="radio" name="created_by" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="modified_on" type="radio" name="modified_on" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="modified_by" type="radio" name="modified_by" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="deleted" type="radio" name="deleted" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="deleted on" type="radio" name="deleted_on" value="<?= $field->name ?>"></td>
						<td><input data-toggle="tooltip" title="Deleted By" type="radio" name="deleted_by" value="<?= $field->name ?>"></td>
						


                                               
                        
						
                    </tr>
                    <?php endforeach; ?>
                </tbody>
	  </table>		
             
                <!-- text input -->
                
				
                
 				
	</div>
    <div class="box-footer clearfix">
	
		<input type="submit" name="delete" class="btn btn-danger" id="delete-me" value="Save">

	</div>	
</div>	
<?= form_close() ?>	

    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script> 
    <script src="https://getbootstrap.com/dist/js/bootstrap.min.js"></script>
	<script src="<?=base_url()?>assets/plugins/freezetable/freeze-table.js"></script>

<script type="text/javascript">
      $('tbody').sortable();
	   //$("#table-basic").freezeTable();
    </script>	