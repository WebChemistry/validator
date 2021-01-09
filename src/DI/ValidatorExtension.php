<?php declare(strict_types = 1);

namespace WebChemistry\Validator\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\Validator\Constraint\UniqueEntityValidator;
use WebChemistry\Validator\FormObjectBuilder;
use WebChemistry\Validator\Resolver\CaptionResolver;
use WebChemistry\Validator\Resolver\InputTypeResolver;
use WebChemistry\Validator\Resolver\RulesResolver;
use WebChemistry\Validator\Rule\ConstraintRules;
use WebChemistry\Validator\Validator;

final class ValidatorExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('validator'))
			->setType(Validator::class);

		$builder->addDefinition($this->prefix('builder'))
			->setType(FormObjectBuilder::class);

		$builder->addDefinition($this->prefix('resolver.inputType'))
			->setType(InputTypeResolver::class);

		$builder->addDefinition($this->prefix('resolver.caption'))
			->setType(CaptionResolver::class);

		$builder->addDefinition($this->prefix('resolver.rules'))
			->setType(RulesResolver::class);

		$builder->addDefinition($this->prefix('rule.constraint'))
			->setType(ConstraintRules::class);

		$builder->addDefinition($this->prefix('constraint.validator.uniqueEntity'))
			->setFactory(UniqueEntityValidator::class);
	}

}
