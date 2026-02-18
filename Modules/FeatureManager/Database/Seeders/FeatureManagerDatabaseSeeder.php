<?php

namespace Modules\FeatureManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureManagerDatabaseSeeder extends Seeder
{
    public function run()
    {
        $features = [
            // ORDER SUMMARY LOGIC
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Total Makanan & Minuman', 'feature_key' => 'summary_mamin'],
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Disc (Item & Bill)', 'feature_key' => 'summary_disc'],
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Service Charge (5%)', 'feature_key' => 'summary_service'],
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Packaging & Delivery', 'feature_key' => 'summary_pkg'],
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Lain-lain A & B', 'feature_key' => 'summary_others'],
            ['feature_group' => 'ORDER SUMMARY LOGIC', 'feature_name' => 'Grand Total Calculation', 'feature_key' => 'summary_grandtotal'],

            // LIVE TRANSACTION
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'POS / Order Entry', 'feature_key' => 'pos_entry'],
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'Table Management', 'feature_key' => 'pos_table'],
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'Take out / Dine-in', 'feature_key' => 'pos_mode'],
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'Sales Return (Retur)', 'feature_key' => 'pos_return'],
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'Multi-Payment', 'feature_key' => 'pos_multipay'],
            ['feature_group' => 'LIVE TRANSACTION', 'feature_name' => 'Split Bill & Merge', 'feature_key' => 'pos_split_merge'],

            // FINANCE & BACKOFFICE
            ['feature_group' => 'FINANCE & BACKOFFICE', 'feature_name' => 'Biaya Operasional', 'feature_key' => 'fin_expense'],
            ['feature_group' => 'FINANCE & BACKOFFICE', 'feature_name' => 'Cash Management', 'feature_key' => 'fin_cash_mgmt'],
            ['feature_group' => 'FINANCE & BACKOFFICE', 'feature_name' => 'Penawaran Harga', 'feature_key' => 'fin_quotation'],
            ['feature_group' => 'FINANCE & BACKOFFICE', 'feature_name' => 'Customers & Suppliers', 'feature_key' => 'fin_parties'],

            // INVENTORY & STOCK
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Item & Category', 'feature_key' => 'inv_product'],
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Penyesuaian Stok', 'feature_key' => 'inv_adjust'],
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Stok Masuk (PO)', 'feature_key' => 'inv_purchase'],
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Retur Beli', 'feature_key' => 'inv_purch_ret'],
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Recipe / BOM', 'feature_key' => 'inv_recipe'],
            ['feature_group' => 'INVENTORY & STOCK', 'feature_name' => 'Low Stock Alert', 'feature_key' => 'inv_alert'],

            // REPORTS
            ['feature_group' => 'REPORTS', 'feature_name' => 'Sales Report', 'feature_key' => 'rep_sales'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Profit / Loss Report', 'feature_key' => 'rep_profit_loss'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Payments Report', 'feature_key' => 'rep_payment'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Purchases Report', 'feature_key' => 'rep_purchases'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Sales Return Report', 'feature_key' => 'rep_sales_ret'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Purchase Ret Report', 'feature_key' => 'rep_purch_ret'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Laporan Shift', 'feature_key' => 'rep_shift'],
            ['feature_group' => 'REPORTS', 'feature_name' => 'Export Coretax', 'feature_key' => 'rep_coretax'],
            ['feature_group' => 'INVENTORY REP', 'feature_name' => 'Stock Inventory Rep', 'feature_key' => 'rep_stock_inv'],
            ['feature_group' => 'F&B REPORT', 'feature_name' => 'Sales by Order Type', 'feature_key' => 'rep_fnb_order_type'],
            ['feature_group' => 'F&B REPORT', 'feature_name' => 'Sales by Table', 'feature_key' => 'rep_fnb_table'],
            ['feature_group' => 'CONTROL REP', 'feature_name' => 'Audit Trail Report', 'feature_key' => 'rep_audit'],

            // SETTINGS
            ['feature_group' => 'SETTINGS', 'feature_name' => 'Receive Method', 'feature_key' => 'set_receive'],
            ['feature_group' => 'SETTINGS', 'feature_name' => 'System Settings', 'feature_key' => 'set_system'],
            ['feature_group' => 'SETTINGS', 'feature_name' => 'User & Role (RBAC)', 'feature_key' => 'set_user_role'],
        ];

        foreach ($features as $f) {
            DB::table('feature_managements')->updateOrInsert(
                ['feature_key' => $f['feature_key']],
                [
                    'feature_group' => $f['feature_group'],
                    'feature_name' => $f['feature_name'],
                    // Default: aktifkan semua untuk P3 (Premium) saat pertama install
                    'package_1' => 0,
                    'package_2' => 0,
                    'package_3' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
