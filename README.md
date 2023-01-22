<p align="center">
<img src="info/logo.jpg">
</p>
 
## MrProperter
Кароч изи пакет 
   

## Установка из composer

```  
composer require slavawins/mrproperter
```

 Опубликовать комндой Контроллер с примером использования:
```
php artisan vendor:publish --provider="MrProperter\Providers\MrProperterServiceProvider"
``` 

 В роутере routes/web.php  добавить
 ```
    Route::get('/mr-properter', [\App\Http\Controllers\MrProperter\MrProperterController::class, 'index'])->name('mrproperter.index');
    Route::post('/mr-properter', [\App\Http\Controllers\MrProperter\MrProperterController::class, 'store'])->name('mrproperter.store');
 ```

Выполнить миграцию
 ```
    php artisan migrate 
 ``` 
