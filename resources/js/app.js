import './bootstrap';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle'); // Tombol Hamburger
    const sidebarClose = document.getElementById('sidebarClose');   // Tombol X (BARU)
    const body = document.body;

    // 1. BUKA Sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            body.classList.add('sidebar-open');
        });
    }

    // 2. TUTUP Sidebar (Klik Tombol X)
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function (e) {
            e.preventDefault();
            body.classList.remove('sidebar-open');
        });
    }

    // 3. TUTUP Sidebar (Klik Overlay / Layar Gelap)
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function () {
            body.classList.remove('sidebar-open');
        });
    }
});