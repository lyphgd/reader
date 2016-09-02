<?php
error_reporting(E_ALL);
require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();


define('ROOT_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', ROOT_PATH . '/applications');
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('FILE_PATH', ROOT_PATH . '/files');
define('VENDOR_PATH', ROOT_PATH . '/vendor');

$app = new \Slim\Slim(array(
    'templates.path' => TEMPLATE_PATH
));

require CONFIG_PATH . '/config_mysql.php';
require CORE_PATH . '/MysqliDb.php';

include VENDOR_PATH . '/autoload.php';

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
        "/^后记$/",
        "/^第[0-9]*章.*$/",
        "/^第(一|二|三|四|五|六|七|八|九|十|百|千|零|两| )*章.*$/",
        "/^第(一|二|三|四|五|六|七|八|九|十|百|千|零|两| )*编.*$/"
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

function getCollection()
{
    $m = new MongoClient('mongodb://localhost');
    $db = $m->reader;
    $collection = $db->book;

    return $collection;
}

function getBookById($bookId = 0)
{
    $db = new \Mysqlidb(\Config_Mysql::$masterServer);
    $db->where('id', $bookId);
    $book = $db->getOne('books');

    return $book;
}

function getCurrLocation($collection, $file_name)
{
    $cursor = $collection->find([
        'filename' => $file_name,
        'type' => 'currentLocation'
    ]);
    if ($cursor->count() > 0) {
        foreach ($cursor as $key => $val) {
            $start = $val['currentLocation'];
        }
    } else {
        $start = 0;
    }

    return $start;
}

function path_info($filepath)
{
    $path_parts = array();
    $path_parts ['dirname'] = rtrim(substr($filepath, 0, strrpos($filepath, '/')),"/")."/";
    $path_parts ['basename'] = ltrim(substr($filepath, strrpos($filepath, '/')),"/");
    $path_parts ['extension'] = substr(strrchr($filepath, '.'), 1);
    $path_parts ['filename'] = ltrim(substr($path_parts ['basename'], 0, strrpos($path_parts ['basename'], '.')),"/");
    return $path_parts;
}
// GET route

$app->get(
    '/index',
    function () use ($app) {

        $app->render('index.php', array());
    }
);

$app->get(
    '/bookrack',
    function () use ($app) {
        $db = new \Mysqlidb(\Config_Mysql::$masterServer);
        $collection = getCollection();

        $bookRows = $db->get('books');
        $books = array();
        foreach ($bookRows as $bookRow) {
            $currLocation = getCurrLocation($collection, $bookRow['file_name']);
            $catalogCount = $collection->find([
                'filename' => $bookRow['file_name'],
                'type' => ['$ne' => 'currentLocation']
            ])->count();
            $books[] = [
                'filename' => pathinfo($bookRow['file_name'])['filename'],
                'name' => $bookRow['book_name'],
                'book_id' => $bookRow['id'],
                'currLocation' => $currLocation,
                'catalogCount' => $catalogCount
            ];
        }

        $app->render('bookrack.php', array('books' => $books));
    }
);

$app->get(
    '/list',
    function () use ($app) {
        $db = new \Mysqlidb(\Config_Mysql::$masterServer);
        $collection = getCollection();

        $bookRows = $db->get('books');
        $books = array();
        foreach ($bookRows as $bookRow) {
            $currLocation = getCurrLocation($collection, $bookRow['file_name']);
            $catalogCount = $collection->find([
                'filename' => $bookRow['file_name'],
                'type' => ['$ne' => 'currentLocation']
            ])->count();
            $books[] = [
                'filename' => pathinfo($bookRow['file_name'])['filename'],
                'name' => $bookRow['book_name'],
                'book_id' => $bookRow['id'],
                'currLocation' => $currLocation,
                'catalogCount' => $catalogCount
            ];
        }

        $sets = $db->get('sets');
        $app->render('list.php', array('books' => $books, 'notebooks' => $sets));
    }
);

