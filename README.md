<p align="center">
<img src="info/logo.jpg">
</p>
 
## MrProperter
Кароч изи пакет 
   

## Установка из composer

```  
composer require slavawins/mrproperter
```

 Опубликовать js файлы, вью и миграции необходимые для работы пакета.
Вызывать команду:
```
php artisan vendor:publish --provider="MrProperter\Providers\MrProperterServiceProvider"
``` 

 В роутере routes/web.php удалить:
 добавить
 ```
    \MrProperter\Library\MrProperterRoute::routes();
 ```

Выполнить миграцию
 ```
    php artisan migrate 
 ``` 
