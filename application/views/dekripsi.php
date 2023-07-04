<?= $this->session->flashdata('pesan'); ?>
    <div class="card-body">
		<form class="form-horizontal" method="post" action="./dekripsi/process" enctype="multipart/form-data">
			<fieldset>
						<div class="form-group">
							<label class="col-lg-2 control-label" style="color:#333;" for="inputFile">Input Stego Image</label>
							<div class="col-lg-4">
								<input class="form-control" id="inputFile" placeholder="Input File" type="file" name="image" accept=".gif, .jpg, .jpeg, .png" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" style="color:#333;" for="inputPassword">Private Key</label>
							<div class="col-lg-4">
								<input class="form-control" id="inputPassword" type="text" placeholder="(d,n)" name="private_key" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label" for="textArea"></label>
							<div class="col-lg-2">
								<input type="submit" name="encrypt_now" value="Dekrip File dan Ekstrak" class="form-control btn btn-info">
							</div>
						</div>
			</fieldset>
		</form>
    </div>
