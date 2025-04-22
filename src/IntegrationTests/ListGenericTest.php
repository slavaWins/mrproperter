<?php


use MrProperter\Library\PropertyConfigStructure;
use Tests\TestCase;

class ListGenericTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }


    public function test_EmptyStringCan()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->Json("companyTestName")->SetLabel("Your company")
            ->ListClassGeneric(\MrProperter\IntegrationTests\ExampleGenericListContract::class)
            ->SetDescr("Ex x descr")->SetDefault([])
            ->AddTag(['admin', 'companyTestName']);
        $model->_propertyConfigStructure = $config;


        \MrProperter\Models\MPModel::saving(function () {
            return false;
        });


        //Положительный результат
        $req = [
            'companyTestName__amountMax' => [
                1, 2
            ],
            'companyTestName__percentAgent' => [
                1, 2
            ],
        ];
        $this->assertEquals(0, $model->GetValidatorRequestInModel($req, "admin")->errors()->count());


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                1, 2
            ],
            'companyTestName__percentAgent' => [
                120, 2
            ],
        ];

        $this->assertStringContainsString("должно быть в пределах",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                "x", 2
            ],
            'companyTestName__percentAgent' => [
                1, 2
            ],
        ];

        $this->assertStringContainsString("должно быть целое число",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                2.3, 2
            ],
            'companyTestName__percentAgent' => [
                1, 2
            ],
        ];

        $this->assertStringContainsString("должно быть целое число",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                23, 2
            ],
            'companyTestName__percentAgent' => [
                "1x", 2
            ],
        ];
        $this->assertStringContainsString("должно быть число",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                23, 2
            ],
            'companyTestName__percentAgent' => [
                "1.1,3", 2
            ],
        ];
        $this->assertStringContainsString("должно быть корректное",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                "100 - 155", 2
            ],
            'companyTestName__percentAgent' => [
                "1.1", 2
            ],
        ];
        $this->assertStringContainsString("должно быть целое число",
            $model->GetValidatorRequestInModel($req, "admin")->errors()->first() ?? null
        );


        //Строка
        $req = [
            'companyTestName__amountMax' => [
                23, 2
            ],
            'companyTestName__percentAgent' => [
                "2,666", 2
            ],
        ];
        $this->assertEquals(0, $model->GetValidatorRequestInModel($req, "admin")->errors()->count());
        $model->ValidateAndFilibleByRequest($req, "admin");

        $this->assertEquals(2.666, $model->companyTestName[0]['percentAgent']);

    }
}
