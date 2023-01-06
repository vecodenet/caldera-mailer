<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer;

use GdImage;
use InvalidArgumentException;

use Caldera\Mailer\RecipientType;

class Message {

	/**
	 * Message subject
	 * @var string
	 */
	protected $subject;

	/**
	 * Message sender
	 * @var MailBox
	 */
	protected $sender;

	/**
	 * Message recipients
	 * @var array
	 */
	protected $recipients = [];

	/**
	 * Message body
	 * @var string
	 */
	protected $body;

	/**
	 * Message type
	 * @var MessageType
	 */
	protected $type;

	/**
	 * Message attachments
	 * @var array
	 */
	protected $attachments = [];

	/**
	 * Constructor
	 * @param string      $subject Message subject
	 * @param string      $body    Message body
	 * @param MessageType $type    Message type
	 */
	public function __construct(string $subject = '', string $body = '', MessageType $type = MessageType::PlainText) {
		$this->subject = $subject;
		$this->body = $body;
		$this->type = $type;
	}

	/**
	 * Set message subject
	 * @param string $subject Message subject
	 * @return $this
	 */
	public function setSubject(string $subject) {
		$this->subject = $subject;
		return $this;
	}

	/**
	 * Set message sender
	 * @param mixed  $sender Sender email
	 * @param string $name   Sender name
	 * @return $this
	 */
	public function setSender($sender, string $name = '') {
		if ($sender instanceof MailBox) {
			$this->sender = $sender;
		} else if ( is_string($sender) ) {
			if ( !$name && preg_match('/(?:"?([^"]*)"?\s)?<?(.+@[^>]+)>?/', $sender, $matches) === 1 ) {
				$name = $matches[1] ?? '';
				if ($name) {
					$sender = $matches[2] ?? '';
				}
			}
			$this->sender = new MailBox($sender, $name);
		} else {
			throw new InvalidArgumentException('Unsupported sender type');
		}
		return $this;
	}

	/**
	 * Set message body
	 * @param string $body Message body
	 * @return $this
	 */
	public function setBody(string $body) {
		$this->body = $body;
		return $this;
	}

	/**
	 * Set message type
	 * @param MessageType $type Message type
	 * @return $this
	 */
	public function setType(MessageType $type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Add message recipient
	 * @param mixed         $recipient Message recipient
	 * @param RecipientType $type      Recipient type
	 * @return $this
	 */
	public function addRecipient($recipient, RecipientType $type = RecipientType::To) {
		if (! isset( $this->recipients[$type->value] ) ) {
			$this->recipients[$type->value] = [];
		}
		if ($recipient instanceof MailBox) {
			$this->recipients[$type->value][] = $recipient;
		} else if ( is_array($recipient) ) {
			foreach ($recipient as $key => $value) {
				$key = is_numeric($key) ? '' : $key;
				if ( !$key && preg_match('/(?:"?([^"]*)"?\s)?<?(.+@[^>]+)>?/', $value, $matches) === 1 ) {
					$key = $matches[1] ?? '';
					if ($key) {
						$value = $matches[2] ?? '';
					}
				}
				$this->recipients[$type->value][] = new MailBox($value, $key);
			}
		} else if ( is_string($recipient) ) {
			$name = '';
			if ( preg_match('/(?:"?([^"]*)"?\s)?<?(.+@[^>]+)>?/', $recipient, $matches) === 1 ) {
				$name = $matches[1] ?? '';
				if ($name) {
					$recipient = $matches[2] ?? '';
				}
			}
			$this->recipients[$type->value][] = new MailBox($recipient, $name);
		} else {
			throw new InvalidArgumentException('Unsupported recipient type');
		}
		return $this;
	}

	/**
	 * Add message attachment
	 * @param mixed $attachment Message attachment
	 * @return $this
	 */
	public function addAttachment($attachment, string $name = '', AttachmentType $type = AttachmentType::Regular) {
		if ($attachment instanceof Attachment) {
			$this->attachments[] = $attachment;
		} else if ($attachment instanceof GdImage) {
			ob_start();
			imagepng($attachment);
			$contents = ob_get_clean();
			if ($contents) {
				$this->attachments[] = new Attachment($name, $contents, $type);
			}
		} else if ( is_string($attachment) ) {
			$this->attachments[] = new Attachment($name, $attachment, $type);
		} else if ( is_resource($attachment) ) {
			$resource_type = get_resource_type($attachment);
			switch ($resource_type) {
				case 'stream':
					$contents = stream_get_contents($attachment);
					if ($contents) {
						$this->attachments[] = new Attachment($name, $contents, $type);
					}
				break;
				default:
					throw new InvalidArgumentException('Unsupported resource type');
			}
		} else {
			throw new InvalidArgumentException('Unsupported attachment type');
		}
		return $this;
	}

	/**
	 * Get message subject
	 * @return string
	 */
	public function getSubject(): string {
		return $this->subject;
	}

	/**
	 * Get message subject
	 * @return MailBox
	 */
	public function getSender(): MailBox {
		return $this->sender;
	}

	/**
	 * Get message body
	 * @return string
	 */
	public function getBody(): string {
		return $this->body;
	}

	/**
	 * Get message type
	 * @return MessageType
	 */
	public function getType(): MessageType {
		return $this->type;
	}

	/**
	 * Get message recipients
	 * @param  mixed $type Recipient type
	 * @return array
	 */
	public function getRecipients($type = null): array {
		$type = $type instanceof RecipientType ? $type->value : $type;
		return $type ? ($this->recipients[$type] ?? []) : $this->recipients;
	}

	/**
	 * Get message attachments
	 * @return array
	 */
	public function getAttachments(): array {
		return $this->attachments;
	}

	/**
	 * Has attachments
	 * @return bool
	 */
	public function hasAttachments(): bool {
		return count( $this->attachments ) > 0;
	}
}
