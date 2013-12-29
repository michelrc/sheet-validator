<?php

/**
 * Description of PayrollTest
 *
 * @author mrcalvo
 */

namespace Payroll\Test;

use \Payroll\PayrollValidator;
use Payroll\Test\Log\DummyLogger;
use Ruler\Context;
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

        for ($i = 2; $i < 4; $i++) {
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 0);
        }

        for ($i = 4; $i < 6; $i++) {
            $this->assertEquals($sheet->getCellByColumnAndRow('A', $i)->getValue(), 1);
        }
    }

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidatorPluggableRules(
    $path_excel_file, $repositories, $logger) {

        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);

        $repositories = $prv->getRepositories();

        // really got the repositories?
        $this->assertNotEmpty($repositories);
        $this->assertCount(2, $repositories);

        // instance implement proper interface
        $r = New \ReflectionClass(get_class($repositories[0]));
        $interfaces = $r->getInterfaces();
        $this->assertNotEmpty($interfaces);

        $this->assertTrue(
                in_array('Payroll\Rules\RuleRepository', array_keys($interfaces)));

        $r = New \ReflectionClass(get_class($repositories[1]));
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

        $this->assertNotEmpty($context);
        $this->assertArrayHasKey($sheet_names[0], $context);

        $this->assertTrue(is_array($context));
        $this->assertTrue(is_array($context['TruthTable']));
        $this->assertArrayHasKey('B1', $context['TruthTable']);
        $this->assertArrayHasKey('B2values', $context['TruthTable']);

        $this->assertContains(1, $context['TruthTable']['B2values']);
        $this->assertNotContains(2, $context['TruthTable']['B2values']);

        // after calling getContext on validator repositories should have set up
        // the total count of row to process
        foreach( $prv->getRepositories() as $r){
            $this->assertEquals($r->getTotalCount(), 5);
            $this->assertFalse($r->getTotalCount() == 4);
        }
    }

    /**
     * @dataProvider tablePath
     */
    public function testPayrollValidatorRulesExecution(
    $path_excel_file, $repositories, $logger) {

        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);

        $context = new Context($prv->getContext());

        $repositories = $prv->getRepositories();

        $builder = new RuleBuilder();

        /*
         * Each repository represents a sheet on an excel document, execute all rules
         * for each of them.
         */
        foreach ($repositories as $k => $r) {
            $r->setBuilder($builder);
            $r->setLogger($logger);

            $this->assertTrue($r->getName() == "TruthTable" || $r->getName() == "TruthTableGenerated");
            $this->assertNotNull($r->getBuilder());
            $this->assertEquals($builder, $r->getBuilder());

            // testing rules individually
            $rules = $r->getRules();
            $this->assertCount(12, $rules);

            $this->assertEquals($context['TruthTable']['A1'], "P");
            $this->assertFalse($rules["A1=P"]->evaluate($context));

            $this->assertEquals(
                $context['TruthTable']['A2values'], array('0', '1'));
            $this->assertFalse($rules["A2valuesContain"]->evaluate($context));

            // test expected values
            $this->assertEquals(
                $context['TruthTable']['B4'], "d");
            $this->assertEquals(
                $context['TruthTableGenerated']['B4'], "d");

            $this->assertEquals(
                $context['TruthTable']['B4values'], array('0', '1'));
            $this->assertEquals(
                $context['TruthTableGenerated']['B4values'], array('0', '1'));

            // B4 is a letter, allowed values are 0|1, the next should fail
            $this->assertTrue($rules["B4valuesContain"]->evaluate($context));
            // we should get a message logged
            $rules["B4valuesContain"]->execute($context);

            $messages = $logger->getMessages();
            $this->assertEquals($messages[0],
                "Column value at TruthTable:B4 doesn't match expected value. Expecting: [0,1].");

            $this->assertEquals(
                $context['TruthTable']['B5'], "p");
            $this->assertEquals(
                $context['TruthTable']['B5values'], array('0', '1'));
            // B5 is a letter, allowed values are 0|1, the next should fail
            $this->assertTrue($rules["B5valuesContain"]->evaluate($context));
            // we should get a message logged
            $rules["B5valuesContain"]->execute($context);

            $messages = $logger->getMessages();
            $this->assertEquals($messages[1],
                "Column value at TruthTable:B5 doesn't match expected value. Expecting: [0,1].");

            // now lets check a more complex rule
            // if B2 = 0  assert  !( ( B2 + (B2 + 1) ) / 1 = 1 )
            $this->assertFalse($rules["complex"]->evaluate($context));

            // !(A2+A3+A4 == (A2*A3*A4) / 3)
            $this->assertTrue($rules["complex2"]->evaluate($context));
        }
    }

    /**
     * @dataProvider tablePath
     */
    public function testRulesEvaluator($path_excel_file, $repositories, $logger) {
        $prv = new PayrollValidator($path_excel_file, $repositories, $logger);
        $prv->rulesEvaluator();

        $messages = $prv->getLogger()->getMessages();
        // same two messages from above
        $this->assertCount(2, $messages);

    }

    public function tablePath() {
        return array(
            array('./tests/Payroll/repository/truth-table.xlsx',
                array(
                    './tests/Payroll/Test/Rules/TruthTable.php',
                    './tests/Payroll/Test/Rules/TruthTableGenerated.php'
                ),
                new DummyLogger()),
            array('./tests/Payroll/repository/truth-table.ods',
                array(
                    './tests/Payroll/Test/Rules/TruthTable.php',
                    './tests/Payroll/Test/Rules/TruthTableGenerated.php'
                ),
                new DummyLogger())
        );
    }

}

