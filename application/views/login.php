<?php defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>  
<head>
<meta charset="UTF-8">
</head>
<body>
	<div class="container py-5">
		<div class="card mx-auto" style="max-width: 900px;">
			<div class="row-fluid mt-5" align="center">
				<h2>Login</h2>
			</div>
			
			<div class="row-fluid mt-2 mx-5">

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

				<div class="row">
					<div class="col-6">
						<button type="submit" class="btn btn-success">Login</button>
					</div>
					<div class="col-6" align="right">
						<a href="#">Lupa password ?</a>
					</div>
				</div>

				<?php echo form_close();?>

				<p class="mt-4">
				Belum punya akun ? <?php echo anchor(site_url().'/register','Daftar disini'); ?>
				</p>
			</div>
		</div>
  	</div>
</body>
</html>
