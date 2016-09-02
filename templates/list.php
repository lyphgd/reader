<?php require "head.php"; ?>
    <link href="/css/sb-admin-2.css" media="all" rel="stylesheet" type="text/css"/>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">惠人贷前端管理平台</a>
            </div>
            <ul class="nav navbar-nav">
                <li class="active">
                </li>
                <li>
                </li>

            </ul>

            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        &nbsp;
                    </a>
                    <ul class="dropdown-menu dropdown-user">

                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </button>
                                </span>
                            </div>
                            <!-- /input-group -->
                        </li>

                        <li>
                            <a href="/upload" type="buqtton" class="btn btn-link">
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 上传
                            </a>
                        </li>

                        <li>
                            <a href="#"> 我的笔记本<span class="glyphicon glyphicon-menu-down" style="float:right"></span></a>
                            <ul class="nav nav-second-level">
                                <?php foreach ($notebooks as $notebook) { ?>
                                <li>
                                    <a href="/note/list/<?=$notebook['id']?>"><?=$notebook['set_name']?></a>
                                </li>
                                <?php } ?>
                            </ul>
                        </li>

                        <li>
                            <a href="#"> 我的文件<span class="glyphicon glyphicon-menu-down" style="float:right"></span></a>
                        </li>

                        <li>
                            <a href="#"> 我的订阅<span class="glyphicon glyphicon-menu-down" style="float:right"></span></a>
                        </li>

                        <li>
                            <a href="#"> 我的有道云笔记<span class="glyphicon glyphicon-menu-down" style="float:right"></span></a>
                        </li>

                        <li>
                            <a href="#"> 我的Dropbox<span class="glyphicon glyphicon-menu-down" style="float:right"></span></a>
                        </li>

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <!--<div class="col-md-12">-->
                        <div class="panel-group" id="accordion">

                            <?php foreach ($books as $key => $book): ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">

                                        <h5 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $book['book_id'] ?>" style="display: block;text-decoration: none">
                                                <?= $book['name'] ?>&nbsp;
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
                                                <a href="/delete/<?= $book['book_id'] ?>" type="button" class="btn btn-primary">删除</a>
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <!--</div>-->
                </div>


            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>

<?php require "foot.php"; ?>