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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

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
     * @var array
     */
    private $context = array();

    /**
     * @return array
     */
    public function getContext()
    {
        for ($i = 2; $i <= $this->totalCount; $i++) {
            // check expected values on each cell
            $this->context["A{$i}values"] = array('0', '1');
            $this->context["B{$i}values"] = array('0', '1');
        }
        return $this->context;
    }

    /**
     * @var int Total amount of record (rows) to iterate
     */
    private $totalCount = 0;

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

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
     * Return collection of rules to validate the payroll structure.
     *
     * This information is extracted from the declared columns
     * in model for each sheet.
     *
     * @return array Ruler\Rule
     */
    public function getStructureValidationRules()
    {
        $rules = array();
        $rb = $this->builder;
        $self = $this;

        // checking each column header name and values
        $rules["A1=P"] = $rb->create(
            $rb->logicalNot($rb['TruthTable']['A1']->equalTo("P")),
            function () use ($self) {
                $self->getLogger()->log(1, "Column name at TruthTable:A1 doesn't match expected name. Expecting 'P')");
            }
        );

        // each row in column A must met accepted values
        for ($i = 2; $i <= $this->totalCount; $i++) {
            // check expected values
            $rules["A{$i}valuesContain"] = $rb->create(
                $rb->logicalNot(
                    $rb['TruthTable']["A{$i}values"]->contains($rb['TruthTable']["A$i"])
                ),
                function () use ($self, $i) {
                    $self->getLogger()->log(1, "Column value at TruthTable:A{$i} doesn't match expected value. Expecting: [0,1].");
                }
            );
        }

        $rules["B1=Q"] = $rb->create(
            $rb->logicalNot($rb['TruthTable']['B1']->equalTo("Q")),
            function () use ($self) {
                $self->getLogger()->log(1, "Column name at TruthTable:B1 doesn't match expected name. Expecting 'Q'.");
            }
        );

        // each row in column B must met accepted values
        for ($i = 2; $i <= $this->totalCount; $i++) {
            // check expected values
            $rules["B{$i}valuesContain"] = $rb->create(
                $rb->logicalNot(
                    $rb['TruthTable']["B{$i}values"]->contains($rb['TruthTable']["B$i"])
                ),
                function () use ($self, $i) {
                    $self->getLogger()->log(1, "Column value at TruthTable:B{$i} doesn't match expected value. Expecting: [0,1].");
                }
            );
        }

        return $rules;

    }

    /**
     * Return collection of user defined rules to validate the payroll.
     *
     * @return array Ruler\Rule
     */
    public function getUserDefinedValidationRules()
    {
        $rules = array();
        $rb = $this->builder;
        // TODO Generate user defined rules, this one is for testing purposes
        $rules['complex'] = $rb->create(
            $rb->logicalNot(
                new \Ruler\Operator\EqualTo(
                    new \Ruler\Operator\Division(
                        new \Ruler\Operator\Addition(
                            $rb['TruthTableGenerated']["B2"],
                            new \Ruler\Operator\Addition(
                                $rb['TruthTableGenerated']["B2"],
                                new \Ruler\Variable(null, 1)
                            )
                        ),
                        new \Ruler\Variable(null, 1)
                    ),
                    new \Ruler\Variable(null, 1)
                )
            )
        );

        $rules["complex2"] = $rb->create(
            $rb->logicalNot(
                new \Ruler\Operator\EqualTo(
                    new \Ruler\Operator\Addition(
                        $rb['TruthTableGenerated']['A2'],
                        new \Ruler\Operator\Addition(
                            $rb['TruthTableGenerated']['A3'], $rb['TruthTableGenerated']['A4']
                        )
                    )
                    ,
                    new \Ruler\Operator\Division(
                        new \Ruler\Operator\Multiplication(
                            $rb['TruthTableGenerated']['A2'],
                            new \Ruler\Operator\Multiplication(
                                $rb['TruthTableGenerated']['A3'], $rb['TruthTableGenerated']['A4']
                            )
                        )
                        ,
                        new \Ruler\Variable(null, 3)
                    )
                )
            )
        );

        return $rules;
    }

    /**
     * Return collection of rules to validate the payroll.
     *
     * The returned rules include those to validate structure as well
     * as the domain expert declared rules.
     *
     * @return array Ruler\Rule
     */
    public function getRules()
    {
        $result = array_merge(
            $this->getStructureValidationRules(),
            $this->getUserDefinedValidationRules()
        );

        return $result;
    }

}
