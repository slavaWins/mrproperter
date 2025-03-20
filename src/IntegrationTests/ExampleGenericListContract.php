<?php

namespace MrProperter\IntegrationTests;


class ExampleGenericListContract
{

    #[Name('Сумма')]
    #[Description('Сумма до которой действует комиссия')]
    #[Type('int')]
    #[Lengh(1,14000000)]
    public int $amountMax = 10;

    #[Name('Комиссия Агента')]
    #[Description('Комиссия агента в процентах от сделки')]
    #[Type('float')]
    #[Lengh(0,100)]
    public int $percentAgent = 10;
}
