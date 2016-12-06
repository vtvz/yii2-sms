<?php

namespace vtvz\yii2\sms\services;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use vtvz\yii2\sms\SmsServiceInterface;
use yii\base\Component;


/**
 * Class Smsc
 */
class Smsc extends Component implements SmsServiceInterface
{
    const SMSC_URL = 'http://smsc.ru/sys/send.php';

    /**
     * @var string Login for Smsc
     */
    public $login;
    /**
     * @var string Password for Smsc
     */
    public $password;
    /**
     * @var string Default from
     */
    public $from;

    /**
     * @param string $phone
     * @param string $message
     * @param string $from
     * @return bool
     * @throws Exception
     * @throws \Exception
     */
    public function send($phone, $message, $from)
    {
        if ($from === null) {
            $from = $this->from;
        }

        $params = array(
            'login' => $this->login,
            'psw' => md5($this->password),
            'phones' => $phone,
            'translit' => 0,
            'fmt' => 3, // json
            'charset' => 'utf-8',
            'mes' => $message,
        );

        if ($from !== null) {
            $params['sender'] = $from;
        } else {
            $params['sender'] = '';
        }

        $client = new Client();
        try {
            $response = $client->get(self::SMSC_URL, $params);
        } catch (Exception $e) {
            \Yii::error(strtr('SMS sending to SMSC.RU results in system error: {error}', [
                '{error}' => $e->getMessage()
            ]), self::className());

            throw $e;
        }

        return true;
    }
}