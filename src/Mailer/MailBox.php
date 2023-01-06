<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer;

use Stringable;

class MailBox implements Stringable {

	/**
	 * Mailbox name
	 * @var string
	 */
	protected $name;

	/**
	 * Mailbox address
	 * @var string
	 */
	protected $address;

	/**
	 * Constructor
	 * @param string $address Mailbox address
	 * @param string $name    Mailbox name
	 */
	public function __construct(string $address = '', string $name = '') {
		$this->name = $name;
		$this->address = $address;
	}

	/**
	 * Set mailbox name
	 * @param string $name Mailbox name
	 * @return $this
	 */
	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Set mailbox address
	 * @param string $address Mailbox address
	 * @return $this
	 */
	public function setAddress(string $address) {
		$this->address = $address;
		return $this;
	}

	/**
	 * Get mailbox name
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get mailbox address
	 * @return string
	 */
	public function getAddress(): string {
		return $this->address;
	}

	/**
	 * Convert to string
	 * @return string
	 */
	public function __toString(): string {
		return $this->name ? sprintf('%s <%s>', $this->name, $this->address) : sprintf('<%s>', $this->address);
	}
}
