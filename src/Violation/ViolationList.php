<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Violation;

use ArrayIterator;
use IteratorAggregate;

final class ViolationList implements IteratorAggregate
{

	/**
	 * @param Violation[] $violations
	 */
	public function __construct(
		private array $violations,
	)
	{
	}

	/**
	 * @return ArrayIterator<Violation>
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->violations);
	}

	/**
	 * @return Violation[]
	 */
	public function getViolations(): array
	{
		return $this->violations;
	}

	public function __toString(): string
	{
		return implode("\n", array_map(fn (Violation $violation) => $violation->getError(), $this->violations));
	}

}
