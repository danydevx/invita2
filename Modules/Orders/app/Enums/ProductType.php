<?php

namespace Modules\Orders\Enums;

enum ProductType: string
{
    case MENU_PRODUCT = 'menu_product';
    case BUSINESS_PRODUCT = 'business_product';

    public function label(): string
    {
        return match($this) {
            self::MENU_PRODUCT => 'Producto del menú',
            self::BUSINESS_PRODUCT => 'Producto',
        };
    }
}
