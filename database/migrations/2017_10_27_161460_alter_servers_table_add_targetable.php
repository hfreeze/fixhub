<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Fixhub\Models\Server;

class AlterServersTableAddTargetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('targetable_type')->default('')->after('enabled');
            $table->integer('targetable_id')->default(0)->after('enabled');

            $table->index(['targetable_id', 'targetable_type']);
        });

        Schema::table('servers', function (Blueprint $table) {
            $servers = Server::withTrashed()->get();

            foreach ($servers as $server) {
                $server->targetable_id= $server->environment_id;
                $server->targetable_type = 'Fixhub\\Models\\Environment';
                $server->save();
            }
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign('servers_environment_id_foreign');
            $table->dropColumn('environment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('targetable_id');
            $table->dropColumn('targetable_type');
            $table->unsignedInteger('environment_id')->change();
            $table->foreign('environment_id')->references('id')->on('environments');
        });
    }
}
