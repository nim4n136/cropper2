<style>
    .cropper-img{
        max-width: 350px;
        max-height: 350px;;
        border: 1px solid #ddd;
        box-shadow: 1px 1px 5px 0 #a2958a;
        padding: 6px;
        display: block;
        margin-top:.5em;
    }
    .cropper-btn{
        margin-bottom: 10px;
        margin-bottom: .5em;

    }
    .cropper-file{
        display: none !important;
    }
    #cropping-img{
        max-width: 100%;
    }
    #cropping-div{
        width: 98%;
        height: 98%;
        margin: 0 auto;
    }
</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div data-id="{{$id}}" class="btn btn-info cropper-btn">{{ __("Browse") }}</div>
        <input id="{{$id}}-file" {!! $attributes !!} data-id="{{$id}}" class="cropper-file" type="file" accept="image/*"/>
        @if($value)
        <img id="{{$id}}-img" {!! $attributes !!} data-id="{{$id}}" class="cropper-img" {!! empty($value) ? '' : 'src="'.old($column, $value).'"'  !!}>
        @endif
        <input id="{{$id}}-input" type="hidden" name="{{$name}}" value="{{ old($column, $value) }}" {!! $attributes !!} />
        @include('admin::form.help-block')
    </div>
</div>


