<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_accessories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('maintenance_cost_id');
            $table->bigInteger('accessory_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('price')->nullable();
            $table->timestamps();

            $table->index('maintenance_cost_id');
            $table->index('accessory_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_accessories');
    }
}
