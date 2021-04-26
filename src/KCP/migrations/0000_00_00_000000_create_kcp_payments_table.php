<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKcpPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kcp_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string("tno")->unique();
            $table->string("order_no", 100)->comment("주문번호");
            $table->integer("amount")->comment("결제금액");;
            $table->string("card_cd")->comment("카드번호");;
            $table->string("card_name")->comment("카드명");
            $table->string("app_time")->comment("승인시간");;
            $table->string("app_no", 10)->comment("승인번호");
            $table->string("noinf", 2)->comment("무이자");
            $table->string("quota", 2)->comment("할부");
            $table->string("partcanc_yn", 2)->comment("부분취소여부");
            $table->timestamps();

            $table->index(['order_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kcp_payments');
    }
}
