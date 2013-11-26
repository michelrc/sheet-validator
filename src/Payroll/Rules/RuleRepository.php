<?php

namespace Payroll\Rules;

use \Psr\Log\LoggerInterface;
use \Ruler\RuleBuilder;

/**
 * Class RuleRepository
 *
 */
interface RuleRepository
{
    /**
     * The RuleBuilder to use when creating rules
     * @param RuleBuilder $builder
     * @return void
     */
    public function setBuilder($builder);

    /**
     * @internal param $builder
     * @return RuleBuilder
     */
    public function getBuilder();

    /**
     * The logger to use when reporting messages
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger($logger);

    /**
     * @internal param $logger
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * Set total amount of row to process
     * @param $count
     * @return void
     */
    public function setTotalCount($count);

    /**
     * @return int
     */
    public function getTotalCount();

    /**
     * All rule repositories must be named
     * @return string Name of repository
     */
    public function getName();

    /**
     * Uses a RuleBuilder and create the rules.
     *
     * @return  array Rule
     */
    public function getRules();

    /**
     * Internal variables declared to use in the rules context
     * @return array
     */
    public function getContext();

}
