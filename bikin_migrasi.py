import os
import sys
import re
from datetime import datetime

# --- KONFIGURASI ---
MIGRATION_DIR = 'database/migrations'

# --- WARNA TEXT (Agar terminal cantik) ---
class Colors:
    HEADER = '\033[95m'
    BLUE = '\033[94m'
    GREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'

def get_timestamp():
    return datetime.now().strftime('%Y_%m_%d_%H%M%S')

def sanitize_filename(name):
    # Ubah spasi jadi underscore, hapus karakter aneh, lowercase
    name = name.lower().strip()
    name = re.sub(r'[^a-z0-9_\s]', '', name)
    name = re.sub(r'\s+', '_', name)
    return name

def write_file(filename, content):
    full_path = os.path.join(MIGRATION_DIR, filename)
    
    # Pastikan folder ada
    if not os.path.exists(MIGRATION_DIR):
        print(f"{Colors.FAIL}Error: Folder {MIGRATION_DIR} tidak ditemukan! Pastikan script ini ada di root project Laravel.{Colors.ENDC}")
        sys.exit(1)

    with open(full_path, 'w') as f:
        f.write(content)
    
    print(f"\n{Colors.GREEN}Sukses! File migrasi dibuat:{Colors.ENDC}")
    print(f"{Colors.BOLD}{full_path}{Colors.ENDC}")
    print(f"{Colors.BLUE}Silakan cek file tersebut untuk memastikan query sudah benar.{Colors.ENDC}")

# --- TEMPLATE GENERATORS ---

def template_new_table(class_name, table_name):
    return f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{{
    public function up(): void
    {{
        Schema::create('{table_name}', function (Blueprint $table) {{
            $table->id();
            // --- INPUT KOLOM ANDA DISINI ---
            
            $table->timestamps();
        }});
    }}

    public function down(): void
    {{
        Schema::dropIfExists('{table_name}');
    }}
}};
"""

def template_add_column(class_name, table_name, column_code):
    return f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{{
    public function up(): void
    {{
        Schema::table('{table_name}', function (Blueprint $table) {{
            // {column_code}
            // Contoh: $table->string('alamat')->nullable();
        }});
    }}

    public function down(): void
    {{
        Schema::table('{table_name}', function (Blueprint $table) {{
            // TODO: Masukkan codingan untuk drop kolom disini (Rollback)
            // Contoh: $table->dropColumn('nama_kolom');
        }});
    }}
}};
"""

def template_stored_procedure(class_name, sp_name, sql_body):
    # Escape tanda kutip untuk PHP string
    # sql_body_escaped = sql_body.replace('"', '\\"') 
    
    return f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{{
    public function up(): void
    {{
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `{sp_name}`");

        // 2. Buat SP Baru
        DB::unprepared("
{sql_body}
        ");
    }}

    public function down(): void
    {{
        DB::unprepared("DROP PROCEDURE IF EXISTS `{sp_name}`");
    }}
}};
"""

def template_raw_sql(class_name, sql_query):
    return f"""<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{{
    public function up(): void
    {{
        DB::statement("
{sql_query}
        ");
    }}

    public function down(): void
    {{
        // TODO: Tulis query kebalikan (DELETE/DROP) manual disini
    }}
}};
"""

# --- MAIN LOGIC ---

def main():
    print(f"{Colors.HEADER}==========================================")
    print(f"   GENERATOR MIGRASI LARAVEL OTOMATIS")
    print(f"=========================================={Colors.ENDC}")
    print("Pilih tipe perubahan database:")
    print("1. Buat Tabel Baru (Create Table)")
    print("2. Tambah/Edit Kolom (Alter Table)")
    print("3. Stored Procedure (Create/Update SP)")
    print("4. Insert Data Baru / Raw SQL Query")
    print("0. Keluar")

    choice = input(f"\n{Colors.BLUE}Masukkan pilihan (0-4): {Colors.ENDC}")

    if choice == '0':
        sys.exit()

    timestamp = get_timestamp()

    # --- OPSI 1: TABEL BARU ---
    if choice == '1':
        table_name = input("Masukkan nama tabel (contoh: produk): ")
        desc = f"create_{table_name}_table"
        filename = f"{timestamp}_{sanitize_filename(desc)}.php"
        content = template_new_table(sanitize_filename(desc), table_name)
        write_file(filename, content)

    # --- OPSI 2: KOLOM BARU ---
    elif choice == '2':
        table_name = input("Nama tabel yang mau diedit: ")
        col_desc = input("Deskripsi singkat (contoh: add phone to user): ")
        col_code = input("Masukkan kode Laravel (opsional, tekan enter jika kosong): ")
        
        filename = f"{timestamp}_{sanitize_filename(col_desc)}.php"
        content = template_add_column(sanitize_filename(col_desc), table_name, col_code)
        write_file(filename, content)

    # --- OPSI 3: STORED PROCEDURE ---
    elif choice == '3':
        sp_name = input("Masukkan Nama Procedure (contoh: sp_dashboard_bph): ")
        print(f"{Colors.WARNING}Masukkan Query CREATE PROCEDURE lengkap (tekan Enter 2x untuk selesai):{Colors.ENDC}")
        
        lines = []
        while True:
            line = input()
            if line:
                lines.append(line)
            else:
                break
        sql_body = "\n".join(lines)
        
        desc = f"create_sp_{sp_name}"
        filename = f"{timestamp}_{sanitize_filename(desc)}.php"
        content = template_stored_procedure(sanitize_filename(desc), sp_name, sql_body)
        write_file(filename, content)

    # --- OPSI 4: RAW SQL / DATA ---
    elif choice == '4':
        desc_input = input("Deskripsi singkat (contoh: insert data divisi): ")
        print(f"{Colors.WARNING}Masukkan Query SQL (tekan Enter 2x untuk selesai):{Colors.ENDC}")
        
        lines = []
        while True:
            line = input()
            if line:
                lines.append(line)
            else:
                break
        sql_query = "\n".join(lines)

        filename = f"{timestamp}_{sanitize_filename(desc_input)}.php"
        content = template_raw_sql(sanitize_filename(desc_input), sql_query)
        write_file(filename, content)

    else:
        print("Pilihan tidak valid.")

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\nDibatalkan oleh user.")
