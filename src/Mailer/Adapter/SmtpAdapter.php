<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer\Adapter;

use Caldera\Mailer\AttachmentType;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Caldera\Mailer\Message;
use Caldera\Mailer\MessageType;
use Caldera\Mailer\RecipientType;
use Caldera\Mailer\Adapter\AbstractAdapter;

class SmtpAdapter extends AbstractAdapter {

	/**
	 * PHPMailer instance
	 * @var PHPMailer
	 */
	protected $mail;

	/**
	 * Options array
	 * @var array
	 */
	protected $options = [];

	/**
	 * Constructor
	 * @param array $options Options array
	 */
	public function __construct(array $options = []) {
		$this->options = $options;
		# Configure PHPMailer
		$this->mail = new PHPMailer(true);
		$this->mail->isSMTP();
		$this->mail->Host = $this->options['host'] ?? '';
		$this->mail->Port = $this->options['port'] ?? 465;
		$this->mail->Username = $this->options['user'] ?? '';
		$this->mail->Password = $this->options['password'] ?? '';
		$this->mail->SMTPSecure = $this->options['secure'] ?? PHPMailer::ENCRYPTION_SMTPS;
		$this->mail->SMTPAuth = $this->options['auth'] ?? true;
		$this->mail->SMTPDebug = $this->options['debug'] ?? SMTP::DEBUG_OFF;
	}

	/**
	 * Get adapter driver
	 * @return mixed
	 */
	public function getDriver() {
		return $this->mail;
	}

	/**
	 * Send a message
	 * @param  Message $message Message instance
	 * @return bool
	 */
	public function send(Message $message): bool {
		$ret = false;
		# Get recipients
		$to = $message->getRecipients(RecipientType::To);
		$cc = $message->getRecipients(RecipientType::CC);
		$bcc = $message->getRecipients(RecipientType::BCC);
		if ($to) {
			# Set sendder and recipients
			$sender = $message->getSender();
			$this->mail->setFrom($sender->getAddress(), $sender->getName());
			foreach ($to as $recipient) {
				$this->mail->addAddress($recipient->getAddress(), $recipient->getName());
			}
			foreach ($cc as $recipient) {
				$this->mail->addCC($recipient->getAddress(), $recipient->getName());
			}
			foreach ($bcc as $recipient) {
				$this->mail->addBCC($recipient->getAddress(), $recipient->getName());
			}
			# Add attachments
			if ( $message->hasAttachments() ) {
				foreach ($message->getAttachments() as $attachment) {
					$this->mail->addStringAttachment($attachment->getContents(), $attachment->getName(), PHPMailer::ENCODING_BASE64, '', $attachment->getType() == AttachmentType::Inline ? 'inline' : 'attachment' );
				}
			}
			# Set subject and body
			$this->mail->isHTML( $message->getType() == MessageType::Html );
			$this->mail->Subject = $message->getSubject();
			$this->mail->Body = $message->getBody();
			if ( $message->getType() == MessageType::Html ) {
				$this->mail->AltBody = strip_tags( $message->getBody() );
			}
			# Send message
			try {
				$ret = $this->mail->send();
			} catch (Exception $e) {
				error_log( $e->getMessage() );
			}
		}
		return $ret;
	}
}
