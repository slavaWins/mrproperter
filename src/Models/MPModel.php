<?php

namespace MrProperter\Models;

use MrProperter\Library;
use App\Library\MrProperter\MigrationRender;
use App\Library\MrProperter\PropertyBuilderStructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use SlavaWins\Formbuilder\Library\FElement;


class MPModel extends Model
{


    public static function GetValidateRules($tag = null)
    {
        /** @var MPModel $cl */
        $cln = get_called_class();
        $cl = new $cln();
        $props = $cl->GetByTag($tag);

        $rules = [];

        /**
         * @var  $K
         * @var PropertyBuilderStructure $prop
         */
        foreach ($props as $K => $prop) {
            $text = Library\MigrationRender::GetType( $prop->typeData);
            if ($prop->max) $text .= "|max:" . $prop->max;
            if ($prop->min) $text .= "|min:" . $prop->min;
            $rules[$K] = $text;
        }

        return $rules;
    }

    /**
     * @param $tag
     * @return PropertyBuilderStructure[]
     */
    public function GetByTag($tag = null)
    {

        if(!$tag)return $this->GetPropertys();
        return collect($this->GetPropertys())->filter(function (Library\PropertyBuilderStructure $e) use ($tag) {
            if ($tag == null) return true;
            if (isset($e->tags[$tag])) return true;
            return false;
        });
    }

    public function BuildInputAll($tag = null)
    {
        $p = $this->GetPropertys();

        $html = "";
        foreach ($this->toArray() as $K => $V) {
            if (!isset($p[$K])) continue;

            if ($tag) {
                if (!$p[$K]->tags) continue;
                if (!isset($p[$K]->tags[$tag])) continue;
            }
            $html .= $this->BuildInput($K);
        }
    }

    public function BuildInput($ind)
    {
        $p = $this->GetPropertys();
        if (!isset($p[$ind])) return null;
        $prop = $p[$ind];

        $inp = FElement::NewInputText();

        if ($prop->typeData <> "checkbox") {

        }

        if ($prop->typeData == "checkbox") {
            $inp = FElement::NewInputText()->SetView()->InputBoolRow();
        } elseif ($prop->typeData == "text" or $prop->typeData == "int") {
            $inp = FElement::NewInputText();
        } elseif ($prop->typeData == "select" ) {
            $inp = FElement::NewInputText()->SetView()->InputSelect()->AddOptionFromArray($prop->options);
        }

        $inp =$inp
            ->SetLabel($prop->name)
            ->SetPlaceholder($prop->descr ?? null)
            ->SetName($ind)
            ->SetDescr($prop->descr ?? null); //->FrontendValidate()->String(0, 75)

        if ($prop->typeData == "string") {
            $inp->FrontendValidate()->String($prop->min, $prop->max ?? 999999);
        }

        $html = $inp->SetValue(old($ind, $this->$ind ?? ""))
            ->RenderHtml(true);

    }

    /**
     * @return PropertyBuilderStructure[]
     */
    public function GetPropertys()
    {
        return [
            'name' => PropertyBuilderStructure::New("Название")
                ->SetDescr("Описание поля")->SetIcon("🌟"),
            'className' => PropertyBuilderStructure::New("Название класса")->SetDescr("Описание поля")->SetIcon("🌟"),
        ];
    }

}
