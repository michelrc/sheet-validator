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
     * Constructor
     * @param string $path_excel_file Document path
     * @param array $rule_repositories Array of file paths containing rules
     * @throws \Exception
     */
    public function __construct($path_excel_file, array $rule_repositories) {
        $this->path_excel_file = $path_excel_file;
        try {
            $this->excel_file = \PHPExcel_IOFactory::load($this->path_excel_file);
        } catch (Exception $e) {
            die('Error loading file');
        }

        foreach($rule_repositories as $file) {

            $path = realpath($file);
            if(is_file($path)){

                // lets plug the rules
                require_once($path);
                // get rid of any extension
                $class = explode('.', basename($path));
                $class = $class[0];
                // instantiate the class
                $this ->repositories[] = new $class();

            } else throw new \Exception("Invalid path for rules. [" . $path. "]");
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
     * @return array RuleRepository
     */
    public function getRepositories() {
        return $this->repositories;
    }

}
