<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('type')->nullable();
            $table->integer('charge_type')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->integer('schedule_month')->nullable();
            $table->integer('schedule_year')->nullable();
            $table->date('maintained_date')->nullable();
            $table->bigInteger('vehicle_id');
            $table->double('mileage_last_time')->nullable();
            $table->double('mileage_current')->nullable();
            $table->double('total_amount_excluding_tax')->nullable();
            $table->double('discount')->nullable();
            $table->double('total_amount_including_tax')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('scheduled_date');
            $table->index('schedule_month');
            $table->index('schedule_year');
            $table->index('maintained_date');
            $table->index('vehicle_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_costs');
    }
}
