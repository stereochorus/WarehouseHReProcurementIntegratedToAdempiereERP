# CI/CD Pipeline — WHR-ePIS
## GitHub Actions + Docker Hub + Azure Web Apps

---

## Daftar Isi
1. [Gambaran Umum](#1-gambaran-umum)
2. [Alur Kerja Branch](#2-alur-kerja-branch)
3. [Prasyarat](#3-prasyarat)
4. [Setup Docker Hub Access Token](#4-setup-docker-hub-access-token)
5. [Setup Azure Web App Publish Profile](#5-setup-azure-web-app-publish-profile)
6. [Setup GitHub Secrets](#6-setup-github-secrets)
7. [Penjelasan Workflow File](#7-penjelasan-workflow-file)
8. [Cara Deploy](#8-cara-deploy)
9. [Monitoring & Troubleshooting](#9-monitoring--troubleshooting)

---

## 1. Gambaran Umum

Pipeline ini secara otomatis melakukan build Docker image dan deploy ke Azure Web App setiap kali ada perubahan yang di-push ke branch `prod`.

```
Developer push ke prod
         ↓
GitHub Actions trigger (.github/workflows/deploy.yml)
         ↓
Build Docker image dari Dockerfile
         ↓
Push image ke Docker Hub
(madsbwcn/whhreprocadempierelaravel:latest)
         ↓
Azure Web App pull image terbaru
         ↓
✅ Aplikasi live di Azure
```

**Teknologi yang digunakan:**
| Komponen | Teknologi |
|---|---|
| Source Code | GitHub |
| CI/CD Runner | GitHub Actions |
| Container Registry | Docker Hub |
| Hosting | Azure Web Apps (Linux Container) |
| Framework | Laravel 12 + PHP 8.2 (Apache) |

---

## 2. Alur Kerja Branch

```
main (development)
  │
  │  merge setelah fitur selesai
  ▼
staging (user testing / UAT)
  │
  │  merge setelah user ACC / approve
  ▼
prod (production — trigger auto-deploy)
  │
  ▼
Azure Web App live
```

| Branch | Fungsi | Auto Deploy |
|---|---|---|
| `main` | Development harian developer | ❌ Tidak |
| `staging` | User Acceptance Testing (UAT) | ❌ Tidak |
| `prod` | Production live | ✅ Ya — otomatis ke Azure |

**Aturan:**
- Jangan langsung push fitur ke `prod`
- Semua perubahan harus melalui `main` → `staging` → `prod`
- Push ke `prod` hanya setelah user menyatakan ACC di staging

---

## 3. Prasyarat

Sebelum setup, pastikan hal-hal berikut sudah tersedia:

- [x] Akun **GitHub** dengan akses ke repository ini
- [x] Akun **Docker Hub** (username: `madsbwcn`)
- [x] **Azure Web App** sudah dibuat dan berjalan
  - Publishing model: **Container**
  - OS: **Linux**
  - Container Image: `madsbwcn/whhreprocadempierelaravel:latest`
- [x] **Azure Basic Authentication** sudah diaktifkan di Web App

---

## 4. Setup Docker Hub Access Token

Access token digunakan agar GitHub Actions bisa login ke Docker Hub dan push image.

### Langkah-langkah:

**1.** Login ke [hub.docker.com](https://hub.docker.com)

**2.** Klik avatar di pojok kanan atas → **"My Account"**

**3.** Di sidebar kiri, klik **Settings** → **Personal access tokens**

**4.** Klik tombol **"Generate new token"** (pojok kanan atas)

**5.** Isi form:
```
Description  : github-actions-whr-epis
Expiration   : No expiry  (atau sesuai kebijakan)
Permissions  : Read, Write, Delete
```

**6.** Klik **"Generate"**

**7.** ⚠️ **PENTING:** Token hanya ditampilkan **sekali**. Langsung copy dan simpan di tempat aman.

```
Format token: dckr_pat_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

> Jika token hilang/lupa, harus generate baru — token lama tidak bisa dilihat lagi.

---

## 5. Setup Azure Web App Publish Profile

Publish Profile digunakan agar GitHub Actions bisa trigger deployment ke Azure Web App.

### Aktifkan Basic Authentication (jika belum):

**Via Azure Portal:**
1. Buka **Azure Portal** → pilih Web App `whhrdemo`
2. Di sidebar kiri → **Settings** → **Configuration**
3. Klik tab **"General settings"**
4. Cari **"SCM Basic Auth Publishing Credentials"** → set **On**
5. Cari **"FTP Basic Auth Publishing Credentials"** → set **On**
6. Klik **Save**

**Via Azure Cloud Shell** (alternatif lebih cepat):
```bash
az webapp update \
  --name whhrdemo \
  --resource-group RGDebugTest \
  --set basicAuthEnabled=true

az resource update \
  --resource-group RGDebugTest \
  --name scm \
  --namespace Microsoft.Web \
  --resource-type basicPublishingCredentialsPolicies \
  --parent sites/whhrdemo \
  --set properties.allow=true
```

### Download Publish Profile:

**1.** Buka **Azure Portal** → pilih Web App `whhrdemo`

**2.** Di toolbar atas, klik **"Download publish profile"**

**3.** File `.PublishSettings` akan terdownload (format XML)

**4.** Buka file tersebut dengan text editor (Notepad/VSCode)

**5.** **Select All** → **Copy** seluruh isinya

```xml
<!-- Contoh isi file (JANGAN share file ini secara publik) -->
<publishData>
  <publishProfile profileName="whhrdemo - Web Deploy"
    publishMethod="MSDeploy"
    publishUrl="whhrdemo.scm.azurewebsites.net:443"
    userName="$whhrdemo"
    userPWD="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    .../>
  ...
</publishData>
```

> ⚠️ File ini mengandung **credentials sensitif**. Jangan commit ke repository atau share secara publik.

### Reset Publish Profile (jika credentials bocor):

Di Azure Portal → Web App → toolbar atas → **"Reset publish profile"** → credentials lama langsung tidak berlaku.

---

## 6. Setup GitHub Secrets

Secrets disimpan terenkripsi di GitHub dan digunakan oleh workflow saat runtime.

### Cara menambahkan secret:

**1.** Buka repository di GitHub: `github.com/stereochorus/WarehouseHReProcurementIntegratedToAdempiereERP`

**2.** Klik tab **"Settings"** (navbar atas)

**3.** Di sidebar kiri → **"Secrets and variables"** → **"Actions"**

**4.** Klik **"New repository secret"**

**5.** Isi **Name** dan **Secret**, klik **"Add secret"**

### Daftar secrets yang dibutuhkan:

| Secret Name | Nilai | Sumber |
|---|---|---|
| `DOCKERHUB_TOKEN` | Token dari Docker Hub | [Langkah 4](#4-setup-docker-hub-access-token) |
| `AZURE_WEBAPP_PUBLISH_PROFILE` | Seluruh isi file `.PublishSettings` | [Langkah 5](#5-setup-azure-web-app-publish-profile) |

### Verifikasi secrets sudah tersimpan:

Setelah ditambahkan, secrets akan terlihat di daftar (nilainya tersembunyi):
```
AZURE_WEBAPP_PUBLISH_PROFILE  Updated X minutes ago
DOCKERHUB_TOKEN               Updated X minutes ago
```

---

## 7. Penjelasan Workflow File

File: `.github/workflows/deploy.yml`

```yaml
name: Build & Deploy to Azure Web App

on:
  push:
    branches:
      - prod        # Hanya trigger saat push ke branch prod

env:
  DOCKERHUB_USERNAME: madsbwcn
  IMAGE_NAME:         madsbwcn/whhreprocadempierelaravel
  WEBAPP_NAME:        whhrdemo

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest   # Runner: mesin Linux di GitHub

    steps:
      # Step 1: Download source code ke runner
      - name: Checkout repository
        uses: actions/checkout@v4

      # Step 2: Login ke Docker Hub menggunakan token
      - name: Login to Docker Hub
        uses: docker/login-action@v3
        with:
          username: ${{ env.DOCKERHUB_USERNAME }}
          password: ${{ secrets.DOCKERHUB_TOKEN }}   # Dari GitHub Secrets

      # Step 3: Build Docker image dengan 2 tag
      # - :latest  → selalu dipakai Azure untuk pull terbaru
      # - :<sha>   → tag unik per commit untuk rollback
      - name: Build Docker image
        run: |
          docker build \
            --tag ${{ env.IMAGE_NAME }}:latest \
            --tag ${{ env.IMAGE_NAME }}:${{ github.sha }} \
            .

      # Step 4: Push kedua tag ke Docker Hub
      - name: Push image to Docker Hub
        run: |
          docker push ${{ env.IMAGE_NAME }}:latest
          docker push ${{ env.IMAGE_NAME }}:${{ github.sha }}

      # Step 5: Trigger Azure Web App untuk pull image terbaru
      - name: Deploy to Azure Web App
        uses: azure/webapps-deploy@v3
        with:
          app-name:        ${{ env.WEBAPP_NAME }}
          publish-profile: ${{ secrets.AZURE_WEBAPP_PUBLISH_PROFILE }}
          images:          ${{ env.IMAGE_NAME }}:latest

      # Step 6: Tampilkan URL hasil deploy
      - name: Print deployment URL
        run: |
          echo "✅ Deployed: https://whhrdemo-fjhtd3e5e6c8hhc6.indonesiacentral-01.azurewebsites.net"
```

### Penjelasan variabel penting:

| Variabel | Nilai | Keterangan |
|---|---|---|
| `DOCKERHUB_USERNAME` | `madsbwcn` | Username Docker Hub |
| `IMAGE_NAME` | `madsbwcn/whhreprocadempierelaravel` | Nama repository di Docker Hub |
| `WEBAPP_NAME` | `whhrdemo` | Nama Azure Web App |
| `github.sha` | Auto (hash commit) | Tag unik tiap build untuk rollback |

---

## 8. Cara Deploy

### Deploy normal (fitur baru):

```bash
# 1. Kerjakan fitur di branch main
git checkout main
# ... edit kode ...
git add .
git commit -m "feat: deskripsi fitur"
git push origin main

# 2. Pindah ke staging untuk user testing
git checkout staging
git merge main
git push origin staging
# → berikan URL staging ke user untuk UAT

# 3. Setelah user ACC → merge ke prod (auto-deploy)
git checkout prod
git merge staging
git push origin prod
# → GitHub Actions otomatis jalan ±3-4 menit
# → Azure Web App update otomatis
```

### Via Pull Request (lebih terstruktur):

1. Buat PR: `main` → `staging` di GitHub
2. Review + merge setelah siap testing
3. Buat PR: `staging` → `prod` setelah user ACC
4. Merge PR → pipeline otomatis berjalan

### Cek status deploy:

1. Buka GitHub → tab **"Actions"**
2. Lihat workflow run terbaru
3. Klik untuk melihat detail setiap step
4. Jika semua ✅ → buka URL Azure untuk verifikasi

---

## 9. Monitoring & Troubleshooting

### Cek status di GitHub Actions:

```
GitHub repo → Actions → pilih workflow run terbaru
```

**Status yang mungkin:**
| Status | Arti |
|---|---|
| 🟡 In progress | Sedang berjalan |
| ✅ Success | Deploy berhasil |
| ❌ Failed | Ada error — klik untuk lihat log |

### Cek logs di Azure:

**Azure Portal** → Web App `whhrdemo` → **Deployment Center** → **Logs**

Atau via Azure Cloud Shell:
```bash
az webapp log tail --name whhrdemo --resource-group RGDebugTest
```

### Error umum dan solusinya:

**Error: `unauthorized: incorrect username or password`**
```
Penyebab : DOCKERHUB_TOKEN salah atau expired
Solusi   : Generate token baru di Docker Hub → update GitHub Secret
```

**Error: `BasicAuthEnabled is false`**
```
Penyebab : Basic Auth Azure dinonaktifkan
Solusi   : Aktifkan Basic Auth (lihat Langkah 5) atau Reset publish profile
```

**Error: `Error: No package found`**
```
Penyebab : Publish profile tidak valid
Solusi   : Download ulang publish profile dari Azure → update GitHub Secret
```

**Deploy sukses tapi aplikasi tidak berubah:**
```
Penyebab : Azure masih menggunakan image cache lama
Solusi   : Azure Portal → Web App → Restart
           Atau tunggu beberapa menit untuk propagasi
```

### Rollback ke versi sebelumnya:

Setiap build menghasilkan tag berdasarkan commit SHA. Untuk rollback:

```bash
# Lihat daftar image di Docker Hub
# https://hub.docker.com/r/madsbwcn/whhreprocadempierelaravel/tags

# Update Azure Web App ke image versi lama via Azure CLI
az webapp config container set \
  --name whhrdemo \
  --resource-group RGDebugTest \
  --container-image-name madsbwcn/whhreprocadempierelaravel:<SHA-COMMIT-LAMA>
```

---

## Informasi Konfigurasi

| Item | Nilai |
|---|---|
| GitHub Repository | `stereochorus/WarehouseHReProcurementIntegratedToAdempiereERP` |
| Docker Hub Image | `madsbwcn/whhreprocadempierelaravel` |
| Azure Web App | `whhrdemo` |
| Resource Group | `RGDebugTest` |
| Azure Region | Indonesia Central |
| App URL | `https://whhrdemo-fjhtd3e5e6c8hhc6.indonesiacentral-01.azurewebsites.net` |
| Workflow File | `.github/workflows/deploy.yml` |

---

*Dokumentasi ini dibuat berdasarkan proses setup CI/CD aktual untuk project WHR-ePIS.*
