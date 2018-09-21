<?php
declare(strict_types=1);
/**
 * FontEncoding class
 *
 * @package   YetiForcePDF\Objects
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Objects;

/**
 * Class FontEncoding
 */
class FontEncoding extends \YetiForcePDF\Objects\Resource
{
	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		return implode("\n", [
			$this->getRawId() . " obj",
			'<<',
			'  /Type Encoding',
			'  /BaseEncoding /Identity-H',
			'>>',
			'endobj'
		]);
	}
}