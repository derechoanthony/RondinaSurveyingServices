@extends('layouts.app')

@section('content')
<style>
    .currency{text-align:right;}
    .totalamt{
        font-size: 15px;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        @lang('quickadmin.account.aLedger')<br>
        <span>Account No.: </span> <strong>{{ $transCode->sequence }}</strong>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <td colspan="2" style="background-color: antiquewhite;">@lang('quickadmin.qa_account_info')</td>
                    </tr>
                    <tr>
                        <th>@lang('quickadmin.account.fields.first-name')</th>
                        <td>{{ $clientInfo->first_name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('quickadmin.account.fields.last-name')</th>
                        <td>{{ $clientInfo->last_name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('quickadmin.account.fields.phone')</th>
                        <td>{{ $clientInfo->phone }}</td>
                    </tr>
                    <tr>
                        <th>@lang('quickadmin.account.fields.email')</th>
                        <td>{{ $clientInfo->email }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                @can('payments_access')
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.accounts.store']]) !!}
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td colspan="2" style="background-color: antiquewhite;">@lang('quickadmin.qa_payment')</td>
                            </tr>
                            <tr>
                                <th>@lang('quickadmin.account.fields.balance')</th>
                                <td class="currency">@lang('quickadmin.quickadmin_currency')&nbsp;{{ number_format(($clientInfo->total-$payments->amount),2) }}
                                    <input type="hidden" name="renderAmount" id="renderAmount" value="{{ $clientInfo->total }}" readonly>
                                    <input type="hidden" name="appointmentId" id="appointmentId" value="{{ $appointment->_id }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('quickadmin.account.fields.or')</th>
                                <td class="currency"><input type="text" name="officialReceipt" id="officialReceipt" value="{{ old('officialReceipt') }}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <th>@lang('quickadmin.account.fields.amount')</th>
                                <td class="currency"><input type="text" name="renderAmount" id="renderAmount" value="{{ old('renderAmount') }}" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <th>
                                    @lang('quickadmin.payments.transactionType')
                                </th>
                                <td>
                                    <select id="paymentType" name="paymentType">
                                        <option value="">---</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Checque">Checque</option>
                                        <option value="Bank">Bank Payment</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="lbltrackingNo" style="display:none">
                                <th id='lblTransType'></th>
                                <td>
                                    <input type="text" name="trackingNo" id="trackingNo" value="" autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                        {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-info']) !!}
                                </td>
                            </tr>
                        </table>
                    {!! Form::close() !!}
                @endcan
            </div>
        </div><!-- Nav tabs -->

        <div class="panel-body table-responsive">
                <table class="table table-bordered table-striped {{ count($allpayment) > 0 ? 'datatable' : '' }} ">
                    <thead>
                        <tr>
                            <th>@lang('quickadmin.payments.fields.sequence')</th>
                            <th>@lang('quickadmin.payments.fields.official_receipt')</th>
                            <th>@lang('quickadmin.payments.fields.paymentType')</th> 
                            <th>@lang('quickadmin.payments.fields.created_at')</th>
                            <th>@lang('quickadmin.payments.fields.amount_render')</th>                            
                                                       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @if (count($allpayment) > 0)
                            @foreach ($allpayment as $allpayment)
                                <tr data-entry-id="{{ $allpayment->id }}">
                                    <td>{{ substr(md5($allpayment->id),0,10) }}</td>
                                    <td>{{ $allpayment->official_receipt }}</td>
                                    <td>{{ $allpayment->paymentType }} [{{ $allpayment->trackingNo }}]</td>
                                    <td>{{ $allpayment->created_at }}</td>
                                    <td  class="currency">@lang('quickadmin.quickadmin_currency')&nbsp;<strong>{{ number_format($allpayment->amount_render,2) }}</strong></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">@lang('quickadmin.qa_no_entries_in_table')</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="currency">@lang('quickadmin.quickadmin_currency')&nbsp;<strong class="totalamt">{{ number_format($payments->amount,2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

    </div>
</div>
@stop
@section('javascript')
<script>
        $(document).ready(function() {
            $('#paymentType').change(function() {
                var paymentType = $("#paymentType").val();
                    if(paymentType == 'Cash'){
                        $('#trackingNo').val('cash')
                        $('#lbltrackingNo').hide();
                    }else{
                        $('#lbltrackingNo').show();
                        $('#trackingNo').val('');
                        if(paymentType == 'Bank'){
                            $('#lblTransType').html(''+paymentType+' Account No.: ');
                        }else{
                            $('#lblTransType').html(''+paymentType+' No.: ');
                        }
                        
                    }
              });
        });
</script>
@endsection