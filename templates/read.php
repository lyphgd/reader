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
        font-family: Microsoft YaHei, '宋体', Tahoma, Helvetica, Arial, "\5b8b\4f53", sans-serif;
    }

</style>
<div class="container">

    <div class="row col-md-6 col-md-offset-3">
        <!--<h1 id="temp"></h1>-->
        <div class="lists" id="lists">
        </div>
    </div>

    <div class="footer">
        <a href="/catalog/<?= $file ?>" class="item">目录</a>
    </div>
</div>

<input name="curr_location" id="curr_location" type="hidden"/>
<input name="curr_location2" id="curr_location2" type="hidden"/>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>
<script src="/js/flowtype.js"></script>
<script src="/js/dropload.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">

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

        $('body').flowtype({
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
        $('#curr_location').val($('#lists').height());
        $('#curr_location').val($('.container').height());

    });

    var curr_chapter = <?=$start?>;

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
                    $.ajax({url: "/recordlocation/" + curr_chapter, async: false});
                    //$('#curr_location').val($('.container').height());
                    //$('#curr_location').val($('.lists').height() + '|' + $('.lists').width());
                    //$('#curr_location').val(curr_chapter);
                }
            }

        }

    });

    var counter = 0;
    // 每页展示5个
    var num = 5;
    var start = <?= $start ?>;
    var pageStart = start;
    // dropload
    $('.container').dropload({
        scrollArea: window,
        loadDownFn: function (me) {
            $.ajax({
                type: 'GET',
                url: '/getmore/<?=$file?>/' + pageStart,
                dataType: 'json',
                success: function (data) {
                    var result = '';
                    counter++;
                    pageEnd = num * counter;
                    pageStart = pageStart + num;
                    var index4Result = 0;
                    for (var i = 0; i < data.lists.length; i++) {

                        result += '<h3 class="chapter" id="chapter_' + (i + pageStart - num - start) + '" data-rank="' + data.lists[i].chapterCount + '">'
                            + data.lists[i].chapter
                                //+ data.lists[i].chapterCount
                            + '</h3>';
                        for (var j = 0; j < data.lists[i].sections.length; j++) {
                            result += '<p>' + data.lists[i].sections[j] + '</p>';
                        }
                        result += '</br></br>';

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

    $(function () {
        setInterval(function () {
            //$.ajax({url: "/recordlocation/" + $('#curr_location2').val(), async: false});
        }, 1000);
    });
</SCRIPT>
</body>
</html>