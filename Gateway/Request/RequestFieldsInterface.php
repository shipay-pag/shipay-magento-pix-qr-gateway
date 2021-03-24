<?php

declare(strict_types=1);

namespace Shipay\PixQrGateway\Gateway\Request;

interface RequestFieldsInterface
{
    const ORDER_REF = 'order_ref';
    const WALLET = 'wallet';
    const TOTAL = 'total';
    const ITEMS = 'items';
    const ITEM_TITLE = 'item_title';
    const UNIT_PRICE = 'unit_price';
    const QUANTITY = 'quantity';
    const BUYER = 'buyer';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const CPF = 'cpf';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const CALLBACK_URL = 'callback_url';
}
