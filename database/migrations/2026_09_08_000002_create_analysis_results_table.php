<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained()->cascadeOnDelete();
            $table->string('parameter_key', 50);
            $table->string('parameter_name');
            $table->string('statistic_type', 20);
            $table->json('result_data');
            $table->json('chart_config')->nullable();
            $table->text('narrative')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['tracer_study_id', 'parameter_key'], 'analysis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
