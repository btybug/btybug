@if(isset($model))
    {!! Form::model($model,['url' => '/save-form']) !!}
    @if(isset($model[$form->fields_type.'_id']))
        {!! Form::hidden($form->fields_type.'_id',$model[$form->fields_type.'_id']) !!}
    @endif
@else
    {!! Form::open(['url' => '/save-form']) !!}
@endif

@if(isset($edit) && $edit)
    {!! Form::hidden('edit',$edit) !!}
@endif

{!! Form::hidden('form_id',$form->id) !!}
{!! $form->render() !!}

{!! Form::close() !!}
