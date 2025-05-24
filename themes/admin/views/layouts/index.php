<?php echo $this->template->load_view('header');?>

<?php echo Template::message(); ?>
<?php echo $template['body']; ?>

<?php echo $this->template->load_view('footer'); ?>