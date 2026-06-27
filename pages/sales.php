<?php

$sales = db()->query('SELECT * FROM sales ORDER BY created_at DESC LIMIT 100')->fetchAll();

render_layout('Transaksi', function () use ($sales) {
?>
<div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
        <h2 class="font-semibold text-gray-900 dark:text-white">Daftar Transaksi</h2>
        <a href="index.php?page=pos" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Transaksi Baru</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400"><tr><th class="px-5 py-3">No Transaksi</th><th class="px-5 py-3">Pelanggan</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Bayar</th><th class="px-5 py-3">Kembali</th><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <?php foreach ($sales as $sale): ?>
                    <tr class="text-gray-700 dark:text-gray-300"><td class="px-5 py-3 font-medium text-gray-900 dark:text-white"><?= e($sale['sale_number']) ?></td><td class="px-5 py-3"><?= e($sale['customer_name']) ?></td><td class="px-5 py-3"><?= rupiah($sale['total_amount'] ?: $sale['subtotal']) ?></td><td class="px-5 py-3"><?= rupiah($sale['paid_amount']) ?></td><td class="px-5 py-3"><?= rupiah($sale['change_amount']) ?></td><td class="px-5 py-3"><?= e(date('d/m/Y H:i', strtotime($sale['created_at']))) ?></td><td class="px-5 py-3"><a class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs dark:border-gray-700" href="index.php?page=receipt&id=<?= e((string) $sale['id']) ?>">Struk</a></td></tr>
                <?php endforeach; ?>
                <?php if (!$sales): ?><tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">Belum ada transaksi.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
});
