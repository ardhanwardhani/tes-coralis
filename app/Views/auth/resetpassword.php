<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center" style="font-weight: bold;">LOGIN</h4>
                    <hr>

                    <?php 
                    if(isset($validation)): ?>
                        <div class="alert alert-danger">
                            <?= $validation->listErrors()?>
                        </div>
                    <?php endif;?>

                    <?php if(session()->getFlashdata('error')):?>
	                    <div class='alert alert-danger'><?= session()->getFlashdata('error');?></div>
	                <?php endif;?>
            
                    <?php if(isset($error)):?>
                        <div class='alert alert-danger'><?= $error;?></div>
                    <?php else: ?>
                    <?= form_open();?>
                    <div class='form-group'>
                        <label>Enter new password:</label>
                        <input type="password" name="password" class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>Confirm new password:</label>
                        <input type="password" name="cpassword" class='form-control'>
                    </div>
                    <div class='form-group'>
                        <input type="submit" value='Update' class='btn btn-primary'>
                    </div>
                    <?= form_close();?>
                    <?php endif ?>

                </div>

            </div>
            <div class="text-center mt-2">
                <a href="<?php echo base_url('/forgot_password'); ?>">Lupa Password?</a><br>
                Belum punya akun? <a href="<?php echo base_url('register'); ?>">Silakan daftar.</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>