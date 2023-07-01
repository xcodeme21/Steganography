        <section class="breadcome-list">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form class="form-horizontal" method="post" action="./enkripsi/process" enctype="multipart/form-data">
									<fieldset>
											<div class="form-group">
													<label class="col-lg-2 control-label" style="color:#333;" for="inputFile">Input File Excel</label>
													<div class="col-lg-4">
															<input class="form-control" id="inputFile" placeholder="Input File (excel)" type="file" name="excel_file" accept=".xlsx, .xls" required>
													</div>
											</div>
											<div class="form-group">
													<label class="col-lg-2 control-label" style="color:#333;" for="inputPassword">Public Key</label>
													<div class="col-lg-4">
															<input class="form-control" id="inputPassword" type="text" placeholder="(e,n)" name="pwdfile" required>
													</div>
											</div>
											<div class="form-group">
													<label class="col-lg-2 control-label" style="color:#333;" for="inputGambar">Input Gambar</label>
													<div class="col-lg-4">
															<input class="form-control" id="inputGambar" placeholder="Input Gambar" type="file" name="gambar_file" accept=".gif, .jpg, .jpeg, .png" required>
													</div>
											</div>
											<div class="form-group">
													<label class="col-lg-2 control-label" for="textArea"></label>
													<div class="col-lg-2">
															<input type="submit" name="encrypt_now" value="Enkripsi File & Sisip" class="form-control btn btn-info">
													</div>
											</div>
									</fieldset>
							</form>

              </div>
            </div>
          </div>
        </div>
        </section>
