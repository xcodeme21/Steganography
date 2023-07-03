
<?= $this->session->flashdata('pesan'); ?>
<div class="card-body">
    <form method="post" action="./generatekey/generate" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group col-md-2">
                <label class="control-label">Nilai p</label>
                <input class="form-control" value="<?php echo $this->session->userdata('p'); ?>" type="number" placeholder="Bilangan Prima" name="inputp" required>
            </div>
            <div class="form-group col-md-2">
                <label class="control-label">Nilai q</label>
                <input class="form-control" value="<?php echo $this->session->userdata('q'); ?>" type="number" placeholder="Bilangan Prima" name="inpuip" required>
            </div>
        </div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Nilai n</label>
					<input type="text" name="nilain" value="<?php echo $this->session->userdata('n'); ?>" class="form-control" placeholder="p*q" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Nilai m</label>
					<input type="text" name="nilaim" value="<?php echo $this->session->userdata('m'); ?>" class="form-control" placeholder="(p-1)*(q-1)" readonly>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Nilai e</label>
					<input type="text" name="nilaie" value="<?php echo $this->session->userdata('e'); ?>" class="form-control" placeholder="e > 1 and GCD(m,e) = 1" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Nilai d</label>
					<input type="text" name="nilaid" value="<?php echo $this->session->userdata('d'); ?>" class="form-control" placeholder="(d * e) mod m = 1" readonly >
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label class="control-label">Private Key</label>
					<input type="text" name="privkey" value="<?php echo $this->session->userdata('privateKey'); ?>" class="form-control" placeholder="(d,n)" readonly >
			</div>
			<div class="form-group col-md-2">
				<label class="control-label">Public Key</label>
					<input type="text" name="pubkey" value="<?php echo $this->session->userdata('publicKey'); ?>" class="form-control" placeholder="(e,n)" readonly >
			</div>
		</div>  

        <div class="form-row">
            <div class="col-lg-2">
                <button type="submit" class="form-control btn btn-info">Generate Key</button>
            </div>
        </div>
    </form>
</div>
