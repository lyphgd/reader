<?php require "head.php"; ?>
<style>
    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        width: 100%;
        height: 40px;
    }

    .footer a {
        position: relative;
        display: block;
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
        line-height: 40px;
        text-align: center;
        color: #666;
        background-color: #eee;
        text-decoration: none;
    }

    .footer a:before {
        content: '';
        position: absolute;
        left: 0;
        top: 10px;
        width: 1px;
        height: 20px;
        background-color: #ccc;
    }

    .footer a:first-child:before {
        display: none;
    }

    p {
        text-indent: 2em;
    }

    .main-body-collapse {
        height: 200px;
        overflow: hidden
    }
</style>
<div class="container">

        </div>
    </div>

</div>


<div class="modal fade" id="jumpNotice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">提醒</h4>
            </div>
            <div class="modal-body">
                <p>检测到您已阅读到第 <?= $currLocation ?> 章，是否要前往？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">否</button>
                <a href="/read/<?= $bookId ?>" type="button" class="btn btn-primary">是</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->

<input name="curr_location" id="curr_location" type="hidden"/>
<input name="curr_location2" id="curr_location2" type="hidden"/>
<input name="showJumpNotice" id="showJumpNotice" type="hidden" value="<?= $showJumpNotice ?>"/>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/flowtype.js"></script>
<script src="/js/dropload.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">

    var curr_chapter = 0;
    var counter = 0;
    // 每页展示5个
    var num = 5;
    var start = <?= $start ?>;
    var pageStart = start;
    var wait = false;
    var collapseFlag = false;

    function format() {
        String.prototype.Trim = function () {
            return this.replace(/(^\s*)|(\s*$)/g, "");
        }
        String.prototype.LTrim = function () {
            return this.replace(/(^\s*)/g, "");
        }
        String.prototype.RTrim = function () {
            return this.replace(/(\s*$)/g, "");
        }

            minimum: 500,
            maximum: 1200,
            minFont: 20,
            maxFont: 20,
            fontRatio: 30
        });
        $("p").each(function (index) {
            var ss = $("p:eq(" + index + ")").text();

            $("p:eq(" + index + ")").text(ss.Trim())

            $("p:eq(" + index + ")").css("text-indent", "2em");
        });

        //$(document).scrollTop(100);
    }

    $(document).ready(function () {
        load();
    });

    $('.footer-collapse').click(function () {
        if (!collapseFlag) {
            $('.mainBody').addClass('main-body-collapse');
            $('.footer-collapse').html('展开');
            collapseFlag = true;
        } else {
            $('.mainBody').removeClass('main-body-collapse');
            $('.footer-collapse').html('折叠');
            collapseFlag = false;
        }

    });

    $(document).scroll(function () {
        var curr_location = $(document).scrollTop();
        var chapters = $('.chapter');
        var bottom = $(document).height();
        for (var i = 0, len = chapters.length; i < len; i++) {

            if (i < len - 1) {
                var nextIndex = i + 1;
                var bottom = $('#chapter_' + nextIndex.toString()).offset().top;
            }

            if (curr_location < bottom && curr_location >= $('#chapter_' + i).offset().top) {
                if (curr_chapter != $('#chapter_' + i).attr('data-rank')) {
                    curr_chapter = $('#chapter_' + i).attr('data-rank');
                    $.ajax({
                        url: "/recordlocation/<?=$bookId?>/" + curr_chapter,
                        async: false,
                        dataType: 'json',
                        success: function (data) {
                            // 如果检测到有当前章节有变化，则提示是否跳转

                        },
                        error: function (xhr, type) {
                        }
                    });
                }
            }

        }

    });

    $('#jumpNotice').on('hidden.bs.modal', function (e) {
        $('#showJumpNotice').val(0);
        load();
    })

    function load() {
        $('.container').dropload({
            scrollArea: window,

                $.ajax({
                    type: 'GET',
                    url: '/getmore/<?=$bookId?>/' + pageStart,
                    dataType: 'json',
                    success: function (data) {
                        var result = '';
                        counter++;
                        pageEnd = num * counter;
                        pageStart = pageStart + num;
                        for (var i = 0; i < data.lists.length; i++) {

                            result += '<div class="mainBody"><h3 class="chapter" id="chapter_' + (i + pageStart - num - start) + '" data-rank="' + data.lists[i].chapterCount + '">'
                                + data.lists[i].chapter
                                    //+ data.lists[i].chapterCount
                                + '</h3>';
                            for (var j = 0; j < data.lists[i].sections.length; j++) {
                                result += '<p>' + data.lists[i].sections[j] + '</p>';
                            }
                            result += '</br></br></div>';

                            if (1 != data.next) {
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                break;
                            }
                        }
                        // 为了测试，延迟1秒加载
                        setTimeout(function () {
                            $('.lists').append(result);
                            format();

                            // 每次数据加载完，必须重置
                            me.resetload();
                        }, 1000);
                    },
                    error: function (xhr, type) {
                        alert('Ajax error!');
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            }
        });

    }

</SCRIPT>
</body>
</html>