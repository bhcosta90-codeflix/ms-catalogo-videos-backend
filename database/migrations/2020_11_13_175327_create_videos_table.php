<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->smallInteger('year_launched')->unsigned();
            $table->boolean('opened')->default(false);
            $table->string('rating', 3)->nullable();
            $table->smallInteger('duration')->nullable()->unsigned();
            $table->string('file_thumb')->nullable();
            $table->string('file_banner')->nullable();
            $table->string('file_trailer')->nullable();
            $table->string('file_video')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
