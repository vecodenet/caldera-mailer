<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer\Adapter;

use Caldera\Mailer\Message;
use Caldera\Mailer\MessageType;
use Caldera\Mailer\RecipientType;
use Caldera\Mailer\Adapter\AbstractAdapter;

class MailAdapter extends AbstractAdapter {

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
	}

	/**
	 * Send a message
	 * @param  Message $message Message instance
	 * @return bool
	 */
	public function send(Message $message): bool {
		$ret = false;
		$headers = [];
		$to = $message->getRecipients(RecipientType::To);
		if ( $to ) {
			# Format the To addresses
			$items = [];
			foreach ($to as $recipient) {
				$items[] = (string) $recipient;
			}
			$to = implode(', ', $items);
			# Format the CC addresses, if any
			$cc = $message->getRecipients(RecipientType::CC);
			$headers['From'] = (string) $message->getSender();
			if ( $cc ) {
				$items = [];
				foreach ($cc as $recipient) {
					$items[] = (string) $recipient;
				}
				$headers['Cc'] = implode(', ', $items);
			}
			# Format the BCC addresses, if any
			$bcc = $message->getRecipients(RecipientType::BCC);
			if ( $bcc ) {
				$items = [];
				foreach ($bcc as $recipient) {
					$items[] = (string) $recipient;
				}
				$headers['Bcc'] = implode(', ', $items);
			}
			# HTML message
			if ( $message->getType() == MessageType::Html ) {
				$headers['MIME-Version'] = '1.0';
				$headers['Content-type'] = 'text/html; charset=utf8';
			}
			# Send the mail
			$ret = mail($to, $message->getSubject(), $message->getBody(), $headers);
		}
		return $ret;
	}
}
