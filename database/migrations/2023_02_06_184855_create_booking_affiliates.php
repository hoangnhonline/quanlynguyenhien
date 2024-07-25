<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingAffiliates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_commissions', function (Blueprint $table) {
            $table->integer('booking_id');
            $table->integer('user_id');
            $table->integer('level')->default(0);
            $table->integer('amount')->default(0);
            $table->tinyInteger('status')->default(\App\Models\BookingCommission::STATUS_PENDING);
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
        Schema::dropIfExists('booking_commissions');
    }
}
