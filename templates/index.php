<?php require "head.php"; ?>
<style>
    body {
        padding-top: 50px;
        overflow: hidden;
    }

    #wrapper {
        min-height: 100%;
        height: 100%;
        width: 100%;
        position: absolute;
        top: 0px;
        left: 0;
        display: inline-block;
    }

    #main-wrapper {
        height: 100%;
        overflow-y: auto;
        padding: 50px 0 0px 0;
    }

    #main {
        position: relative;
        height: 100%;
        overflow-y: auto;
        padding: 0 15px;
    }

    #sidebar-wrapper {
        height: 100%;
        padding: 50px 0 0px 0;
        position: fixed;
        border-right: 0px solid gray;
        background-color: #eeeeee;
    }

    #sidebar {
        position: relative;
        height: 100%;
        overflow-y: auto;
    }

    #sidebar .list-group-item {
        border-radius: 0;
        border-left: 0;
        border-right: 0;
        border-top: 0;
        background-color: #eeeeee;
    }

    #main {
        /*padding-top: 15px;*/
    }
    #main .list-group-item {
        border-radius: 0;
    }

    @media (max-width: 992px) {
        body {
            padding-top: 0px;
        }
    }

    @media (min-width: 992px) {
        #main-wrapper {
            float: right;
        }
    }

    @media (max-width: 992px) {
        #main-wrapper {
            padding-top: 0px;
        }
    }

    @media (max-width: 992px) {
        #sidebar-wrapper {
            position: static;
            height: auto;
            max-height: 300px;
            border-right: 0;
        }
    }
</style>
<div id="header" class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-header">
        <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
            <i class="icon-reorder"></i>
        </button>
        <a class="navbar-brand" href="#">
            Brand
        </a>
    </div>
    <nav class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
            <li>
                <a href="#">Navbar Item 1</a>
            </li>

        </ul>
        <ul class="nav navbar-nav pull-right">
            <li class="dropdown">
                <a href="#" id="nbAcctDD" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i>Username<i class="icon-sort-down"></i></a>
                <ul class="dropdown-menu pull-right">
                    <li><a href="#">Log Out</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<div id="wrapper">
    <div id="sidebar-wrapper" class="col-md-2">
        <div id="sidebar">
            <ul class="nav list-group">
                <li>
                    <a class="list-group-item" href="javascript:;" data-url="/bookrack"><i class="icon-home icon-1x"></i>My Bookrack</a>
                </li>
                <li>
                    <a class="list-group-item" href="javascript:;" url="/catalogs"><i class="icon-home icon-1x"></i>My Subscribe</a>
                </li>
                <li>
                    <a class="list-group-item" href="javascript:;" url="/catalogs"><i class="icon-home icon-1x"></i>My Notebook</a>
                </li>
                <li>
                    <a class="list-group-item" href="javascript:;" url="/catalogs"><i class="icon-home icon-1x"></i>My Youdaonote</a>
                </li>
            </ul>
        </div>
    </div>
    <div id="main-wrapper" class="col-md-10">
        <div id="main">
            <!--<div class="page-header">-->
            <!--<h3>Admin</h3>-->
            <!--</div>-->



        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>

<script src="/js/flowtype.js"></script>
<script src="/js/fileinput.js" type="text/javascript"></script>
<script src="/js/fileinput_locale_zh.js" type="text/javascript"></script>
<script src="/js/metisMenu.js" type="text/javascript"></script>
<script src="/js/sb-admin-2.js" type="text/javascript"></script>


<script>
    $('.list-group-item').click(function () {
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-url'),
            //dataType: 'json',
            success: function (data) {
                $('#main').html(data);
            },
            error: function (xhr, type) {

            }
        });
    });
</script>


</body>
</html>
