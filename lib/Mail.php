<?php
/**
 * メール関連
 *
 * @filename Mail.php
 * @category MyLib
 * @package  MyLibMail
 * @author   fromearth
 * @link     https://github.com/fromearth
 */


/**
 * メール送信処理クラス
 *
 * @example Usage
 * <code>
 * $mail = new Mail();
 * $mail->setFrom("from@example.com")
 *      ->setTo("to@example.com")
 *      ->send("Here is subject", "Here is body message");
 * </code>
 */
class Mail
{
    // 連結子
    const JOIN_HEADER = "\r\n";
    const JOIN_FIELD = ",";
    // X-Mailerヘッダフィールド情報
    const MAILER = 'MyLibMail-Sender';
    const VERSION = '1.0';

    // メールアドレス内部保持
    private $from = array();
    private $to = array();
    private $cc = array();
    private $bcc = array();

    /**
     * コンストラクタ
     * @access public
     */
    public function __construct()
    {
    }

    /**
     * メール送信
     * 注意点：mb_send_mail()は「件名」「本文」しかエンコードしてくれない
     * @param string $subject 件名
     * @param string $message 本文
     * @return bool 送信が成功したかどうか
     */
    public function send($subject, $message)
    {
        // 文字エンコーディング指定
        mb_language('ja');
        mb_internal_encoding('JIS');

        $header = $this->makeHeader();  // ヘッダー生成・取得
        $toField = $this->makeField(null, $this->to); // Toフィールドデータ取得

        if (empty($toField) || empty($subject) || empty($message)) {
            // あて先・件名・本文のいずれかが空の場合
            return false;
        }

        return mb_send_mail($toField, self::toJis($subject), self::toJis($message), $header);
    }

    /**
     * 差出人情報をセット
     * @param string $from        送信元メールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function setFrom($from, $text=null)
    {
        $this->from[0] = array(
            "mail_address"=>$from,
            "text"=>$text,
        );

        return $this;
    }

    /**
     * 送信先情報をセット
     * @param string $to          送信先メールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function setTo($to, $text=null)
    {
        $this->to = array();      // 保持配列初期化
        $this->addTo($to, $text); // 情報追加

        return $this;
    }

    /**
     * カーボンコピー情報をセット
     * @param string $cc          カーボンコピーのメールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function setCc($cc, $text=null)
    {
        $this->cc = array();      // 保持配列初期化
        $this->addCc($cc, $text); // 情報追加

        return $this;
    }

    /**
     * ブラインドカーボンコピー情報をセット
     * @param string $bcc         ブラインドカーボンコピーのメールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function setBcc($bcc, $text=null)
    {
        $this->bcc = array();       // 保持配列初期化
        $this->addBcc($bcc, $text); // 情報追加

        return $this;
    }

    /**
     * 送信先情報を追加セット
     * @param string $to          送信先メールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function addTo($to, $text=null)
    {
        $this->to[] = array(
            "mail_address"=>$to,
            "text"=>$text,
        );

        return $this;
    }

    /**
     * カーボンコピー情報を追加セット
     * @param string $cc          カーボンコピーのメールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function addCc($cc, $text=null)
    {
        $this->cc[] = array(
            "mail_address"=>$cc,
            "text"=>$text,
        );

        return $this;
    }

    /**
     * ブラインドカーボンコピー情報を追加セット
     * @param string $bcc         ブラインドカーボンコピーのメールアドレス
     * @param string [$text=null] 表示テキスト
     * @return object インスタンス
     */
    public function addBcc($bcc, $text=null)
    {
        $this->bcc[] = array(
            "mail_address"=>$bcc,
            "text"=>$text,
        );

        return $this;
    }

