<?php

declare(strict_types=1);
/**
 * BorderTopWidth class.
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class BorderTopWidth.
 */
class BorderTopWidth extends Normalizer
{
	public function normalize($ruleValue, string $ruleName = ''): array
	{
		if ($this->normalized === null) {
			return $this->normalized = ['border-top-width' => $this->getNumberValues($ruleValue)[0]];
		}
		return $this->normalized;
	}
}
