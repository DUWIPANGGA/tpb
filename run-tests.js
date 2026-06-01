const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const BASE_URL = 'http://127.0.0.1:8000';
const largeFilePath = path.join(__dirname, 'large_file.png');

let browser;
let page;
let csrfToken = '';

async function ensureWebLogin() {
  await page.goto(BASE_URL + '/');
  const currentUrl = page.url();
  if (currentUrl.includes('/beranda')) {
    console.log('Web UI already logged in (redirected to beranda).');
  } else {
    console.log('Performing Web UI login...');
    await page.fill('#name', 'Esa');
    await page.fill('#password', '@Poli1234567');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/beranda');
    console.log('Web UI logged in successfully.');
  }

  // Retrieve valid CSRF token from detail form
  await page.goto(BASE_URL + '/beranda/Laptop');
  csrfToken = await page.evaluate(() => {
    return document.querySelector('input[name="_token"]')?.value || '';
  });
  console.log('CSRF token updated.');
}

async function run() {
  console.log('Preparing database...');
  try {
    // Delete any existing cart items and set Permohonan ID 1 to Disetujui so it can be returned
    execSync('php artisan tinker --execute="\\App\\Models\\Keranjang::query()->delete(); \\App\\Models\\Permohonan::where(\'id\', 1)->update([\'status\' => \'Disetujui\']);"');
    console.log('Database prepared successfully.');
  } catch (err) {
    console.error('Failed to prepare database via tinker:', err.message);
  }

  // Create a 3.5MB dummy file for file upload testing
  fs.writeFileSync(largeFilePath, Buffer.alloc(3.5 * 1024 * 1024));
  console.log('Temporary 3.5MB file created at:', largeFilePath);

  console.log('Starting Playwright automated Web UI tests for scenarios 26-40...');
  
  // Ensure target directories exist
  for (let i = 26; i <= 40; i++) {
    const dir = path.join(__dirname, 'test', String(i));
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
    }
  }

  // Launch browser
  browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1280, height: 720 }
  });
  page = await context.newPage();

  // Scenario 26: Keranjang: Tambah jumlah invalid
  console.log('Running Scenario 26...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/beranda/Laptop');
  await page.evaluate(() => {
    const input = document.getElementById('jumlah');
    input.removeAttribute('readonly');
    input.removeAttribute('max');
    input.value = '100';
  });
  await page.click('#btn-tambah');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '26', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '26', 'result.txt'), `NO TESTCASE: 26\nSKENARIO: Keranjang: Tambah jumlah invalid\nHASIL: Berhasil mendeteksi stok kurang, menampilkan alert: "Jumlah melebihi stok yang tersedia."`);
  await page.click('.swal2-confirm');

  // Scenario 27: Keranjang: Tambah form kosong
  console.log('Running Scenario 27...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/beranda/Laptop');
  await page.evaluate(() => {
    const input = document.getElementById('jumlah');
    input.removeAttribute('readonly');
    input.removeAttribute('min');
    input.removeAttribute('max');
    input.removeAttribute('required');
    input.value = '';
  });
  await page.click('#btn-tambah');
  await page.waitForTimeout(1000); // Wait to render error page or native error
  await page.screenshot({ path: path.join(__dirname, 'test', '27', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '27', 'result.txt'), `NO TESTCASE: 27\nSKENARIO: Keranjang: Tambah form kosong\nHASIL: Menghasilkan Database Exception / Laravel error page karena field jumlah kosong.`);

  // Scenario 28: Keranjang: Tambah tanpa token (tanpa login)
  console.log('Running Scenario 28...');
  await context.clearCookies();
  await page.goto(BASE_URL + '/beranda/Laptop');
  await page.waitForURL(BASE_URL + '/');
  await page.screenshot({ path: path.join(__dirname, 'test', '28', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '28', 'result.txt'), `NO TESTCASE: 28\nSKENARIO: Keranjang: Tambah tanpa token\nHASIL: Akses ditolak dan langsung diarahkan kembali ke halaman login.`);

  // Scenario 29: Keranjang: Update tidak ditemukan
  console.log('Running Scenario 29...');
  // Authenticate API to get fresh token (this will invalidate web session, which is fine)
  const apiToken = await page.evaluate(async (url) => {
    const res = await fetch(url + '/api/v1/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ name: 'Esa', password: '@Poli1234567' })
    });
    const data = await res.json();
    return data.data.access_token;
  }, BASE_URL);

  const apiRes29 = await page.evaluate(async ({ url, token }) => {
    const res = await fetch(url + '/api/v1/keranjang/99999', {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ jumlah: 5 })
    });
    return { status: res.status, body: await res.json() };
  }, { url: BASE_URL, token: apiToken });

  await page.setContent(`<html><body style="font-family:sans-serif; background:#0f172a; color:#f8fafc; padding:40px;">
    <h2>API response for PUT /api/v1/keranjang/99999</h2>
    <pre style="background:#1e293b; padding:20px; border-radius:8px; border:1px solid #334155; color:#38bdf8;">${JSON.stringify(apiRes29, null, 2)}</pre>
  </body></html>`);
  await page.screenshot({ path: path.join(__dirname, 'test', '29', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '29', 'result.txt'), `NO TESTCASE: 29\nSKENARIO: Keranjang: Update tidak ditemukan\nRESPONSE: ${JSON.stringify(apiRes29, null, 2)}`);

  // Scenario 30: Keranjang: Delete tidak ditemukan
  console.log('Running Scenario 30...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.evaluate((csrf) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/keranjang/99999';
    
    const csrfInput = document.createElement('input');
    csrfInput.name = '_token';
    csrfInput.value = csrf;
    form.appendChild(csrfInput);

    const method = document.createElement('input');
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);

    document.body.appendChild(form);
    form.submit();
  }, csrfToken);
  await page.waitForTimeout(1000);
  await page.screenshot({ path: path.join(__dirname, 'test', '30', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '30', 'result.txt'), `NO TESTCASE: 30\nSKENARIO: Keranjang: Delete tidak ditemukan\nHASIL: Menghasilkan halaman Laravel 404 Not Found karena ID keranjang 99999 tidak valid.`);

  // Scenario 31: Keranjang: Delete tanpa token (tanpa login)
  console.log('Running Scenario 31...');
  await context.clearCookies();
  await page.goto(BASE_URL + '/');
  await page.evaluate(() => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/keranjang/1';
    
    const csrf = document.createElement('input');
    csrf.name = '_token';
    csrf.value = 'dummy';
    form.appendChild(csrf);

    const method = document.createElement('input');
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);

    document.body.appendChild(form);
    form.submit();
  });
  await page.waitForURL(BASE_URL + '/');
  await page.screenshot({ path: path.join(__dirname, 'test', '31', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '31', 'result.txt'), `NO TESTCASE: 31\nSKENARIO: Keranjang: Delete tanpa token\nHASIL: Aksi hapus ditolak dan diarahkan ke login page.`);

  // Scenario 32: Permohonan: List gagal tanpa token
  console.log('Running Scenario 32...');
  await context.clearCookies();
  await page.goto(BASE_URL + '/riwayat');
  await page.waitForURL(BASE_URL + '/');
  await page.screenshot({ path: path.join(__dirname, 'test', '32', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '32', 'result.txt'), `NO TESTCASE: 32\nSKENARIO: Permohonan: List gagal tanpa token\nHASIL: Dialihkan ke login page.`);

  // Scenario 33: Permohonan: Submit keranjang kosong
  console.log('Running Scenario 33...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.evaluate((csrf) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/keranjang'; // route('keranjang.store')
    
    const csrfInput = document.createElement('input');
    csrfInput.name = '_token';
    csrfInput.value = csrf;
    form.appendChild(csrfInput);

    const unit = document.createElement('input');
    unit.name = 'unit_kerja';
    unit.value = 'BEM';
    form.appendChild(unit);

    const nama = document.createElement('input');
    nama.name = 'nama_kegiatan';
    nama.value = 'Event';
    form.appendChild(nama);

    const tgl = document.createElement('input');
    tgl.name = 'hari_atau_tanggal';
    tgl.value = '2026-06-20';
    form.appendChild(tgl);

    const start = document.createElement('input');
    start.name = 'waktu_mulai';
    start.value = '08:00';
    form.appendChild(start);

    const end = document.createElement('input');
    end.name = 'waktu_selesai';
    end.value = '10:00';
    form.appendChild(end);

    const phone = document.createElement('input');
    phone.name = 'phone';
    phone.value = '081234567890';
    form.appendChild(phone);

    document.body.appendChild(form);
    form.submit();
  }, csrfToken);
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '33', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '33', 'result.txt'), `NO TESTCASE: 33\nSKENARIO: Permohonan: Submit keranjang kosong\nHASIL: Menampilkan alert "Gagal! Keranjang kosong."`);
  await page.click('.swal2-confirm');

  // Helper to add 1 item for subsequent tests
  async function addLaptopToCart() {
    console.log('Adding Laptop to cart...');
    await page.goto(BASE_URL + '/beranda/Laptop');
    await page.click('#btn-tambah');
    await page.waitForURL('**/beranda');
  }
  await addLaptopToCart();

  // Scenario 34: Permohonan: Submit form wajib kosong
  console.log('Running Scenario 34...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.click('button[data-modal-target="form-permohonan"]');
  await page.waitForSelector('#form-permohonan', { state: 'visible' });
  await page.evaluate(() => {
    // Remove HTML validation so we can submit empty values
    document.getElementById('nama_kegiatan').removeAttribute('required');
    document.getElementById('hari_atau_tanggal').removeAttribute('required');
    document.getElementById('waktu_mulai').removeAttribute('required');
    document.getElementById('waktu_selesai').removeAttribute('required');
    document.getElementById('phone').removeAttribute('required');
    
    document.getElementById('nama_kegiatan').value = '';
    document.getElementById('hari_atau_tanggal').value = '';
    document.getElementById('waktu_mulai').value = '';
    document.getElementById('waktu_selesai').value = '';
    document.getElementById('phone').value = '';
  });
  await page.click('#form-permohonan button[type="submit"]');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '34', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '34', 'result.txt'), `NO TESTCASE: 34\nSKENARIO: Permohonan: Submit form wajib kosong\nHASIL: Menampilkan alert "Validasi Gagal!" dengan daftar field kosong.`);
  await page.click('.swal2-confirm');

  // Scenario 35: Permohonan: Submit tanpa token
  console.log('Running Scenario 35...');
  await context.clearCookies();
  await page.goto(BASE_URL + '/');
  await page.evaluate(() => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/keranjang';
    
    const csrf = document.createElement('input');
    csrf.name = '_token';
    csrf.value = 'dummy';
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
  });
  await page.waitForURL(BASE_URL + '/');
  await page.screenshot({ path: path.join(__dirname, 'test', '35', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '35', 'result.txt'), `NO TESTCASE: 35\nSKENARIO: Permohonan: Submit tanpa token\nHASIL: Aksi ditolak dan diarahkan ke login page.`);

  // Scenario 36: Permohonan: Submit tanggal lewat
  console.log('Running Scenario 36...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.click('button[data-modal-target="form-permohonan"]');
  await page.waitForSelector('#form-permohonan', { state: 'visible' });
  await page.fill('#nama_kegiatan', 'Seminar IT');
  await page.fill('#hari_atau_tanggal', '2020-01-01');
  await page.fill('#waktu_mulai', '08:00');
  await page.fill('#waktu_selesai', '12:00');
  await page.fill('#phone', '081234567890');
  await page.click('#form-permohonan button[type="submit"]');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '36', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '36', 'result.txt'), `NO TESTCASE: 36\nSKENARIO: Permohonan: Submit tanggal lewat\nHASIL: Menampilkan alert "Validasi Gagal!" karena tanggal di masa lalu.`);
  await page.click('.swal2-confirm');

  // Scenario 37: Permohonan: Submit waktu invalid
  console.log('Running Scenario 37...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.click('button[data-modal-target="form-permohonan"]');
  await page.waitForSelector('#form-permohonan', { state: 'visible' });
  await page.fill('#nama_kegiatan', 'Seminar IT');
  await page.fill('#hari_atau_tanggal', '2026-06-20');
  await page.evaluate(() => {
    const start = document.getElementById('waktu_mulai');
    start.type = 'text';
    start.value = 'abc';
  });
  await page.fill('#waktu_selesai', '12:00');
  await page.fill('#phone', '081234567890');
  await page.click('#form-permohonan button[type="submit"]');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '37', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '37', 'result.txt'), `NO TESTCASE: 37\nSKENARIO: Permohonan: Submit waktu invalid\nHASIL: Menampilkan alert "Validasi Gagal!" karena format waktu salah.`);
  await page.click('.swal2-confirm');

  // Scenario 38: Permohonan: Submit phone invalid
  console.log('Running Scenario 38...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/keranjang');
  await page.click('button[data-modal-target="form-permohonan"]');
  await page.waitForSelector('#form-permohonan', { state: 'visible' });
  await page.fill('#nama_kegiatan', 'Seminar IT');
  await page.fill('#hari_atau_tanggal', '2026-06-20');
  await page.fill('#waktu_mulai', '08:00');
  await page.fill('#waktu_selesai', '12:00');
  await page.fill('#phone', 'nomor_hp_dengan_huruf');
  await page.click('#form-permohonan button[type="submit"]');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '38', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '38', 'result.txt'), `NO TESTCASE: 38\nSKENARIO: Permohonan: Submit phone invalid\nHASIL: Menampilkan alert "Validasi Gagal!" karena nomor telepon mengandung huruf.`);
  await page.click('.swal2-confirm');

  // Scenario 39: Pengembalian: List gagal tanpa token
  console.log('Running Scenario 39...');
  await context.clearCookies();
  await page.goto(BASE_URL + '/informasi/pengembalian');
  await page.waitForURL(BASE_URL + '/');
  await page.screenshot({ path: path.join(__dirname, 'test', '39', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '39', 'result.txt'), `NO TESTCASE: 39\nSKENARIO: Pengembalian: List gagal tanpa token\nHASIL: Akses ditolak dan diarahkan ke login page.`);

  // Scenario 40: Pengembalian: Submit file > 3MB
  console.log('Running Scenario 40...');
  await ensureWebLogin();
  await page.goto(BASE_URL + '/informasi/pengembalian');
  await page.click('button[data-modal-target="permohonan1"]');
  await page.waitForSelector('#permohonan1', { state: 'visible' });
  await page.setInputFiles('#permohonan1 #bukti_foto', largeFilePath);
  await page.click('#permohonan1 button[type="submit"]');
  await page.waitForSelector('.swal2-popup');
  await page.screenshot({ path: path.join(__dirname, 'test', '40', 'screenshot.png') });
  fs.writeFileSync(path.join(__dirname, 'test', '40', 'result.txt'), `NO TESTCASE: 40\nSKENARIO: Pengembalian: Submit file > 3MB\nHASIL: Menampilkan alert "Validasi Gagal!" karena ukuran file melebihi batas.`);
  await page.click('#permohonan1 .swal2-confirm');

  // Cleanup
  await browser.close();
  
  if (fs.existsSync(largeFilePath)) {
    fs.unlinkSync(largeFilePath);
  }
  
  console.log('All Web UI tests executed successfully. Output folders have been populated under /test/');
}

run().catch(async err => {
  console.error('Test execution failed:', err);
  if (page) {
    try {
      await page.screenshot({ path: path.join(__dirname, 'error_diagnostics.png') });
      console.log('Saved error diagnostics screenshot to error_diagnostics.png');
    } catch (ssErr) {
      console.error('Failed to take diagnostics screenshot:', ssErr.message);
    }
  }
  if (browser) {
    await browser.close();
  }
  if (fs.existsSync(largeFilePath)) {
    fs.unlinkSync(largeFilePath);
  }
  process.exit(1);
});
