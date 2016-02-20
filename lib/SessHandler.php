<?php
/**
 * セッション保存機構の定義（DB版）
 *
 * @filename SessHandler.php
 * @category MyLib
 * @package  MyLibSessHandler
 * @author   fromearth
 * @link     https://github.com/fromearth
 * @example  テーブル定義
 * <ddl>
 * CREATE TABLE `session` (
 *   `id` VARCHAR(32) NOT NULL,
 *   `data` MEDIUMTEXT NOT NULL,
 *   `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * </ddl>
 */

/**
 * セッション保存機構の定義クラス（DB版）
 */
class SessHandler
{
    // DB接続保持
    private $conn;

    /**
     * コンストラクタ
     * @param string [$uri=null] DB接続URI情報
     */
    public function __construct($uri=null)
    {
        // DB接続
        if (empty($uri)) {
            $this->conn = DB::connMaster();
        } else {
            $this->conn = DB::conn($uri);
        }
    }

    /**
     * セッションを開始する際に呼ばれる。session_start()で呼ばれる 
     * @param string $savePath    session.save_path
     * @param string $sessionName session.name(PHPSESSID)
     * @return bool
     */
    public function open($savaPath, $sessionName)
    {
        return is_resource($this->conn);
    }

    /**
     * セッションを閉じる際に呼び出される
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * セッションデータを読み込む / 対象レコードを取り出しデータ返却
     * @param string $id セッションID
     * @return string セッションエンコード（シリアライズ）文字列
     */
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT data FROM session WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['data'];
    }

    /**
     * セッションデータを書き込む / レコードの追加・更新
     * @param string $id   セッションID
     * @param string $data セッションのデータ $_SESSIONをシリアライズしたもの
     * @return bool
     */
    public function write($id, $data)
    {
        $stmt = $this->conn->prepare("REPLACE INTO session (id, data) VALUES (:id, :data)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':data', $data);

        return $stmt->execute();
    }

    /**
     * セッションを破棄する / 対象レコード削除
     * @param string $id セッションID
     * @return bool
     */
    public function destroy($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM session WHERE id = :id");
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * 古いセッションを削除
     * @param string $maxlifetime セッションのライフタイム session.gc_maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        $stmt = $this->conn->prepare("DELETE FROM session "
            . "WHERE (TIMESTAMP(CURRENT_TIMESTAMP) - TIMESTAMP(updated_at)) > :maxlifetime");
        $stmt->bindParam(':maxlifetime', $maxlifetime);

        return $stmt->execute();
    }
}


// 実装
$handler = new SessHandler(DB::getMasterUri());

session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
);

register_shutdown_function('session_write_close');
//session_start();
