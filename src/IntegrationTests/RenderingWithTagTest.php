<?php


use MrProperter\Library\PropertyConfigStructure;
use MrProperter\Models\MPModel;
use Tests\TestCase;

class RenderingWithTagTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_GenerateFelementWithTag()
    {

        $model = new \MrProperter\Models\MPModel();
        $config = new PropertyConfigStructure($model);

        $prop = $config->String("companyTestName")->SetLabel("Your company")
            ->SetDescr("Ex x descr")->SetDefault("")
            ->SetPlaceholder("XX")
            ->SetLabelsWithTag("companyTestName", "Wath client company name", "OtherPlaceholder", "Other Descr")
            ->SetMin(2)->SetMax(45)->AddTag(['admin', 'companyTestName', 1]);

        $model->_propertyConfigStructure = $config;


        $felement = MPModel::BuildFElementByStruct("companyTestName", $prop, "", null)[0];
        $this->assertEquals("Your company", $felement->data->label);
        $this->assertEquals($prop->descr, $felement->data->descr);
        $this->assertEquals($prop->placeholder, $felement->data->placeholder);
        $this->assertEquals($prop->name, $felement->data->name);


        $felement = MPModel::BuildFElementByStruct("companyTestName", $prop, "", "admin")[0];
        $this->assertEquals("Your company", $felement->data->label);



        $felement = MPModel::BuildFElementByStruct("companyTestName", $prop, "", "companyTestName")[0];
        $this->assertEquals("Wath client company name", $felement->data->label);
        $this->assertEquals("OtherPlaceholder", $felement->data->placeholder);
        $this->assertEquals("Other Descr", $felement->data->descr);


    }


}
