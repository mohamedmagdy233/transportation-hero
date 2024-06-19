<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_rates', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('from')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->integer('rate');
            $table->text('description');

            $table->foreign('from')
                ->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('to')
                ->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('trip_id')
                ->on('trips')->references('id')->cascadeOnDelete()->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_rates');
    }
}
