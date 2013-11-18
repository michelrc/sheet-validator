<?php

use Ruler\RuleBuilder;
use Payroll\Rules\RuleRepository;

/**
 * Class TruthTable
 *
 */
class TruthTable implements RuleRepository
{
    /**
     * @var \Ruler\RuleBuilder
     */
    private $builder = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Rules collection (Repository)
     * @var /Ruler/Rule
     */
    private $rules = array();

    /**
     * @param \Ruler\RuleBuilder $builder
     * @param \Psr\Log\LoggerInterface $logger
     * @internal param array $rules
     */
    public function __construct($builder = null, $logger = null)
    {
        $this->builder = $builder;
        $this->logger = $logger;
    }

    /**
     * @return string The name of the repository
     */
    public function getName()
    {
        return "TruthTable";
    }

    /**
     * @return \Ruler\RuleBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param \Ruler\RuleBuilder $builder
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }


    /**
     * Context variables names used on the rules declared here.
     *
     * @return array
     */
    public function getContextVariables()
    {
        return array('A2', 'B2', 'A3', 'B3', 'A4', 'B4', 'A5', 'B5');
    }

    /**
     * Uses the builder to create the rules. Callable on rules will use the logger
     * interface to report issues.
     *
     * @return array /Ruler/Rule Array of rules
     */
    public function getRules()
    {

        $rb = $this->builder;

        $this->rules[] = $rb->create(
            $rb->logicalNot(
                $rb->logicalAnd(
                    $rb['A2']->equalTo(0),
                    $rb['B2']->equalTo(0)
                )
            )
        );

        $this->rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A3']->equalTo(0)
                ),
                $rb->logicalNot(
                    $rb['B3']->equalTo(1)
                )
            )
        );

        $this->rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A4']->equalTo(1)
                ),
                $rb->logicalNot(
                    $rb['B4']->equalTo(0)
                )
            )
        );

        $this->rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A3']->equalTo(1)
                ),
                $rb->logicalNot(
                    $rb['B3']->equalTo(1)
                )
            )
        );

        return $this->rules;

    }

}
