<?php

use App\Models\AutomaticPickup;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('AUTO_PICKUPLIST', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('AUTO_PICKUPLIST', function (Blueprint $table) {
            $table->increments('id')->first();
            $table->string('customer_id')->after('id')->nullable()->default(null);
            $table->timestamps();
        });

        $list = AutomaticPickup::all();
        foreach ($list as $i){
            $customer = Customer::where('cu_name', $i->cu_name)->first();
            if ($customer){
                $i->update(['customer_id' => $customer->customer_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('AUTO_PICKUPLIST', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('customer_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
