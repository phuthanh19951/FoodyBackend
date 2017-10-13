<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarkColumnTblCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_comment', function (Blueprint $table) {
          $table->double('mark_location')->after('cnt_like');
          $table->double('mark_price')->after('mark_location');
          $table->double('mark_service')->after('mark_price');
          $table->double('mark_serve')->after('mark_service');
          $table->double('mark_space')->after('mark_serve');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_comment', function (Blueprint $table) {
          $table->dropColumn('mark_location');
          $table->dropColumn('mark_price');
          $table->dropColumn('mark_service');
          $table->dropColumn('mark_serve');
          $table->dropColumn('mark_space');
        });
    }
}
