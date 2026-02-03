<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Team Details</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-title"><?= $team['name'] ?></h3>
                    <p class="card-text"><?= $team['description'] ?: 'No description' ?></p>
                    <p class="card-text">
                        <small class="text-muted">
                            Created: <?= date('F j, Y', strtotime($team['created_at'])) ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>