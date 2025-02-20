<?php


use MrProperter\Library\PropertyConfigStructure;
use Tests\TestCase;

class ValidationDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_ValidateString()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->String("companyTestName")->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault("")
            ->SetPlaceholder("XX")
            ->SetLabelsWithTag("companyTestName", "Wath client company name", "OtherPlaceholder", "Other Descr")
            ->SetMin(2)->SetMax(4)
            ->AddTag(['admin', 'companyTestName']);

        $model->_propertyConfigStructure = $config;


        $cases = [
            [
                'value' => "cxx",
                "isSuccess" => true
            ],
            [
                'value' => "c",
                "isSuccess" => false
            ],
            [
                'value' => "cxxx",
                "isSuccess" => true
            ],
            [
                'value' => "cxxxxzvxz",
                "isSuccess" => false
            ],
            [
                'value' => null,
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
                $this->assertEquals(0, $errors->count(), "Expected success reuslt, but of value " . $case["value"] . ' error = ' . json_encode($errors));
            } else {
                $this->assertNotEmpty($errors, "Expected error reuslt, but of value " . $case["value"] . ' not have error');
            }

        }

    }


    public function test_ValidateInt()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->Int("companyTestName")->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault("")
            ->SetPlaceholder("XX")
            ->SetLabelsWithTag("companyTestName", "Wath client company name", "OtherPlaceholder", "Other Descr")
            ->SetMin(2)->SetMax(4)
            ->AddTag(['admin', 'companyTestName']);

        $model->_propertyConfigStructure = $config;


        $cases = [
            [
                'value' => "cxx",
                "isSuccess" => false
            ],
            [
                'value' => "c",
                "isSuccess" => false
            ],
            [
                'value' => "cxxx",
                "isSuccess" => false
            ],
            [
                'value' => "cxxxxzvxz",
                "isSuccess" => false
            ],
            [
                'value' => null,
                "isSuccess" => false
            ],
            [
                'value' => "2",
                "isSuccess" => true
            ],
            [
                'value' => 2,
                "isSuccess" => true
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
                $this->assertEquals(0, $errors->count(), "Expected success reuslt, but of value " . $case["value"] . ' error = ' . json_encode($errors));
            } else {
                $this->assertNotEmpty($errors, "Expected error reuslt, but of value " . $case["value"] . ' not have error');
            }

        }

    }


}
