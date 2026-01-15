import tkinter as tk
from tkinter import ttk, scrolledtext, messagebox
import os
import re
from datetime import datetime

# --- KONFIGURASI FOLDER ---
MIGRATION_DIR = 'database/migrations'

# --- LOGIC UTAMA ---

def get_timestamp():
    return datetime.now().strftime('%Y_%m_%d_%H%M%S')

def sanitize_filename(name):
    # Bersihkan nama untuk jadi nama file
    name = name.lower().strip()
    name = re.sub(r'[^a-z0-9_\s]', '', name)
    name = re.sub(r'\s+', '_', name)
    return name

def extract_table_name_from_sql(sql):
    # Coba tebak nama tabel dari query CREATE TABLE
    pattern = re.compile(r'CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?', re.IGNORECASE)
    match = pattern.search(sql)
    return match.group(1) if match else None

def tpl_raw_sql(up_sql, down_sql):
    return f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{{
    public function up(): void
    {{
        DB::unprepared(<<<'SQL'
{up_sql}
SQL
        );
    }}

    public function down(): void
    {{
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
{down_sql}
SQL
        );
    }}
}};
"""

# --- GUI CLASS ---

class MigrationApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Migration Generator (Raw SQL Only)")
        self.root.geometry("600x700")
        
        # Style Setting
        style = ttk.Style()
        style.theme_use('clam')

        # --- 1. Tipe & Nama ---
        frame_top = ttk.Frame(root, padding=15)
        frame_top.pack(fill="x")

        # Dropdown Tipe
        ttk.Label(frame_top, text="Tipe Perubahan:").pack(anchor="w")
        self.migration_type = tk.StringVar()
        types = [
            ("Buat Tabel Baru (Create Table)", "create_table"),
            ("Tambah/Edit Kolom (Alter Table)", "alter_table"),
            ("Stored Procedure (SP)", "sp"),
            ("Insert Data / Lainnya", "custom")
        ]
        self.map_types = {t[0]: t[1] for t in types} # Mapping label ke key
        
        self.cb_type = ttk.Combobox(frame_top, textvariable=self.migration_type, values=[t[0] for t in types], state="readonly", font=("Segoe UI", 10))
        self.cb_type.current(0)
        self.cb_type.pack(fill="x", pady=(5, 15))
        self.cb_type.bind("<<ComboboxSelected>>", self.on_type_change)

        # Input Nama Objek
        self.lbl_name = ttk.Label(frame_top, text="Nama Tabel:")
        self.lbl_name.pack(anchor="w")
        
        self.entry_name = ttk.Entry(frame_top, font=("Segoe UI", 10))
        self.entry_name.pack(fill="x", pady=5)
        
        # --- 2. Editor SQL ---
        frame_code = ttk.LabelFrame(root, text="Paste Query SQL Disini", padding=10)
        frame_code.pack(fill="both", expand=True, padx=15, pady=5)

        self.txt_code = scrolledtext.ScrolledText(frame_code, height=10, font=("Consolas", 10))
        self.txt_code.pack(fill="both", expand=True)

        # --- 3. Tombol Aksi ---
        frame_btn = ttk.Frame(root, padding=15)
        frame_btn.pack(fill="x")

        # Tombol Generate Besar
        btn_gen = tk.Button(frame_btn, text="GENERATE FILE MIGRASI", bg="#2563eb", fg="white", font=("Segoe UI", 10, "bold"), command=self.generate, relief="flat", padx=20, pady=10)
        btn_gen.pack(side="left", fill="x", expand=True, padx=(0, 10))

        # Tombol Clear
        btn_clr = tk.Button(frame_btn, text="RESET FORM", bg="#64748b", fg="white", font=("Segoe UI", 10), command=self.clear_form, relief="flat", padx=15, pady=10)
        btn_clr.pack(side="right")

        # --- 4. Status Bar ---
        self.lbl_status = ttk.Label(root, text="Siap...", relief="sunken", anchor="w", padding=5)
        self.lbl_status.pack(fill="x")

    def on_type_change(self, event):
        """Ubah label berdasarkan pilihan dropdown"""
        selected = self.migration_type.get()
        mode = self.map_types.get(selected)

        if mode == "create_table":
            self.lbl_name.config(text="Nama Tabel (Kosongkan jika ingin auto-detect dari query):")
        elif mode == "sp":
            self.lbl_name.config(text="Nama Procedure (Wajib):")
        elif mode == "alter_table":
            self.lbl_name.config(text="Nama Tabel Target (Wajib):")
        else:
            self.lbl_name.config(text="Topik / Keterangan Singkat (Untuk nama file):")

    def clear_form(self):
        self.entry_name.delete(0, tk.END)
        self.txt_code.delete("1.0", tk.END)
        self.lbl_status.config(text="Form dibersihkan.", foreground="black")

    def generate(self):
        if not os.path.exists(MIGRATION_DIR):
            messagebox.showerror("Error", f"Folder {MIGRATION_DIR} tidak ditemukan!")
            return

        selected = self.migration_type.get()
        mode = self.map_types.get(selected)
        name_input = self.entry_name.get().strip()
        sql_input = self.txt_code.get("1.0", tk.END).strip()

        if not sql_input:
            messagebox.showwarning("Peringatan", "Query SQL tidak boleh kosong!")
            return

        # --- Logic Pembuatan File ---
        timestamp = get_timestamp()
        final_filename = ""
        up_sql = sql_input
        down_sql = "" # Default kosong

        # 1. CREATE TABLE
        if mode == "create_table":
            # Coba deteksi nama tabel jika input kosong
            if not name_input:
                detected = extract_table_name_from_sql(sql_input)
                if detected:
                    name_input = detected
                else:
                    messagebox.showerror("Error", "Nama tabel tidak terdeteksi. Harap isi kolom Nama Tabel.")
                    return
            
            clean_name = sanitize_filename(name_input)
            final_filename = f"{timestamp}_create_{clean_name}_table.php"
            down_sql = f"DROP TABLE IF EXISTS `{name_input}`;"

        # 2. ALTER TABLE
        elif mode == "alter_table":
            if not name_input:
                messagebox.showerror("Error", "Nama tabel target harus diisi!")
                return
            clean_name = sanitize_filename(name_input)
            final_filename = f"{timestamp}_modify_{clean_name}_table.php"
            down_sql = f"-- Tulis query rollback manual disini (misal: DROP COLUMN...)"

        # 3. STORED PROCEDURE
        elif mode == "sp":
            if not name_input:
                messagebox.showerror("Error", "Nama Procedure harus diisi!")
                return
            
            # Bersihkan Delimiter
            up_sql = re.sub(r'DELIMITER\s*\$\$', '', up_sql, flags=re.IGNORECASE)
            up_sql = re.sub(r'\$\$\s*DELIMITER\s*;', '', up_sql, flags=re.IGNORECASE)
            up_sql = up_sql.replace('$$', '')

            # Tambah DROP IF EXISTS otomatis di atas create
            up_sql = f"DROP PROCEDURE IF EXISTS `{name_input}`;\n{up_sql}"
            down_sql = f"DROP PROCEDURE IF EXISTS `{name_input}`;"
            
            clean_name = sanitize_filename(name_input)
            final_filename = f"{timestamp}_create_sp_{clean_name}.php"

        # 4. CUSTOM / INSERT
        else:
            if not name_input:
                name_input = "custom_sql"
            clean_name = sanitize_filename(name_input)
            final_filename = f"{timestamp}_{clean_name}.php"
            down_sql = "-- Optional: Delete data logic"

        # --- Write File ---
        try:
            content = tpl_raw_sql(up_sql, down_sql)
            full_path = os.path.join(MIGRATION_DIR, final_filename)
            
            with open(full_path, 'w') as f:
                f.write(content)
            
            self.lbl_status.config(text=f"SUKSES: {final_filename}", foreground="green")
            
            # Auto clear deskripsi biar siap input berikutnya, tapi SQL dibiarkan dulu takut mau diedit dikit
            # self.entry_name.delete(0, tk.END) 
            
        except Exception as e:
            messagebox.showerror("Gagal", str(e))

if __name__ == "__main__":
    root = tk.Tk()
    app = MigrationApp(root)
    root.mainloop()
    