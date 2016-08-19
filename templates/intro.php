<?php require "head.php"; ?>
    <div class="container">
        <h1><?= $book['book_name'] ?></h1>

        <p class="lead">无简介</p>

        <p>共 <?= $catalogCount ?> 章，当前进度为第 <?= $currLocation ?> 章</p>

        <p>
            <a href="/catalog/<?= $book['id'] ?>" type="button" class="btn btn-primary">进入目录</a>
        </p>

        <p>
            <a href="/read/<?= $book['id'] ?>" type="button" class="btn btn-primary">书接上回</a>

        </p>

        <p>
            <a href="/parse/<?= $book['id'] ?>" type="button" class="btn btn-primary">解析文件</a>
        </p>

    </div>
<?php require "foot.php"; ?>