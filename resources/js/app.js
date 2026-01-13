import './bootstrap';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery; 

// Logic untuk Toggle Sidebar di Mobile
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    
    // Pasang overlay ke body
    body.appendChild(overlay);

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            body.classList.toggle('sidebar-toggled');
        });
    }

    // Tutup sidebar jika overlay diklik (Mobile)
    overlay.addEventListener('click', function () {
        body.classList.remove('sidebar-toggled');
    });
});