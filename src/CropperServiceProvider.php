<?php

namespace Encore\Cropper2;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class CropperServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Cropper $extension)
    {
        if (!Cropper::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-cropper2');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/cropper2')],
                'laravel-admin-cropper2'
            );
        }

        Admin::booting(function () {
            Form::extend('cropper2', Crop::class);
        });
    }
}
