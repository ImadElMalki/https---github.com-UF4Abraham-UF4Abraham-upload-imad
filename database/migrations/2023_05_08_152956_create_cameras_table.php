<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('cameras', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });


        Schema::create('cameras', function (Blueprint $table) {
            $table->bigInteger('id_cam_seguretat');
            $table->string('codi_cam_seguretat');
            $table->string('tipus_cam_seguretat');
            //
            $table->string('num_cam_seguretat')->nullable();
            $table->string('codi_suport');
            $table->integer('codi_districte');
            $table->string('nom_districte');
            $table->integer('codi_barri');
            $table->string('nom_barri');
            $table->bigInteger('X_ETRS89');
            $table->bigInteger('Y_ETRS89');
            $table->double('longitud');
            $table->double('latitud');
            $table->date('data_alta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
