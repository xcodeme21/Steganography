<?php defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>  
<head>
<meta charset="UTF-8">
</head>
<body>
	<div class="container py-5">
		<div class="card mx-auto" style="max-width: 900px;">
			<div class="row-fluid mt-5" align="center">
				<h2>SIgn Up</h2>
			</div>
			
			<div class="row-fluid mt-2 mx-5">
				<form method="post" action="<?php echo base_url(); ?>/login/process">
					<p>Nama:</p>
					<p>
					<input type="text" name="nama" class="form-control" placeholder="Masukkan nama..." value="<?php echo set_value('nama'); ?>" required/>
					</p>
					<p> <?php echo form_error('nama'); ?> </p>

					<p>Email:</p>
					<p>
					<input type="email" name="email" class="form-control" placeholder="Masukkan email..." value="<?php echo set_value('email'); ?>" required/>
					</p>
					<p> <?php echo form_error('email'); ?> </p>

					<p>Password:</p>
					<p>
					<input type="password" name="password" class="form-control" placeholder="Masukkan password..." value="<?php echo set_value('password'); ?>" required/>
					</p>
					<p> <?php echo form_error('password'); ?> </p>

					<div class="row mb-5" align="center">
						<button type="submit" class="btn btn-success">Signup</button>
					</div>
				</form>

			</div>
		</div>
  	</div>
</body>
</html>
