<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_input_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('defect_input_id');   // relasi ke defect_inputs
            $table->unsignedBigInteger('defect_category_id'); // relasi ke defect_categories
            $table->unsignedBigInteger('defect_sub_id');      // relasi ke defect_subs
            $table->integer('jumlah_defect')->default(0);
            $table->integer('total')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('defect_input_id')->references('id')->on('defect_inputs')->onDelete('cascade');
            $table->foreign('defect_category_id')->references('id')->on('defect_categories');
            $table->foreign('defect_sub_id')->references('id')->on('defect_subs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_input_details');
    }
};
