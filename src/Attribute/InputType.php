<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class InputType
{

	public function __construct(
		public string $name,
	)
	{
	}

}
