<?php
error_reporting(E_ALL);
require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();


define('ROOT_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', ROOT_PATH . '/applications');
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');

// var_dump(ROOT_PATH);exit;

$app = new \Slim\Slim(array(
    'templates.path' => TEMPLATE_PATH
));

require CONFIG_PATH . '/config_mysql.php';
require CORE_PATH . '/MysqliDb.php';


function clear_utf8_blank($data)
{
    if (is_array($data)) {
        $return_data = array();
        foreach ($data as $key => $item) {
            $item = trim(str_replace(array(chr(194) . chr(160), '　', ' ', chr(227) . chr(128) . chr(128)), ' ', $item));
            if ($item != '') {
                $return_data[] = $item;
            }
        }
    } else {
        $return_data = trim(str_replace(array(chr(194) . chr(160), chr(227) . chr(128) . chr(128)), ' ', $data));
    }

    return $return_data;
}

function resolve($contents)
{
    /*
     * 通过正则解析出前言、引子、章节等信息
     * 解析规则：从文章头部开始，套用规则数组，遇到符合条件的，即记录其位置A，下一个符合条件的位置记为B，A和B之间的内容为一段正文
     * 按上述规则将文本切开，分别套用不通的版式
     */
    $patterns = array(
        "/^前言$/",
        "/^引子$/",
        "/^第[0-9]*章.*$/",
        "/^第(一|二|三|四|五|六|七|八|九|十|百|千)*章.*$/"
        //"/^第.[一二三四五六七八九十]章.*$/"
    );

    $chapters = array();
    $body = array();

    foreach ($contents as $position => $content) {
        $chapterFlag = false;
        foreach ($patterns as $pattern) {
            $content = trim(clear_utf8_blank($content));
            if (preg_match($pattern, $content)) {
                $chapters[] = $content;
                $body[count($chapters) - 1] = array();
                $chapterFlag = true;
                break;
            }
        }

        if (!$chapterFlag) {
            $body[count($chapters) - 1][] = $content;
        }
    }
    unset($body[-1]);

    return [$contents, $chapters, $body];
}

// GET route

$app->get(
    '/list',
    function () use ($app) {
        $db = new \Mysqlidb(\Config_Mysql::$masterServer);
        $bookRows = $db->get('books');
        $books = array();
        foreach ($bookRows as $bookRow) {
            $books[] = ['filename' => pathinfo($bookRow['file_name'])['filename'], 'name' => $bookRow['book_name']];
        }

        $app->render('list.php', array('books' => $books));
    }
);

$app->get(
    '/catalog/:filename',
    function ($filename) use ($app) {
        $m = new MongoClient('mongodb://localhost');
        $db = $m->reader;
        $collection = $db->book;

        $catalogs = array();
        $res = $collection->find([
            'filename' => $filename . '.txt',
            'type' => ['$ne' => 'currentLocation']
        ])->sort(array("chapterCount" => 1));

        foreach ($res as $key => $val) {
            $catalogs[$val['chapterCount']] = $val['chapter'];
        }

        $app->render('catalog.php', array('file' => $filename, 'catalogs' => $catalogs));
    }
);

$app->get(
    '/read/:filename(/:start)',
    function ($filename, $start = '') use ($app) {
        $m = new MongoClient('mongodb://localhost');
        $db = $m->reader;
        $collection = $db->book;

        $chapters = array();
        $bodys = array();
        if ($start === '') {
            $cursor = $collection->find([
                'filename' => $filename . '.txt',
                'type' => 'currentLocation'
            ]);
            if ($cursor->count() > 0) {
                foreach ($cursor as $key => $val) {
                    $start = $val['currentLocation'];
                }
            } else {
                $start = 0;
            }
        }

        $app->render('read.php', array(
            'body' => $bodys,
            'chapters' => $chapters,
            'loc' => 100,
            'start' => $start,
            'file' => $filename
        ));
    }
);

$app->get(
    '/getmore/:filename/:start',
    function ($filename, $start = 0) use ($app) {

        $m = new MongoClient('mongodb://localhost');
        $db = $m->reader;
        $collection = $db->book;

        $filename .= '.txt';
        $chapters = array();
        $bodys = array();
        $data = array();
        $data['lists'] = array();


        $cursor = $collection->find([
            'filename' => $filename,
            'chapterCount' => array('$gte' => $start + 0, '$lt' => $start + 5)
        ])->sort(array("chapterCount" => 1));
        foreach ($cursor as $key => $val) {
            $chapters[$val['chapterCount']] = $val['chapter'];
            $bodys[$val['chapterCount']] = json_decode($val['content'], true);

            $item = array();
            $item['chapterCount'] = $val['chapterCount'];
            $item['chapter'] = $val['chapter'];
            //$item['sections'] = '';
            $item['sections'] = json_decode($val['content'], true);
            $data['lists'][] = $item;

        }

        $data['next'] = (!isset($key) || $key < 5) ? 0 : 1;
        echo json_encode($data);
        exit;
    }
);

$app->get(
    '/resolve/:filename',
    function ($filename) use ($app) {
        $s = microtime(true);
        $filename .= '.txt';
        $filepath = ROOT_PATH . '/public/' . $filename;
        if (!file_exists($filepath)) {
            die('file not found');
        }
        $content = file_get_contents($filepath, null, null, 0, filesize($filepath));
        $content = mb_convert_encoding($content, 'UTF-8', array('UTF-8', 'GBK', 'GB2312'));
        $contents = explode("\n", $content);
        list($contents, $chapters, $body) = resolve($contents);

        $m = new MongoClient('mongodb://localhost');
        $db = $m->reader;
        $collection = $db->book;

        foreach ($chapters as $key => $chapter) {
            $res = $collection->find(['filename' => $filename, 'chapterCount' => $key]);
            if ($res->count() > 0) {
                continue;
            }
            $document = array(
                'filename' => $filename,
                'chapterCount' => $key,
                'chapter' => $chapter,
                'content' => json_encode($body[$key])
            );
            $res = $collection->insert($document);
        }

        var_dump(microtime(true) - $s);
    }
);


$app->get(
    '/recordlocation/:loc',
    function ($loc) use ($app) {
        error_log($loc . "\r\n", 3, ROOT_PATH . '/logs/xxx.log');
        $m = new MongoClient('mongodb://localhost');
        $db = $m->reader;
        $collection = $db->book;

        $filename = 'yishi.txt';
        $cursor = $collection->find([
            'filename' => $filename,
            'type' => 'currentLocation'
        ]);

        if ($cursor->count() > 0) {
            $collection->update([
                'filename' => $filename,
                'type' => 'currentLocation'
            ], ['$set' => ['currentLocation' => $loc]]);
        } else {
            $collection->insert(['filename' => $filename, 'type' => 'currentLocation', 'currentLocation' => $loc]);
        }
    }
);


$app->run();
