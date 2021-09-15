<?php declare(strict_types = 1);

namespace WebChemistry\Validator\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\Validator\Constraint\UniqueEntityValidator;
use WebChemistry\Validator\Decorator\BuiltInTypeDecorator;
use WebChemistry\Validator\Decorator\CaptionDecorator;
use WebChemistry\Validator\Decorator\ControlDecoratorInterface;
use WebChemistry\Validator\Decorator\RequireDecorator;
use WebChemistry\Validator\Decorator\SymfonyValidatorDecorator;
use WebChemistry\Validator\Factory\InputFactory;
use WebChemistry\Validator\Factory\InputFactoryInterface;
use WebChemistry\Validator\FormValidatorBuilder;

final class ValidatorExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$this->compiler->addExportedType(UniqueEntityValidator::class);

		$builder->addDefinition($this->prefix('formValidator'))
			->setType(FormValidatorBuilder::class);

		$builder->addDefinition($this->prefix('inputFactory'))
			->setType(InputFactoryInterface::class)
			->setFactory(InputFactory::class);

		$builder->addDefinition($this->prefix('decorator.symfonyValidator'))
			->setType(ControlDecoratorInterface::class)
			->setFactory(SymfonyValidatorDecorator::class);

		$builder->addDefinition($this->prefix('decorator.builtInType'))
			->setType(ControlDecoratorInterface::class)
			->setFactory(BuiltInTypeDecorator::class);

		$builder->addDefinition($this->prefix('decorator.caption'))
			->setType(ControlDecoratorInterface::class)
			->setFactory(CaptionDecorator::class);

		$builder->addDefinition($this->prefix('decorator.require'))
			->setType(ControlDecoratorInterface::class)
			->setFactory(RequireDecorator::class);

		$builder->addDefinition($this->prefix('constraint.validator.uniqueEntity'))
			->setFactory(UniqueEntityValidator::class);
	}

}
