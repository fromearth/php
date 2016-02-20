/**
 * ファイル関連
 *
 * 実践的なファイル処理メモ
 *
 * @filename file.php
 * @category Examples
 * @package  ExamplesFile
 * @author   fromearth
 * @link     https://github.com/fromearth
 */



//-----------------------------------------
// ファイル処理記述例
//-----------------------------------------

// 読み出し（１行ごと）
ini_set("auto_detect_line_endings", true);  // Mac改行が検出できない場合は必要
$file = './foo.txt';
if (!is_readable($file) or !$fp=fopen($file, "r")) { // ※is_resource($fp)でチェックするまでもない
    // ファイルが読み出し不可の場合
    die("file open error!");
}
while (1) {
    if (($line=fgets($fp)) === false) {
        // 読み込めない場合
        if (feof($fp)) {
            // ファイル終端
            break;
        } else {
            // ファイル終端ではない
            die("file read error!");
        }
    }
    // ここで1行ごとに処理
    echo $line;
}
fclose($fp);



// 読み出し（一気に変数にセット）
$file = './foo.txt';
if (!is_readable($file)) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
$data = file_get_contents($file);



// 読み出し（一気に配列にセット）
$file = './foo.txt';
if (!is_readable($file)) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
$lines = file($file);



// 書き出し
$file = './foo.txt';
if (!is_dir(dirname($file)) or !$fp=fopen($file, "w")) {
    // ファイルが書き出し不可の場合
    die("file open error!");
}
fwrite($fp, "データ1\n");
fwrite($fp, "データ2\n");
fclose($fp);



// 書き出し（一気に）
$file = './foo.txt';
if (!is_dir(dirname($file))) {
    // ファイルが書き出し不可の場合
    die("file open error!");
}
file_put_contents($file, $data);  // $dataはスカラー変数でも配列でも可




//-----------------------------------------
// CSVファイル処理記述例
//-----------------------------------------

// 読み出し
ini_set("auto_detect_line_endings", true);  // Mac改行が検出できない場合は必要
$file = './foo.csv';
if (!is_readable($file) or !$fp=fopen($file, "r")) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
while (($data=fgetcsv($fp)) !== false) {
    // ここで1行ごとに処理
}
fclose($fp);



// 書き出し
$file = './foo.csv';
if (!is_dir(dirname($file)) or !$fp=fopen($file, "w")) {
    // ファイルが書き出し不可の場合
    die("file open error!");
}
$row1 = array("foo", "bar", "baz");
$row2 = array(2, 4, 6);
fputcsv($fp, $row1);
fputcsv($fp, $row2);
fclose($fp);





//-----------------------------------------
// TSVファイル処理記述例
//-----------------------------------------

// 読み出し
ini_set("auto_detect_line_endings", true);  // Mac改行が検出できない場合は必要
$file = './foo.tsv';
if (!is_readable($file) or !$fp=fopen($file, "r")) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
while (($data=fgetcsv($fp, 0, "\t")) !== false) {
    // ここで1行ごとに処理
}
fclose($fp);



// 書き出し
$file = './foo.tsv';
if (!is_dir(dirname($file)) or !$fp=fopen($file, "w")) {
    // ファイルが書き出し不可の場合
    die("file open error!");
}
$row1 = array("foo", "bar", "baz");
$row2 = array(2, 4, 6);
fputcsv($fp, $row1, "\t");
fputcsv($fp, $row2, "\t");
fclose($fp);





//-----------------------------------------
// iniファイル処理記述例
//
// [セクション]
// パラメータ名 = パラメータ値
//-----------------------------------------

// セクション分けした配列を取得
$file = './foo.ini';
$section = 'sectionA';
if (!is_readable($file)) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
$list = parse_ini_file($file, true);
if (isset($list[$section])) {
    var_dump($list[$section]);
}



// セクション無視した配列を取得
$file = './foo.ini';
if (!is_readable($file)) {
    // ファイルが読み出し不可の場合
    die("file open error!");
}
$list = parse_ini_file($file);
var_dump($list);
