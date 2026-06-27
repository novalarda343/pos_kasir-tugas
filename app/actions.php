<?php

declare(strict_types=1);

function handle_request(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $action = $_POST['action'] ?? '';

    try {
        match ($action) {
            'save_category' => save_category(),
            'delete_category' => delete_category(),
            'save_product' => save_product(),
            'delete_product' => delete_product(),
            'adjust_stock' => adjust_stock(),
            'create_sale' => create_sale(),
            default => null,
        };
    } catch (Throwable $exception) {
        try {
            $pdo = db();
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        } catch (Throwable) {
            // Keep the original error visible when the database connection itself fails.
        }
        flash($exception->getMessage(), 'error');
        redirect(current_page());
    }
}

function save_category(): never
{
    $name = trim((string) post_value('name'));
    if ($name === '') {
        throw new RuntimeException('Nama kategori wajib diisi.');
    }

    if ((int) post_value('id', 0) > 0) {
        $stmt = db()->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, (int) post_value('id')]);
    } else {
        $stmt = db()->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
    }

    flash('Kategori berhasil disimpan.');
    redirect('categories');
}

function delete_category(): never
{
    $stmt = db()->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([(int) post_value('id')]);
    flash('Kategori berhasil dihapus.');
    redirect('categories');
}

function save_product(): never
{
    $pdo = db();
    $name = trim((string) post_value('name'));
    $sku = trim((string) post_value('sku'));
    $categoryId = post_value('category_id') !== '' ? (int) post_value('category_id') : null;
    $purchasePrice = max(0, (float) post_value('purchase_price', 0));
    $sellingPrice = max(0, (float) post_value('selling_price', 0));
    $stock = max(0, (int) post_value('stock', 0));
    $minStock = max(0, (int) post_value('min_stock', 0));
    $productId = (int) post_value('id', 0);

    if ($name === '' || $sku === '') {
        throw new RuntimeException('Nama produk dan SKU wajib diisi.');
    }

    $currentImage = null;
    if ($productId > 0) {
        $stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $currentImage = $stmt->fetchColumn() ?: null;
    }

    $uploadedImage = upload_product_image();
    $imagePath = $currentImage;
    if ($uploadedImage !== null) {
        delete_product_image($currentImage);
        $imagePath = $uploadedImage;
    } elseif (post_value('remove_image', '0') === '1') {
        delete_product_image($currentImage);
        $imagePath = null;
    }

    if ($productId > 0) {
        $stmt = $pdo->prepare(
            'UPDATE products SET category_id = ?, sku = ?, name = ?, image_path = ?, purchase_price = ?, selling_price = ?, stock = ?, min_stock = ? WHERE id = ?'
        );
        $stmt->execute([$categoryId, $sku, $name, $imagePath, $purchasePrice, $sellingPrice, $stock, $minStock, $productId]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, sku, name, image_path, purchase_price, selling_price, stock, min_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$categoryId, $sku, $name, $imagePath, $purchasePrice, $sellingPrice, $stock, $minStock]);
    }

    flash('Produk berhasil disimpan.');
    redirect('products');
}

function delete_product(): never
{
    $pdo = db();
    $productId = (int) post_value('id');

    $stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $imagePath = $stmt->fetchColumn() ?: null;

    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    delete_product_image($imagePath);

    flash('Produk berhasil dihapus.');
    redirect('products');
}

function upload_product_image(): ?string
{
    if (!isset($_FILES['product_image']) || !is_array($_FILES['product_image'])) {
        return null;
    }

    $file = $_FILES['product_image'];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload foto produk gagal.');
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran foto produk maksimal 2MB.');
    }

    $imageInfo = @getimagesize((string) $file['tmp_name']);
    if ($imageInfo === false) {
        throw new RuntimeException('File foto produk harus berupa gambar.');
    }

    $extensions = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];

    $extension = $extensions[$imageInfo[2] ?? 0] ?? null;
    if ($extension === null) {
        throw new RuntimeException('Format foto harus JPG, PNG, GIF, atau WEBP.');
    }

    $uploadDir = __DIR__ . '/../uploads/products';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $filename = 'product-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        throw new RuntimeException('Foto produk tidak bisa disimpan.');
    }

    return 'uploads/products/' . $filename;
}

