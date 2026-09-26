<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update libraries table
        Schema::table('libraries', function (Blueprint $table) {
            $table->string('book_code')->nullable()->unique()->after('id');
            $table->string('title')->after('book_code');
            $table->string('author')->after('title');
            $table->string('publisher')->nullable()->after('author');
            $table->string('isbn')->nullable()->after('publisher');
            $table->string('category')->default('General')->after('isbn');
            $table->string('rack_location')->nullable()->after('category');
            $table->integer('total_copies')->default(1)->after('rack_location');
            $table->integer('available_copies')->default(1)->after('total_copies');
            $table->integer('issued_copies')->default(0)->after('available_copies');
            $table->decimal('price', 10, 2)->default(0.00)->after('issued_copies');
            $table->string('cover_image')->nullable()->after('price');
            $table->string('status')->default('Available')->after('cover_image');
            $table->text('description')->nullable()->after('status');
            $table->foreignId('created_by')->nullable()->after('description')->constrained('users')->onDelete('set null');
            $table->softDeletes()->after('created_by');
        });

        // Create book_issues table
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_code')->unique();
            $table->foreignId('library_id')->constrained('libraries')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->string('status')->default('Issued'); // Issued, Returned, Overdue, Lost
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->boolean('fine_paid')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
        Schema::table('libraries', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'book_code',
                'title',
                'author',
                'publisher',
                'isbn',
                'category',
                'rack_location',
                'total_copies',
                'available_copies',
                'issued_copies',
                'price',
                'cover_image',
                'status',
                'description',
                'created_by',
                'deleted_at',
            ]);
        });
    }
};
