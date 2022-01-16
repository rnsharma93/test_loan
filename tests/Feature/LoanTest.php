<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class LoanTest extends TestCase
{
    use RefreshDatabase, WithFaker ;

    //apply loan without middleware => without user login token
    public function testLoanApplyWithoutToken() {
        $data = $this->json('POST','/api/loan/apply');
        $data->assertStatus(401);
        $data->assertJson(["message" => "Unauthenticated."]);
    }

    //test loan apply without fields
    public function testLoanApplyRequiredFields() {
        $user = User::factory()->create();
        $response = $this->actingAs($user,'sanctum')->json('POST','/api/loan/apply');
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "amount" => [
                    "The amount field is required."
                ],
                "tenure" => [
                    "The tenure field is required."
                ]
            ]
        ]);
    }

    //apply loan successfull
    public function testLoanApplySuccessfull() {

        $amount = $p = rand(1000,10000);  
        $data = ['amount'=>$amount,'tenure'=>12,'interest_rate'=>12];

        //calculate EMI
        $r = $data['interest_rate']/52/100;
        $n = $data['tenure'];
        $var = pow((1+$r),$n);
        $emi = $p * $r * ($var/($var-1));
        $emi = round($emi,0,PHP_ROUND_HALF_UP);
        $total_amount_to_paid = round( $emi * $n, 2, PHP_ROUND_HALF_UP) ;
        $total_interest_to_paid = round( $total_amount_to_paid - $p, 2, PHP_ROUND_HALF_UP);

        $user = User::factory()->create();

        $response = $this->actingAs($user,'sanctum')->json('POST','/api/loan/apply', $data, ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status" => [],
            'data' =>[
                'amount',
                'tenure',
                'interest_rate',
                'emi',
                'total_amount_to_paid',
                'total_interest_to_paid',
                'tenure_type',
                'user_id'
            ]
        ]);
        $response->assertJson(["data"=> 
            ["amount"=>$amount,
            "tenure"=>12,
            "interest_rate"=>12,
            "emi"=>$emi,
            "total_amount_to_paid" => $total_amount_to_paid,
            'total_interest_to_paid' => $total_interest_to_paid
            ]
        ]);
        //$data = $response->getData(); 
        //dd($data->data->emi);
    }

    //approve loan
    //user auth -> apply loan -> approve loan
    public function testLoanApprove() {
        $amount = $p = rand(1000,10000);  
        $data = ['amount'=>$amount,'tenure'=>12,'interest_rate'=>12];

        $user = User::factory()->create();

        $auth = $this->actingAs($user,'sanctum');

        //loan application
        $loan = $auth->json('POST','/api/loan/apply', $data, ['Accept' => 'application/json']);
        $loan->assertStatus(200);

        $loan_data = $loan->getData();
        $loan_id = $loan_data->data->id;
        //loan Approve
        $approve = $auth->json('POST',"api/loan/$loan_id/approve",['Accept' => 'application/json']);

        $approve->assertStatus(200);
        $approve->assertJsonStructure([
            "status",
            "message"
        ]);

    }

    //loan repayment
    //user auth, loan apply, loan approve, get loan detail , pay the emi
    public function testLoanPay() {
        $amount = $p = rand(1000,10000);  
        $data = ['amount'=>$amount,'tenure'=>12,'interest_rate'=>12];

        $user = User::factory()->create();

        $auth = $this->actingAs($user,'sanctum');

        //loan application
        $loan = $auth->json('POST','/api/loan/apply', $data, ['Accept' => 'application/json']);
        $loan->assertStatus(200);

        $loan_data = $loan->getData();
        $loan_id = $loan_data->data->id;
        //loan Approve
        $approve = $auth->json('POST',"api/loan/$loan_id/approve",['Accept' => 'application/json']);

        $approve->assertStatus(200);
        $approve->assertJsonStructure([
            "status",
            "message"
        ]);

        //get loan detail
        $detail = $auth->json('GET',"api/loan/$loan_id", ['Accept' => 'application/json']);
        $detail->assertStatus(200);
        $detail->assertJsonStructure([
            "data" => [
                "loan" => [],
                "schedules" => [],
                "total_paid",
                "total_remaining"
            ]
        ]);
        
        $detail_data = $detail->getData();
        $emi = $detail_data->data->loan->emi;  
        $schedule_id = $detail_data->data->schedules[0]->id;
        
        //pay 
        $pay = $auth->json('POST', "api/loan/$loan_id/pay",['schedule_id'=>$schedule_id], ['Accept' => 'application/json']);
        $pay->assertStatus(200);
        $pay->assertJson([
            "status" => 1,
            "message" => "EMI paid successfully"
        ]);

    }

}
