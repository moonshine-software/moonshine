# PR: `ActionButton::toggleModal(..., asyncUrl)` для одной общей async-модалки

## Проблема

Сейчас в MoonShine удобно открывать модалки двумя способами:

- `inModal(...)` на самой кнопке
- `toggleModal('modal-name')` для отдельно стоящей модалки

Но для кейса "одна общая модалка + разный async-контент от разных кнопок" штатного решения нет.

Почему:

- `ActionButton::toggleModal('name')` только открывает/закрывает модалку
- отдельно стоящая `Modal::make(...)->asyncUrl(...)` использует **свой фиксированный `asyncUrl`**
- передать в модалку новый `asyncUrl` (или параметры для него) из кнопки нельзя

В результате приходится:

- либо создавать много `inModal(...)->async(...)` (по модалке на каждую кнопку)
- либо писать кастомный JS-хелпер для обновления `asyncUrl` у одной общей модалки

Это особенно неудобно для табличных/сеточных интерфейсов, где на странице много одинаковых кнопок-счётчиков.

## Что предлагает этот PR

Добавить поддержку передачи `asyncUrl` из `ActionButton::toggleModal(...)` в отдельно стоящую модалку.

Идея:

- кнопка формирует `asyncUrl` (строкой или через `Closure`)
- вызывает `MoonShine.ui.toggleModal(name, asyncUrl)`
- модалка при открытии получает этот URL из события и, если URL изменился:
  - обновляет `this.asyncUrl`
  - сбрасывает `this.asyncLoaded = false`
- затем загружает новый async-контент уже по URL из кнопки

## Что изменено

### 1. PHP API: `ActionButton::toggleModal(...)`

Расширена сигнатура:

```php
toggleModal(Closure|string $name = 'default', Closure|string|null $asyncUrl = null): static
```

Поддерживается:

- старый вызов (без второго аргумента)
- строковый `asyncUrl`
- `Closure`, возвращающий строку `asyncUrl`

### 2. JS API: `MoonShine.ui.toggleModal(...)`

`toggleModal(name, asyncUrl)` теперь пробрасывает `asyncUrl` через `CustomEvent.detail`.

### 3. `Modal.js`

Обработчик открытия модалки принимает событие и использует `event.detail` как `asyncUrl` (если это строка).

Если `asyncUrl` отличается от текущего:

- `this.asyncUrl = incomingAsyncUrl`
- `this.asyncLoaded = false`

Это позволяет переиспользовать одну и ту же async-модалку для разных кнопок.

### 4. Fix в шаблоне modal blade

В шаблоне модалки обработчик события теперь вызывается с `$event`, иначе `Modal.js` не получает `event.detail`.

## Обратная совместимость

Сохранена.

- `ActionButton::toggleModal('name')` работает как раньше
- `MoonShine.ui.toggleModal('name')` работает как раньше
- если `asyncUrl` не передан, поведение модалки не меняется

## Поведение (зафиксировано в этом PR)

- Если модалка уже открыта, повторный вызов `toggleModal(...)` ведёт себя как и раньше (toggle): модалка закрывается.
- Если `event.detail` передан, но не является строкой, он игнорируется (тихо, без ошибок).

## Пример использования

### До (много модалок через `inModal`)

```php
ActionButton::make((string) $count)
    ->async()
    ->inModal(
        title: 'Участники',
        content: fn () => '...',
    );
```

### После (одна общая модалка + динамический `asyncUrl`)

```php
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Components\ActionButton;

$modalName = 'competition-stat-modal';

Modal::make('Участники')
    ->name($modalName)
    ->asyncUrl(route('api.competition.tabs.main.statModal', [
        'competition' => $competition->id,
    ]))
    ->alwaysLoad();

ActionButton::make((string) $count)
    ->toggleModal($modalName, fn () => route('api.competition.tabs.main.statModal', [
        'competition' => $competition->id,
        'age_id' => $ageId,
        'rank_id' => $rankId,
        'vid_id' => $vidId,
        'count' => $count,
    ]));
```

## Реальный кейс, который решает PR

На странице статистики соревнования есть сетка кнопок (количество участников по группам).

Требование:

- одна модалка на странице
- кнопка в ячейке открывает модалку
- модалка асинхронно загружает список участников именно для этой ячейки (`age/rank/vid`)

До PR это требовало кастомного JS или множества `inModal`.
После PR это делается декларативно через `ActionButton::toggleModal(..., asyncUrl)`.
