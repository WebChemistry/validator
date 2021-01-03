<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Violation;

use ReflectionProperty;
use Symfony\Component\Validator\ConstraintViolation;
use WebChemistry\Validator\Attribute\Caption;

final class Violation
{

	private ?string $name;

	public function __construct(
		private ConstraintViolation $violation,
	)
	{
	}

	public function getViolation(): ConstraintViolation
	{
		return $this->violation;
	}

	public function hasPropertyNameAttribute(): bool
	{
		return (bool) $this->getPropertyNameAttribute();
	}

	public function getError(): string
	{
		$name = $this->getPropertyNameAttribute();

		if (!$name) {
			return $this->violation->getMessage();
		}

		return sprintf('%s: %s', $name, lcfirst($this->violation->getMessage()));
	}

	private function getPropertyNameAttribute(): ?string
	{
		if (!isset($this->name)) {
			$object = $this->violation->getRoot();
			$property = $this->violation->getPropertyPath();

			$this->name = null;

			if ($property) {
				$reflection = new ReflectionProperty($object, $property);
				$attribute = $reflection->getAttributes(Caption::class)[0] ?? null;
				if ($attribute) {
					$this->name = $attribute->newInstance()->name;
				}
			}
		}

		return $this->name;
	}

}
