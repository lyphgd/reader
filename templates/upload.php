<?php require "head.php"; ?>
    <div class="container  kv-main">
        <h1>上传文件</h1>

        <form enctype="multipart/form-data">
            <input id="file-0a" class="file" type="file" multiple data-min-file-count="1" >
            <br>
            <button type="submit" class="btn btn-primary">提交</button>
            <button type="reset" class="btn btn-default">重置</button>
        </form>

    </div>

    <script>
        $('#file-0a').fileinput({
            alert(1);
            language: 'zh',
            uploadUrl: '#',
            allowedFileExtensions: ['jpg', 'png', 'gif'],
        });
    </script>
<?php require "foot.php"; ?>