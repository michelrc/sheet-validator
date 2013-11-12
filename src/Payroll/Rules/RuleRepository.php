<?php

namespace Payroll\Rules;

/**
 * Class RuleRepository
 *
 */
interface RuleRepository
{
    /**
     * Receives a RuleBuilder and modify it adding rules to it
     *
     * @param RuleBuilder $rb
     */
    public function buildRules($rb);

    /**
     * Return an array with context variables names used on the rules added
     *
     * @return array
     */
    public function getContextVariables();
}
