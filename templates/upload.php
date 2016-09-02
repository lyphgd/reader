<?php require "head.php"; ?>
    <div class="container  kv-main">
        <h1>上传文件</h1>

        <form enctype="multipart/form-data" method="post" action="/upload">
            <input id="uploadFile" name="uploadFile" class="file"  type="file" multiple data-min-file-count="1" data-max-file-count="1">
            <br>
            <button type="submit" class="btn btn-primary">提交</button>
            <button type="reset" class="btn btn-default">重置</button>
        </form>

    </div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/flowtype.js"></script>
<!--<script src="/js/fileinput.js" type="text/javascript"></script>-->
<!--<script src="/js/fileinput_locale_zh.js" type="text/javascript"></script>-->


<SCRIPT LANGUAGE="JavaScript">

    //$('#uploadFile').fileinput({
    //    language: 'zh',
    //    allowedFileExtensions: ['txt', 'pdf', 'docx'],
    //});

</SCRIPT>


</body>
</html>
