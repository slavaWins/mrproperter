<?php

namespace MrProperter\IntegrationTests;


class ExampleGenericListContract
{

    #[Name('�����')]
    #[Description('����� �� ������� ��������� ��������')]
    #[Type('int')]
    #[Lengh(1,14000000)]
    public int $amountMax = 10;

    #[Name('�������� ������')]
    #[Description('�������� ������ � ��������� �� ������')]
    #[Type('float')]
    #[Lengh(0,100)]
    public int $percentAgent = 10;
}
