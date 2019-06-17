<?php

namespace Encore\Cropper2;

use Encore\Admin\Admin;
use Encore\Admin\Form\Field\File;
use Encore\Admin\Form\Field\ImageField;

class Crop extends File
{
    use ImageField;

    private $ratioW = 200;

    private $ratioH = 230;

    protected $view = 'laravel-admin-cropper2::cropper';

    protected static $css = [
        '/vendor/laravel-admin-ext/cropper/cropper.min.css',
    ];

    protected static $js = [
        '/vendor/laravel-admin-ext/cropper/cropper.min.js',
        '/vendor/laravel-admin-ext/cropper/layer/layer.js',
    ];

    /**
     * [将Base64图片转换为本地图片并保存].
     *
     * @E-mial wuliqiang_aa@163.com
     * @TIME   2017-04-07
     * @WEB    http://blog.iinu.com.cn
     *
     * @param [Base64] $base64_image_content [要保存的Base64]
     * @param [目录] $path                 [要保存的路径]
     */
    private function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = $path.'/'.date('Ymd', time()).'/';
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0755, true);
            }
            $new_file = $new_file.md5(microtime()).".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return $new_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function prepare($base64)
    {
        if (preg_match('/data:image\/.*?;base64/is', $base64)) {
            $imagePath = $this->base64_image_content($base64, public_path('uploads/base64img'));
            if ($imagePath !== false) {
                @unlink(public_path('uploads/').$this->original);

                preg_match('/base64img\/.*/is', $imagePath, $matches);

                $this->callInterventionMethods($imagePath);

                return $matches[0];
            } else {
                return 'lost';
            }
        } else {
            preg_match('/base64img\/.*/is', $base64, $matches);

            return isset($matches[0]) ? $matches[0] : $base64;
        }
    }

    public function cRatio($width, $height)
    {
        if (!empty($width) and is_numeric($width)) {
            $this->attributes['data-w'] = $width;
        } else {
            $this->attributes['data-w'] = $this->ratioW;
        }
        if (!empty($height) and is_numeric($height)) {
            $this->attributes['data-h'] = $height;
        } else {
            $this->attributes['data-h'] = $this->ratioH;
        }

        return $this;
    }

    public function render()
    {
        $this->name = $this->formatName($this->column);
        $this->script = <<<EOT

var cropperMIME = '';

function getMIME(base64)
{
    var preg = new RegExp('data:(.*);base64','i');
    var result = preg.exec(base64);
    return result[1];
}

function cropper(imgSrc,id,w,h)
{
    
    var cropperImg = '<div id="cropping-div"><img id="cropping-img" src="'+imgSrc+'"><\/div>';
    
    
    //生成弹层模块
    layer.open({
        type: 1,
        skin: 'layui-layer-demo', //样式类名
        area: ['800px', '600px'],
        closeBtn: 2, 
        anim: 2,
        resize: false,
        shadeClose: false, 
        title: 'Cropper picture',
        content: cropperImg,
        btn: ['Crop'],
        btn1: function(){
            var cas = cropper.getCroppedCanvas({
                width: w,
                height: h
            });
            var base64url = cas.toDataURL(cropperMIME);
            $('#'+id+'-img').attr('src',base64url);
            $('#'+id+'-input').val(base64url);
            cropper.destroy();
            layer.closeAll('page');
        }
    });

    var image = document.getElementById('cropping-img');
    var cropper = new Cropper(image, {
        aspectRatio: w / h,
        viewMode: 2,
    });
}

$('.cropper-btn').click(function(){
    var id = $(this).attr('data-id');
    $('#'+id+'-file').click();
});

$('.cropper-file').change(function(){
    var id = $(this).attr('data-id');
    var w = $(this).attr('data-w');
    var h = $(this).attr('data-h');
    
    var file = $(this)[0].files[0];
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function(e){
        $('#'+id+'-img').attr('src',e.target.result);
        cropperMIME = getMIME(e.target.result);
        cropper(e.target.result,id,w,h);
        $('#'+id+'-input').val(e.target.result);
    };
});

$('.cropper-img').click(function(){
    var id = $(this).attr('data-id');
    var w = $(this).attr('data-w');
    var h = $(this).attr('data-h');
    cropper($(this).attr('src'),id,w,h);
});

EOT;

        if (!$this->display) {
            return '';
        }

        Admin::script($this->script);

        return view($this->getView(), $this->variables());
    }
}
