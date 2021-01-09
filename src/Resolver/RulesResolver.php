<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Resolver;

use Nette\Forms\Controls\BaseControl;
use ReflectionProperty;
use WebChemistry\Validator\Rule\ControlRuleInterface;

final class RulesResolver
{

	/**
	 * @param ControlRuleInterface[] $rules
	 */
	public function __construct(
		private array $rules
	)
	{
	}

	public function apply(BaseControl $control, ReflectionProperty $property, array $groups = []): void
	{
		foreach ($this->rules as $rule) {
			$rule->apply($control, $property, $groups);
		}
	}

}
