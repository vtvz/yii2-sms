<?php

namespace vtvz\yii2-sms\services;

use yii\httpclient\Client;
use yii\httpclient\Exception;
use vtvz\yii2-sms\SmsServiceInterface;
use yii\base\Component;


/**
 * Class SmsOnline
 * @package \vtvz\yii2-sms\services
 */
class SmsOnline extends Component implements SmsServiceInterface
{
    const SMS_ONLINE_URL = 'http://sms.smsonline.ru/mt.cgi';

    /**
     * Login for SMS Online
     * @var string
     */
    public $login;
    /**
     * Password for SMS Online
     * @var
     */
    public $password;
    /**
     * Default 'From' name for SMS
     * @var string
     */
    public $from;

    /**
     * @param string $phone
     * @param string $message
     * @param string $from
     * @throws \Exception
     * @throws Exception
     * @return bool
     */
    public function send($phone, $message, $from = null)
    {
        if ($from === null)
            $from = $this->from;

        $params = array(
            'user' => $this->login,
            'pass' => $this->password,
            'to' => $phone,
            'txt' => nl2br($message),
            'utf' => 1,
        );

        if ($from !== null)
            $params['from'] = $from;

        $client = new Client();
        try {
            $response = $client->get(self::SMS_ONLINE_URL, $params);
        } catch (Exception $e) {
            \Yii::error(strtr('SMS sending to SMS online results in system error: {error}', [
                '{error}' => $e->getMessage()
            ]), self::className());

            throw $e;
        }

        if (preg_match('#<code>0</code>#', $response->getData()))
            return true;
        else {
            \Yii::error(strtr('SMS online returned error: {error}', [
                '{error}' => $response->getData(),
            ]), self::className());
            return false;
        }
    }


} 