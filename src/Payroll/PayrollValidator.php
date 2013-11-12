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

/**
 * Payroll Validator
 *
 * This class will act as a collector
 */
class PayrollValidator {

    private $path_excel_file;
    private $excel_file;

    public function __construct($path_excel_file) {
        $this->path_excel_file = $path_excel_file;
        try {
            $this->excel_file = \PHPExcel_IOFactory::load($this->path_excel_file);
        } catch (Exception $e) {
            die('Error loading file');
        }
    }

    /**
     * Return a 
     * @return \PHPExcel
     */
    public function getExcelFile() {
        return $this->excel_file;
    }

}
