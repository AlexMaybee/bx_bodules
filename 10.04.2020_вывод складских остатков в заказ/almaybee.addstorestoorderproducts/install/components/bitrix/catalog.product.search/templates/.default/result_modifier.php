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
if(isset($arResult['PRODUCTS']) && $arResult['PRODUCTS'])
{
//    $productsIds = array_keys($arResult['PRODUCTS']);

    foreach ($arResult['PRODUCTS'] as $product)
    {
        //для торг. предложений
        if(isset($product['SKU_ITEMS']['SKU_ELEMENTS_ID']))
        {
            foreach ($product['SKU_ITEMS']['SKU_ELEMENTS_ID'] as $productPredlID)
            {
                $productsIds[] = $productPredlID;
            }
        }
        else{
            if($product['PRODUCT']['ID'])
                $productsIds[] = $product['PRODUCT']['ID'];
        }
    }

}

//$arResult['TEST'] = $productsIds;


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
        if($prodArr['AMOUNT'] != 0)
        {
            foreach ($arResult['PRODUCTS'] as $id => $productArr)
            {
                //Для торговых предложений
                if(isset($productArr['SKU_ITEMS']['SKU_ELEMENTS']) && $productArr['SKU_ITEMS']['SKU_ELEMENTS'])
                {
                    foreach ($productArr['SKU_ITEMS']['SKU_ELEMENTS'] as $num => $productPredlArr)
                    {
                        if($productPredlArr['ID'] == $prodArr['PRODUCT_ID'])
                        {
                            $arResult['PRODUCTS'][$id]['SKU_ITEMS']['SKU_ELEMENTS'][$num]['CUSTOM_STORE'] .=
                                $arResult['MY_CUSTOM_STORES'][$prodArr['STORE_ID']]['ADDRESS'].': '.$prodArr['AMOUNT'].' '.$productPredlArr['MEASURE']['SYMBOL']."; ";
                        }
                    }
                }
                elseif($productArr['PRODUCT']['ID'] == $prodArr['PRODUCT_ID'])
                {
                    $arResult['PRODUCTS'][$id]['CUSTOM_STORE'] =
                        $arResult['MY_CUSTOM_STORES'][$prodArr['STORE_ID']]['ADDRESS'].': '.$prodArr['AMOUNT'].' '.$productArr['PRODUCT']['MEASURE']['SYMBOL']."; ";
                }
            }
        }
    }

    $prodsReserved = \Bitrix\Catalog\ProductTable::getList([
        'filter' => ['ID' => $productsIds],
        'select' => ['ID','QUANTITY_RESERVED'], // выбираем идентификатор п-ля, имя и фамилию
        'order' => ['ID' => 'DESC'], // сортируем по идентификатору пользователя
    ]);
    while($res = $prodsReserved->fetch())
    {
        if($res['QUANTITY_RESERVED'] > 0)
        {
            foreach ($arResult['PRODUCTS'] as $id => $productArr)
            {
                //Для торговых предложений
                if(isset($productArr['SKU_ITEMS']['SKU_ELEMENTS']))
                {
                    foreach ($productArr['SKU_ITEMS']['SKU_ELEMENTS'] as $num1 => $productPredlArr1)
                    {
                        if($productPredlArr1['ID'] == $res['ID'])
                            $arResult['PRODUCTS'][$id]['SKU_ITEMS']['SKU_ELEMENTS'][$num1]['CUSTOM_QUANTITY_RESERVED'] = $res['QUANTITY_RESERVED'];
                    }
                }
                elseif($productArr['PRODUCT']['ID'] == $res['ID'])
                {
                    $arResult['PRODUCTS'][$id]['CUSTOM_QUANTITY_RESERVED'] = $res['QUANTITY_RESERVED'];
                }

                $arResult['TEST'][$productArr['PRODUCT']['ID']][] = $res;
            }
        }
    }

}



//Добавление в массив фильтров своего столбца
$arResult['HEADERS'][] = [
    'id' => 'CUSTOM_STORE',
    'content' => 'Товар на складах:',
    'default' => 1,
];
$arResult['HEADERS'][] = [
    'id' => 'CUSTOM_QUANTITY_RESERVED',
    'content' => 'Зарезервировано',
    'sort' => 'CUSTOM_QUANTITY_RESERVED', //для возможности сортировки
    'default' => 1,
    'align' => 'center'
];