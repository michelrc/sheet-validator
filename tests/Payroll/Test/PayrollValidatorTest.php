<?php

/**
 * Description of PayrollTest
 *
 * @author mrcalvo
 */

namespace Payroll\Test;

use \Payroll\PayrollValidator;
use Payroll\Test\Log\DummyLogger;
use \Ruler\RuleBuilder;


class PayrollValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidator(
        $path_excel_file, $repositories, $logger) {

        $rb = new PayrollValidator($path_excel_file, $repositories, $logger);
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
    public function testPayrollValidatorPluggableRules(
        $path_excel_file, $repositories, $logger){

        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);

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
     public function testGetContextVariables(
         $path_excel_file, $repositories, $logger) {
        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);

        $sheet_names = $prv->getExcelFile()->getSheetNames();
        $context = $prv->getContext();

        // really got the repositories?
        $this->assertNotEmpty($context);
        //$this->fail(print_r($context, true));
        foreach($sheet_names as $sheet_name) {
           $this->assertArrayHasKey($sheet_name,$context);
        }


    }

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidatorRulesExecution(
        $path_excel_file, $repositories, $logger)
    {

        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);

        $repositories = $prv->getRepositories();

        $builder = new RuleBuilder();

        /*
         * Each repository represents a sheet on an excel document, execute all rules
         * for each of them.
         */
        foreach ($repositories as $r) {
            $r->setBuilder($builder);

            $this->assertEquals($r->getName(), "TruthTable");
            $this->assertNotNull($r->getBuilder());
            $this->assertEquals($builder, $r->getBuilder());
        }

        //TODO lets evaluate some rules with a concrete context
    }

    public function tablePath()
    {
        return array(
            array('./tests/Payroll/repository/truth-table.xlsx',
                array('./tests/Payroll/Test/Rules/TruthTable.php'),
                new DummyLogger()),
            array('./tests/Payroll/repository/truth-table.ods',
                array('./tests/Payroll/Test/Rules/TruthTable.php'),
                new DummyLogger())
        );
    }

}
