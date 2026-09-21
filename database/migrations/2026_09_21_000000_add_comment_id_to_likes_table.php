<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A like targets either a post OR a comment, never both
     * (enforced by request validation with prohibits rules).
     */
    public function up(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            if (!Schema::hasColumn('likes', 'comment_id')) {
                $table->foreignId('comment_id')->nullable()->after('post_id')->constrained()->onDelete('cascade');
            }
        });

        // Make post_id nullable so comment-likes can store NULL there.
        // Avoids doctrine/dbal dependency required by ->change().
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `likes` MODIFY `post_id` BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE "likes" ALTER COLUMN "post_id" DROP NOT NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite cannot MODIFY columns; rebuild the table preserving data.
            DB::statement('PRAGMA foreign_keys=off');
            Schema::create('likes_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('post_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('comment_id')->nullable()->constrained()->onDelete('cascade');
                $table->timestamps();
            });
            DB::statement('INSERT INTO likes_new (id, user_id, post_id, comment_id, created_at, updated_at) SELECT id, user_id, post_id, comment_id, created_at, updated_at FROM likes');
            Schema::drop('likes');
            Schema::rename('likes_new', 'likes');
            DB::statement('PRAGMA foreign_keys=on');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('likes', 'comment_id')) {
            Schema::table('likes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('comment_id');
            });
        }
    }
};
