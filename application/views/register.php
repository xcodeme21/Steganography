<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>  
<head>
<meta charset="UTF-8">
</head>
<body>
	<div class="container py-5">
		<div class="card mx-auto" style="max-width: 900px;">
			<div class="row-fluid mt-5" align="center">
				<h2>Sign Up</h2>
			</div>
			
			<div class="row-fluid mt-2 mx-5">
				<?php include(APPPATH . 'views/session_messages.php'); ?>

				<form method="post" class="mt-5 mb-5" action="<?php echo base_url(); ?>/register/process">
					<div class="form-group">
						<label for="nama">Nama</label>
						<input type="text" class="form-control" name="nama" id="nama" placeholder="Masukkan nama...">
					</div>
					<div class="form-group">
						<label for="username">Email</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email...">
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password...">
					</div>
					<button type="submit" class="btn btn-primary">Register</button>
				</form>

			</div>
		</div>
  	</div>
</body>
<script src="<?= site_url('assets/template/dist/js/jquery-3.6.0.min.js') ?>"></script>
</html>
