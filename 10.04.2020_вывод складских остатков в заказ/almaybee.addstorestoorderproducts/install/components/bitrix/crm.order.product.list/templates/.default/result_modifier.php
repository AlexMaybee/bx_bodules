<?php
use \Bitrix\Main\Localization\Loc;



//Получаем склады
$bxStores = \Bitrix\Catalog\StoreTable::getList([
//    'filter' => ['PRODUCT_ID' => 27],
    'select' => ['ID','TITLE','ADDRESS'], // выбираем идентификатор п-ля, имя и фамилию
    'order' => ['ID' => 'DESC'], // сортируем по идентификатору пользователя
]);
while($result = $bxStores->fetch())
{
    $arResult['MY_CUSTOM_STORES'][$result['ID']] = ['TITLE' => $result['TITLE'],'ADDRESS' => $result['ADDRESS']];
}


//получаем ID товаров для фильтра
$productsIds = [];
foreach ($arResult['PRODUCTS'] as $product)
{
//    $productsIds[] = $product['PRODUCT_ID'];
    ($product['PRODUCT_ID'] == $product['OFFER_ID'])
        ? $productsIds[] = $product['PRODUCT_ID']
        : $productsIds[] = $product['OFFER_ID'];
}

//Получаем значения со складов и засовываем в массив каждого товара
if($productsIds)
{
    $productsAtStores = \Bitrix\Catalog\StoreProductTable::getList([
        'filter' => ['PRODUCT_ID' => $productsIds],
        'select' => ['*'], // выбираем идентификатор п-ля, имя и фамилию
        'order' => ['ID' => 'DESC'], // сортируем по идентификатору пользователя
    ]);
    while($prodArr = $productsAtStores->fetch())
    {
        foreach ($arResult['PRODUCTS'] as $num => $product)
        {
            if($product['OFFER_ID'] == $product['PRODUCT_ID'] && $product['PRODUCT_ID'] == $prodArr['PRODUCT_ID'])
            {
//                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'][] = $prodArr;
//                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'] .= $prodArr;

                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'] .= ($prodArr['AMOUNT'] > 0)
                    ? '<div class="crm-order-product-control-amount-desc">'.$arResult['MY_CUSTOM_STORES'][$prodArr['STORE_ID']]['ADDRESS'].' - '.$prodArr['AMOUNT'].' '.$product['MEASURE_TEXT'].';</div> '
                    : '';

//                if($prodArr['AMOUNT'] > 0)
//                    $arResult['PRODUCTS'][$num]['CUSTOM_STORES'] .=
//                        '<div class="crm-order-product-control-amount-desc">'.$arResult['MY_CUSTOM_STORES'][$prodArr['STORE_ID']]['ADDRESS'].' - '.$prodArr['AMOUNT'].' '.$product['MEASURE_TEXT'].';</div> ';
           }
            elseif($product['OFFER_ID'] != $product['PRODUCT_ID'] && $product['OFFER_ID'] == $prodArr['PRODUCT_ID'])
            {
//                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'][] = $prodArr;
                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'] .= ($prodArr['AMOUNT'] > 0)
                    ? '<div class="crm-order-product-control-amount-desc">'.$arResult['MY_CUSTOM_STORES'][$prodArr['STORE_ID']]['ADDRESS'].' - '.$prodArr['AMOUNT'].' '.$product['MEASURE_TEXT'].';</div> '
                    : '';
            }
        }
    }

    //зарезервированное кол-во товара (в админке)
    $prodsReserved = \Bitrix\Catalog\ProductTable::getList([
        'filter' => ['ID' => $productsIds],
        'select' => ['ID','QUANTITY_RESERVED'], // выбираем идентификатор п-ля, имя и фамилию
        'order' => ['ID' => 'DESC'], // сортируем по идентификатору пользователя
    ]);
    while($res = $prodsReserved->fetch())
    {
        foreach ($arResult['PRODUCTS'] as $num => $product)
        {
            if($product['OFFER_ID'] == $product['PRODUCT_ID'] && $product['PRODUCT_ID'] == $res['ID'])
            {
                $arResult['PRODUCTS'][$num]['CUSTOM_QUANTITY_RESERVED'] = ($res['QUANTITY_RESERVED'] > 0)
                    ? '<div class="crm-order-product-control-amount-desc">'.Loc::getMessage('CRM_ORDER_CUSTOM_QUANTITY_RESERVED').': '.$res['QUANTITY_RESERVED'].' '.$product['MEASURE_TEXT'].'</div>'
                    : '';
            }
            elseif($product['OFFER_ID'] != $product['PRODUCT_ID'] && $product['OFFER_ID'] == $res['ID'])
            {
                $arResult['PRODUCTS'][$num]['CUSTOM_QUANTITY_RESERVED'] = ($res['QUANTITY_RESERVED'] > 0)
                    ? '<div class="crm-order-product-control-amount-desc">'.Loc::getMessage('CRM_ORDER_CUSTOM_QUANTITY_RESERVED').': '.$res['QUANTITY_RESERVED'].' '.$product['MEASURE_TEXT'].'</div>'
                    : '';
            }
        }
    }

}
