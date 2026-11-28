import os
import shutil
import zipfile

PROJECT_DIR = r"D:\e-transkip"
DEPLOY_DIR = os.path.join(PROJECT_DIR, "deploy")
OUTPUT_ZIP_BARU = os.path.join(DEPLOY_DIR, "etranskrip-baru.zip")
OUTPUT_ZIP_MAIN = os.path.join(DEPLOY_DIR, "etranskrip.zip")

ROOT_HTACCESS = """<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
"""

ENV_PRODUCTION = """APP_NAME="E-Transkrip"
APP_ENV=production
APP_KEY=base64:bnMt1zAm+8IgXW8ct+9ydaalfLBGx7i0fXVQroGOTQQ=
APP_DEBUG=true
APP_URL=http://e-transkrip.freehosting.dev

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sql201.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_43512285_transkrip
DB_USERNAME=if0_43512285
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_STORE=file
QUEUE_CONNECTION=sync

SIAKAD_API_BASE_URL=dummy
"""

def create_full_zip(target_path):
    print(f"Creating full deployment zip: {target_path} ...")
    if os.path.exists(target_path):
        os.remove(target_path)

    with zipfile.ZipFile(target_path, "w", zipfile.ZIP_DEFLATED, compresslevel=6) as zipf:
        # 1. Root special files
        zipf.writestr(".htaccess", ROOT_HTACCESS)
        zipf.writestr(".env", ENV_PRODUCTION)
        
        # sync.php at root
        sync_src = os.path.join(PROJECT_DIR, "public", "sync.php")
        if os.path.exists(sync_src):
            zipf.write(sync_src, "sync.php")
            
        # artisan, composer.json, composer.lock
        for f in ["artisan", "composer.json", "composer.lock"]:
            fp = os.path.join(PROJECT_DIR, f)
            if os.path.exists(fp):
                zipf.write(fp, f)

        # 2. Key Directories
        dirs_to_copy = [
            "app",
            "bootstrap",
            "config",
            "database",
            "public",
            "resources",
            "routes",
            "storage",
            "vendor",
        ]

        for d in dirs_to_copy:
            src_dir = os.path.join(PROJECT_DIR, d)
            if not os.path.exists(src_dir):
                continue
            for root, dirs, files in os.walk(src_dir):
                # Filter out cached views/sessions/logs to keep zip clean
                rel_root = os.path.relpath(root, PROJECT_DIR)
                
                # Exclude node_modules, git, tests, deploy
                if "node_modules" in rel_root or ".git" in rel_root or "deploy" in rel_root:
                    continue
                if rel_root.startswith("storage\\framework\\views") or rel_root.startswith("storage/framework/views"):
                    continue
                if rel_root.startswith("storage\\framework\\cache") or rel_root.startswith("storage/framework/cache"):
                    continue
                if rel_root.startswith("bootstrap\\cache") or rel_root.startswith("bootstrap/cache"):
                    continue

                for f in files:
                    if f.endswith(".log") or f.endswith(".tmp"):
                        continue
                    file_path = os.path.join(root, f)
                    arcname = os.path.relpath(file_path, PROJECT_DIR).replace("\\", "/")
                    zipf.write(file_path, arcname)

        # Ensure empty storage dirs exist
        storage_dirs = [
            "storage/app/public",
            "storage/framework/cache/data",
            "storage/framework/sessions",
            "storage/framework/views",
            "storage/logs",
            "bootstrap/cache",
        ]
        for sd in storage_dirs:
            zipf.writestr(sd + "/.gitignore", "*\n!.gitignore\n")

    size_mb = os.path.getsize(target_path) / (1024 * 1024)
    print(f"Successfully generated {target_path} ({size_mb:.2f} MB)")

if __name__ == "__main__":
    create_full_zip(OUTPUT_ZIP_BARU)
    # Also copy to etranskrip.zip so either name works
    shutil.copy2(OUTPUT_ZIP_BARU, OUTPUT_ZIP_MAIN)
    print("Copied to etranskrip.zip as well.")

