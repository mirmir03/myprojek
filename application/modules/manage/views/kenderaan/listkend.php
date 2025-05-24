<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;

    echo "Bilangan Data " . $data->num_rows();
?>
<div class="widget-content searchable-container list">
            <div class="card card-body">
              <div class="row">
                <div class="col-md-4 col-xl-3">
                 
                    <input type="text" name="table_search" class="form-control product-search ps-5" id="input-search" value="" placeholder="Search ..">
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                  
                </div>
                <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                  <div class="action-btn show-btn">
                    <a href="javascript:void(0)" class="delete-multiple bg-danger-subtle btn me-2 text-danger d-flex align-items-center ">
                      <i class="ti ti-trash text-danger me-1 fs-5"></i> Delete All Row
                    </a>
                  </div>
                  
				  	  	        <a href="http://localhost/myprojek/setup/ccc/create" class="btn btn-primary d-flex align-items-center" title="New"><i class="ti ti-users text-white me-1 fs-5"></i>New</a>
                                </div>
              </div>
            </div>
</div>
<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>  
 <a class ="btn btn-primary" href="<?php echo module_url("kenderaan/form_add")?>">Add New Vehivle</a> 
<div class="card">
  
        <div class="card-header">Senarai Permohonan Tuntutan  Benchfee 
    </div>
    
  <div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
            <th width="50">No.Xray</th>
            <th>No Rujukan</th>
            <th>Nama Pesakit</th>
            <th>Jantina</th> 
            <th>Edit</th>         
            <th>Delete</th>                        
                    </tr>
                </thead>
                <tbody>
                 
<?php $i=0; foreach ($data->result() as $row) {?> 
          <tr>
             <td><?php echo ++$i ?></td>
            <td><?php echo $row->T01_FLAT ?></td>
            <td><?php echo $row->T01_NAMA_KENDERAAN ?></td>
            <td><?php echo $row->T01_VARIAN ?></td>
            <td><a class= "btn btn-flat btn-warning" href="<?php echo module_url("kenderaan/form_edit/".$row->T01_ID_KENDERAAN)?>">Edit</a></td>
            <td><a class= "btn btn-flat btn-danger" href="<?php echo module_url("kenderaan/delete/".$row->T01_ID_KENDERAAN)?>">Delete</a></td>
          </tr>
<?php } ?>
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

