<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Application;
use App\Models\Schedule;
use DB;

class LoanController extends Controller
{
    //loan application 
    /**
     * Params
     *  Amount : required, minimum 1000
     *  Tenure : required
     *  interest_rate : optional, default 12 % per year
     * 
     * Response : JSON
     *  EMI Amount per week
     *  Loan data
     */
    public function apply(Request $request) {
        $this->validate($request,[
            'amount' => 'required|numeric|min:1000',
            'tenure' => 'required|numeric'
        ]);
        $rate = ($request->has('interest_rate')) ? $request->get('interest_rate') : 12; //yeary interest rate
        $data = [];
        $p =  $data['amount'] = $request->get('amount');
        $n = $data['tenure'] = $request->get('tenure');
        $r = $data['interest_rate'] = $rate;
        $r = $r/52/100; //weekly tenure

        $var = pow((1+$r),$n);
        $emi = $p * $r * ($var/($var-1));
        $data['emi'] = $emi = round($emi,0,PHP_ROUND_HALF_UP);
        $total_amount_to_paid = $data['emi']*$n;
        $total_interest_to_paid = $total_amount_to_paid - $p;
        $data['total_amount_to_paid'] = round($total_amount_to_paid,2,PHP_ROUND_HALF_UP);
        $data['total_interest_to_paid'] = round($total_interest_to_paid,2,PHP_ROUND_HALF_UP);
        $data['tenure_type'] = 'weekly';
        $data['user_id'] = $request->user()->id;

        $application = Application::create($data);

        return ['status'=>1,'data'=>$application];
    }

    //approve the loan application
    /**
     * It will approve the loan application
     * and create all schedules with payment date and amount according to tenure. 
     */
    public function approve(Application $application) {
        if(!$application) {
            return response()->json(['status'=>0,'message'=>'application not found'],404);
        }
        if($application->is_approve) {
            return response()->json(['status'=>0,'message'=>'application already approved','data'=>$application->totalPaid()],422);
        }
        $application->is_approve = 1;
        $application->approved_at = date("Y-m-d H:i:s");
        $application->save();

        //add schedules for each of tenure
        for($i=1;$i<=$application->tenure;$i++) {
            $next_day = 7*$i;
	        $date = date("Y-m-d",strtotime("+$next_day days")); 
            $insert_data['application_id'] = $application->id; 
            $insert_data['date'] = $date;
            $insert_data['amount'] = $application->emi;
            DB::table('schedules')->insert($insert_data);
        }

        return ['status'=>1,'message'=>'application approved successfully','total_paid'=>$application->totalPaid()];
    }

    //get loan application detail 
    /**
     * Get complete detail of loan application
     * Total Amount Paid
     * Total Amount remaining
     * Schedules for Repayments
     */
    public function detail(Application $application) {

        $data['loan'] = $application;
        $data['schedules'] = $application->schedules()->get();
        //$data['user'] = $application->user;
        $data['total_paid'] = $application->totalPaid();
        $data['total_remaining'] = $application->totalRemaining();

        return ['data'=>$data];
    }

    //EMI payment
    /**
     * Params 
     *  Application id from URL
     *  Schedule Id from post data
     * Return 
     *  Status =1 if success, 0 if error
     */
    public function pay(Request $request, Application $application) {
        $this->validate($request,['schedule_id'=>'required']);
        $schedule_id = $request->get('schedule_id');
        $schedule = Schedule::where(['application_id'=>$application->id,'id'=>$schedule_id])->get()->First();
        if(! $schedule) {
            return response()->json(['status'=>0,'error'=>'Schedule not found'],404);
        }

        if($schedule->is_paid) {
            return response()->json(['status'=>0,'error'=>'EMI already paid'],422);
        }

        $schedule->is_paid = 1;
        $schedule->paid_at = date("Y-m-d H:i:s");
        $schedule->save();


        return ['status'=>1,'message'=>'EMI paid successfully'];
    }
}
