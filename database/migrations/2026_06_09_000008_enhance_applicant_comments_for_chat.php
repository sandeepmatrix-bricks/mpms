<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * WhatsApp-style chat extras: file/image/video attachments, edit window,
 * "delete for everyone" (is_deleted) and "delete for me" (deleted_for),
 * plus a reactions table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_comments', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('body');
            $table->string('attachment_type', 20)->nullable()->after('attachment'); // image | video | file
            $table->timestamp('edited_at')->nullable()->after('mentions');
            $table->boolean('is_deleted')->default(false)->after('edited_at');
            $table->json('deleted_for')->nullable()->after('is_deleted');
        });

        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comment_id')->constrained('applicant_comments')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('emoji', 16);
            $table->timestamps();

            $table->unique(['comment_id', 'user_id', 'emoji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');

        Schema::table('applicant_comments', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'attachment_type', 'edited_at', 'is_deleted', 'deleted_for']);
        });
    }
};
