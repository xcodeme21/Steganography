<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>  
<head>
<meta charset="UTF-8">
<body>
	<div class="container py-5">
		<div class="card mx-auto" style="max-width: 900px;">
			<div class="row-fluid mt-5" align="center">
				<h2>Login</h2>
			</div>
			
			<div class="row-fluid mt-2 mx-5">
				<?php include(APPPATH . 'views/session_messages.php'); ?>
				
				<form method="post" action="<?php echo base_url(); ?>/login/process">
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email...">
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password...">
					</div>

					<div class="row">
						<div class="col-6">
							<button type="submit" class="btn btn-success">Login</button>
						</div>
						<div class="col-6" align="right">
							<a href="<?php echo site_url().'forgotpassword'; ?>">Lupa password ?</a>
						</div>
					</div>
				</form>

				<p class="mt-4 mb-5">
				Belum punya akun ? <?php echo anchor(site_url().'register','Daftar disini'); ?>
				</p>
			</div>
		</div>
  	</div>
</body>
<script src="<?= site_url('assets/template/dist/js/jquery-3.6.0.min.js') ?>"></script>
</html>
