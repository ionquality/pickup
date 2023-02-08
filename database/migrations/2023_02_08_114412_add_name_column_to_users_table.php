<?php

use App\Models\User;
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
        if (!Schema::hasColumn('users', 'name')){
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->after('last_name')->nullable()->default(null);
            });
            $users = User::all();
            foreach ($users as $user){
                $name = $user->first_name . ' ' . $user->last_name;
                $user->update(['name' => $name]);
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
        if (Schema::hasColumn('users', 'name')){
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
