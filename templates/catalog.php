<?php require "head.php"; ?>

    <div class="container">
        <h1></h1>

        <div class="lists">

            <p>
                <a href="/intro/<?= $bookId ?>" type="button" class="btn btn-primary">返回书页</a>
                <a href="/read/<?= $bookId ?>" type="button" class="btn btn-primary">书接上回</a>
                <a href="#chapter<?= $currLocation ?>" type="button" class="btn btn-primary">当前位置</a>
            </p>

            <div class="list-group">
                <h4 class="list-group-item">目录<br></h4>
                <?php foreach ($catalogs as $chapterCount => $chapter): ?>
                    <a href="/read/<?= $bookId ?>/<?= $chapterCount ?>" class="list-group-item" id="chapter<?= $chapterCount ?>"><?= $chapter ?></a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

<?php require "foot.php"; ?>