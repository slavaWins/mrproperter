<?php


use MrProperter\Library\PropertyConfigStructure;
use Tests\TestCase;

class FloatReplaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }


    public function test_ValidateFloatAutoReplaceDot()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->Float("companyTestName")->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault("")
            ->SetPlaceholder("XX")
            ->SetLabelsWithTag("companyTestName", "Wath client company name", "OtherPlaceholder", "Other Descr")
            ->SetMin(2)->SetMax(4)
            ->AddTag(['admin', 'companyTestName']);

        $model->_propertyConfigStructure = $config;

        \MrProperter\Models\MPModel::saving(function () {
            return false;
        });

        $cases = [
            [
                'value' => "2.666",
                "isSuccess" => true,
                "resultValue" => 2.666
            ],
            [
                'value' => "2.666757574",
                "isSuccess" => true,
                "resultValue" => 2.666757574
            ],
            [
                'value' => "2,777",
                "isSuccess" => true,
                "resultValue" => 2.777
            ],
            [
                'value' => "cxx",
                "isSuccess" => false
            ],
            [
                'value' => 4,
                "isSuccess" => true
            ],
            [
                'value' => 6,
                "isSuccess" => false
            ],
            [
                'value' => 1,
                "isSuccess" => false
            ]
        ];

        foreach ($cases as $case) {
            $req = [
                'companyTestName' => $case['value']
            ];


            $validator = $model->GetValidatorRequestInModel($req, "admin");

            $errors = $validator->errors();


            if ($case['isSuccess']) {
                $this->assertEquals(0, $errors->count(), "Expected success reuslt, but of value " . $case["value"] . ' error = ' . $errors->first());
            } else {
                $this->assertNotEmpty($errors, "Expected error reuslt, but of value " . $case["value"] . ' not have error');
            }



            if ($case['isSuccess'] && isset($case['resultValue'])) {
                $model->ValidateAndFilibleByRequest($req, "admin");
                $this->assertEquals($case['resultValue'], $model->companyTestName, "Expected eqals value");
            }

        }

    }
}
