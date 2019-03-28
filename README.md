# cropper2
Cropper image for laravel-admin 

### Install
``composer require nim4n/cropper2``

``php artisan vendor:publish --tag=laravel-admin-cropper2``

### Usage
> Basic
```
$form->cropper2("content","Label");
```
> Option width & height
```
$form->cropper2("content","Label")->cRatio($width,$height);
```

### Original repo 
> https://github.com/laravel-admin-extensions/cropper

### License
MIT License
