<div class="login-box">
  <div class="login-logo">
    <a href="<?= site_url() ?>"><b><?= isset($idt->nm_perusahaan) ? $idt->nm_perusahaan : 'MyNEMO' ?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sila login menggunakan UmtID untuk Pengesahan Lokasi Terkini Anda</p>
	<?php
        if (Template::message()) :
    ?>
    <div class="row">
        <div class="col-md-12">
            <?= Template::message(); ?>
        </div>
    </div>
    <?php endif; ?>
    <?= form_open($this->uri->uri_string(), array('id' => 'frm_login', 'name' => 'frm_login')) ?>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="username" placeholder="Username" value="<?= set_value('username') ?>" required autofocus>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="Password" value="" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" name="login" id="btnlogin">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    <?= form_close() ?>
   <br>
    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script type="text/javascript">
  $(document).ready(function(){
    $("body").addClass("login-page");
	
  });
</script>