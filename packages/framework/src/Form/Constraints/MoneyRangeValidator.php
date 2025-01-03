<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Form\Exception\NotMoneyTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MoneyRangeValidator extends ConstraintValidator
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MoneyRange) {
            throw new UnexpectedTypeException($constraint, MoneyRange::class);
        }

        if ($value === null) {
            return;
        }

        if (!($value instanceof Money)) {
            throw new NotMoneyTypeException($value);
        }

        if ($constraint->max !== null && $value->isGreaterThan($constraint->max)) {
            $this->context->addViolation($constraint->maxMessage, [
                '{{ limit }}' => $constraint->max->getAmount(),
            ]);
        }

        if ($constraint->min !== null && $value->isLessThan($constraint->min)) {
            $this->context->addViolation($constraint->minMessage, [
                '{{ limit }}' => $constraint->min->getAmount(),
            ]);
        }
    }
}
