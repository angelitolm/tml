<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 16/10/2015
 * Time: 0:39
 */

namespace AppBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Code
 * @package AppBundle\Constraints
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Code extends Constraint
{
    public $message = 'The code {{ code }} is not a valid User Code.';
    public $messageLimit = 'The code {{ code }} has reach the limit valid.';
    public $service = 'tml.code_validator';

    public function validatedBy() {
        return $this->service;
    }
}