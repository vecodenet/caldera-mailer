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

class Attachment implements Stringable {

	/**
	 * Attachment type
	 * @var AttachmentType
	 */
	protected $type;

	/**
	 * Attachment name
	 * @var string
	 */
	protected $name;

	/**
	 * Attachment contents
	 * @var string
	 */
	protected $contents;

	/**
	 * Constructor
	 * @param string         $name     Attachment name
	 * @param string         $contents Attachment address
	 * @param AttachmentType $type     Attachment type
	 */
	public function __construct(string $name = '', string $contents = '', AttachmentType $type = AttachmentType::Regular) {
		$this->name = $name;
		$this->contents = $contents;
		$this->type = $type;
	}

	/**
	 * Set attachment type
	 * @param AttachmentType $type Attachment type
	 * @return $this
	 */
	public function setType(AttachmentType $type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set attachment name
	 * @param string $name Attachment name
	 * @return $this
	 */
	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Set attachment contents
	 * @param string $contents Attachment contents
	 * @return $this
	 */
	public function setContents(string $contents) {
		$this->contents = $contents;
		return $this;
	}

	/**
	 * Get attachment type
	 * @return AttachmentType
	 */
	public function getType(): AttachmentType {
		return $this->type;
	}

	/**
	 * Get attachment name
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get attachment contents
	 * @return string
	 */
	public function getContents(): string {
		return $this->contents;
	}

	/**
	 * Convert to string
	 * @return string
	 */
	public function __toString(): string {
		return base64_encode($this->contents);
	}
}