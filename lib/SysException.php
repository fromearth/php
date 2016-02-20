<?php
/**
 * 例外拡張（システム要因）
 *
 * システム要因による例外は、データ修正・プログラムバグ修正・サーバ復旧など行われない限り問題解消されない
 *
 * @filename SysException.php
 * @category MyLib
 * @package  MyLibSysException
 * @author   fromearth
 * @link     https://github.com/fromearth
 */

/**
 * 例外拡張クラス（システム要因）
 *
 * @example
 * <code>
 * try {
 *     //throw new SysException('test');
 * } catch (SysException $e) {
 *     // システム要因の例外発生
 *     if ($e->getCode() == 0) {
 *         // ユーザ側にそのまま表示してもよいメッセージの場合
 *     } else {
 *     }
 * } catch (UsrException $e) {
 *     // ユーザ要因の例外発生
 *     if ($e->getCode() == 0) {
 *         // ユーザ側にそのまま表示してもよいメッセージの場合
 *     } else {
 *     }
 * } catch (Exception $e) {
 *     // その他例外発生
 *     switch (get_class($e)) {
 *     case 'FooException':
 *     case 'BarException':
 *         break;
 *     default:
 *         break;
 *     }
 * }
 * </code>
 */
class SysException extends Exception
{
    /**
     * コンストラクタ
     * @param string $message  例外メッセージ
     * @param mixed  [$code=0] 例外コード
     */
    public function __construct($message, $code=0)
    {
        parent::__construct($message, $code);
    }

    /**
     * 文字列扱い時データ
     * @return string
     */
    public function __toString()
    {
        return "{$this->file}({$this->line}) [{$this->code}]:{$this->message}\n";
    }
}
