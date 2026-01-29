<?php
/**
 * getPageBlockContent v1.1
 * Вывод блоков PageBlocks с любой страницы (Free версия)
 * 
 * Параметры:
 * @param int    $resourceId   ID страницы-источника (обязательный)
 * @param string $blockIds     ID блоков через запятую (обязательный)
 * @param string $tpl          Чанк для рендера (опционально, по умолчанию из блока)
 * @param int    $limit        Лимит блоков (0 = все)
 * @param string $sortby       Сортировка (menuindex, id)
 * @param string $sortdir      Направление (ASC, DESC)
 * @param string $wrapper      Обёртка для всех блоков (@INLINE <div>{$output}</div>)
 * @param string $toPlaceholder Сохранить в placeholder вместо вывода
 * 
 * Примеры:
 * [[!getPageBlockContent? &resourceId=`10` &blockIds=`4,7,21`]]
 * {'!getPageBlockContent' | snippet : ['resourceId' => 10, 'blockIds' => '4,7']}
 */

// 1. Загрузка pdoTools для рендеринга чанков
if ($modx->services instanceof MODX\Revolution\Services\Container) {
    $pdotools = $modx->services->get('pdotools');
} else {
    $pdotools = $modx->getService('pdotools', 'pdoTools', 
        MODX_CORE_PATH . 'components/pdotools/model/', $scriptProperties);
}

if (!$pdotools) {
    $pdotools = $modx;
}

// 2. Загрузка модели PageBlocks
$corePath = $modx->getOption('pageblocks_core_path', null, 
    $modx->getOption('core_path') . 'components/pageblocks/');
$modx->addPackage('pageblocks', $corePath . 'model/');

// 3. Получение параметров
$resourceId = (int)$modx->getOption('resourceId', $scriptProperties, 0);
$blockIds = $modx->getOption('blockIds', $scriptProperties, '');
$tpl = $modx->getOption('tpl', $scriptProperties, '');
$limit = (int)$modx->getOption('limit', $scriptProperties, 0);
$sortby = $modx->getOption('sortby', $scriptProperties, 'menuindex');
$sortdir = strtoupper($modx->getOption('sortdir', $scriptProperties, 'ASC'));
$wrapper = $modx->getOption('wrapper', $scriptProperties, '');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');

// Проверка обязательных параметров
if (empty($resourceId) || empty($blockIds)) {
    return '';
}

// 4. Парсинг ID блоков
$ids = array_map('trim', explode(',', $blockIds));
$ids = array_filter($ids, 'is_numeric');

if (empty($ids)) {
    return '';
}

// 5. Формирование xPDO запроса
$c = $modx->newQuery('pbBlockValue');
$c->where([
    'model_id' => $resourceId,
    'id:IN' => $ids,
    'published' => 1,
    'deleted' => 0
]);

$c->sortby($sortby, $sortdir);

if ($limit > 0) {
    $c->limit($limit);
}

// 6. Получение блоков
$items = $modx->getIterator('pbBlockValue', $c);

$list = [];
$idx = 0;

foreach ($items as $item) {
    // Декодируем JSON значения и объединяем с полями объекта
    $values = array_merge(
        json_decode($item->get('values'), true) ?: [], 
        $item->toArray()
    );
    
    $values['resource_id'] = $item->get('model_id');
    $values['id'] = $item->get('id');
    $values['idx'] = $idx;
    $values['pls'] = $values;
    
    // Выбор чанка для рендеринга
    $chunkName = $tpl ?: $item->get('chunk');
    
    // Рендеринг блока
    $list[] = $pdotools->getChunk($chunkName, $values);
    $idx++;
}

// 7. Формирование вывода
$output = implode("\n", $list);

// Обёртка
if (!empty($wrapper)) {
    $output = str_replace('{$output}', $output, $wrapper);
}

// Вывод или placeholder
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
    return '';
}

return $output;
