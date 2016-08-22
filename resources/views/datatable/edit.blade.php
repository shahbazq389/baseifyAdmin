@extends('app')

@section('title', 'Advertisers')

@section('page_level_styles')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/select2/select2.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}"/>
@stop

@section('theme_level_styles')
    <link href="{{ URL::asset('assets/global/css/components-rounded.css')}}" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('assets/global/css/plugins.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/layout.css')}}" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="{{ URL::asset('css/themes/light.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/custom.css')}}" rel="stylesheet" type="text/css"/>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="panel panel-info">
                    <div class="panel-heading"><h2 style="text-align: center">Update-Data</h2></div>
                    <div class="panel-body">
                        @foreach($edit_data as $value)
                            <form method="post" action="{{ url('datatable/update') }}">
                                <div class="form-group">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="id" value="{{ $value->id }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $value->name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Update-date</label>
                                    <input type="text" name="Updatedate" value="{{ $value->created_at }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="submit" value="Submit" class="btn btn-primary form-control">
                                </div>
                                @endforeach
                            </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
@endsection