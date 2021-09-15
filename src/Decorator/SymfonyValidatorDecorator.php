<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Decorator;

use Nette\Application\UI\Form;
use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextBase;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class SymfonyValidatorDecorator implements ControlDecoratorInterface
{

	public function __construct(
		private ValidatorInterface $validator,
	)
	{
	}

	public function decorate(
		Control $control,
		ReflectionProperty $property,
		array $groups = [],
		array $options = []
	): void
	{
		if (!$control instanceof BaseControl) {
			return;
		}

		/** @var ClassMetadata $metadata */
		$metadata = $this->validator->getMetadataFor($property->getDeclaringClass()->getName());

		$propertyMetadata = $metadata->properties[$property->getName()] ?? null;
		if (!$propertyMetadata) {
			return;
		}

		foreach ($propertyMetadata->getConstraints() as $constraint) {
			if (($constraint->payload['form'] ?? true) === false) {
				continue;
			}

			if ($constraint instanceof NotBlank) {
				if ($constraint->allowNull) {
					if ($control instanceof TextBase) {
						$control->setNullable();
					}

					continue;
				}

				$control->setRequired();
			} elseif ($constraint instanceof Length) {
				if ($constraint->min && $constraint->max) {
					$control->addRule(Form::LENGTH, null, [$constraint->min, $constraint->max]);
				} elseif ($constraint->min !== null) {
					$control->addRule(Form::MIN_LENGTH, null, $constraint->min);
				} elseif ($constraint->max !== null) {
					$control->addRule(Form::MAX_LENGTH, null, $constraint->max);
				}

			} elseif ($constraint instanceof Email) {
				$control->addRule(Form::EMAIL);
			} elseif ($constraint instanceof Regex) {
				$control->addRule(function (IControl $control) use ($constraint): bool {
					return (bool) preg_match($constraint->pattern, $control->getValue());
				}, $constraint->message);

				if (($constraint->payload['attribute'] ?? true) === true) {
					$control->setHtmlAttribute('pattern', $constraint->getHtmlPattern());
				}
			} elseif ($constraint instanceof Url) {
				$control->addRule(Form::URL);
			} elseif ($constraint instanceof Range) {
				$this->rangeRule($control, $constraint->min, $constraint->max);
			} elseif ($constraint instanceof GreaterThan) {
				$this->rangeRule($control, $constraint->value + 1);
			} elseif ($constraint instanceof GreaterThanOrEqual) {
				$this->rangeRule($control, $constraint->value);
			} elseif ($constraint instanceof LessThan) {
				$this->rangeRule($control, max: $constraint->value - 1);
			} elseif ($constraint instanceof LessThanOrEqual) {
				$this->rangeRule($constraint, max: $constraint->value);
			}
		}
	}

	private function rangeRule(BaseControl $control, int|float|null $min = null, int|float|null $max = null): void
	{
		if ($min !== null && $max !== null) {
			$control->addRule(Form::RANGE, null, [$min, $max]);
		} elseif ($min !== null) {
			$control->addRule(Form::MIN, null, $min);
		} elseif ($max !== null) {
			$control->addRule(Form::MAX, null, $max);
		}
	}

}
