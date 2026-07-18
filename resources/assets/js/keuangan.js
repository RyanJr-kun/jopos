/**
 * Dashboard Keuangan
 */

'use strict';

// Config dikirim dari Blade lewat window.keuanganConfig (lihat index_blade.php)
const cfg = window.keuanganConfig || {};

// ============================================================
// Flatpickr — Date Range
// ============================================================
const fpRange = flatpickr('#dateRange', {
  mode: 'range',
  locale: 'id',
  dateFormat: 'Y-m-d',
  defaultDate: [cfg.startDate, cfg.endDate],
  altInput: true,
  altFormat: 'j M Y',
  onChange: function (dates) {
    if (dates.length === 2) {
      document.getElementById('startDateInput').value = flatpickr.formatDate(dates[0], 'Y-m-d');
      document.getElementById('endDateInput').value = flatpickr.formatDate(dates[1], 'Y-m-d');
    }
  }
});

// ============================================================
// Filter metode pembayaran
// ============================================================
function setMetode(val) {
  document.getElementById('inputMetode').value = val;
  const bankWrap = document.getElementById('bankSelectWrap');
  bankWrap.classList.toggle('d-none', !['transfer', 'qris'].includes(val));
  document.getElementById('filterForm').submit();
}

// ============================================================
// Modal Mutasi — tipe masuk/keluar
// ============================================================
function setTipe(tipe) {
  document.getElementById('inputTipe').value = tipe;
  document.getElementById('btnMasuk').classList.toggle('active', tipe === 'masuk');
  document.getElementById('btnKeluar').classList.toggle('active', tipe === 'keluar');
}

// Modal Mutasi — tampilkan select bank
function toggleMutasiBank(metode) {
  const wrap = document.getElementById('mutasiBankWrap');
  const note = document.getElementById('qrisNote');
  const select = document.getElementById('mutasiBank');
  const show = ['transfer', 'qris'].includes(metode);

  wrap.style.display = show ? '' : 'none';
  note.style.display = metode === 'qris' ? '' : 'none';

  // Otopilot: pilih Mandiri jika QRIS
  if (metode === 'qris') {
    const mandiriOpt = select.querySelector('[data-default-qris="1"]');
    if (mandiriOpt) select.value = mandiriOpt.value;
  }
}

// ============================================================
// Preview logo bank (create)
// ============================================================
function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => (document.getElementById('logoPreview').src = e.target.result);
    reader.readAsDataURL(input.files[0]);
  }
}

function previewEditLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => (document.getElementById('editLogoPreview').src = e.target.result);
    reader.readAsDataURL(input.files[0]);
  }
}

// ============================================================
// Toggle field form akun (create/edit) berdasarkan tipe_akun
// ============================================================
// prefix: 'create' atau 'edit' — dipakai untuk mencocokkan id elemen
// (createNorekWrap/editNorekWrap, dst).
function toggleAccountFields(tipe, prefix) {
  // id nomor_rekening/nama_pemilik beda antara modal create & edit (edit memakai
  // id lama editNomorRekening/editNamaPemilik supaya tidak perlu ubah field lain).
  const ids =
    prefix === 'edit'
      ? { norekInput: 'editNomorRekening', pemilikInput: 'editNamaPemilik' }
      : { norekInput: 'createNorek', pemilikInput: 'createPemilik' };

  const fieldsWrap = document.getElementById(`${prefix}AccountFields`);
  const norekWrap = document.getElementById(`${prefix}NorekWrap`);
  const pemilikWrap = document.getElementById(`${prefix}PemilikWrap`);
  const norekInput = document.getElementById(ids.norekInput);
  const pemilikInput = document.getElementById(ids.pemilikInput);
  const storeSelect = document.getElementById(`${prefix}StoreId`);
  const storeNote = document.getElementById(`${prefix}StoreNote`);

  // Untuk modal create, field lain baru muncul setelah tipe akun dipilih.
  if (!tipe) {
    if (fieldsWrap) fieldsWrap.classList.add('d-none');
    return;
  }
  if (fieldsWrap) fieldsWrap.classList.remove('d-none');

  // Nomor rekening & nama pemilik: hanya untuk QRIS & Bank.
  // Untuk Tunai, disembunyikan DAN dikosongkan supaya terkirim null.
  const showNorekPemilik = tipe === 'qris' || tipe === 'bank';
  if (norekWrap) norekWrap.classList.toggle('d-none', !showNorekPemilik);
  if (pemilikWrap) pemilikWrap.classList.toggle('d-none', !showNorekPemilik);
  if (norekInput) {
    norekInput.required = showNorekPemilik;
    if (!showNorekPemilik) norekInput.value = '';
  }
  if (pemilikInput) {
    pemilikInput.required = showNorekPemilik;
    if (!showNorekPemilik) pemilikInput.value = '';
  }

  // Toko: wajib untuk Tunai & QRIS, opsional untuk Bank (bisa lintas toko).
  if (storeSelect) storeSelect.required = tipe !== 'bank';
  if (storeNote) {
    storeNote.textContent =
      tipe === 'bank' ? 'Opsional — akun bank bisa dipakai lintas toko.' : 'Wajib dipilih.';
  }
}

