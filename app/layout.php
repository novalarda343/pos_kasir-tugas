<?php

declare(strict_types=1);

function render_layout(string $title, callable $content): void
{
    $flash = flash();
    $page = current_page();
    $menus = [
        ['pos', 'Kasir', 'M4 7h16v10H4z M8 7V5h8v2'],
        ['products', 'Barang & Stok', 'M20 7l-8-4-8 4 8 4 8-4z M4 7v10l8 4 8-4V7'],
        ['sales', 'Riwayat Transaksi', 'M6 3h12v18l-3-2-3 2-3-2-3 2z'],
        ['dashboard', 'Laporan', 'M3 12h18M3 6h18M3 18h18'],
        ['categories', 'Kategori', 'M4 4h7v7H4z M13 4h7v7h-7z M4 13h7v7H4z M13 13h7v7h-7z'],
        ['stock', 'Pengaturan Stok', 'M12 3v18M5 8h14M5 16h14'],
    ];
    ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | POS Kasir</title>
    <link rel="icon" href="build/favicon.ico">
    <link rel="stylesheet" href="build/style.css">
</head>
<body x-data="{ loaded: false, sidebarToggle: false, darkMode: JSON.parse(localStorage.getItem('darkMode') || 'false') }"
      x-init="$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
      :class="{ 'dark bg-gray-900': darkMode }">
    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">
        <aside :class="sidebarToggle ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed left-0 top-0 z-9999 flex h-screen w-[238px] flex-col border-r border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-black lg:static">
            <div class="flex items-center justify-between pb-6 pt-6">
                <a href="index.php?page=pos" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-600 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16v10H4zM8 7V5h8v2"></path></svg>
                    </span>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Pos System</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Sumber Rezeki</div>
                    </div>
                </a>
                <button class="lg:hidden" @click="sidebarToggle = false" type="button">x</button>
            </div>

            <nav class="flex flex-col gap-1">
                <?php foreach ($menus as [$key, $label, $path]): ?>
                    <a href="index.php?page=<?= e($key) ?>" class="<?= is_active($key) ?> group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($path) ?>"></path>
                        </svg>
                        <span><?= e($label) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="mt-auto space-y-2 pb-5 text-sm">
                <button class="flex w-full items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-gray-600 dark:border-gray-800 dark:text-gray-300" @click="darkMode = !darkMode" type="button">
                    <span>Mode Tampilan</span>
                    <span class="text-xs">Auto</span>
                </button>
                <a href="index.php?page=pos" class="flex items-center gap-2 rounded-lg px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-950">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m6-6-6 6 6 6M21 4v16"></path></svg>
                    Keluar
                </a>
            </div>
        </aside>

        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <header class="sticky top-0 z-999 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex grow items-center justify-between px-4 py-4 md:px-6 2xl:px-10">
                    <div class="flex items-center gap-3">
                        <button class="rounded-lg border border-gray-200 p-2 text-gray-600 dark:border-gray-700 dark:text-gray-300 lg:hidden" @click="sidebarToggle = true" type="button">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white"><?= e($title) ?></h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?= date('d M Y H:i') ?></p>
                        </div>
                    </div>
                    <div class="hidden items-center gap-3 text-sm text-gray-600 dark:text-gray-300 sm:flex">
                        <div class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">
                            <span class="block text-xs text-gray-400">Waktu</span><?= e(date('H:i')) ?>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">
                            <span class="block text-xs text-gray-400">User</span>Admin
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 md:p-5 2xl:p-6">
                <?php if ($flash): ?>
                    <div class="mb-5 rounded-lg border px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300' : 'border-green-200 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300' ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>
                <?php $content(); ?>
            </main>
        </div>
    </div>
    <script src="build/bundle.js"></script>
</body>
</html>
<?php
}
