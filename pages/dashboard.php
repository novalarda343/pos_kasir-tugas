<?php

$todaySales = db()->query("SELECT COALESCE(SUM(COALESCE(NULLIF(total_amount, 0), subtotal)), 0) total FROM sales WHERE DATE(created_at) = CURDATE()")->fetch()['total'];
$todayTransactions = db()->query("SELECT COUNT(*) total FROM sales WHERE DATE(created_at) = CURDATE()")->fetch()['total'];
$productCount = db()->query('SELECT COUNT(*) total FROM products')->fetch()['total'];
$lowStock = db()->query('SELECT COUNT(*) total FROM products WHERE stock <= min_stock')->fetch()['total'];
$recentSales = db()->query('SELECT * FROM sales ORDER BY created_at DESC LIMIT 5')->fetchAll();
$criticalProducts = db()->query('SELECT sku, name, stock, min_stock FROM products WHERE stock <= min_stock ORDER BY stock ASC LIMIT 5')->fetchAll();

render_layout('Dashboard', function () use ($todaySales, $todayTransactions, $productCount, $lowStock, $recentSales, $criticalProducts) {
?>
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ([
        ['Penjualan Hari Ini', rupiah($todaySales), 'Kas masuk tanggal ini'],
        ['Transaksi', (string) $todayTransactions, 'Nota tersimpan hari ini'],
        ['Produk Aktif', (string) $productCount, 'Item dalam katalog'],
        ['Stok Menipis', (string) $lowStock, 'Perlu restock'],
    ] as [$label, $value, $hint]): ?>
        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= e($label) ?></p>
            <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white"><?= e($value) ?></div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= e($hint) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400">
                    <tr><th class="px-5 py-3">No</th><th class="px-5 py-3">Pelanggan</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Waktu</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php foreach ($recentSales as $sale): ?>
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-5 py-3"><?= e($sale['sale_number']) ?></td>
                            <td class="px-5 py-3"><?= e($sale['customer_name']) ?></td>
                            <td class="px-5 py-3"><?= rupiah($sale['total_amount'] ?: $sale['subtotal']) ?></td>
                            <td class="px-5 py-3"><?= e(date('d/m H:i', strtotime($sale['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentSales): ?>
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Stok Perlu Restock</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400">
                    <tr><th class="px-5 py-3">SKU</th><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Stok</th><th class="px-5 py-3">Minimum</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php foreach ($criticalProducts as $product): ?>
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-5 py-3"><?= e($product['sku']) ?></td>
                            <td class="px-5 py-3"><?= e($product['name']) ?></td>
                            <td class="px-5 py-3"><?= e((string) $product['stock']) ?></td>
                            <td class="px-5 py-3"><?= e((string) $product['min_stock']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$criticalProducts): ?>
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Stok masih aman.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
});
