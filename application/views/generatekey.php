
<?= $this->session->flashdata('pesan'); ?>
<div class="card-body">
    <form id="myForm" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group col-md-2">
                <label class="control-label">Nilai p</label>
                <input class="form-control" id="inputp" type="number" placeholder="Bilangan Prima" name="inputp" required>
            </div>
            <div class="form-group col-md-2">
                <label class="control-label">Nilai q</label>
                <input class="form-control" id="inputq" type="number" placeholder="Bilangan Prima" name="inpuip" required>
            </div>
        </div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Nilai n</label>
					<input type="text" name="nilain" id="kali" class="form-control" placeholder="p*q" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Nilai m</label>
					<input type="text" name="nilaim" id="kurang1" class="form-control" placeholder="(p-1)*(q-1)" readonly>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Nilai e</label>
					<input type="text" name="nilaie" id="kali4" class="form-control" placeholder="e > 1 and GCD(m,e) = 1" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Nilai d</label>
					<input type="text" name="nilaid" id="kali5" class="form-control" placeholder="(d * e) mod m = 1" readonly >
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Private Key</label>
					<input type="text" name="privkey" id="kali6" class="form-control" placeholder="(d, n)" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Public Key</label>
					<input type="text" name="pubkey" id="kali7" class="form-control" placeholder="(e, n)" readonly >
			</div>
		</div>  

        <div class="form-row">
            <div class="col-lg-2">
                <button type="button" id="generateBtn" class="form-control btn btn-info">Generate Key</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/"></script>

<script src="<?= site_url('assets/template/dist/js/jquery-3.6.0.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#generateBtn').click(function() {
            // Serialize form data
            var formData = $('#myForm').serialize();

            // Send AJAX request
            $.ajax({
                url: './generatekey/generate',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
					console.log(response);
                    // Handle the response data
                    var n = response.n;
                    var m = response.m;
                    var e = response.e;
                    var d = response.d;
                    var privateKey = response.privateKey;
                    var publicKey = response.publicKey;

                    // Update the input fields with the received values
                    $('#kali').val(n);
                    $('#kurang1').val(m);
                    $('#kali4').val(e);
                    $('#kali5').val(d);
                    $('#kali6').val(privateKey);
                    $('#kali7').val(publicKey);

                    // Rest of the input fields

                    // Show success message or perform further actions
                },
                error: function() {
                    // Handle the error case
                }
            });
        });
    });
</script>
