<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->string('nama_institusi');
            $table->string('nama_prodi');
            $table->string('jenjang', 20);
            $table->string('tahun_lulusan', 4);
            $table->string('tahun_tracer', 4);
            $table->unsignedInteger('total_lulusan');
            $table->unsignedInteger('total_responden')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->string('status', 20)->default('processing');
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->unique(['nama_prodi', 'tahun_lulusan', 'tahun_tracer'], 'tracer_unique');
            $table->index('tahun_tracer');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_studies');
    }
};
