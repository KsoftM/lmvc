<?php

use ksoftm\system\database\RawQuery;
use ksoftm\system\Schema;
use ksoftm\system\utils\database\Migration;

class LessonsMigration extends Migration
{

    public function up(): void
    {
        Schema::CreateIfNotExists('lessons', function (RawQuery $query) {
            $query->id();
            $query->string('firstName', 50);
            $query->string('lastName', 50);
            $query->string('username', 50)->nullable()->unique();
            $query->string('email', 300)->unique();
            $query->string('password', 500);
            $query->year('batch');
            $query->boolean('active')->default(0);
            $query->integer('roles_id')->primaryKey()->foreignKey('roles.id');

            $query->timestamps();
        });
    }

    public function down(): void
    {
        Schema::DropIfExists('lessons');
    }
}
