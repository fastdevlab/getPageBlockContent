# getPageBlockContent

Сниппет для вывода блоков PageBlocks (Free версия) с других страниц в MODX.  
The getPageBlockContent snippet for displaying PageBlocks (Free version) from other pages in MODX.

## Параметры

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|--------------|----------|
| `resourceId` | int | — | **Обязательный**. ID страницы-источника блоков |
| `blockIds` | string | — | **Обязательный**. ID блоков через запятую: `4,7,21` |
| `tpl` | string | — | Чанк для всех блоков (переопределяет дефолтный) |
| `limit` | int | 0 | Лимит блоков (`0` = все) |
| `sortby` | string | `menuindex` | Поле сортировки (`menuindex`, `id`) |
| `sortdir` | string | `ASC` | Направление (`ASC`, `DESC`) |
| `wrapper` | string | — | Обёртка: `@INLINE <div class="blocks">{$output}</div>` |
| `toPlaceholder` | string | — | Сохранить в placeholder вместо вывода |

## Примеры использования

### Прямой вызов @FILE:

```fenom
{'@FILE snippets/getPageBlockContent.php' | snippet : [
    'resourceId' => 10,
    'blockIds' => '4,7,21'
]}
```

Через `runSnippet`:
```php
{$_modx->runSnippet('@FILE snippets/getPageBlockContent.php', [
    'resourceId' => 10,
    'blockIds' => '4,7,21'
])}
```

### Примеры вызова обычного (не файлового) сниппета:

#### 1. Базовый вывод (Fenom):

```fenom
{* Вывести блоки 4, 7, 21 со страницы 10 *}
{'!getPageBlockContent' | snippet : [
    'resourceId' => 10,
    'blockIds' => '4,7,21'
]}
```

#### 2. С кастомным чанком:

```modx
[[!getPageBlockContent? 
    &resourceId=`15` 
    &blockIds=`1,2,3,4,5`
    &tpl=`reviews_card`
]]
```

#### 3. С лимитом и сортировкой:

```fenom
{* Последние 3 блока (сортировка DESC) *}
{'!getPageBlockContent' | snippet : [
    'resourceId' => 20,
    'blockIds' => '10,11,12,13,14',
    'limit' => 3,
    'sortdir' => 'DESC'
]}
```

#### 4. С обёрткой:

```modx
[[!getPageBlockContent?
    &resourceId=`25`
    &blockIds=`5,6,7`
    &wrapper=`@INLINE <div class="carousel-inner">{$output}</div>`
]]
```

#### 5. В placeholder:

```fenom
{* Сохранить в переменную для использования позже *}
{'!getPageBlockContent' | snippet : [
    'resourceId' => 30,
    'blockIds' => '8,9',
    'toPlaceholder' => 'testimonials_html'
]}

{* Использовать позже *}
<section class="testimonials">
    {$testimonials_html}
</section>
```

## Документация

Более подробное описание и примеры использования:  
A more detailed description and usage examples are here:  
[https://modx.pro/solutions/25456](https://modx.pro/solutions/25456)

Просто скопируйте этот текст и вставьте в файл `README.md` в вашем репозитории GitHub.
