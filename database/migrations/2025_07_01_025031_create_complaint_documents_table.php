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
        Schema::create('complaint_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size');
            $table->string('mime_type');
            $table->boolean('is_result_document')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_documents');
    }
};
