<?php
/**
 * PHPバッチファイル作成に実装すべき処理まとめ
 *
 * @filename batch.php
 * @category Examples
 * @package  ExamplesBatch
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


/**
 * 二重起動防止
 * Linux環境で手軽に実装するには、セマフォを利用
 * Windows環境にも対応させるには、ファイルロックやプロセスチェックなどで行う必要あり
 *
 * @see ftok()
 */
if (!function_exists('sem_get') or !$semId=sem_get(101) or !sem_acquire($semId)) {
    // セマフォを取得できなかった場合
    die("stop!");
}

// バッチ処理が終わったら最後に解放
sem_release($semId);



/**
 * 実行にかかった時間/レポート
 */
$start = microtime(true);

// バッチ処理の最後の方で計算
printf('%02f', microtime(true) - $start);



/**
 * 実行時間無制限設定
 */
ignore_user_abort(true);  // ユーザによる中断を無視
set_time_limit(0);        // 実行時間を無制限に



/**
 * コマンドライン引数処理
 *
 * @see getopt.php
 */

