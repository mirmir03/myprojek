<form method ="POST" action ="<?php echo module_url("kenderaan/save/".$vehicle->T01_ID_KENDERAAN)?>">
<div class="col-lg-12">
  <div class="card">
	<div class="px-4 py-3 border-bottom">
	  <h5 class="card-title fw-semibold mb-0">EDIT VEHICLE</h5>
	
	</div>
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Xray</label>
		<div class="col-sm-9">
		  <input type="text" class="form-control" id="exampleInputText6" name="no_plat" value="<?php echo $vehicle->T01_FLAT?>">
		</div>
	  </div>
	  <div class="mb-4 row align-items-center"> 
		<label for="exampleInputText5" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pesakit</label>
		<div class="col-sm-9">
		  <input type="text" class="form-control" id="exampleInputText6"  name="nama_kend" value="<?php echo $vehicle->T01_NAMA_KENDERAAN?>">
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="exampleInputText6" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Rujukan</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" name="var" value="<?php echo $vehicle->T01_VARIAN?>">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jantina</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<input type="text" class="form-control" id="exampleInputText6" placeholder="Tujuan">
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kategori</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<select class="form-select" id="exampleInputselect" aria-label="Default select example">
                        <option selected="">Kenderaan Sendiri</option>
                        <option value="1">Kenderaan Universiti</option>
						<option value="2">Pengangkutan Awam</option>
                      </select>
		  </div>
		</div>
	  </div>
	  <div class="mb-4 row align-items-center">
		<label for="startDate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh</label>
		<div class="col-sm-9">
		  <div class="input-group">
			<textarea type="text" class="form-control" id="exampleInputText6" placeholder="Alasan" rows"4"> </textarea>
		  </div>
		</div>
	  </div>
	   <div class="row">
                          <div class="col-sm-3"></div>
                          <div class="col-sm-9">
                            <div class="d-flex align-items-center gap-6">
                             
							  <button class="btn btn-primary">Simpan</button>
                              <button class="btn bg-danger-subtle text-danger">Cancel</button>
                            </div>
                          </div>
                        </div>
	 
	  </div>
	</div>
  </div>
  
  </form>