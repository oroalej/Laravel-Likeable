<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('likes')) {
            Schema::create('likes', static function (Blueprint $table) {
                $table->id();
                $table->morphs('user');
                $table->morphs('likeable');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_type', 'likeable_type', 'likeable_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('likeable_counter')) {
            Schema::create('likeable_counter', static function (Blueprint $table) {
                $table->id();
                $table->morphs('likeable');
                $table->unsignedInteger('count')->default(0);
            });
        }

        if (! Schema::hasTable('liker_counter')) {
            Schema::create('liker_counter', static function (Blueprint $table) {
                $table->id();
                $table->morphs('user');
                $table->string('likeable_type');
                $table->unsignedInteger('count')->default(0);

                $table->index([ 'likeable_type', 'user_type', 'user_id' ]);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
        Schema::dropIfExists('likeable_counter');
        Schema::dropIfExists('liker_counter');
    }
};
