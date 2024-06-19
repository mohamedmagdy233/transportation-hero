<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['complete', 'new', 'progress', 'accept','reject']);
            $table->enum('trip_type', ['scheduled','with','without','quick']);
            $table->text('from_address');
            $table->text('from_long');
            $table->text('from_lat');
            $table->text('to_address');
            $table->text('to_long');
            $table->text('to_lat');
            $table->time('time_ride')->nullable();
            $table->time('time_arrive')->nullable();
            $table->text('distance')->nullable();
            $table->string('time')->nullable();
            $table->double('price', 10, 2)->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->boolean('ended')->default(false);
            $table->foreign('driver_id')
                ->on('users')->references('id')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('user_id')
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
        Schema::dropIfExists('trips');
    }
}
