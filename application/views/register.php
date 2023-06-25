<?php defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>  
<head>
<meta charset="UTF-8">
</head>
<body>
	<div class="container">
		<div class="row-fluid mt-5" align="center">
			<h2>Signup</h2>
		</div>
		<div class="row-full mt-2">
			<?php echo form_open('register');?>
			<p>Nama:</p>
			<p>
			<input type="text" name="name" class="form-control" value="<?php echo set_value('name'); ?>" required/>
			</p>
			<p> <?php echo form_error('name'); ?> </p>

			<p>Email:</p>
			<p>
			<input type="text" name="email" class="form-control" value="<?php echo set_value('email'); ?>" required/>
			</p>
			<p> <?php echo form_error('email'); ?> </p>

			<p>Password:</p>
			<p>
			<input type="password" name="password" class="form-control" value="<?php echo set_value('password'); ?>" required/>
			</p>
			<p> <?php echo form_error('password'); ?> </p>

			<p align="center">
			<button type="submit" class="btn btn-success">Signup</button>
			</p>

			<?php echo form_close();?>

	<!-- <p>
	Kembali ke beranda, Silakan klik <?php echo anchor(site_url().'/beranda','di sini..'); ?>
	</p> -->
		</div>
  	</div>
</body>
</html>
