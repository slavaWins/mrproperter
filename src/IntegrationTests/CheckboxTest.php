<?php


use App\Models\ResponseApi;
use MrProperter\Library\PropertyConfigStructure;
use MrProperter\Models\MPModel;
use Tests\TestCase;

class CheckboxTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }


    public function test_EmptyStringCan()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->Checkbox("companyTestName")
            ->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault(false)
            ->AddTag(['admin', 'companyTestName']);

        MPModel::saving(function () {
            return false;
        });

        $model->_propertyConfigStructure = $config;

        $req = [
            'companyTestName' => "on"
        ];
        $validator = $model->GetValidatorRequestInModel($req, "admin");

        $errors = $validator->errors();
        $this->assertEquals(0, $errors->count());

        $data = $validator->validated();
        \MrProperter\Library\MrpValidateCommon::PropertyFillebleByTag($model, $data, "admin");
        $this->assertEquals(true, $model->companyTestName);





        $this->assertEquals(true, $data['companyTestName']);




        $req = [

        ];
        $validator = $model->GetValidatorRequestInModel($req, "admin");
        $this->assertEquals(0, $validator->errors()->count());

        \MrProperter\Library\MrpValidateCommon::PropertyFillebleByTag($model, $req, "admin");
        $this->assertEquals(false, $model->companyTestName);
        $this->assertEquals(0, $model->companyTestName);


    }


}
