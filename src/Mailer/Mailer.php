<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer;

use Caldera\Mailer\Adapter\AdapterInterface;
use Caldera\Mailer\Message;

class Mailer {

	/**
	 * Adapter implementation
	 * @var AdapterInterface
	 */
	protected $adapter;

	/**
	 * Constructor
	 * @param AdapterInterface $adapter Adapter implementation
	 */
	public function __construct(AdapterInterface $adapter) {
		$this->adapter = $adapter;
	}

	/**
	 * Get Adapter implementation
	 * @return AdapterInterface
	 */
	public function getAdapter(): AdapterInterface {
		return $this->adapter;
	}

	/**
	 * Send a message
	 * @param  Message $message Message instance
	 * @return bool
	 */
	public function send(Message $message): bool {
		return $this->adapter->send($message);
	}
}