// ============================================================
// Isi modal edit bank
// ============================================================
function openEditBank(id, nama, tipeAkun, norek, pemilik, storeId, saldoAwal, isActive, logoUrl) {
  const base = cfg.accountBaseUrl;
  document.getElementById('formEditAccount').action = `${base}/${id}`;
  document.getElementById('formDeleteBank').action = `${base}/${id}`;
  document.getElementById('editTipeAkun').value = tipeAkun;
  document.getElementById('editNamaBank').value = nama;
  document.getElementById('editNomorRekening').value = norek;
  document.getElementById('editNamaPemilik').value = pemilik;
  document.getElementById('editStoreId').value = storeId ?? '';
  document.getElementById('editSaldoAwal').value = saldoAwal ?? 0;
  document.getElementById('editBankAktif').checked = isActive == 1;
  document.getElementById('editLogoPreview').src = logoUrl || cfg.defaultLogoUrl;

  toggleAccountFields(tipeAkun, 'edit');

  new bootstrap.Modal(document.getElementById('modalEditBank')).show();
}

// ============================================================
// Reset modal create setiap kali ditutup, supaya kembali ke
// step 1 (hanya select tipe akun) saat dibuka lagi.
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
  const modalBank = document.getElementById('modalBank');
  if (modalBank) {
    modalBank.addEventListener('hidden.bs.modal', function () {
      const form = document.getElementById('formCreateAccount');
      if (form) form.reset();
      document.getElementById('createAccountFields')?.classList.add('d-none');
    });
  }
});

// Fungsi-fungsi di atas dipanggil lewat atribut onclick="..." di HTML.
// Karena file ini dikompilasi Vite sebagai module, scope-nya tidak otomatis
// global — jadi harus di-attach manual ke window supaya onclick tetap jalan.
window.setMetode = setMetode;
window.setTipe = setTipe;
window.toggleMutasiBank = toggleMutasiBank;
window.previewLogo = previewLogo;
window.previewEditLogo = previewEditLogo;
window.openEditBank = openEditBank;
window.toggleAccountFields = toggleAccountFields;

// ============================================================
// ApexCharts
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
  const chartData = cfg.chartData;

  const opts = {
    series: [
      { name: 'Pemasukan', data: chartData.income },
      { name: 'Pengeluaran', data: chartData.expense }
    ],
    chart: {
      type: 'area',
      height: 280,
      toolbar: { show: false },
      zoom: { enabled: false },
      sparkline: { enabled: false },
      fontFamily: 'inherit'
    },
    colors: ['#28c76f', '#ea5455'],
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.25,
        opacityTo: 0.02,
        stops: [0, 95, 100]
      }
    },
    stroke: { curve: 'smooth', width: 2.5 },
    dataLabels: { enabled: false },
    xaxis: {
      categories: chartData.labels,
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: { style: { fontSize: '11px', colors: '#6c757d' } }
    },
    yaxis: {
      labels: {
        formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
        style: { fontSize: '11px', colors: '#6c757d' }
      }
    },
    grid: {
      borderColor: 'rgba(0,0,0,.06)',
      strokeDashArray: 4,
      xaxis: { lines: { show: false } }
    },
    tooltip: {
      y: {
        formatter: v =>
          new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
          }).format(v)
      }
    },
    legend: {
      position: 'top',
      horizontalAlign: 'right',
      fontSize: '12px',
      markers: { width: 8, height: 8, radius: 4 }
    }
  };

  new ApexCharts(document.getElementById('financial-chart'), opts).render();
});