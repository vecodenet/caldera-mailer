<?php

declare(strict_types = 1);

/**
 * Caldera Mailer
 * Mailing abstraction layer, part of Vecode Caldera
 * @author  biohzrdmx <github.com/biohzrdmx>
 * @copyright Copyright (c) 2023 Vecode. All rights reserved
 */

namespace Caldera\Mailer;

enum RecipientType: string {
	case To = 'to';
	case CC = 'cc';
	case BCC = 'bcc';
}
