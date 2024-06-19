<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_wallets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('driver_id');
            $table->decimal('total',10);
            $table->decimal('vat_total',10);
            $table->date('day');
            $table->boolean('status')->default(false);

            $table->foreign('driver_id')
                ->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();



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
        Schema::dropIfExists('driver_wallets');
    }
}
