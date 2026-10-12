<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_document_type', 12)->nullable()->after('customer_name');
            $table->string('customer_document_number', 20)->nullable()->after('customer_document_type');
            $table->string('customer_address')->nullable()->after('customer_document_number');
            $table->string('customer_email')->nullable()->after('customer_address');
            $table->string('voucher_type', 12)->default('BOLETA')->after('customer_email');
            $table->decimal('subtotal', 10, 2)->default(0)->after('voucher_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'customer_document_type',
                'customer_document_number',
                'customer_address',
                'customer_email',
                'voucher_type',
                'subtotal',
                'discount_amount',
            ]);
        });
    }
};
