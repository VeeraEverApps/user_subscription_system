<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ?></title>
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css') ?>
    <?= $this->Html->css('https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css') ?>
    <?= $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js') ?>
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js') ?>
    <?= $this->Html->script('https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js') ?>
    <?= $this->Html->script('https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js') ?>
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css') ?>
    <?= $this->Html->script([
        'https://code.jquery.com/jquery-3.6.0.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11'
    ]) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    <style>
    .hobby-input {
        margin-bottom: 10px;
    }
    .remove-hobby {
        margin-left: 10px;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">User Subscription</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
</body>
</html>