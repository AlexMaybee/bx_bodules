<?php

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
    $productsIds[] = $product['PRODUCT_ID'];
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
            if($product['PRODUCT_ID'] == $prodArr['PRODUCT_ID'])
            {
                $arResult['PRODUCTS'][$num]['CUSTOM_STORES'][] = $prodArr;
            }

        }
    }

}
