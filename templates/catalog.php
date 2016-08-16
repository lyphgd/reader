<?php require "head.php"; ?>

<div class="container">
    <h1></h1>

    <div class="lists">
        <h5><a href="/read/<?= $file ?>">书接上回</a><br></h5>

        <div class="list-group">
            <?php foreach ($catalogs as $chapterCount => $chapter): ?>
                <a href="/read/<?= $file ?>/<?= $chapterCount ?>" class="list-group-item"><?= $chapter ?></a>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php require "foot.php"; ?>