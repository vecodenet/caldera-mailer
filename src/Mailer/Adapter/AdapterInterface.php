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

interface AdapterInterface {

	/**
	 * Send a message
	 * @param  Message $message Message instance
	 * @return bool
	 */
	public function send(Message $message): bool;

	/**
	 * Get adapter driver
	 * @return mixed
	 */
	public function getDriver();
}
