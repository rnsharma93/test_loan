<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->double('amount',15,2);
            $table->integer('tenure');
            $table->string('tenure_type',20); //weekly, monthly, yearly etc
            $table->float('interest_rate',8,2);
            $table->double('emi',15,2);
            $table->double('total_amount_to_paid',15,2);
            $table->double('total_interest_to_paid',15,2);
            $table->boolean('is_approve');
            $table->datetime('approved_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
