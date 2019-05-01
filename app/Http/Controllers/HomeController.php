<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Charts;
use App\User;
use App\Appointment;
use App\payment;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * Service Chart
         */

        $appointment = Appointment::where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))->get();
        $appointmentChart = Charts::database($appointment, 'bar', 'highcharts') 
                    ->title("Monthly Service") 
                    ->elementLabel("Total Service Made") 
                    ->dimensions(1000, 500) 
                    ->responsive(true) 
                    ->groupByMonth(date('Y'), true);
        
        /**
         * Income chart
         */
        $monthlyIncome = $this->monthlyIncome();
        $lineData = $monthlyIncome['data'];
        $lineLabel = $monthlyIncome['label'];
        $montlyIncomeChart = Charts::create('line', 'highcharts')
                            ->title('Montly Income Chart')
                            ->elementLabel('Income')
                            ->labels($lineLabel)
                            ->values($lineData)
                            ->dimensions(1000,500)
                            ->responsive(true);


        $earnings = "430,000.00";
        $receivables = "30,000.00";
        /**
         * Disk Chart
         */
        $diskSpace = $this->roundsize(disk_total_space("/"));
        $diskFreeSpace = $this->roundsize(disk_free_space("/"));
        $diskSpace_c = $this->roundsize(disk_free_space("/"))['size'];

        $freeSpace = $diskFreeSpace['size'];
        $useSpace = $diskSpace['size'] - $diskFreeSpace['size'];
        $diskStatus = Charts::create('donut', 'highcharts')
                    ->title('Disk Monitoring')
                    ->labels(['Free space', 'Used space'])
                    ->values([$freeSpace,$useSpace])
                    ->dimensions(1000,500)
                    ->responsive(true);

        return view('home', compact('appointmentChart','earnings','receivables','diskSpace_c','diskStatus','montlyIncomeChart'));
        
    }
    public function monthlyIncome(){
        $month = [];
        $monthlyIncome = [];
        for ($i=1; $i <=12 ; $i++) { 
            # code...
            $income = DB::table('payments')->select(DB::raw("SUM(amount_render) AS monthlyTotal,DATE_FORMAT(created_at,'%M') as month"))
                ->whereMonth('created_at','=', $i)
                ->whereYear('created_at','=',date('Y'))
                ->groupBy(DB::raw("DATE_FORMAT(created_at,'%M')"))
                ->get();
            foreach ($income as $income) {
                array_push($month,$income->month);
                array_push($monthlyIncome,$income->monthlyTotal);
            }
        }
        return [
            "label" => $month,
            "data" => $monthlyIncome,
        ];
    }
    
    public function roundsize($size){
        $i=0;
        $iec = array("B", "Kb", "Mb", "Gb", "Tb");
        while (($size/1024)>1) {
            $size=$size/1024;
            $i++;}
        return [
                "size" => round($size,1),
                "iec" => $iec[$i]
        ];
    }
}
