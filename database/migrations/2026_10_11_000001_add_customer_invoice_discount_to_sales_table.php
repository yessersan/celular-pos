<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('document_type', 20)->default('boleta')->after('customer_name');
            $table->string('customer_document', 20)->nullable()->after('document_type');
            $table->string('customer_address')->nullable()->after('customer_document');
            $table->decimal('subtotal', 10, 2)->default(0)->after('customer_address');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'document_type', 'customer_document', 'customer_address',
                'subtotal', 'discount_percent', 'discount_amount',
            ]);
        });
    }
};
