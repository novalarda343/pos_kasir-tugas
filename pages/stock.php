<?php

$products = db()->query('SELECT id, sku, name, stock, min_stock FROM products ORDER BY name')->fetchAll();
$movements = db()->query('SELECT sm.*, p.sku, p.name FROM stock_movements sm JOIN products p ON p.id = sm.product_id ORDER BY sm.created_at DESC LIMIT 20')->fetchAll();

render_layout('Manajemen Stok', function () use ($products, $movements) {
?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[360px_1fr]">
    <form method="post" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <input type="hidden" name="action" value="adjust_stock">
        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Mutasi Stok</h2>
        <label class="mb-4 block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Produk</span><select name="product_id" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" required><?php foreach ($products as $product): ?><option value="<?= e((string) $product['id']) ?>"><?= e($product['sku'] . ' - ' . $product['name'] . ' (stok ' . $product['stock'] . ')') ?></option><?php endforeach; ?></select></label>
        <label class="mb-4 block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Tipe</span><select name="type" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="in">Stok Masuk</option><option value="out">Stok Keluar</option></select></label>
        <label class="mb-4 block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Jumlah</span><input type="number" name="quantity" min="1" value="1" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></label>
        <label class="mb-4 block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Catatan</span><input name="note" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></label>
        <button class="h-11 w-full rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600" type="submit">Simpan Mutasi</button>
    </form>

    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">Riwayat Mutasi</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400"><tr><th class="px-5 py-3">Waktu</th><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Tipe</th><th class="px-5 py-3">Qty</th><th class="px-5 py-3">Catatan</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php foreach ($movements as $movement): ?>
                        <tr class="text-gray-700 dark:text-gray-300"><td class="px-5 py-3"><?= e(date('d/m/Y H:i', strtotime($movement['created_at']))) ?></td><td class="px-5 py-3"><?= e($movement['sku'] . ' - ' . $movement['name']) ?></td><td class="px-5 py-3"><?= $movement['type'] === 'in' ? 'Masuk' : 'Keluar' ?></td><td class="px-5 py-3"><?= e((string) $movement['quantity']) ?></td><td class="px-5 py-3"><?= e($movement['note']) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!$movements): ?><tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">Belum ada mutasi.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
});
