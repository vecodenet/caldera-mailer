<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Tests\Mailer;

use InvalidArgumentException;

use PHPUnit\Framework\TestCase;

use Caldera\Mailer\Attachment;
use Caldera\Mailer\AttachmentType;
use Caldera\Mailer\MailBox;
use Caldera\Mailer\Message;
use Caldera\Mailer\MessageType;
use Caldera\Mailer\RecipientType;

class MessageTest extends TestCase {

	public function testMailbox() {
		$mailbox = new MailBox();
		$mailbox->setName('Test')
			->setAddress('test@example.com');
		$this->assertEquals('Test', $mailbox->getName());
		$this->assertEquals('test@example.com', $mailbox->getAddress());
		$this->assertEquals('Test <test@example.com>', (string) $mailbox);
	}

	public function testAttachment() {
		$attachment = new Attachment();
		$attachment->setName('lorem.txt');
		$attachment->setContents('Lorem, ipsum dolor.');
		$attachment->setType(AttachmentType::Inline);
		$this->assertEquals('lorem.txt', $attachment->getName());
		$this->assertEquals('Lorem, ipsum dolor.', $attachment->getContents());
		$this->assertEquals(AttachmentType::Inline, $attachment->getType());
		$this->assertEquals('TG9yZW0sIGlwc3VtIGRvbG9yLg==', (string) $attachment);
	}

	public function testMessageSetSubject() {
		$message = new Message();
		$message->setSubject('Test');
		$this->assertEquals('Test', $message->getSubject());
	}

	public function testMessageSetSender() {
		$message = new Message();
		$message->setSender('test@example.com', 'Test');
		$this->assertEquals('Test <test@example.com>', (string) $message->getSender());
		#
		$message = new Message();
		$message->setSender('Test <test@example.com>');
		$this->assertEquals('Test <test@example.com>', (string) $message->getSender());
		#
		$message = new Message();
		$message->setSender('test@example.com');
		$this->assertEquals('<test@example.com>', (string) $message->getSender());
		#
		$message = new Message();
		$mailbox = new MailBox('test@example.com', 'Test');
		$message->setSender($mailbox);
		$this->assertEquals('Test <test@example.com>', (string) $message->getSender());
		#
		$this->expectException(InvalidArgumentException::class);
		$message->setSender($this);
	}

	public function testMessageSetBody() {
		$message = new Message();
		$message->setBody('<h1>Lorem, ipsum dolor.</h1>');
		$this->assertEquals('<h1>Lorem, ipsum dolor.</h1>', $message->getBody());
	}

	public function testMessageSetType() {
		$message = new Message();
		$message->setType(MessageType::Html);
		$this->assertEquals(MessageType::Html, $message->getType());
	}

	public function testMessageAddRecipient() {
		$message = new Message();
		$message->addRecipient(['Test' => 'test@example.com']);
		$this->assertArrayHasKey('to', $message->getRecipients());
		#
		$message = new Message();
		$message->addRecipient('Test <test@example.com>');
		$this->assertArrayHasKey('to', $message->getRecipients());
		#
		$message = new Message();
		$message->addRecipient('test@example.com');
		$this->assertArrayHasKey('to', $message->getRecipients());
		#
		$message = new Message();
		$mailbox = new MailBox('test@example.com', 'Test');
		$message->addRecipient($mailbox);
		$this->assertArrayHasKey('to', $message->getRecipients());
		#
		$message = new Message();
		$message->addRecipient(['Copy <copy@example.com>'], RecipientType::CC);
		$this->assertArrayHasKey('cc', $message->getRecipients());
		#
		$message = new Message();
		$message->addRecipient('copy@example.com', RecipientType::BCC);
		$this->assertArrayHasKey('bcc', $message->getRecipients());
		#
		$this->expectException(InvalidArgumentException::class);
		$message->addRecipient($this);
	}

	public function testMessageAddAttachment() {
		$message = new Message();
		$message->addAttachment('Lorem ipsum', 'lorem.txt');
		$this->assertContainsOnlyInstancesOf(Attachment::class, $message->getAttachments());
		$this->assertTrue($message->hasAttachments());
		#
		$message = new Message();
		$attachment = new Attachment('lorem.txt', 'Lorem ipsum');
		$message->addAttachment($attachment);
		$this->assertContainsOnlyInstancesOf(Attachment::class, $message->getAttachments());
		#
		$message = new Message();
		$handle = fopen(__FILE__, 'r');
		$message->addAttachment($handle, 'lorem.txt');
		fclose($handle);
		$this->assertContainsOnlyInstancesOf(Attachment::class, $message->getAttachments());
		#
		$message = new Message();
		$image = imagecreatetruecolor(10, 10);
		$message->addAttachment($image, 'lorem.png');
		imagedestroy($image);
		$this->assertContainsOnlyInstancesOf(Attachment::class, $message->getAttachments());
		#
		$this->expectException(InvalidArgumentException::class);
		$message->addAttachment($this, 'lorem.png');
	}
}
