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


