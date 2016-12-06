<?php

namespace vtvz\yii2\sms\services;
use yii\base\Component;
use vtvz\yii2\sms\SmsServiceInterface;
use yii\httpclient\Client;
use yii\httpclient\Exception;


/**
 * Class SmsOnlineBulk
 */
class SmsOnlineBulk extends Component implements SmsServiceInterface
{
    const SMS_ONLINE_URL = 'https://bulk.sms-online.com/';
    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $secretKey;
    /**
     * @var string
     */
    public $from;

    /**
     * @param string $phone
     * @param string $message
     * @param string $from
     * @return bool
     */
    public function send($phone, $message, $from)
    {
        if ($from === null)
            $from = $this->from;

        $params = array(
            'user' => $this->login,
            'phone' => $phone,
            'txt' => nl2br($message),
        );

        if ($from !== null)
            $params['from'] = $from;

        $params['sign'] = md5(
            $params['user'].
            $params['from'].
            $params['phone'].
            $params['txt'].
            $this->secretKey
        );

        $client = new Client();
        try {
            $response = $client->get(self::SMS_ONLINE_URL, $params);
        } catch (Exception $e) {
            \Yii::error(strtr('SMS sending to SMS online Bulk API results in system error: {error}', [
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