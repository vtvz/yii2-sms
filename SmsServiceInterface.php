<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 08.01.14
 * Time: 17:21
 */

namespace vtvz\yii2-sms;

/**
 * Interface SmsServiceInterface
 * @package vtvz\sms
 */
interface SmsServiceInterface
{
	/**
	 * @param string $phone
	 * @param string $message
	 * @param string $from
	 * @return bool
	 */
	public function send($phone, $message, $from);
} 