<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();

            $table->longText('agency_number');
            $table->longText('bike_license');
            $table->longText('id_card');
            $table->longText('house_card');
            $table->longText('bike_image');
            $table->boolean('status')->default(false);


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
        Schema::dropIfExists('driver_documents');
    }
}
