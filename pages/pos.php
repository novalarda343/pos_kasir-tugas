<?php

$products = db()->query('
    SELECT p.id, p.sku, p.name, p.image_path, p.selling_price, p.stock, p.category_id, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.stock > 0
    ORDER BY c.name, p.name
')->fetchAll();
$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$imagePool = [
    'build/src/images/product/product-01.jpg',
    'build/src/images/product/product-02.jpg',
    'build/src/images/product/product-03.jpg',
    'build/src/images/product/product-04.jpg',
    'build/src/images/product/product-05.jpg',
];

foreach ($products as $index => $product) {
    $products[$index]['image'] = $product['image_path'] ?: $imagePool[$index % count($imagePool)];
    $products[$index]['selling_price'] = (float) $product['selling_price'];
    $products[$index]['stock'] = (int) $product['stock'];
    $products[$index]['category_id'] = (int) ($product['category_id'] ?? 0);
}

render_layout('Kasir', function () use ($products, $categories) {
?>
<form method="post" x-data="posKasir()" @keydown.f9.window.prevent="openPayment()" class="-m-2 grid min-h-[calc(100vh-130px)] grid-cols-1 gap-4 lg:grid-cols-[1fr_360px]">
    <input type="hidden" name="action" value="create_sale">
    <input type="hidden" name="discount_percent" :value="discountPercent">
    <input type="hidden" name="tax_enabled" :value="taxEnabled ? '1' : '0'">
    <input type="hidden" name="payment_method" :value="paymentMethod">
    <input type="hidden" name="paid_amount" :value="paid">

    <section class="min-w-0 rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
        <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Toko Sumber Rezeki</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih barang lalu tekan tombol tambah.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-600 dark:border-gray-700 dark:text-gray-300">
                    <span class="block text-gray-400">Waktu</span><?= e(date('H:i')) ?>
                </div>
                <div class="rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-600 dark:border-gray-700 dark:text-gray-300">
                    <span class="block text-gray-400">Kasir</span>Admin
                </div>
            </div>
        </div>

        <label class="relative mb-3 block">
            <span class="sr-only">Cari produk</span>
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"></path>
            </svg>
            <input type="search" x-model.debounce.200ms="search" class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Cari produk / SKU">
        </label>

        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            <button type="button" @click="activeCategory = 'all'" :class="activeCategory === 'all' ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="h-9 shrink-0 rounded-lg px-3 text-xs font-medium">Semua produk</button>
            <?php foreach ($categories as $category): ?>
                <button type="button" @click="activeCategory = '<?= e((string) $category['id']) ?>'" :class="activeCategory === '<?= e((string) $category['id']) ?>' ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="h-9 shrink-0 rounded-lg px-3 text-xs font-medium"><?= e($category['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 2xl:grid-cols-4">
            <template x-for="product in filteredProducts()" :key="product.id">
                <article class="group overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                        <img :src="product.image" :alt="product.name" class="h-full w-full object-cover">
                        <span class="absolute right-2 top-2 rounded-full bg-brand-500 px-2 py-1 text-[10px] font-medium text-white" x-text="product.category_name || 'Produk'"></span>
                    </div>
                    <div class="p-3">
                        <h3 class="line-clamp-2 min-h-[40px] text-sm font-semibold leading-5 text-gray-900 dark:text-white" x-text="product.name"></h3>
                        <p class="mt-1 text-[11px] text-gray-500" x-text="product.sku"></p>
                        <div class="mt-3 flex items-end justify-between gap-2">
                            <div>
                                <div class="text-sm font-semibold text-brand-600" x-text="formatRupiah(product.selling_price)"></div>
                                <div class="text-[11px] text-gray-500" x-text="`Stok ${product.stock}`"></div>
                            </div>
                            <button type="button" @click="addProduct(product)" class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700" aria-label="Tambah produk">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"></path></svg>
                            </button>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        <div x-show="filteredProducts().length === 0" class="rounded-lg border border-dashed border-gray-300 py-12 text-center text-sm text-gray-500 dark:border-gray-700">
            Produk tidak ditemukan.
        </div>
    </section>

    <aside class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800 lg:sticky lg:top-24 lg:h-[calc(100vh-150px)] lg:overflow-y-auto">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">Ringkasan Pembayaran</h2>
            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-300" x-text="`${items.length} item`"></span>
        </div>

        <label class="mb-4 block">
            <span class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Nama pelanggan</span>
            <input name="customer_name" value="Umum" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </label>

        <div class="mb-4 space-y-2">
            <p class="text-xs font-semibold uppercase text-gray-500">Item Dipilih</p>
            <template x-for="(item, index) in items" :key="item.id">
                <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                    <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="line-clamp-2 text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></div>
                            <div class="text-xs text-gray-500" x-text="`${item.sku} - ${formatRupiah(item.selling_price)}`"></div>
                        </div>
                        <button type="button" @click="removeItem(item.id)" class="text-red-500" aria-label="Hapus item">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5h6v2m-8 0 1 12h8l1-12"></path></svg>
                        </button>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-700">
                            <button type="button" @click="decrease(item)" class="h-8 w-8 text-gray-500">-</button>
                            <span class="w-8 text-center text-sm font-medium text-gray-800 dark:text-white" x-text="item.quantity"></span>
                            <button type="button" @click="increase(item)" class="h-8 w-8 text-gray-500">+</button>
                        </div>
                        <strong class="text-sm text-gray-900 dark:text-white" x-text="formatRupiah(item.selling_price * item.quantity)"></strong>
                    </div>
                </div>
            </template>
            <div x-show="items.length === 0" class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700">
                Keranjang masih kosong.
            </div>
        </div>

        <div class="space-y-3 border-t border-gray-100 pt-4 text-sm dark:border-gray-800">
            <div class="flex justify-between text-gray-600 dark:text-gray-300"><span>Subtotal</span><span x-text="formatRupiah(subtotal())"></span></div>
            <div class="flex items-center justify-between gap-3 text-gray-600 dark:text-gray-300">
                <span>Diskon</span>
                <div class="flex items-center gap-2">
                    <input type="number" x-model.number="discountPercent" min="0" max="100" class="h-8 w-16 rounded-lg border border-gray-200 px-2 text-right text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <span>%</span>
                </div>
            </div>
            <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                <span>PPN 11%</span>
                <button type="button" @click="taxEnabled = !taxEnabled" :class="taxEnabled ? 'bg-brand-500' : 'bg-gray-300'" class="relative h-5 w-9 rounded-full transition">
                    <span :class="taxEnabled ? 'translate-x-4' : 'translate-x-0.5'" class="absolute top-0.5 h-4 w-4 rounded-full bg-white transition"></span>
                </button>
            </div>
            <div class="flex justify-between text-lg font-semibold text-brand-600"><span>Total</span><span x-text="formatRupiah(total())"></span></div>
        </div>

        <div class="mt-4">
            <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Metode pembayaran</p>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="method in ['Tunai', 'QRIS', 'Debit']" :key="method">
                    <button type="button" @click="paymentMethod = method" :class="paymentMethod === method ? 'border-brand-500 bg-brand-50 text-brand-600 dark:bg-brand-500/10' : 'border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-300'" class="h-9 rounded-lg border text-xs font-medium" x-text="method"></button>
                </template>
            </div>
        </div>

        <button type="button" @click="openPayment()" :disabled="items.length === 0" class="mt-4 h-11 w-full rounded-lg bg-brand-600 text-sm font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">Bayar (F9)</button>
        <button type="button" @click="clearCart()" :disabled="items.length === 0" class="mt-2 h-10 w-full rounded-lg border border-red-200 text-sm font-medium text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-red-900 dark:hover:bg-red-950">Hapus Keranjang</button>
    </aside>

    <input type="hidden" name="print_receipt" :value="printReceipt ? '1' : '0'">

    <div x-show="paymentOpen" x-cloak class="fixed inset-0 z-99999 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="paymentOpen = false">
        <div class="w-full max-w-xl rounded-lg bg-white shadow-xl dark:bg-gray-900" @click.outside="paymentOpen = false">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">Pembayaran</h2>
                <button type="button" @click="paymentOpen = false" class="text-gray-500">x</button>
            </div>
            <div class="p-5">
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Pastikan jumlah bayar sudah sesuai sebelum menyimpan transaksi.</p>
                <div class="mb-4 flex items-center justify-between rounded-lg bg-brand-50 px-4 py-3 text-sm dark:bg-brand-500/10">
                    <span class="text-gray-600 dark:text-gray-300">Total yang harus dibayar</span>
                    <strong class="text-lg text-brand-600" x-text="formatRupiah(total())"></strong>
                </div>

                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Metode pembayaran</p>
                <div class="mb-4 grid grid-cols-3 gap-2">
                    <template x-for="method in ['Tunai', 'QRIS', 'Debit']" :key="method">
                        <button type="button" @click="paymentMethod = method" :class="paymentMethod === method ? 'border-brand-500 bg-brand-50 text-brand-600 dark:bg-brand-500/10' : 'border-gray-200 text-gray-600 dark:border-gray-700 dark:text-gray-300'" class="h-10 rounded-lg border text-sm font-medium" x-text="method"></button>
                    </template>
                </div>

                <label class="mb-3 block">
                    <span class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Jumlah bayar</span>
                    <input type="number" x-model.number="paid" min="0" class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </label>
                <div class="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <template x-for="amount in quickAmounts()" :key="amount">
                        <button type="button" @click="paid = amount" class="h-9 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800" x-text="formatRupiah(amount)"></button>
                    </template>
                </div>
                <div class="mb-5 flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Kembalian</span>
                    <strong x-text="formatRupiah(Math.max(0, paid - total()))"></strong>
                </div>
                <div class="grid gap-2 sm:grid-cols-2">
                    <button type="submit" @click="printReceipt = true" :disabled="paid < total()" class="h-11 rounded-lg bg-brand-600 text-sm font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan & Cetak Struk</button>
                    <button type="submit" @click="printReceipt = false" :disabled="paid < total()" class="h-11 rounded-lg border border-brand-500 text-sm font-semibold text-brand-600 hover:bg-brand-50 disabled:cursor-not-allowed disabled:opacity-50">Simpan tanpa cetak</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function posKasir() {
    const products = <?= json_encode($products, JSON_THROW_ON_ERROR) ?>;
    return {
        products,
        search: '',
        activeCategory: 'all',
        items: [],
        discountPercent: 0,
        taxEnabled: true,
        paymentMethod: 'Tunai',
        paid: 0,
        printReceipt: true,
        paymentOpen: false,
        filteredProducts() {
            const query = this.search.trim().toLowerCase();
            return this.products.filter((product) => {
                const matchCategory = this.activeCategory === 'all' || String(product.category_id) === String(this.activeCategory);
                const matchSearch = !query || `${product.name} ${product.sku}`.toLowerCase().includes(query);
                return matchCategory && matchSearch;
            });
        },
        addProduct(product) {
            const existing = this.items.find((item) => item.id === product.id);
            if (existing) {
                this.increase(existing);
            } else {
                this.items.push({ ...product, quantity: 1 });
            }
        },
        increase(item) {
            if (item.quantity < Number(item.stock)) item.quantity++;
        },
        decrease(item) {
            if (item.quantity > 1) item.quantity--;
        },
        removeItem(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
        clearCart() {
            this.items = [];
            this.paid = 0;
            this.paymentOpen = false;
        },
        subtotal() {
            return this.items.reduce((total, item) => total + (Number(item.selling_price) * Number(item.quantity || 0)), 0);
        },
        discountAmount() {
            return Math.round(this.subtotal() * (Math.min(100, Math.max(0, Number(this.discountPercent || 0))) / 100));
        },
        taxAmount() {
            const base = Math.max(0, this.subtotal() - this.discountAmount());
            return this.taxEnabled ? Math.round(base * 0.11) : 0;
        },
        total() {
            return Math.max(0, this.subtotal() - this.discountAmount() + this.taxAmount());
        },
        openPayment() {
            if (!this.items.length) return;
            this.paid = Math.max(this.paid, this.total());
            this.paymentOpen = true;
        },
        quickAmounts() {
            const total = this.total();
            const rounded = Math.ceil(total / 50000) * 50000 || 50000;
            return [total, rounded, rounded + 50000, rounded + 100000].filter((value, index, arr) => arr.indexOf(value) === index);
        },
        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
        },
    };
}
</script>
<?php
});
