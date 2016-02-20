/**
 * ディレクトリ処理関連
 *
 * ディレクトリにあるファイル探索処理 / 再帰
 *
 * @filename directory.php
 * @category Examples
 * @package  ExamplesDirectory
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


/**
 * 指定ディレクトリ配下のファイルリスト取得
 * (ファイル数多い場合は巨大配列が生成されメモリ圧迫する可能性あり)
 * @param string $dir 探索ディレクトリ
 * @return array
 */
function getFileList($dir) {
    // 返却する配列
    $fileList = array();

    if (!is_dir($dir)) return $fileList;      // ディレクトリが渡されてこなかった
    if (!$dp=opendir($dir)) return $fileList; // ディレクトリが開けなかった

    $DS = DIRECTORY_SEPARATOR;

    while (($file=readdir($dp)) !== false) {
        // スキップ
        if ($file === '.') continue;  // 自ディレクトリへのリンク
        if ($file === '..') continue; // 親ディレクトリへのリンク

        // ファイルパス
        $filePath = $dir . $DS . $file;

        // 取得
        if (is_dir($filePath)) {
            // ディレクトリの場合
            $fileList = array_merge($fileList, getFileList($filePath));  // 再帰結果をマージ
        } else {
            // ディレクトリ以外の場合
            $fileList[] = $filePath;
        }
    }

    closedir($dp);    // 閉じる

    return $fileList;
}


/**
 * 指定ディレクトリ配下のファイル検索
 * @param string $dir     探索ディレクトリ
 * @param string $pattern 検索パターン文字列
 * @return array
 */
function searchDir($dir, $pattern) {
    // 返却する配列
    $fileList = array();

    if (!is_dir($dir)) return $fileList;      // ディレクトリが渡されてこなかった
    if (!$dp=opendir($dir)) return $fileList; // ディレクトリが開けなかった

    $DS = DIRECTORY_SEPARATOR;
    $PAT = '/' . preg_quote($pattern, '/') . '/i'; // 大小英文字区別なしでマッチさせる

    while (($file=readdir($dp)) !== false) {
        // スキップ
        if ($file === '.') continue;  // 自ディレクトリへのリンク
        if ($file === '..') continue; // 親ディレクトリへのリンク

        // ファイルパス
        $filePath = $dir . $DS . $file;

        // 取得
        if (is_dir($filePath)) {
            // ディレクトリの場合
            $fileList = array_merge($fileList, searchDir($filePath, $pattern));  // 再帰結果をマージ
        } else if (preg_match($PAT, $file) === 1) {
            // ディレクトリ以外でパターン一致の場合
            $fileList[] = $filePath;
        }
    }

    closedir($dp);    // 閉じる

    return $fileList;
}
