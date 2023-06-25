<?php if ($this->session->flashdata('error') != '') : ?>
    <div id="error-alert" class="alert alert-danger" role="alert">
        <?php echo $this->session->flashdata('error'); ?>
    </div>
    <script>
        setTimeout(function() {
            $('#error-alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    </script>
<?php endif; ?>

<?php if ($this->session->flashdata('success') != '') : ?>
    <div id="success-alert" class="alert alert-success" role="alert">
        <?php echo $this->session->flashdata('success'); ?>
    </div>
    <script>
        setTimeout(function() {
            $('#success-alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    </script>
<?php endif; ?>
