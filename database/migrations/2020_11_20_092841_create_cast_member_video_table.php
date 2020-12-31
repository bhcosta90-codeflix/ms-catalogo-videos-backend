<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastMemberVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast_member_video', function (Blueprint $table) {
            $table->foreignUuid('cast_member_id')->constrained('cast_members');
            $table->foreignUuid('video_id')->constrained('videos');
            $table->primary(['cast_member_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cast_member_video');
    }
}
