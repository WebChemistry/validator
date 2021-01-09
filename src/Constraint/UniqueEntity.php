<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class UniqueEntity extends Constraint
{

	public string $message = 'Already exists, use different value please';

}
