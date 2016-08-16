<?php require "head.php"; ?>
    <div class="container">
        <h1></h1>

        <div class="list-group">
            <?php foreach ($books as $key => $book): ?>
                <a href="/catalog/<?= $book['filename'] ?>" class="list-group-item"><?= $book['name'] ?></a>
            <?php endforeach; ?>
        </div>

    </div>
<?php require "foot.php"; ?>