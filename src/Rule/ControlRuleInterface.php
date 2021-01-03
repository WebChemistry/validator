<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Rule;

use Nette\Forms\Controls\BaseControl;
use ReflectionProperty;

interface ControlRuleInterface
{

	public function apply(BaseControl $control, ReflectionProperty $property): void;

}
