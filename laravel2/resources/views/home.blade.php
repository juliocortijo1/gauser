@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                        <div class="media">
                            <div class="pull-left" href="#">
                                <span class="glyphicon glyphicon-user"></span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading"><strong>Step 1:</strong></h5>
                                Company Sign Up
                            </div>

                        </div>
                        <div class="media">
                            <div class="pull-left" href="#">
                                <span class="glyphicon glyphicon-user"></span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading"><strong>Step 1:</strong></h5>
                                Company Sign Up
                            </div>

                        </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
