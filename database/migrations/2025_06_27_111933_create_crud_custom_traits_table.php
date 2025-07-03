<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crud_custom_traits', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('crud_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custom_trait_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
