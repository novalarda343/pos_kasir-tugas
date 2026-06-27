<?php

$editId = (int) ($_GET['edit'] ?? 0);
$editProduct = null;
if ($editId > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}
$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$products = db()->query('SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.name')->fetchAll();

render_layout('Produk', function () use ($products, $categories, $editProduct) {
$fallbackImage = 'build/src/images/product/product-01.jpg';
?>
<div class="grid grid-cols-1 gap-6 2xl:grid-cols-[420px_1fr]">
    <form method="post" enctype="multipart/form-data" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <input type="hidden" name="action" value="save_product">
        <input type="hidden" name="id" value="<?= e((string) ($editProduct['id'] ?? 0)) ?>">
        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white"><?= $editProduct ? 'Edit Produk' : 'Tambah Produk' ?></h2>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Foto produk</span>
                <div class="flex items-center gap-4">
                    <img src="<?= e($editProduct['image_path'] ?? $fallbackImage) ?>" alt="Foto produk" class="h-24 w-24 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                    <div class="min-w-0 flex-1">
                        <input type="file" name="product_image" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-brand-500/10 dark:file:text-brand-300">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Format JPG, PNG, GIF, atau WEBP. Maksimal 2MB.</p>
                        <?php if (!empty($editProduct['image_path'])): ?>
                            <label class="mt-3 flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                                Hapus foto saat menyimpan
                            </label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">SKU</span><input name="sku" value="<?= e($editProduct['sku'] ?? '') ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" required></label>
            <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Nama produk</span><input name="name" value="<?= e($editProduct['name'] ?? '') ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" required></label>
            <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Kategori</span><select name="category_id" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="">Tanpa kategori</option><?php foreach ($categories as $category): ?><option value="<?= e((string) $category['id']) ?>" <?= (int) ($editProduct['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></label>
            <div class="grid grid-cols-2 gap-3">
                <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Harga beli</span><input type="number" name="purchase_price" value="<?= e((string) ($editProduct['purchase_price'] ?? 0)) ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" min="0"></label>
                <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Harga jual</span><input type="number" name="selling_price" value="<?= e((string) ($editProduct['selling_price'] ?? 0)) ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" min="0"></label>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Stok</span><input type="number" name="stock" value="<?= e((string) ($editProduct['stock'] ?? 0)) ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" min="0"></label>
                <label class="block"><span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Min stok</span><input type="number" name="min_stock" value="<?= e((string) ($editProduct['min_stock'] ?? 5)) ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" min="0"></label>
            </div>
        </div>
        <button class="mt-4 h-11 w-full rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600" type="submit">Simpan Produk</button>
    </form>

    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">Daftar Produk</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400"><tr><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Kategori</th><th class="px-5 py-3">Harga</th><th class="px-5 py-3">Stok</th><th class="px-5 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php foreach ($products as $product): ?>
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="<?= e($product['image_path'] ?: $fallbackImage) ?>" alt="<?= e($product['name']) ?>" class="h-12 w-12 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white"><?= e($product['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= e($product['sku']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3"><?= e($product['category_name'] ?? '-') ?></td>
                            <td class="px-5 py-3"><?= rupiah($product['selling_price']) ?></td>
                            <td class="px-5 py-3"><?= e((string) $product['stock']) ?></td>
                            <td class="px-5 py-3"><div class="flex gap-2"><a class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs dark:border-gray-700" href="index.php?page=products&edit=<?= e((string) $product['id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Hapus produk ini?')"><input type="hidden" name="action" value="delete_product"><input type="hidden" name="id" value="<?= e((string) $product['id']) ?>"><button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 dark:border-red-900" type="submit">Hapus</button></form></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
});
