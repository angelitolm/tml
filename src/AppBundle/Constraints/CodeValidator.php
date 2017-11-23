<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/10/2015
 * Time: 0:42
 */

namespace AppBundle\Constraints;

use AppBundle\Services\Configuration;
use AppBundle\Services\GuestCode;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CodeValidator extends ConstraintValidator
{
    private $userManager;
    private $promoCode;
    private $guestCode;
    private $configuration;

    public function __construct(UserManagerInterface $userManager,GuestCode $guestCode, $promo_code, Configuration $configuration) {
        $this->userManager = $userManager;
        $this->guestCode = $guestCode;
        $this->promoCode = $promo_code;
        $this->configuration = $configuration;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Code) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Code');
        }

        if($this->promoCode == $value) {
            return;
        }

        $user = $this->userManager->findUserBy(array('code'=>$value));
        if(null === $user) {
            $guest = $this->guestCode->findValidCode($value);
            if (null === $guest) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ code }}', $value)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->message)
                        ->setParameter('{{ code }}', $value)
                        ->addViolation();
                }
            }
        } elseif(!$user->isAccountNonLocked()) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ code }}', $value)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->message)
                    ->setParameter('{{ code }}', $value)
                    ->addViolation();
            }
        } else {
            /*$limit = (integer)$this->configuration->findByField('basic_limit_sponsor')->getValue();
            if($user->hasRole('ROLE_SUPERIOR')) {
                $limit = (integer)$this->configuration->findByField('superior_limit_sponsor')->getValue();
            }
            if($user->hasRole('ROLE_PROFESSIONAL')) {
                $limit = (integer)$this->configuration->findByField('professional_limit_sponsor')->getValue();
            }
            if(count($user->getActiveProfile()->getChildrenMembers()) >= $limit) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->messageLimit)
                        ->setParameter('{{ code }}', $value)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->messageLimit)
                        ->setParameter('{{ code }}', $value)
                        ->addViolation();
                }
            }*/
        }
    }
}