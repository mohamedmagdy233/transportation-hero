<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();

            $table->string('bike_type');
            $table->string('bike_model');
            $table->string('bike_color');

            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')
                ->on('areas')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id')
                ->on('users')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('driver_details');
    }
}
