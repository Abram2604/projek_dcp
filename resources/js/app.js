import './bootstrap';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Definisi Elemen
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;
    
    // 2. Buat Elemen Overlay Secara Dinamis
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    body.appendChild(overlay);

    // 3. Logic Tombol Hamburger
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            // Kita pakai class 'sidebar-open' agar konsisten dengan SCSS
            body.classList.toggle('sidebar-open'); 
        });
    }

    // 4. Logic Tutup Sidebar saat klik Overlay (Background gelap)
    overlay.addEventListener('click', function () {
        body.classList.remove('sidebar-open');
    });
});