function delete_product_image(?string $imagePath): void
{
    if (!$imagePath || !str_starts_with($imagePath, 'uploads/products/')) {
        return;
    }

    $fullPath = realpath(__DIR__ . '/../' . $imagePath);
    $uploadRoot = realpath(__DIR__ . '/../uploads/products');

    if ($fullPath && $uploadRoot && str_starts_with($fullPath, $uploadRoot) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

function adjust_stock(): never
{
    $productId = (int) post_value('product_id');
    $type = post_value('type') === 'out' ? 'out' : 'in';
    $quantity = max(1, (int) post_value('quantity', 1));
    $note = trim((string) post_value('note'));

    $pdo = db();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    if ($type === 'out' && (int) $product['stock'] < $quantity) {
        throw new RuntimeException('Stok tidak cukup untuk pengurangan.');
    }

    $delta = $type === 'in' ? $quantity : -$quantity;
    $stmt = $pdo->prepare('UPDATE products SET stock = stock + ? WHERE id = ?');
    $stmt->execute([$delta, $productId]);

    $stmt = $pdo->prepare('INSERT INTO stock_movements (product_id, type, quantity, note) VALUES (?, ?, ?, ?)');
    $stmt->execute([$productId, $type, $quantity, $note]);

    $pdo->commit();
    flash('Stok berhasil diperbarui.');
    redirect('stock');
}

function create_sale(): never
{
    $items = $_POST['items'] ?? [];
    $paid = max(0, (float) post_value('paid_amount', 0));
    $customer = trim((string) post_value('customer_name', 'Umum'));
    $printReceipt = post_value('print_receipt', '1') === '1';
    $discountPercent = min(100, max(0, (float) post_value('discount_percent', 0)));
    $taxEnabled = post_value('tax_enabled', '0') === '1';
    $paymentMethod = in_array(post_value('payment_method', 'Tunai'), ['Tunai', 'QRIS', 'Debit'], true)
        ? (string) post_value('payment_method', 'Tunai')
        : 'Tunai';

    $cleanItems = [];
    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity = max(0, (int) ($item['quantity'] ?? 0));
        if ($productId > 0 && $quantity > 0) {
            $cleanItems[] = ['product_id' => $productId, 'quantity' => $quantity];
        }
    }

    if ($cleanItems === []) {
        throw new RuntimeException('Tambahkan minimal satu item transaksi.');
    }

    $pdo = db();
    $pdo->beginTransaction();

    $saleNumber = 'TRX-' . date('Ymd-His');
    $subtotal = 0;
    $saleItems = [];

    foreach ($cleanItems as $item) {
        $stmt = $pdo->prepare('SELECT id, name, selling_price, stock FROM products WHERE id = ? FOR UPDATE');
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new RuntimeException('Produk transaksi tidak ditemukan.');
        }

        if ((int) $product['stock'] < $item['quantity']) {
            throw new RuntimeException('Stok ' . $product['name'] . ' tidak cukup.');
        }

        $lineTotal = (float) $product['selling_price'] * $item['quantity'];
        $subtotal += $lineTotal;
        $saleItems[] = [
            'product_id' => (int) $product['id'],
            'quantity' => $item['quantity'],
            'price' => (float) $product['selling_price'],
            'total' => $lineTotal,
        ];
    }

    $discountAmount = round($subtotal * ($discountPercent / 100));
    $taxBase = max(0, $subtotal - $discountAmount);
    $taxAmount = $taxEnabled ? round($taxBase * 0.11) : 0;
    $totalAmount = $taxBase + $taxAmount;

    if ($paid < $totalAmount) {
        throw new RuntimeException('Jumlah bayar kurang dari total transaksi.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO sales (sale_number, customer_name, subtotal, discount_percent, discount_amount, tax_enabled, tax_amount, total_amount, payment_method, paid_amount, change_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $saleNumber,
        $customer !== '' ? $customer : 'Umum',
        $subtotal,
        $discountPercent,
        $discountAmount,
        $taxEnabled ? 1 : 0,
        $taxAmount,
        $totalAmount,
        $paymentMethod,
        $paid,
        $paid - $totalAmount,
    ]);
    $saleId = (int) $pdo->lastInsertId();

    foreach ($saleItems as $item) {
        $stmt = $pdo->prepare('INSERT INTO sale_items (sale_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$saleId, $item['product_id'], $item['quantity'], $item['price'], $item['total']]);

        $stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
        $stmt->execute([$item['quantity'], $item['product_id']]);

        $stmt = $pdo->prepare('INSERT INTO stock_movements (product_id, type, quantity, note) VALUES (?, "out", ?, ?)');
        $stmt->execute([$item['product_id'], $item['quantity'], 'Penjualan ' . $saleNumber]);
    }

    $pdo->commit();
    flash('Transaksi berhasil disimpan.');
    redirect($printReceipt ? 'receipt' : 'sales', $printReceipt ? ['id' => $saleId] : []);
}
