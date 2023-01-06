<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer\Adapter {

	function mail() {
		return true;
	}
}

namespace Caldera\Tests\Mailer {

	use PHPUnit\Framework\TestCase;

	use Caldera\Mailer\Mailer;
	use Caldera\Mailer\Adapter\MailAdapter;
	use Caldera\Mailer\Adapter\SmtpAdapter;
	use Caldera\Mailer\Message;
	use Caldera\Mailer\MessageType;
	use Caldera\Mailer\RecipientType;

	class MailerTest extends TestCase {

		/**
		 * Config path
		 * @var string
		 */
		protected static $path;

		/**
		 * Config data
		 * @var mixed
		 */
		protected static $config;

		protected function setUp(): void {
			# Create storage
			self::$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json';
			if ( file_exists(self::$path) ) {
				self::$config = json_decode( file_get_contents(self::$path) );
			} else {
				$this->fail('Config file not found');
			}
		}

		public function testWithMailAdapter() {
			$sender = self::$config->general->from;
			$recipient = self::$config->general->to;
			$recipient_cc = self::$config->general->cc;
			$recipient_bcc = self::$config->general->bcc;
			#
			$options = [];
			$adapter = new MailAdapter($options);
			$mailer = new Mailer($adapter);
			$message = new Message('Test', '<h1>This is a test</h1>', MessageType::Html);
			$message->setSender($sender, 'Test')
				->addRecipient($recipient)
				->addRecipient($recipient_cc, RecipientType::CC)
				->addRecipient($recipient_bcc, RecipientType::BCC);
			$sent = $mailer->send($message);
			$this->assertInstanceOf(MailAdapter::class, $mailer->getAdapter());
			$this->assertTrue($sent);
			$this->assertNull($adapter->getDriver());
		}

		public function testWithSmtpAdapter() {
			$sender = self::$config->general->from;
			$recipient = self::$config->general->to;
			$recipient_cc = self::$config->general->cc;
			$recipient_bcc = self::$config->general->bcc;
			#
			$options = [
				'host' => self::$config->smtp->host,
				'port' => self::$config->smtp->port,
				'user' => self::$config->smtp->user,
				'password' => self::$config->smtp->password,
			];
			$adapter = new SmtpAdapter($options);
			$mailer = new Mailer($adapter);
			$adapter->getDriver()->SMTPDebug = 2;
			$adapter->getDriver()->Debugoutput = 'error_log';
			$message = new Message('Test', '<h1>This is a test</h1>', MessageType::Html);
			$message->setSender($sender, 'Test')
				->addRecipient($recipient)
				->addRecipient($recipient_cc, RecipientType::CC)
				->addRecipient($recipient_bcc, RecipientType::BCC)
				->addAttachment('Lorem ipsum', 'lorem.txt');
			$sent = $mailer->send($message);
			$this->assertInstanceOf(SmtpAdapter::class, $mailer->getAdapter());
			$this->assertTrue($sent);
		}
	}
}
