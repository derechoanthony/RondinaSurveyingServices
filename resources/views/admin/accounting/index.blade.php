@extends('layouts.app')

@section('content')
<h3 class="page-title">@lang('quickadmin.account.title')</h3>



<div class="panel panel-default">
    <div class="panel-heading">
        @lang('quickadmin.qa_list')
    </div>

    <div class="panel-body table-responsive">
        <table class="table table-bordered table-striped {{ count($sales) > 0 ? 'datatable' : '' }} ">
            <thead>
                <tr>
                    <th>@lang('quickadmin.account.fields.sequence')</th>
                    <th>@lang('quickadmin.account.fields.name')</th>
                    <th>@lang('quickadmin.account.fields.services')</th>
                    <th>@lang('quickadmin.account.fields.timeSpent')</th>
                    <th>@lang('quickadmin.account.fields.charges')</th>
                    <th>@lang('quickadmin.account.fields.balance')</th>
                    <th>@lang('quickadmin.account.fields.status')</th>
                </tr>
            </thead>
            
            <tbody>
                @if (count($sales) > 0)
                    @foreach ($sales as $key => $val)
                        <tr data-entry-id="{{ $sales[$key]['appointment_id'] }}">
                            <td><a href="{{ route('admin.accounts.show',[$sales[$key]['appointment_id']]) }}">{{ substr($sales[$key]['tracking_id'],0,8) }}</a></td>
                            <td>{{ $sales[$key]['first_name'] }}&nbsp;{{ $sales[$key]['last_name'] }}</td>
                            <td>{{ $sales[$key]['name'] }}</td>
                            <td>{{ $sales[$key]['hourSpent'] }}</td>
                            <td>@lang('quickadmin.quickadmin_currency')&nbsp;{{ number_format($sales[$key]['total'] ,2) }}</td>
                            <td style="text-align:right">@lang('quickadmin.quickadmin_currency')&nbsp;{{ number_format(($sales[$key]['total']-$sales[$key]['payments']) ,2) }}</td>
                            <td>
                                @if($sales[$key]['paid'] == 0)
                                    <span class="label label-danger">Unpaid</span>
                                @else
                                    <span class="label label-success">Paid  </span>
                                @endif
                                
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection