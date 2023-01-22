<?php

namespace App\Models;


use MrProperter\Models\MPModel;
use MrProperter\Library\PropertyBuilderStructure;

class TemplateModel extends MPModel
{

    public function GetPropertys()
    {
        return [
            'selector_character_enabled' => PropertyBuilderStructure::Checkbox("Селектор чарактера")
                ->SetDescr("Вывести все чары определенного типа, и сохр в переменные комнаты"),

            'selector_character' => PropertyBuilderStructure::Select("Селектор чаров")->SetOptions(['Garage', 'Room', 'Enemy'])
                ->SetDescr("Выводим чаректер селектор на этом шаге, и сейвет результат в id"),

            'selector_character_filter' => PropertyBuilderStructure::Select("Фильтр чаров")->SetOptions(['Принадлежит игроку', 'Переменная 1', 'Перменная 2'])
                ->SetDescr("Фильтр для выбора чара"),

            'selector_character_to_varible' => PropertyBuilderStructure::Select("Сохр в")->SetOptions(['Никуда', 'В перемен1', 'Перменная 2'])
                ->SetDescr("После выбора чара куда сохранить выбранное?"),

            'render_character_enabled' => PropertyBuilderStructure::Checkbox("Рендерить переменную")->SetDefault(true)
                ->SetDescr("Отрендерить чарактера сохраненного в комнате?"),

            'render_character' => PropertyBuilderStructure::Select("Рендерить переменную")->SetOptions(['Не рендерить', 'Garage', 'Player'])
                ->SetDescr("Этот чарактер будет отрендерен"),

            'user_id' => PropertyBuilderStructure::Int("Пользователь")
                ->SetMax(10)
                ->SetMin(1)->Comment("Поле пользователя")
                ->SetDescr("Описание поля"),

            'message' => PropertyBuilderStructure::String("описание")
                ->SetMin(3)->SetMax(6)->Comment("Поле с описание типа")
                ->SetDescr("Описание поля")->AddTag('test'),
        ];
    }

}
