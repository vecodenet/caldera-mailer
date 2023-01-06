<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer\Adapter;

abstract class AbstractAdapter implements AdapterInterface {

	/**
	 * Get adapter driver
	 * @return mixed
	 */
	public function getDriver() {
		return null;
	}
}