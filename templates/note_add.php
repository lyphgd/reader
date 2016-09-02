<?php require "head.php"; ?>
    <div class="container">
        <h1>新建笔记</h1>

        <form enctype="multipart/form-data" method="post" action="" class="form-horizontal">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">标题</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="note_title" name="note_title" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">提交</button>
                    <button type="reset" class="btn btn-default">重置</button>
                </div>
            </div>

        </form>

    </div>

<?php require "foot.php"; ?>