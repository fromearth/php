/**
 * コマンドライン引数処理
 *
 * @filename getopt.php
 * @category Examples
 * @package  ExamplesGetopt
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


$shortOpts = '';
$shortOpts .= "f:";  // -f=value or -f "value" 値が必須
$shortOpts .= "v::"; // -v or -v=value 値がオプション
$shortOpts .= "abc"; // -a -b -c or -abc 値なし

$longOpts = array(
    "required:",   // 値が必須
    "optional::",  // 値がオプション
    "option",      // 値なし
    "opt",         // 値なし
);

$options = getopt($shortOpts, $longOpts);
var_dump($options);



if (isset($options['f'])) {
    // -f=value or -f "value" あり
    $value = $options['f'];
}

if (isset($options['v'])) {
    // -v=value or -v "value" or -v あり
    if ($options['v'] !== "") {
        // 値ありオプションの場合
        $value = $options['v'];
    }
}

if (isset($options['a'])) {
    // -a あり
}

if (isset($options['b'])) {
    // -b あり
}

if (isset($options['c'])) {
    // -c あり
}

if (isset($options['required'])) {
    // --required=value or --required "value data"
    $value = $options['required'];
}

if (isset($options['optional'])) {
    // --optional=value or --optional "value data" or --optional
    if ($options['optional'] !== "") {
        // 値ありオプションの場合
        $value = $options['optional'];
    }
}

if (isset($options['option'])) {
    // --option
}

if (isset($options['opt'])) {
    // --opt
}




//---------------------------------------------
// 日次バッチでよくある日付オプション処理例
// # php command.php -d 2016-01-04
// ・オプションなければ、前日の日付で処理
// ・オプションあれば、その日付で処理
//---------------------------------------------
$shortOpts = '-d:';
$options = getopt($shortOpts);

if (isset($options['d'])) {
    // -dオプションあり
    if ($options['d'] === "") {
        // 値（日付）ない場合
        die("date not found");
    }

    $timestamp = strtotime($options['d']);

    if ($ret === false) {
        // 日付が取得できない場合
        die("date invalid");
    }

    $d = getdate($timestamp);
} else {
    // -dオプションなし
    $d = getdate(strtotime("yesterday"));
}

// 処理する日付取得
$year = $d['year'];
$mon = $d['mon'];
$day = $d['mday'];
