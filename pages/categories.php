<?php

$editId = (int) ($_GET['edit'] ?? 0);
$editCategory = null;
if ($editId > 0) {
    $stmt = db()->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch();
}
$categories = db()->query('SELECT c.*, COUNT(p.id) product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name')->fetchAll();

render_layout('Kategori', function () use ($categories, $editCategory) {
?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[360px_1fr]">
    <form method="post" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <input type="hidden" name="action" value="save_category">
        <input type="hidden" name="id" value="<?= e((string) ($editCategory['id'] ?? 0)) ?>">
        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white"><?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
        <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Nama kategori</label>
        <input name="name" value="<?= e($editCategory['name'] ?? '') ?>" class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" required>
        <button class="mt-4 h-11 w-full rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600" type="submit">Simpan</button>
    </form>

    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">Daftar Kategori</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500 dark:bg-gray-800/50 dark:text-gray-400"><tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php foreach ($categories as $category): ?>
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-5 py-3 font-medium"><?= e($category['name']) ?></td>
                            <td class="px-5 py-3"><?= e((string) $category['product_count']) ?></td>
                            <td class="px-5 py-3">
                                <div class="flex gap-2">
                                    <a class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs dark:border-gray-700" href="index.php?page=categories&edit=<?= e((string) $category['id']) ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus kategori ini?')">
                                        <input type="hidden" name="action" value="delete_category"><input type="hidden" name="id" value="<?= e((string) $category['id']) ?>">
                                        <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 dark:border-red-900" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
});
