<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Rule;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextBase;
use Nette\Forms\IControl;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ConstraintRules implements ControlRuleInterface
{

	public function __construct(
		private ValidatorInterface $validator
	)
	{
	}

	public function apply(BaseControl $control, ReflectionProperty $property, array $groups = []): void
	{
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
			}
		}
	}

	private function validateGroups(array $constraintGroups, array $groups): bool
	{
		if (!$groups) {
			$groups = ['Default'];
		}

		foreach ($constraintGroups as $group) {
			if (in_array($group, $groups, true)) {
				return true;
			}
		}

		return false;
	}

}
