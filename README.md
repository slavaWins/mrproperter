<p align="center">
<img src="info/logo.jpg">
</p>
 
## MrProperter
Кароч пакет позволяет заполнять заполнять свойства модели всего один раз
, и с помощью команд в консоль получать актуальную миграцию, сразу же с готовым заполнением. И с учетом
прошлых миграций(столбцов в бд). Так же пакет добавляет новую модель MPModel. От которой нужно наследоваться.

   

## Установка из composer

```  
composer require slavawins/mrproperter
```

## Если вам нужен пример использования

 Опубликовать набор примеров комндой:
```
php artisan vendor:publish --provider="MrProperter\Providers\MrProperterServiceProvider"
``` 

 В роутере routes/web.php  добавить роуты с примерами
 ```    
    Route::get('/mr-properter', [\App\Http\Controllers\MrProperter\MrProperterController::class, 'index'])->name('mrproperter.index');
    Route::post('/mr-properter', [\App\Http\Controllers\MrProperter\MrProperterController::class, 'store'])->name('mrproperter.store');
 ```

У вас появится модель ExampleMPModel. Откройте её, и посмотрите её конфиг с полями

Затем выполните генерацию пхп док
 ```
    php artisan mrp:doc ExampleMPModel
 ```
После этого появится пхп док описание полей


Затем выполните миграцию модели
 ```
    php artisan mrp:migration ExampleMPModel
 ``` 
После этого появится сразу заполнения миграция,со всеми полями и комментариями


Затем добавьте ещё какое-то поле в конфиг. И выполните миграцию и генерацию пхп док
 ```
    php artisan mrp:migration ExampleMPModel
    php artisan mrp:doc ExampleMPModel
 ``` 
После этого появится миграция на модификацию таблицы, и в ней будут только новые поля. Так же и с пхп док.

Так же по ссылке /mr-properter
Можно найти вывод полей. И форму модели.
Там будет отправлятся пост запрос на другой роут, в котором пример валидации модели.

Теперь создайте свою собственную модель
 ```
    php artisan mrp:model Player
 ```

Так же в комплекте есть мега штука для формы ajax. Но скорее всего она в другой пакет перейдет. 
 ```
    <script src="{{ asset('js/mrpform.js') }}" defer></script>
 ``` 


## Документация

Свойства модели описываются **один раз** в `PropertiesSetting()`. Из этого описания получаются миграция, phpdoc, правила валидации, HTML-формы и заполнение модели из запроса.

### Модель
```php
class Player extends MPModel
{
    public function PropertiesSetting()
    {
        $config = new PropertyConfigStructure($this);
        $config->String('name')->SetLabel('Имя')->SetMin(2)->SetMax(40)->AddTag(['profile']);
        $config->Int('level')->SetLabel('Уровень')->SetDefault(1)->NonEditable();
        $config->Select('class')->SetLabel('Класс')->SetOptions(['war'=>'Воин','mag'=>'Маг'])->AddTag(['profile']);
        $config->Checkbox('is_pro')->SetLabel('PRO')->AddTag(['admin']);
        return $config;
    }
}
```
**Типы:** `String`, `Text`, `Int`, `Float`, `Checkbox`, `Select`, `Multioption`, `Date`, `Json`.
**Модификаторы:** `SetLabel`, `SetDescr`, `SetPlaceholder`, `Comment`, `SetDefault`, `SetRequired`, `CanEmpty`, `SetMin`, `SetMax`, `SetValidationRule` (заменяет авто-правило), `AddTag`, `SetOptions` / `SetDynamicOption(fn)`, `SetBelong($class, $method, $prop)`, `ListClassGeneric($class)` (+ `ListClassGenericAfterValidation(fn)`), `Hidden`, `NonEditable` (поле не выводится в форме и никогда не записывается из запроса), `SetPrefix`/`SetPostfix`, `FrontendMask`, `FrontendValidation_Money|Digital|InnAny|Custom`, `VisibleRuleIs($input, $value)`, `SetLabelsWithTag($tag, $label, $ph, $descr)`.

### Теги
Тег — это именованный набор полей, одна форма или вкладка. `GetByTag('profile')` возвращает только поля с этим тегом.
`ValidateAndFilibleByRequest` без тега бросает исключение. Не вешайте пользовательские теги на служебные поля (`is_admin`, `balance`…).

### Сохранение из запроса
```php
$result = $player->ValidateAndFilibleByRequest($request->all(), 'profile');
if ($result !== true) return ResponseApi::Error($result); // текст первой ошибки
```
Метод валидирует поля тега, чистит строки с HTML через HTMLPurifier (строки без `<`/`>` не трогаются), заполняет модель (кроме NonEditable) и делает `save()`.
Ещё есть: `GetValidatorRequestInModel($data, $tag)`, `GetFillDataFromValidator($validator, $data, $tag)` (данные для записи после валидации), `GetValidateRulesInModel($tag)`, `PropertyFillebleByTag($data, $tag)` (без валидации!).

### Вывод формы
```blade
{!! $player->BuildInputAll('profile', false) !!}   {{-- все поля тега --}}
{{ $player->BuildInput('name') }}                  {{-- одно поле (echo) --}}
<x-mrp-form route="{{ route('player.save') }}" btn="Сохранить">…</x-mrp-form>
```
Отрисовкой занимается `slavawins/formbuilder`. Ajax-отправку форм `.mprForm` делает `public/js/mrpform.js`.

### List-generic
`ListClassGeneric(MyRow::class)` — поле-таблица. Колонки описываются PHP-атрибутами на свойствах класса строки (`#[Name]`, `#[Description]`, `#[Type]`, `#[Lengh(min,max)]`, `#[Options([...])]`). Из запроса приходят массивы `field__column[]`.

### Консоль
```
php artisan mrp:model Player        # заготовка модели
php artisan mrp:migration Player    # миграция (create или только новые колонки)
php artisan mrp:doc Player          # @property phpdoc
php artisan mrp:all Player
```
Примеры: `vendor:publish --provider="MrProperter\Providers\MrProperterServiceProvider"` → `ExampleMPModel`, `MrProperterController`.
