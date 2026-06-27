<?php

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM sales WHERE id = ?');
$stmt->execute([$id]);
$sale = $stmt->fetch();

if (!$sale) {
    flash('Transaksi tidak ditemukan.', 'error');
    redirect('sales');
}

$stmt = db()->prepare('SELECT si.*, p.sku, p.name FROM sale_items si JOIN products p ON p.id = si.product_id WHERE si.sale_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll();

render_layout('Struk Transaksi', function () use ($sale, $items) {
?>
<div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-start justify-between border-b border-gray-200 pb-5 dark:border-gray-800">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">POS Kasir</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400"><?= e($sale['sale_number']) ?></p>
        </div>
        <button onclick="window.print()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" type="button">Cetak</button>
    </div>
    <div class="mt-5 grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
        <div><span class="block text-gray-500">Pelanggan</span><?= e($sale['customer_name']) ?></div>
        <div class="text-right"><span class="block text-gray-500">Tanggal</span><?= e(date('d/m/Y H:i', strtotime($sale['created_at']))) ?></div>
    </div>
    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="border-y border-gray-200 text-left text-gray-500 dark:border-gray-800"><tr><th class="py-3">Produk</th><th class="py-3 text-right">Qty</th><th class="py-3 text-right">Harga</th><th class="py-3 text-right">Total</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($items as $item): ?>
                    <tr class="text-gray-700 dark:text-gray-300"><td class="py-3"><?= e($item['name']) ?><div class="text-xs text-gray-500"><?= e($item['sku']) ?></div></td><td class="py-3 text-right"><?= e((string) $item['quantity']) ?></td><td class="py-3 text-right"><?= rupiah($item['price']) ?></td><td class="py-3 text-right"><?= rupiah($item['total']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-5 space-y-2 border-t border-gray-200 pt-5 text-sm dark:border-gray-800">
        <div class="flex justify-between"><span>Subtotal</span><strong><?= rupiah($sale['subtotal']) ?></strong></div>
        <div class="flex justify-between"><span>Diskon</span><strong><?= rupiah($sale['discount_amount'] ?? 0) ?></strong></div>
        <div class="flex justify-between"><span>PPN</span><strong><?= rupiah($sale['tax_amount'] ?? 0) ?></strong></div>
        <div class="flex justify-between"><span>Metode</span><strong><?= e($sale['payment_method'] ?? 'Tunai') ?></strong></div>
        <div class="flex justify-between text-gray-900 dark:text-white"><span>Total</span><strong><?= rupiah(($sale['total_amount'] ?? 0) ?: $sale['subtotal']) ?></strong></div>
        <div class="flex justify-between"><span>Bayar</span><strong><?= rupiah($sale['paid_amount']) ?></strong></div>
        <div class="flex justify-between text-lg text-gray-900 dark:text-white"><span>Kembali</span><strong><?= rupiah($sale['change_amount']) ?></strong></div>
    </div>
</div>
<?php
});