    /**
     * ヘッダー作成
     * @return string ヘッダー情報
     */
    private function makeHeader()
    {
        $fromField      = $this->makeField('From', $this->from);
        $xMailerField   = $this->makeXMailerField();
        $replyToField   = $this->makeReplyToField();
        $messageIdField = $this->makeMessageIdField();
        $dateField      = $this->makeDateField();

        $header = $fromField      . self::JOIN_HEADER
                . $xMailerField   . self::JOIN_HEADER
                . $replyToField   . self::JOIN_HEADER
                . $messageIdField . self::JOIN_HEADER
                . $dateField;

        $ccField = $this->makeField('Cc', $this->cc);
        if ($ccField !== '') {
            // CC情報がセットされていた場合
            $header .= self::JOIN_HEADER
                    . $ccField;
        }

        $bccField = $this->makeField('Bcc', $this->bcc);
        if ($bccField !== '') {
            // BCC情報がセットされていた場合
            $header .= self::JOIN_HEADER
                    . $bccField;
        }

        return $header;
    }

    /**
     * フィールド情報作成
     * @param string $fieldName フィールド名
     * @param array  $fieldInfo フィールド情報配列
     * @return string フィールド情報文字列
     */
    private function makeField($fieldName, &$fieldInfo)
    {
        $res = '';

        foreach ($fieldInfo as &$data) {
            // セットされたフィールド情報ごとに処理
            if ($res !== '') {
                // 少なくとも１フィールド情報が付加済みの場合
                $res .= self::JOIN_FIELD;   // フィールド連結子
            }

            // フィールド情報を付加
            $res .= $this->makeFieldData($data['mail_address'], $data['text']);
        }

        if ($res === '') {
            // フィールド情報がなかった場合
            return '';
        }

        if ($fieldName != '') {
            // フィールド名指定あれば
            $fieldName .= ": "; // フィールド名とデータ間の区切り
        }

        return $fieldName . $res;
    }

    /**
     * フィールド-日付情報を作成
     * @return string 日付情報
     */
    private function makeDateField()
    {
        return 'Date: ' . date('D, d M Y H:i:s O');
    }

    /**
     * フィールド-メッセージID情報を作成
     * @return string メッセージID情報
     */
    private function makeMessageIdField()
    {
        // 送信元メールアドレスを@文字部分で分割
        $split = explode('@', $this->from[0]['mail_address']);

        return 'Message-Id: <' . md5(uniqid()) . "@{$split[1]}>";
    }

    /**
     * フィールド-返信先情報を作成
     * @return string 返信先情報
     */
    private function makeReplyToField()
    {
        return 'Reply-To: ' . $this->from[0]['mail_address'];
    }

    /**
     * フィールド-Xメーラー情報を作成
     * @return string Xメーラー情報
     */
    private function makeXMailerField()
    {
        return 'X-Mailer: ' . self::MAILER . '/' . self::VERSION;
    }

    /**
     * フィールドデータ（メールアドレスと表示テキスト）を連結
     * @param string $mailAddress メールアドレス
     * @param string $text        表示テキスト
     * @return string フィールドデータ
     */
    private function makeFieldData($mailAddress, $text)
    {
        if (strlen($text) > 0) {
            // 表示テキスト指定あり
            $res = $this->encFieldData($text) . " <" . $mailAddress . ">";
        } else {
            // 表示テキスト指定なし
            $res = $mailAddress;
        }

        return $res;
    }

    /**
     * 表示テキスト用エンコード処理
     * @param string $s 表示テキスト文字列
     * @return string エンコード済み表示テキスト
     */
    private function encFieldData($s)
    {
        if (preg_match('/[^\x20-\x7f]/', $s) !== 1) {
            // アスキーのみの場合
            // RFC822の特殊文字 ( ) < > @ , ; : \ " . [ ]
            if (preg_match('/[\(\)\<\>\@\,\;\:\\\"\.\[\]]/', $s) !== 1) {
                // 特殊文字を含まない場合
                return $s;  // そのまま返す
            }

            // エスケープ後ダブルクォートで囲って返す
            return '"' . preg_replace('/\\*\"/', "\\\"", $s) . '"';
        }

        // JISコード変換
        $jisStr = self::toJis($s);

        return mb_encode_mimeheader($jisStr, "JIS"); // RFC 2047
    }

    /**
     * JISコード変換
     * @param string $s 変換前文字列
     * @return string 変換後文字列
     */
    private static function toJis($s)
    {
        return mb_convert_encoding($s, "JIS", "ASCII,JIS,UTF-8,CP932,EUC-JP");
    }
}
