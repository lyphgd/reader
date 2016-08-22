<?php require "head.php"; ?>
    <div class="container">
        <h1></h1>

        <div class="panel-group" id="accordion">
            <?php foreach ($books as $key => $book): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">

                        <h5 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $book['book_id'] ?>" style="display: block;text-decoration: none">
                                <?= $book['name'] ?>
                            </a>
                        </h5>

                    </div>
                    <div id="collapse<?= $book['book_id'] ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p class="lead">无简介</p>

                            <p>共 <?= $book['catalogCount'] ?> 章，当前进度为第 <?= $book['currLocation'] ?> 章</p>

                            <p>
                                <a href="/catalog/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">进入目录</a>
                                <a href="/read/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">书接上回</a>
                                <a href="/parse/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">解析文件</a>
                            </p>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    </div>
<?php require "foot.php"; ?>