<?php

/**
 * Description of PayrollTest
 *
 * @author mrcalvo
 */

namespace Payroll\Test;

use \Payroll\PayrollValidator;


class PayrollValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidator($path_excel_file, $repositories) {
        $rb = new \Payroll\PayrollValidator($path_excel_file, $repositories);
        $excel_file = $rb->getExcelFile();

        $sheet = $excel_file->getSheet(0);

        for($i=2; $i<4;$i++){
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 0);
        }

        for($i=4; $i<6;$i++){
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 1);
        }

    }

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidatorPluggableRules($path_excel_file, $repositories){

        $prv = new PayrollValidator($path_excel_file, $repositories);

        $repositories = $prv->getRepositories();

        // really got the repositories?
        $this->assertNotEmpty($repositories);
        $this->assertCount(1, $repositories);

        // instance implement proper interface
        $r = New \ReflectionClass(get_class($repositories[0]));
        $interfaces = $r->getInterfaces();
        $this->assertNotEmpty($interfaces);

        $this->assertTrue(
            in_array('Payroll\Rules\RuleRepository', array_keys($interfaces)));
    }
    
    /**
     * @dataProvider tablePath
     */
     public function testgetContextVariables($path_excel_file, $repositories) {
        $prv = new PayrollValidator($path_excel_file, $repositories);
        
        $sheet_names = $prv->getExcelFile()->getSheetNames();
        $context = $prv->getContext();

        // really got the repositories?
        $this->assertNotEmpty($context);
        //$this->fail(print_r($context, true));
        foreach($sheet_names as $sheet_name) {
           $this->assertArrayHasKey($sheet_name,$context);    
        }
        
    
    }

    public function tablePath() {
        return array(
            array('./tests/Payroll/repository/truth-table.xlsx',
                array('./tests/Payroll/Test/Rules/TruthTable.php')),
            array('./tests/Payroll/repository/truth-table.ods',
                array('./tests/Payroll/Test/Rules/TruthTable.php'))
        );
    }

}
