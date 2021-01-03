<?php declare(strict_types = 1);

namespace WebChemistry\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WebChemistry\Validator\Violation\Violation;
use WebChemistry\Validator\Violation\ViolationList;

final class Validator
{

	public function __construct(
		private ValidatorInterface $validator,
	)
	{
	}

	/**
	 * @param Constraint|Constraint[]                            $constraints The constraint(s) to validate against
	 * @param string|GroupSequence|(string|GroupSequence)[]|null $groups      The validation groups to validate. If none is given, "Default" is assumed
	 */
	public function validate(object $value, $constraints = null, $groups = null): ?ViolationList
	{
		$errors = $this->validator->validate($value, $constraints, $groups);

		$violations = [];
		/** @var ConstraintViolation $error */
		foreach ($errors as $error) {
			$violations[] = new Violation($error);
		}

		return $violations ? new ViolationList($violations) : null;
	}

}
