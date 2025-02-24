<?php


use MrProperter\Library\PropertyConfigStructure;
use MrProperter\Models\MPModel;
use Tests\TestCase;

class CanEmptyStringDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }


    public function test_EmptyStringCan()
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

        $req = [
            'companyTestName' => "xx"
        ];
        $validator = $model->GetValidatorRequestInModel($req, "admin");

        $errors = $validator->errors();

        $this->assertEquals(0, $errors->count());




        $prop->SetMin(0);
        $prop->CanEmpty(true);


        $rules = MPModel::RenderValidateRuleByPropertyData($prop, true);
        $this->assertStringContainsString("nullable", $rules,"Not added can empty in validation rule ");



        $req = ['companyTestName' => ""];
        $validator = $model->GetValidatorRequestInModel($req, "admin");
        $errors = $validator->errors();
        $this->assertEquals(0, $errors->count(), "Valid error");



        $data = $validator->validated();
        $this->assertTrue(isset($data[ "companyTestName"]), "Deleted from validated array");




        $prop->CanEmpty(false);
        $prop->SetRequired(true);
        $req = ['companyTestName' => ""];
        $validator = $model->GetValidatorRequestInModel($req, "admin");
        $errors = $validator->errors();
        $this->assertEquals(1, $errors->count(), "Valid error #2");

    }



    public function test_EmptyStringFillable()
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




        $prop->CanEmpty(true);

        $req = ['companyTestName' => ""];
        $validator = $model->GetValidatorRequestInModel($req, "admin");
        $errors = $validator->errors();
        $this->assertEquals(0, $errors->count(), "Valid error");



        $model->companyTestName = "DEFDATA";
        $req = ['companyTestName' =>  null];
        \MrProperter\Library\MrpValidateCommon::PropertyFillebleByTag($model, $req, "admin");
        $this->assertEquals("", $model->companyTestName);


    }


}
