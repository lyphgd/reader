<?php //require "head.php"; ?>
<style>
    .panel-group .panel {
        border-radius: 0px;
        border-left: 0px;
        border-right: 0px;
    }
    .panel-group .panel+.panel {
        margin-top: -1px;
    }
    .panel-heading {
        padding-top: 5px;
        padding-bottom: 5px;
    }
</style>
    <!--<div class="container-fluid">-->

        <div class="row">
            <!--<div class="col-md-12">-->
            <div class="panel-group" id="accordion">

                <?php foreach ($books as $key => $book): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">

                            <div class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $book['book_id'] ?>" style="display: block;text-decoration: none">
                                    <?= $book['name'] ?>&nbsp;
                                </a>
                            </div>

                        </div>
                        <div id="collapse<?= $book['book_id'] ?>" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p class="lead">无简介</p>

                                <p>共 <?= $book['catalogCount'] ?> 章，当前进度为第 <?= $book['currLocation'] ?> 章</p>

                                <p>
                                    <a href="/catalog/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">进入目录</a>
                                    <a href="/read/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">书接上回</a>
                                    <a href="/parse/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">解析文件</a>
                                    <a href="/delete/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">删除</a>
                                </p>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!--</div>-->
        </div>

    <!--</div>-->

<?php //require "foot.php"; ?>