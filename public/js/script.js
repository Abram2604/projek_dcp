//import './bootstrap';
//import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

document.addEventListener("DOMContentLoaded", function() {
    
    // Ambil Elemen
    const sidebar = document.querySelector('.sidebar-wrapper');
    const overlay = document.querySelector('.sidebar-overlay');
    const btnToggle = document.getElementById('sidebarToggle'); // Tombol Garis Tiga di Navbar
    const btnClose = document.getElementById('sidebarClose');   // Tombol X di Sidebar

    // Fungsi Buka Sidebar
    if (btnToggle) {
        btnToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.add('show');
            overlay.classList.add('show');
        });
    }

    // Fungsi Tutup Sidebar (Klik Tombol X)
    if (btnClose) {
        btnClose.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Fungsi Tutup Sidebar (Klik Area Gelap / Overlay)
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
});