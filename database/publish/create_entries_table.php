<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Statamic\Eloquent\Database\BaseMigration as Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->prefix('entries'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('site');
            $table->unsignedInteger('origin_id')->nullable();
            $table->boolean('published')->default(true);
            $table->string('status');
            $table->string('slug')->nullable();
            $table->string('uri')->nullable();
            $table->string('date')->nullable();
            $table->string('collection');
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->prefix('entries'));
    }
}