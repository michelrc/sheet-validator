<?php

namespace Payroll\Rules;

/**
 * Class RuleRepository
 *
 */
interface RuleRepository
{
    /**
     * The RuleBuilder to use when creating rules
     * @param $builder
     * @return void
     */
    public function setBuilder($builder);

    /**
     * The logger to use when reporting messages
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public function setLogger($logger);

    /**
     * All rule repositories must be named
     * @return string Name of repository
     */
    public function getName();

    /**
     * Uses a RuleBuilder and create the rules.
     *
     * @return /Ruler/Rule
     */
    public function getRules();

    /**
     * Return an array with context variables names used on the rules added
     *
     * @return array
     */
    public function getContextVariables();
}
