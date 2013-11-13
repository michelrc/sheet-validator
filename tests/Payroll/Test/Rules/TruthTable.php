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
     * Context variables names used on the rules declared here.
     *
     * @return array
     */
    public function getContextVariableNames()
    {
        return array('A2', 'B2', 'A3', 'B3', 'A4', 'B4', 'A5', 'B5');
    }

    /**
     * Add rules to the Builder
     *
     * @param RuleBuilder $rb
     * @return array Rule Array of rules
     */
    public function buildRules($rb)
    {
        $rules = array();

        $rules[] = $rb->create(
            $rb->logicalNot(
                $rb->logicalAnd(
                    $rb['A2']->equalTo(0),
                    $rb['B2']->equalTo(0)
                )
            )
        );

        $rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A3']->equalTo(0)
                ),
                $rb->logicalNot(
                    $rb['B3']->equalTo(1)
                )
            )
        );

        $rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A4']->equalTo(1)
                ),
                $rb->logicalNot(
                    $rb['B4']->equalTo(0)
                )
            )
        );

        $rules[] = $rb->create(
            $rb->logicalOr(
                $rb->logicalNot(
                    $rb['A3']->equalTo(1)
                ),
                $rb->logicalNot(
                    $rb['B3']->equalTo(1)
                )
            )
        );

        return $rules;

    }
}
