<?php

namespace App\Http\Controllers\Admin;

use App\Employee;
use App\Appointment;
use App\payment;
use App\Client;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdateAppointmentsRequest;

class AccountsController extends Controller
{
    /**
     * Display a listing of Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('accounts_management')) {
            return abort(401);
        }

        $sales_data = DB::table('appointments')
        ->select('appointments.id','appointments.total','clients.first_name','clients.last_name','services.name','appointments.hourSpent','appointments.paid')
        ->join('clients', 'appointments.client_id', '=', 'clients.id')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        
        ->get();
        $data =[];
        foreach ($sales_data as $key) {

            $payment = DB::table('payments')
            ->select(DB::raw('SUM(amount_render) as payments'))
            ->where('appointment_id','=',$key->id)
            ->get();
            foreach ($payment as $k) {
                if($k->payments == $key->total){
                    $paid=1;
                }else{
                    $paid=0;
                }
                array_push($data,[
                    "appointment_id" => $key->id,
                    "tracking_id" => md5($key->id),
                    "first_name" => $key->first_name,
                    "last_name" => $key->last_name,
                    "name" => $key->name,
                    "hourSpent" => $key->hourSpent,
                    "paid" => $paid,
                    "total" => $key->total,
                    "payments" => ( !empty($k->payments)) ? $k->payments : "0.00",
                ]);
            }
            
        }
        $sales =  $data;
        return view('admin.accounting.index', compact('sales'));
    }
    public function payment($id){
        return view('admin.accounting.payment');
    }
    /**
     * Show the form for creating new Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('appointment_create')) {
            return abort(401);
        }
        $relations = [
            'clients' => \App\Client::get(),
            'employees' => \App\Employee::get(),
			'services' => \App\Service::get(),
        ];

        return view('admin.appointments.create', $relations);
    }

    /**
     * Store a newly created Appointment in storage.
     *
     * @param  \App\Http\Requests\StoreAppointmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        if(! Gate::allows('payments_access')){
            return abort(401);
        }
        
        $payment = new payment;
        $payment->appointment_id = $request->appointmentId;
        $payment->official_receipt = $request->officialReceipt;
        $payment->amount_render = $request->renderAmount;
        $payment->paymentType = $request->paymentType;
        $payment->trackingNo = $request->trackingNo;
        $payment->save();

        return redirect()->route('admin.accounts.index');
    }


    /**
     * Show the form for editing Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('appointment_edit')) {
            return abort(401);
        }
        $relations = [
            'clients' => \App\Client::get()->pluck('first_name', 'id')->prepend('Please select', ''),
            'employees' => \App\Employee::get()->pluck('first_name', 'id')->prepend('Please select', ''),
        ];

        $appointment = Appointment::findOrFail($id);

        return view('admin.appointments.edit', compact('appointment') + $relations);
    }

    /**
     * Update Appointment in storage.
     *
     * @param  \App\Http\Requests\UpdateAppointmentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentsRequest $request, $id)
    {
        if (! Gate::allows('appointment_edit')) {
            return abort(401);
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());



        return redirect()->route('admin.appointments.index');
    }


    /**
     * Display Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('accounts_management')) {
            return abort(401);
        }
       
        $sales_account = DB::table('appointments')
        ->join('clients', 'appointments.client_id', '=', 'clients.id')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->where('appointments.id','=',$id)
        ->get();

        $payment = DB::table('payments')
            ->select(DB::raw('SUM(amount_render) as payments'))
            ->where('appointment_id','=',$id)
            ->get();
            foreach ($payment as $payment) {
                $totalPayment = $payment->payments;
            }
        $allpayment = DB::table('payments')
            ->select('amount_render','official_receipt','id','created_at','paymentType','trackingNo')
            ->where('appointment_id','=',$id)
            ->get();
        $clientInfo = $sales_account[0];
        $appointment = (object)["_id"=>$id];
        $payments = (object)["amount"=>$totalPayment];
        $transCode = (object)["sequence"=>substr(md5($id),0,8)];
        return view('admin.accounting.payment', compact('clientInfo','appointment','payments','transCode','allpayment'));
    }


    /**
     * Remove Appointment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('appointment_delete')) {
            return abort(401);
        }
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Delete all selected Appointment at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('appointment_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Appointment::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
