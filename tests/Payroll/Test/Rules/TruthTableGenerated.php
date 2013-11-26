<?php

use Ruler\Variable;
use Ruler\Operator;
use Ruler\RuleBuilder;
use Payroll\Rules\RuleRepository;

/**
 * Class TruthTableGenerated
 */
class TruthTableGeneratedOrig implements RuleRepository {
	/**
	 * @var \Ruler\RuleBuilder
	 */
	private $builder = null;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	private $logger = null;

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
		return "TruthTableGenerated";
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
	 * @var array
	 */
	private $context = array();

	/**
	 * @return array
	 */
	public function getContext()
	{
		for( $i = 2; $i <= $this->totalCount; $i++ ){
			// check expected values on each cell
			$this->context["A{$i}values"] = array('0', '1');
			$this->context["B{$i}values"] = array('0', '1');
		}

		return $this->context;
	}

		/**
		 * Return collection of rules to validate the payroll structure.
		 *
		 * This information is extracted from the declared columns
		 * in model for each sheet.
		 *
		 * @return array Rule
		 */
		public function getStructureValidationRules()
		{
			$rules = array();
			$rb = $this->builder;
			$self = $this;

			// checking each column header name and values
			$rules["A1=P"] = $rb->create(
				$rb->logicalNot($rb['TruthTableGenerated']['A1']->equalTo("P")),
				function () use ($self) {
					$self->getLogger()->log(1, "Column name at TruthTableGenerated:A1 doesn't match expected name. Expecting 'P')");
				}
			);

            $rb->create(
                $rb['A2']->equalTo($rb['B2'])
            );

			// each row in column A must met accepted values
			for( $i = 2; $i <= $this->totalCount; $i++ )
			{
				// check expected values
				$rules["A{$i}valuesContain"] = $rb->create(
					$rb->logicalNot(
						$rb['TruthTableGenerated']["A{$i}values"]->contains($rb['TruthTableGenerated']["A$i"])
					),
					function () use ($self, $i) {
						$self->getLogger()->log(1, "Column value at TruthTableGenerated:A{$i} doesn't match expected value. Expecting 0, 1");
					}
				);
			}
			$rules["B1=Q"] = $rb->create(
				$rb->logicalNot($rb['TruthTableGenerated']['B1']->equalTo("Q")),
				function () use ($self) {
					$self->getLogger()->log(1, "Column name at TruthTableGenerated:B1 doesn't match expected name. Expecting 'Q')");
				}
			);


			// each row in column B must met accepted values
			for( $i = 2; $i <= $this->totalCount; $i++ )
			{
				// check expected values
				$rules["B{$i}valuesContain"] = $rb->create(
					$rb->logicalNot(
						$rb['TruthTableGenerated']["B{$i}values"]->contains($rb['TruthTableGenerated']["B$i"])
					),
					function () use ($self, $i) {
						$self->getLogger()->log(1, "Column value at TruthTableGenerated:B{$i} doesn't match expected value. Expecting 0, 1");
					}
				);
			}

			return $rules;
		}

	/**
	 * Return collection of user defined rules to validate the payroll.
	 *
	 * @return array Rule
	 */
	public function getUserDefinedValidationRules()
	{
        $rules = array();
        $rb = $this->builder;
        $sum = new Operator\EqualTo(
            new Variable('Sum', $rb['A2'] + $rb['A3']),
            new Variable('result', 1)
        );

		$rules["verificar"] = $rb->create($sum);

		return $rules;
	}

	/**
	 * Return collection of rules to validate the payroll.
	 *
	 * The returned rules include those to validate structure as well
	 * as the domain expert declared rules.
	 *
	 * @return array Rule
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
