@extends('layouts.app')

@section('content')
<style>
        .notice {
            padding: 15px;
            background-color: #fafafa;
            border-left: 6px solid #7f7f84;
            margin-bottom: 10px;
            -webkit-box-shadow: 0 5px 8px -6px rgba(0,0,0,.2);
               -moz-box-shadow: 0 5px 8px -6px rgba(0,0,0,.2);
                    box-shadow: 0 5px 8px -6px rgba(0,0,0,.2);
        }
        .notice-sm {
            padding: 10px;
            font-size: 80%;
        }
        .notice-lg {
            padding: 35px;
            font-size: large;
        }
        .notice-success {
            border-color: #80D651;
        }
        .notice-success>strong {
            color: #80D651;
        }
        .notice-info {
            border-color: #45ABCD;
        }
        .notice-info>strong {
            color: #45ABCD;
        }
        .notice-warning {
            border-color: #FEAF20;
        }
        .notice-warning>strong {
            color: #FEAF20;
        }
        .notice-danger {
            border-color: #d73814;
        }
        .notice-danger>strong {
            color: #d73814;
        }
    </style>
    {!! Charts::styles() !!}
    <div class="row">
        {{-- <div class="col-sm-4">
                <div class="notice notice-success">
                    <strong>Earnings</strong> {{ $earnings }}
                </div>
        </div>
        <div class="col-sm-4">
                <div class="notice notice-danger">
                    <strong>Receivables</strong> {{ $receivables }}
                </div>
        </div> --}}
        <div class="col-sm-6">
            {!! $appointmentChart->html() !!}
        </div>
        <div class="col-sm-6">
            {!! $montlyIncomeChart->html() !!}
        </div>
        <div class="col-sm-6">
            {!! $diskStatus->html() !!}
        </div>

        {{-- <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div> --}}
    </div>
    {!! Charts::scripts() !!}

        {!! $appointmentChart->script() !!}
        {!! $diskStatus->script() !!}
        {!! $montlyIncomeChart->script() !!}
@endsection