$app->map(
    '/upload',
    function ($bookId = 0) use ($app) {
        if ($app->request->isPost()) {
            $uploadFile = $_FILES['uploadFile'];
            //var_dump($uploadFile);
            //var_dump(path_info($uploadFile['name']));
            //var_dump(pathinfo($uploadFile['name']));
            //exit;
            //if (pathinfo($uploadFile['name']['filename'] == '') {
            //
            //}
            $uploadRes = file_put_contents(FILE_PATH . '/' . $uploadFile['name'], file_get_contents($uploadFile['tmp_name']));
            if ($uploadRes > 0) {
                // 上传成功，写库
                $db = new \Mysqlidb(\Config_Mysql::$masterServer);
                $data = [
                    'user_id' => 0,
                    'book_name' => path_info($uploadFile['name'])['filename'],
                    'file_name' => $uploadFile['name'],
                ];
                $res = $db->insert('books', $data);
                $app->redirect('/list');
                exit;
            }
        }

        $app->render('upload.php', array());
    }
)->via('GET', 'POST');

$app->get(
    '/intro/:bookId',
    function ($bookId = 0) use ($app) {
        $book = getBookById($bookId);
        $collection = getCollection();

        $currLocation = getCurrLocation($collection, $book['file_name']);
        $catalogCount = $collection->find([
            'filename' => $book['file_name'],
            'type' => ['$ne' => 'currentLocation']
        ])->count();

        $app->render('intro.php', array('book' => $book, 'currLocation' => $currLocation, 'catalogCount' => $catalogCount));
    }
);

$app->get(
    '/delete/:bookId',
    function ($bookId = 0) use ($app) {
        $book = getBookById($bookId);

        $collection = getCollection();

        $catalogCount = $collection->remove([
            'filename' => $book['file_name']
        ]);

        unlink(FILE_PATH . '/' . $book['file_name']);

        $db = new \Mysqlidb(\Config_Mysql::$masterServer);
        $db->where('id', $bookId);
        $db->delete('books', 1);

        $app->redirect('/list');
        exit;
    }
);

$app->get(
    '/catalog/:bookId',
    function ($bookId) use ($app) {
        $book = getBookById($bookId);
        if (isset($book)) {
            $collection = getCollection();

            $catalogs = array();
            $res = $collection->find([
                'filename' => $book['file_name'],
                'type' => ['$ne' => 'currentLocation']
            ])->sort(array("chapterCount" => 1));

            foreach ($res as $key => $val) {
                $catalogs[$val['chapterCount']] = $val['chapter'];
            }

            $cursor = $collection->find([
                'filename' => $book['file_name'],
                'type' => 'currentLocation'
            ]);
            if ($cursor->count() > 0) {
                foreach ($cursor as $key => $val) {
                    $currLocation = $val['currentLocation'];
                }
            } else {
                $currLocation = 0;
            }


        }


        $app->render('catalog.php', array('bookId' => $bookId, 'catalogs' => $catalogs, 'currLocation' => $currLocation));
    }
);

$app->get(
    '/read/:bookId(/:start)',
    function ($bookId, $start = '') use ($app) {

        $book = getBookById($bookId);
        if (isset($book)) {
            $collection = getCollection();

            $chapters = array();
            $bodys = array();

            $cursor = $collection->find([
                'filename' => $book['file_name'],
                'type' => 'currentLocation'
            ]);
            $chapter = '';
            if ($cursor->count() > 0) {
                foreach ($cursor as $key => $val) {
                    $currLocation = $val['currentLocation'];
                }
                $cursor = $collection->find([
                    'filename' => $book['file_name'],
                    'chapterCount' => intval($currLocation)
                ]);
                if ($cursor->count() > 0) {
                    foreach ($cursor as $key => $val) {
                        $chapter = $val['chapter'];
                    }
                }
                if ($currLocation != $start) {
                    $showJumpNotice = 1;
                } else {
                    $showJumpNotice = 0;
                }

            } else {
                $currLocation = 0;
                $showJumpNotice = 0;
            }

            if ($start === '') {
                $start = $currLocation;
                $showJumpNotice = 0;
            }

            $app->render('read.php', array(
                'body' => $bodys,
                'chapters' => $chapters,
                'loc' => 100,
                'start' => $start,
                'bookId' => $bookId,
                'currLocation' => $currLocation,
                'showJumpNotice' => $showJumpNotice,
                'chapter' => $chapter
            ));
        }

    }
);

