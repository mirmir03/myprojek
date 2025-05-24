<?php 
//$role = isset($_SESSION["iac"]) ? $role = $_SESSION["iac"]["level"] : NULL ;
?>
<ul id="sidebarnav">
    <!--<li class="header">MAIN NAVIGATION</li> -->
    <!--
			<li>
              <a href="<?=base_url()?>index.php/k1/index/">
                <i class="fa fa-th"></i> <span>Padan Calon EF</span> <small class="label pull-right bg-green">new</small>
              </a>
            </li> -->

    <li class="sidebar-item">
        <!-- <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
            <iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
            <span class="hide-menu">Sample Pages</span>
        </a> -->

        <?php  if (!empty($_SESSION['icno'])) { ?>
        <a href="https://mynemov3.umt.edu.my/mynemov3/mainpage/main" class="sidebar-link">
            <i class="ti ti-corner-up-left-double"></i>
            <span class="hide-menu">Kembali ke MyNemo</span>
        </a>
        <a href="<?=base_url()?>sip/pelajar/carian" class="sidebar-link">
            <i class="ti ti-search"></i>
            <span class="hide-menu">Carian Pelajar</span>
        </a>
		<!-- ---------------------------------- -->
		<!-- Menu Setup -->
		<!-- ---------------------------------- -->
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Setup</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>setup/ccc/index" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Setup Fakulti/Ptj</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>setup/ccc/setup_admin" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Setup Admin Ptj</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>setup/ccc/setup_dekan" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Setup Dekan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>setup/ccc/setup_jenis_tuntutan" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Setup Jenis Tuntutan</span>
			  </a>
			</li>
			
		  </ul>
		</li>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Admin</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/index" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Proses Bench Fee</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/baki" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Bench Fee</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/xaktif" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Pool Tak Aktif</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/txpool" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Transaksi Pool</span>
			  </a>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/statfak" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Statistik Benchfee Fakulti</span>
			  </a>
			</li>
		  </ul>
		</li>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Permohonan</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>pelajar/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>admin/ccc/mohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Mohon Tuntutan</span>
			  </a>
			</li>
			
			
		  </ul>
		</li>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Keluar Kampus</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>pelajar/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>pelajar/ccc/mohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Mohon Keluar Kampus</span>
			  </a>
			</li>
			
			
		  </ul>
		</li>
	
	
		
        <?php } ?>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Penyelia</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>sv/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>sv/tunt/listmohon_all" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Rekod Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>outcampus/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Permohonan Outcampus</span>
			  </a>
			</li>
			
		  </ul>
		</li>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Dekan</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>dekan/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>dekan/tunt/listmohon_all" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Rekod Permohonan</span>
			  </a>
			</li>
			
			
		  </ul>
		</li>
		<li class="sidebar-item">
		  <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
			<iconify-icon icon="solar:document-linear" class="aside-icon"></iconify-icon>
			<span class="hide-menu">Pembayaran</span>
		  </a>
		  <ul aria-expanded="false" class="collapse first-level">
			<li class="sidebar-item">
			  <a href="<?= base_url()?>spayment/ccc/listmohon" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Senarai Permohonan</span>
			  </a>
			</li>
			<li class="sidebar-item">
			  <a href="<?= base_url()?>spayment/tunt/listmohon_all" class="sidebar-link">
				<iconify-icon icon="solar:stop-circle-line-duotone" class="sidebar-icon"></iconify-icon>
				<span class="hide-menu">Rekod Permohonan</span>
			  </a>
			</li>
			
			
		  </ul>
		</li>
    </li>
</ul>