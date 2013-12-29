<?php

/**
 * This file is part of the Payroll package, an CID project.
 *
 * (c) 2013 CID Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Payroll;

use Payroll\Rules\RuleRepository;
use Ruler\RuleBuilder;
use \Ruler\RuleSet;
use \Ruler\Context;
use PHPExcel_Exception;

/**
 * Payroll Validator
 *
 * This class will act as a collector
 */
class PayrollValidator {

    /**
     * @var string Document Path
     */
    private $path_excel_file;

    /**
     * @var \PHPExcel Document instance on memory
     */
    private $excel_file;

    /**
     * @var array RuleRepository
     */
    private $repositories = array();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     * Constructor
     * @param string $path_excel_file Document path
     * @param array $rule_repositories Array of file paths containing rules
     * @param $logger \Psr\Log\LoggerInterface
     * @throws \Exception
     */
    public function __construct($path_excel_file, array $rule_repositories, $logger) {
        $this->logger = $logger;
        $this->path_excel_file = $path_excel_file;
        try {
            $this->excel_file = \PHPExcel_IOFactory::load($this->path_excel_file);
        } catch (Exception $e) {
            throw new \Exception("Invalid path for file", $e);
        }

        foreach ($rule_repositories as $file) {

            $path = realpath($file);
            if (is_file($path)) {

                // lets plug the rules
                require_once($path);
                // get rid of any extension
                $class = explode('.', basename($path));
                $class = $class[0];
                // instantiate the class
                $this->repositories[] = new $class();
            } else
                throw new \Exception("Invalid path for rules. [" . $path . "]");
        }
    }

    /**
     * Return a
     * @return \PHPExcel
     */
    public function getExcelFile() {
        return $this->excel_file;
    }

    /**
     * @return RuleRepository
     */
    public function getRepositories() {
        return $this->repositories;
    }

    public function buildRules($rb) {

    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger) {
        $this->logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }


     /**
     * @throws PHPExcel_Exception
     * @return array
     */
    public function getContext() {

        $context = array();
        foreach($this->repositories as $r){
            $name = $r->getName();

            $sheet = $this->getExcelFile()->getSheetByName($name);

            // total count of row
            $total = $sheet->getHighestRow();
            $r->setTotalCount($total);

            if($sheet == null) {
                throw new PHPExcel_Exception(
                    "Applying rules to non existent sheet {$name} in excel document");
            }

            $cells = $sheet->getCellCollection();
            foreach($cells as $c) {
                $cell = $sheet->getCell($c);
                $context[$name][$c] = $cell->getFormattedValue();
            }

            // internal used variables on repository
            foreach($r->getContext() as $n => $v) {
                $context[$name][$n] = $v;
            }

        }

        return $context;

    }

    public function rulesEvaluator() {
        $rb = new RuleBuilder();
        // this should be called before getting the rules from repositories
        // it set up max count of rows from excel workbook
        $context = new Context($this->getContext());

        $rules_object_array = array();

        foreach ($this->getRepositories() as $repository) {
            $repository->setBuilder($rb);
            $repository->setLogger($this->logger);
            $rules_object_array = array_merge($rules_object_array, $repository->getRules());
        }

        $rules = new RuleSet($rules_object_array);

        $rules->executeRules($context);
    }

}

