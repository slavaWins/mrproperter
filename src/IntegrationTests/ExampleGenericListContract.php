<?php

namespace MrProperter\IntegrationTests;


class ExampleGenericListContract
{

    #[Name('AmountMax')]
    #[Description('����� �� ������� ��������� ��������')]
    #[Type('int')]
    #[Lengh(1,14000000)]
    public int $amountMax = 10;

    #[Name('percentAgent')]
    #[Description('�������� ������ � ��������� �� ������')]
    #[Type('float')]
    #[Lengh(0,100)]
    public int $percentAgent = 10;
}
