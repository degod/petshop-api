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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('category_uuid', 36);
            $table->char('uuid', 36);
            $table->string('title', 255);
            $table->double('price', 12, 2);
            $table->text('description');
            $table->json('metadata');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_uuid')->references('uuid')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