$app->get(
    '/getmore/:bookId/:start',
    function ($bookId, $start = 0) use ($app) {

        $book = getBookById($bookId);
        if (isset($book)) {
            $collection = getCollection();

            $chapters = array();
            $bodys = array();
            $data = array();
            $data['lists'] = array();

            $cursor = $collection->find([
                'filename' => $book['file_name'],
                'chapterCount' => array('$gte' => $start + 0, '$lt' => $start + 5)
            ])->sort(array("chapterCount" => 1));

            foreach ($cursor as $key => $val) {
                $chapters[$val['chapterCount']] = $val['chapter'];
                $bodys[$val['chapterCount']] = json_decode($val['content'], true);

                $item = array();
                $item['chapterCount'] = $val['chapterCount'];
                $item['chapter'] = $val['chapter'];
                $item['sections'] = json_decode($val['content'], true);
                $data['lists'][] = $item;

            }

            $data['next'] = (!isset($key) || $key < 5) ? 0 : 1;
            echo json_encode($data);
            exit;
        }

    }
);

$app->get(
    '/parse/:bookId',
    function ($bookId) use ($app) {
        $startTime = microtime(true);
        $book = getBookById($bookId);
        if (isset($book)) {
            $filename = $book['file_name'];
            $filepath = FILE_PATH . '/' . $filename;
            if (!file_exists($filepath)) {
                die('file not found');
            }

            if (pathinfo($filename)['extension'] == 'pdf') {
                $content = file_get_contents($filepath, null, null, 0, filesize($filepath));

                $parser = new Smalot\PdfParser\Parser();
                try {
                    $pdf = $parser->parseFile($filepath);
                    $pages = $pdf->getPages();

                    foreach ($pages as $page) {
                        var_dump($page->getText());
                    }

                } catch (\Exception $e) {
                    var_dump($e);
                }
            } else if (pathinfo($filename)['extension'] == 'txt') {
                $content = file_get_contents($filepath, null, null, 0, filesize($filepath));
                $content = mb_convert_encoding($content, 'UTF-8', array('UTF-8', 'GBK', 'GB2312'));
                $contents = explode("\n", $content);
                list($contents, $chapters, $body) = resolve($contents);

                $collection = getCollection();
                $collection->remove(['filename' => $filename, 'type' => ['$ne' => 'currentLocation']]);

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
            }
        }
        $consume = microtime(true) - $startTime;

        $app->render('parse.php', array('consume' => $consume));
    }
);


$app->get(
    '/recordlocation/:bookId/:loc',
    function ($bookId, $loc) use ($app) {
        error_log($loc . "\r\n", 3, ROOT_PATH . '/logs/xxx.log');

        $book = getBookById($bookId);
        if (isset($book)) {
            $collection = getCollection();
            $filename = $book['file_name'];
            $res = $collection->find([
                'filename' => $filename,
                'type' => 'currentLocation'
            ]);

            if ($res->count() > 0) {

                //foreach ($res as $key => $value) {
                //    $currLocation = $value['currentLocation'];
                //}
                //
                //if ($loc != $currLocation) {
                //
                //}

                $collection->update([
                    'filename' => $filename,
                    'type' => 'currentLocation'
                ], ['$set' => ['currentLocation' => $loc]]);
            } else {
                $collection->insert(['filename' => $filename, 'type' => 'currentLocation', 'currentLocation' => $loc]);
            }
        }

    }
);

$app->map(
    '/notebook/add',
    function () use ($app) {
        if ($app->request->isPost()) {
            $db = new \Mysqlidb(\Config_Mysql::$masterServer);
            $data = [
                'set_name' => $app->request->post('notebook_name'),
                'user_id' => 0
            ];
            $db->insert('sets', $data);
            var_dump($db->getLastError());
        }

        $app->render('notebook_add.php', array());
    }
)->via('GET', 'POST');

$app->map(
    '/note/add/:notebookId',
    function ($notebookId = 0) use ($app) {
        if ($app->request->isPost()) {
            $db = new \Mysqlidb(\Config_Mysql::$masterServer);
            $data = [
                'cell_name' => $app->request->post('note_title'),
                'set_id' => $notebookId,
                'user_id' => 0
            ];
            $db->insert('cells', $data);
            var_dump($db->getLastError());
        }

        $app->render('note_add.php', array());
    }
)->via('GET', 'POST');

$app->get(
    '/test',
    function () use ($app) {
        $app->render('test.php', array());
    }
);

$app->run();
