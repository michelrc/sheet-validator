<?php

/**
 * Description of PayrollTest
 *
 * @author mrcalvo
 */

namespace Payroll\Test\PayrollVaTest;


class PayrollValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidator($path_excel_file) {
        $rb = new \Payroll\PayrollValidator($path_excel_file);
        $excel_file = $rb->getExcelFile();
        
        $sheet = $excel_file->getSheet(0);
        
        for($i=2; $i<4;$i++){
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 0);
        }
        
        for($i=4; $i<6;$i++){
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 1);
        }
        
        
        
}

    public function tablePath() {
        return array(
            array('./tests/Payroll/repository/truth-table.xlsx'),
            array('./tests/Payroll/repository/truth-table.ods')
        );
    }

}
