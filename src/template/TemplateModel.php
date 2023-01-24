<?php

namespace App\Models;


use MrProperter\Library\PropertyConfigStructure;
use MrProperter\Models\MPModel;
use MrProperter\Library\PropertyBuilderStructure;

class TemplateModel extends MPModel
{

    public function PropertiesSetting()
    {
        $config = new PropertyConfigStructure($this);

        $config->String("name")->SetLabel("Название ")->SetDescr("")->SetMin(2)->SetMax(26)->SetDefault("null");
        $config->String("defval")->SetLabel("Дефалтное значение ")->SetDescr("")->SetMin(0)->SetMax(26);
        $config->Select("typeVal")->SetLabel("Чарактер")->SetOptions(['Int', 'String', 'Select']);
        $config->Int("min")->SetOptions(['Int', 'String', 'Select'])->SetLabel("Min");
        $config->Int("max")->SetOptions(['Int', 'String', 'Select'])->SetLabel("max");
        $config->Int("descr")->SetOptions(['Int', 'String', 'Select'])->SetLabel("descr")->SetDescr("Описание поля");

        $config->Checkbox("selector_character_enabled3")->SetLabel("Селектор чарактера")
            ->SetDescr("Вывести все чары определенного типа, и сохр в переменные комнаты");

        $config->Select("selector_character")->SetLabel("Чарактер")->SetOptions(['Garage', 'Room', 'Enemy'])
            ->SetDescr("Выводим чаректер селектор на этом шаге, и сейвет результат в id");

        $config->Select("selector_character_filter")->SetLabel("Фильтр чаров")->SetOptions(['Принадлежит игроку', 'Переменная 1', 'Перменная 2'])
            ->SetDescr("Фильтр для выбора чара");

        $config->Select("selector_character_to_varible")->SetLabel("Сохр в ")->SetOptions(['Никуда', 'В перемен1', 'Перменная 2'])
            ->SetDescr("После выбора чара куда сохранить выбранное?");

        $config->Checkbox("render_character_enabled")->SetLabel("Включить рендер?")->SetDefault(true)
            ->SetDescr("Отрендерить чарактера сохраненного в комнате?");

        $config->Select("render_character")->SetLabel("Рендерить переменную")->SetOptions(['Не рендерить', 'Garage', 'Player'])
            ->SetDescr("Этот чарактер будет отрендерен");

        $config->Int("render_character")->SetLabel("Пользователь")
            ->SetDescr("Этот чарактер будет отрендерен")->SetMax(10)
            ->SetMin(1)->Comment("Поле пользователя")
            ->SetDescr("Описание поля");

        $config->String("message")->SetLabel("Пользователь")
            ->SetDescr("Поле с описание типа")->SetMin(3)->SetMax(6)->Comment("Поле с описание типа")
            ->SetDescr("Описание поля")->AddTag('test');

        return $config->GetConfig();
    }

}
