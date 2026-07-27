import './bootstrap.js';

const INDONESIAN_LOCALE = 'id-ID';
const DATE_OPTIONS = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
};
const DATE_TIME_OPTIONS = {
    ...DATE_OPTIONS,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
};
const RUPIAH_FORMATTER = new Intl.NumberFormat(INDONESIAN_LOCALE, {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

/**
 * Mengembalikan true saat kode berjalan di browser.
 */
function hasBrowserRuntime() {
    return typeof window !== 'undefined' && typeof document !== 'undefined';
}

/**
 * Memformat tanggal ke tampilan Indonesia.
 */
export function formatTanggalIndonesia(date) {
    return new Date(date).toLocaleDateString(INDONESIAN_LOCALE, DATE_OPTIONS);
}

/**
 * Memformat tanggal dan waktu ke tampilan Indonesia.
 */
export function formatTanggalWaktuIndonesia(date) {
    return new Date(date).toLocaleString(INDONESIAN_LOCALE, DATE_TIME_OPTIONS);
}

/**
 * Memformat angka menjadi mata uang Rupiah.
 */
export function formatRupiah(angka) {
    return RUPIAH_FORMATTER.format(angka);
}

/**
 * Membuat opsi dasar IMask untuk input numerik agar tidak duplikatif.
 */
export function createNumberMaskOptions(overrides = {}) {
    return {
        mask: Number,
        scale: 2,
        thousandsSeparator: '.',
        padFractionalZeros: false,
        normalizeZeros: true,
        radix: ',',
        mapToRadix: ['.'],
        autofix: true,
        ...overrides,
    };
}

/**
 * Menerapkan IMask ke seluruh elemen yang cocok dengan selector tertentu.
 */
export function applyMaskToSelector(selector, options, root = document) {
    if (!root?.querySelectorAll || typeof globalThis.IMask !== 'function') {
        return [];
    }

    return Array.from(root.querySelectorAll(selector)).map((element) => globalThis.IMask(element, options));
}

/**
 * Menginisialisasi seluruh input mask numerik yang digunakan aplikasi.
 */
export function initInputMasks(root = document) {
    return {
        currency: applyMaskToSelector('.currency', createNumberMaskOptions(), root),
        persen: applyMaskToSelector('.persen', createNumberMaskOptions({ min: 0, max: 100 }), root),
    };
}

/**
 * Menginisialisasi perilaku drawer Metronic sederhana berbasis toggle class.
 */
export function initDrawers(root = document) {
    const drawers = root.querySelectorAll('[data-kt-drawer]');

    drawers.forEach((drawer) => {
        const toggles = root.querySelectorAll(`[data-kt-drawer-toggle="#${drawer.id}"]`);

        toggles.forEach((toggle) => {
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                drawer.classList.toggle('hidden');
                drawer.classList.toggle('block');
            });
        });
    });
}

/**
 * Menginisialisasi dropdown menu Metronic sederhana.
 */
export function initMenus(root = document) {
    const menus = root.querySelectorAll('[data-kt-menu="true"]');

    menus.forEach((menu) => {
        const items = menu.querySelectorAll('[data-kt-menu-item-toggle="dropdown"]');

        items.forEach((item) => {
            const trigger = item.querySelector('[data-kt-menu-item-trigger="click"], [data-kt-menu-item-trigger="click|lg:hover"]');
            const dropdown = item.querySelector('.kt-menu-dropdown');

            if (trigger && dropdown) {
                trigger.addEventListener('click', (event) => {
                    event.preventDefault();
                    dropdown.classList.toggle('hidden');
                });
            }
        });
    });
}

/**
 * Menginisialisasi elemen sticky berdasarkan offset scroll.
 */
export function initStickyHeaders(root = document, targetWindow = window) {
    const stickyElements = root.querySelectorAll('[data-kt-sticky="true"]');

    stickyElements.forEach((element) => {
        const stickyClass = element.getAttribute('data-kt-sticky-class') || 'kt-sticky';
        const offset = parseInt(element.getAttribute('data-kt-sticky-offset'), 10) || 0;

        targetWindow.addEventListener('scroll', () => {
            if (targetWindow.scrollY > offset) {
                element.classList.add(...stickyClass.split(' '));
                return;
            }

            element.classList.remove(...stickyClass.split(' '));
        });
    });
}

/**
 * Menginisialisasi toggle modal berbasis selector target.
 */
export function initModals(root = document) {
    const modalToggles = root.querySelectorAll('[data-kt-modal-toggle]');

    modalToggles.forEach((toggle) => {
        toggle.addEventListener('click', function onModalToggle(event) {
            event.preventDefault();

            const modalId = this.getAttribute('data-kt-modal-toggle');
            const modal = root.querySelector(modalId);

            if (modal) {
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }
        });
    });
}

/**
 * Menutup modal ketika backdrop modal diklik.
 */
export function closeModalsOnBackdropClick(event, root = document) {
    const modals = root.querySelectorAll('.kt-modal');

    modals.forEach((modal) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
}

/**
 * Menjalankan seluruh inisialisasi JavaScript utama aplikasi.
 */
export function initApplication(root = document, targetWindow = window) {
    initDrawers(root);
    initMenus(root);
    initStickyHeaders(root, targetWindow);
    initModals(root);
    initInputMasks(root);
}

/**
 * Mendaftarkan utilitas global ke window untuk kompatibilitas kode existing.
 */
export function registerGlobalHelpers(targetWindow = window) {
    targetWindow.formatTanggalIndonesia = formatTanggalIndonesia;
    targetWindow.formatTanggalWaktuIndonesia = formatTanggalWaktuIndonesia;
    targetWindow.formatRupiah = formatRupiah;
    targetWindow.MetronicCore = {
        initDrawers,
        initMenus,
        initStickyHeaders,
        initModals,
        initInputMasks,
        formatTanggalIndonesia,
        formatTanggalWaktuIndonesia,
        formatRupiah,
        createNumberMaskOptions,
        applyMaskToSelector,
        closeModalsOnBackdropClick,
        initApplication,
    };
}

/**
 * Menjalankan Alpine secara dinamis agar file tetap aman diimpor dari test Node.
 */
async function startAlpine() {
    const { default: Alpine } = await import('alpinejs');

    window.Alpine = Alpine;
    Alpine.start();
}

if (hasBrowserRuntime()) {
    registerGlobalHelpers(window);
    void startAlpine();

    document.addEventListener('DOMContentLoaded', () => {
        initApplication(document, window);
    });

    document.addEventListener('click', (event) => {
        closeModalsOnBackdropClick(event, document);
    });
}
