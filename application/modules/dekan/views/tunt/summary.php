<?php 
    //$ENABLE_ADD     = has_permission('menu.Add');
    //$ENABLE_MANAGE  = has_permission('menu.Manage');
    //$ENABLE_DELETE  = has_permission('menu.Delete');
    $ENABLE_ADD  = TRUE;
    $ENABLE_MANAGE  = TRUE;
    $ENABLE_DELETE  = TRUE;
?>
<?= form_open($this->uri->uri_string(),array('id'=>'frm_menu','name'=>'frm_menu')) ?>	
	
<div class="card">
	
        <div class="card-header">Ringkasan
		
		</div>
		
	<div class="card-body ">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="50">No.</th>
						<th>Perician Perbelanjaan</th>
						<th>Kod GL</th>
						<th>Amaun (RM)</th>
						
						


                        
                    </tr>
                </thead>
                <tbody>
                 
                  
					<tr>
					    <td>1</td>
						<td>Gas</td>
						<td>P26706</td>
						<td>200.00</td>
						
						
                    </tr>
					<tr>
					    <td>2</td>
						<td>Bahan Kimia</td>
						<td>P26706</td>
						<td>250.00</td>
						
						
                    </tr>
					<tr>
					    <td colspan="3" align="right"> JUMLAH KESELURUHAN (RM)</td>
						<td>1,677.75</td>
						
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

<div class="col-lg-12">
  <div class="card">
	<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0">Kaedah Bayaran</h5>
	
	</div>
	<div class="card-body">
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jenis Akaun</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<select class="form-select" id="exampleInputselect" aria-label="Default select example">
                        <option selected="">Akaun Bank (Malaysia) </option>
                        <option value="1">Maklumat Pindahan Telegrafik (Pelajar Antarabangsa)</option>
                      </select>
		  </div>
		</div>
	  </div>
	  
	  
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pemilik Akaun : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Bank</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<select class="form-select" id="exampleInputselect" aria-label="Default select example">
                        <option selected="">CIMB </option>
                        <option value="1">Maybank</option>
                        <option value="1">RHB</option>
                      </select>
		  </div>
		</div>
	  </div>
	 
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">Penyata Akaun Bank :</label>
		<div class="col-sm-9">
		  <input type="file" class="form-control" id="exampleInputText6" placeholder="123 4567 201">
		</div>
	  </div>
	  <hr>
	   <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama dan alamat penerima : : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nombor passport (individu) : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Emel penerima : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama dan alamat bank : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nombor IBAN/akaun penerima : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	   <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kod perantara bank (swiftcode) : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	   
	   <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">Amaun pindahan (RM) : </label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="">
		  </div>
		</div>
	  </div>
	  
	  
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">Penyata Akaun Bank :</label>
		<div class="col-sm-9">
		  <input type="file" class="form-control" id="exampleInputText6" placeholder="123 4567 201">
		</div>
	  </div>
	  <div class="row">
                          <div class="col-sm-3"></div>
                          <div class="col-sm-9">
                            <div class="d-flex align-items-center gap-6">
                              <button class="btn btn-primary">Submit</button>
                              <button class="btn bg-danger-subtle text-danger">Cancel</button>
                            </div>
                          </div>
                        </div>
	 
	  </div>
	</div>
  </div>

<p></p>
<div>
                    <div class="mb-3 mb-0 text-end">
                      <a  class="btn btn-success " href="<?=site_url("pelajar/ccc/summary")?>">
                            <i class="ti ti-save fs-4"></i>
                            Hantar Permohonan  
                          </a>
                      
                    </div>
                 
                </div>

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