<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->longText('logo');
            $table->longText('trip_insurance');
            $table->longText('rewards');
            $table->longText('about');
            $table->string('phone');
            $table->longText('support');
            $table->longText('safety_roles');
            $table->longText('polices');
            $table->bigInteger('km');
            $table->bigInteger('vat');
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
        Schema::dropIfExists('settings');
    }
}
