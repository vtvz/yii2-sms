<?php

namespace vtvz\yii2\sms\services;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\base\Component;
use vtvz\yii2\sms\SmsServiceInterface;


/**
 * Class SmsRu implements interface for sending SMS via sms.ru
 */
class SmsRu extends Component implements SmsServiceInterface
{
    const SMS_RU_URL = 'http://sms.ru/sms/send';

    public $apiId;
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
            'api_id' => $this->apiId,
            'text' => $message,
            'to' => $phone,
        );

        if ($from !== null)
            $params['from'] = $from;

        $client = new Client();
        try {
            $response = $client->get(self::SMS_RU_URL, $params);
        } catch (Exception $e) {
            \Yii::error(strtr('SMS sending to SMS.RU results in system error: {error}', [
                '{error}' => $e->getMessage()
            ]), self::className());

            throw $e;
        }

        if (strpos((string)$response->getData(), '100') === 0)
            return true;
        else {
            \Yii::error(strtr('SMS.RU returned error: {error}', [
                '{error}' => $response->getData(),
            ]), self::className());
            return false;
        }
    }
}