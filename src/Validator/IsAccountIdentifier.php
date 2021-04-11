<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsAccountIdentifier extends Constraint
{
    public $message = 'Le numéro de compte : "{{ string }}" ne correspond à aucun compte bancaire actif.';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}