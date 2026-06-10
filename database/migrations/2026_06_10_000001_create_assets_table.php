<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('assets.assets_table', 'assets'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->string('reference');
            $table->string('name');
            $table->string('category')->default('equipment');
            $table->string('status')->default('in_use');

            $table->bigInteger('value_cents')->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('acquired_at')->nullable();
            $table->integer('useful_life_months')->nullable();

            $table->string('assigned_to')->nullable();
            $table->string('location')->nullable();

            $table->date('expires_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->integer('renew_interval_months')->nullable();
            $table->timestamp('last_reminded_at')->nullable();

            $table->json('details')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['owner_type', 'owner_id', 'reference']);
            $table->index(['owner_type', 'owner_id', 'status']);
            $table->index(['owner_type', 'owner_id', 'category']);
            $table->index(['owner_type', 'owner_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('assets.assets_table', 'assets'));
    }
};
