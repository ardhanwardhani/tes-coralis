<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col">
            </div>
            <div class="col">
            <div class="w-100 p-3">
                <img src="<?= base_url().'uploads/images/'.$image; ?>" alt="Profile Picture" class="img-fluid">
            </div>
            </div>
            <div class="col">
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container-fluid text-center">
        <h1>Welcome, <?= $name ?></h1>
        <p>Untuk logout dari sistem silakan klik <a href="<?php echo base_url('logout');?>">Logout</a></p>
    </div>
</section>

<?= $this->endSection() ?>