/* ============================================================
   SOBAT BUMI SAMARINDA — Admin JS
   Sidebar, date, table filter, pagination, action menu,
   clickable table rows, article editor, upload preview,
   validation popup, toast, confirmation modal.
   ============================================================ */

if (!window.SBS_ADMIN_INITIALIZED) {
  window.SBS_ADMIN_INITIALIZED = true;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdmin, { once: true });
  } else {
    initAdmin();
  }
}

function initAdmin() {
  initCurrentDate();
  initAdminFeedback();
  initAdminSidebar();
  initDashboardArticleTable();
  initTableActionMenu();
  initClickableTableRows();
  initConfirmableActions();
  initAutoFilterForms();
  initArticleEditor();
}

/* ============================================================
   Current Date
   ============================================================ */

function initCurrentDate() {
  const dateEl = document.getElementById('currentDate');

  if (!dateEl) return;

  dateEl.textContent = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}

/* ============================================================
   Sidebar Mobile
   ============================================================ */

function initAdminSidebar() {
  const toggle = document.querySelector('[data-sidebar-toggle]');
  const overlay = document.querySelector('[data-sidebar-overlay]');

  function setSidebar(open) {
    document.body.classList.toggle('sidebar-open', open);

    if (toggle) {
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      setSidebar(!document.body.classList.contains('sidebar-open'));
    });
  }

  if (overlay) {
    overlay.addEventListener('click', function () {
      setSidebar(false);
    });
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      setSidebar(false);
      closeAllActionMenus();
      closeArticlePreview();
    }
  });
}

/* ============================================================
   Admin Feedback Toast
   Supports:
   <div data-toast="success" data-toast-title="Berhasil">...</div>
   <div data-toast="danger" data-toast-title="Gagal">...</div>
   ============================================================ */

function initAdminFeedback() {
  document.querySelectorAll('[data-toast]').forEach(function (alertEl) {
    const type = alertEl.dataset.toast || (alertEl.classList.contains('danger') ? 'danger' : 'success');
    const title = alertEl.dataset.toastTitle || (type === 'danger' ? 'Perlu diperbaiki' : 'Berhasil');
    const message = alertEl.textContent.replace(/\s+/g, ' ').trim();

    if (!message) return;

    showAdminToast({
      type,
      title,
      message,
    });
  });
}

function showAdminToast(options) {
  const config = Object.assign(
    {
      type: 'success',
      title: 'Berhasil',
      message: '',
      duration: 4200,
    },
    options || {}
  );

  const stack = getToastStack();
  const toast = document.createElement('div');

  toast.className = `admin-toast ${config.type}`;
  toast.setAttribute('role', config.type === 'danger' ? 'alert' : 'status');

  toast.innerHTML = `
    <div class="admin-toast-icon" aria-hidden="true">${getToastIcon(config.type)}</div>
    <div class="admin-toast-copy">
      <strong></strong>
      <p></p>
    </div>
    <button type="button" class="admin-toast-close" aria-label="Tutup notifikasi">×</button>
  `;

  const titleEl = toast.querySelector('strong');
  const messageEl = toast.querySelector('p');
  const closeBtn = toast.querySelector('.admin-toast-close');

  if (titleEl) titleEl.textContent = config.title;
  if (messageEl) messageEl.textContent = config.message;

  function closeToast() {
    toast.classList.add('is-leaving');

    window.setTimeout(function () {
      toast.remove();
    }, 190);
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', closeToast);
  }

  stack.appendChild(toast);

  if (config.duration > 0) {
    window.setTimeout(closeToast, config.duration);
  }

  return toast;
}

function getToastStack() {
  let stack = document.querySelector('.admin-toast-stack');

  if (!stack) {
    stack = document.createElement('div');
    stack.className = 'admin-toast-stack';
    stack.setAttribute('aria-live', 'polite');
    stack.setAttribute('aria-relevant', 'additions removals');
    document.body.appendChild(stack);
  }

  return stack;
}

function getToastIcon(type) {
  if (type === 'danger') return '!';
  if (type === 'warning') return 'i';
  return '✓';
}

/* ============================================================
   Confirmation Modal
   Supports:
   <form data-confirm
         data-confirm-title="Hapus data?"
         data-confirm-message="Data tidak bisa dikembalikan."
         data-confirm-ok="Hapus"
         data-confirm-cancel="Batal">
   ============================================================ */

function initConfirmableActions() {
  document.addEventListener('submit', function (event) {
    if (event.defaultPrevented) return;

    const form = event.target.closest('form[data-confirm]');

    if (!form || form.dataset.confirmed === 'true') return;

    event.preventDefault();
    event.stopPropagation();

    showAdminConfirm({
      title: form.dataset.confirmTitle || 'Lanjutkan aksi ini?',
      message: form.dataset.confirmMessage || 'Pastikan data yang dipilih sudah benar.',
      okText: form.dataset.confirmOk || 'Lanjutkan',
      cancelText: form.dataset.confirmCancel || 'Batal',
      danger: isDangerConfirmation(form),
    }).then(function (confirmed) {
      if (!confirmed) return;

      form.dataset.confirmed = 'true';
      submitFormDirectly(form);
    });
  });
}

function showAdminConfirm(options) {
  const config = Object.assign(
    {
      title: 'Lanjutkan aksi ini?',
      message: 'Pastikan data yang dipilih sudah benar.',
      okText: 'Lanjutkan',
      cancelText: 'Batal',
      danger: false,
    },
    options || {}
  );

  return new Promise(function (resolve) {
    const modal = document.createElement('div');

    modal.className = 'admin-confirm-modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');

    modal.innerHTML = `
      <div class="admin-confirm-backdrop" data-confirm-cancel></div>
      <section class="admin-confirm-dialog" aria-labelledby="adminConfirmTitle">
        <div class="admin-confirm-icon" aria-hidden="true">!</div>
        <h2 id="adminConfirmTitle"></h2>
        <p></p>
        <div class="admin-confirm-actions">
          <button type="button" class="admin-btn admin-btn-secondary admin-confirm-cancel" data-confirm-cancel></button>
          <button type="button" class="admin-btn admin-btn-primary admin-confirm-ok${config.danger ? ' is-danger' : ''}" data-confirm-ok></button>
        </div>
      </section>
    `;

    const titleEl = modal.querySelector('h2');
    const messageEl = modal.querySelector('p');
    const cancelBtn = modal.querySelector('.admin-confirm-cancel');
    const okBtn = modal.querySelector('.admin-confirm-ok');

    if (titleEl) titleEl.textContent = config.title;
    if (messageEl) messageEl.textContent = config.message;
    if (cancelBtn) cancelBtn.textContent = config.cancelText;
    if (okBtn) okBtn.textContent = config.okText;

    const previousOverflow = document.body.style.overflow;
    const previouslyFocused = document.activeElement;

    function cleanup(value) {
      document.body.style.overflow = previousOverflow;
      modal.remove();
      document.removeEventListener('keydown', handleKeydown);

      if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
        previouslyFocused.focus();
      }

      resolve(value);
    }

    function handleKeydown(event) {
      if (event.key === 'Escape') {
        cleanup(false);
      }
    }

    modal.querySelectorAll('[data-confirm-cancel]').forEach(function (button) {
      button.addEventListener('click', function () {
        cleanup(false);
      });
    });

    if (okBtn) {
      okBtn.addEventListener('click', function () {
        cleanup(true);
      });
    }

    document.addEventListener('keydown', handleKeydown);

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    window.setTimeout(function () {
      if (okBtn) okBtn.focus();
    }, 0);
  });
}

function isDangerConfirmation(form) {
  const method = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
  const okText = (form.dataset.confirmOk || '').toLowerCase();
  const title = (form.dataset.confirmTitle || '').toLowerCase();
  const message = (form.dataset.confirmMessage || '').toLowerCase();

  return (
    method === 'DELETE' ||
    okText.includes('hapus') ||
    title.includes('hapus') ||
    message.includes('hapus') ||
    Boolean(form.querySelector('.danger, .admin-btn-danger, .table-action-item.danger'))
  );
}

function submitFormDirectly(form) {
  HTMLFormElement.prototype.submit.call(form);
}

/* ============================================================
   Auto Filter Forms
   Supports:
   <form data-auto-filter>
   ============================================================ */

function initAutoFilterForms() {
  document.querySelectorAll('[data-auto-filter]').forEach(function (form) {
    let timer = null;

    function submitFilter() {
      if (form.dataset.submitting === 'true') return;

      form.dataset.submitting = 'true';
      submitFormDirectly(form);
    }

    form.querySelectorAll('select').forEach(function (select) {
      select.addEventListener('change', submitFilter);
    });

    form.querySelectorAll('input[type="search"], input[type="text"]').forEach(function (input) {
      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = window.setTimeout(submitFilter, 520);
      });
    });
  });
}

/* ============================================================
   Dashboard / Manage News Table
   ============================================================ */

function initDashboardArticleTable() {
  const table = document.getElementById('dashboardArticleTable');

  if (!table) return;

  const searchInput = document.getElementById('articleSearch');
  const categoryFilter = document.getElementById('categoryFilter');
  const statusFilter = document.getElementById('statusFilter');
  const pagination = document.getElementById('articlePagination');
  const tableInfo = document.getElementById('tableInfo');
  const emptyRow = document.getElementById('emptyRow');

  const rows = Array.from(table.querySelectorAll('tbody tr[data-title]'));
  const perPage = Number(table.dataset.perPage || 10);

  let currentPage = 1;

  function getFilteredRows() {
    const keyword = normalizeText(searchInput?.value || '');
    const category = normalizeText(categoryFilter?.value || 'all');
    const status = normalizeText(statusFilter?.value || 'all');

    return rows.filter(function (row) {
      const rowTitle = normalizeText(row.dataset.title || '');
      const rowCategory = normalizeText(row.dataset.category || '');
      const rowStatus = normalizeText(row.dataset.status || '');
      const rowAuthor = normalizeText(row.dataset.author || '');

      const matchKeyword =
        !keyword ||
        rowTitle.includes(keyword) ||
        rowCategory.includes(keyword) ||
        rowAuthor.includes(keyword);

      const matchCategory = category === 'all' || rowCategory === category;
      const matchStatus = status === 'all' || rowStatus === status;

      return matchKeyword && matchCategory && matchStatus;
    });
  }

  function renderTable() {
    const filteredRows = getFilteredRows();
    const totalItems = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / perPage));

    if (currentPage > totalPages) {
      currentPage = totalPages;
    }

    const startIndex = (currentPage - 1) * perPage;
    const endIndex = startIndex + perPage;

    rows.forEach(function (row) {
      row.hidden = true;
    });

    filteredRows.slice(startIndex, endIndex).forEach(function (row) {
      row.hidden = false;
    });

    if (emptyRow) {
      emptyRow.hidden = totalItems !== 0;
    }

    const startNumber = totalItems === 0 ? 0 : startIndex + 1;
    const endNumber = Math.min(endIndex, totalItems);

    if (tableInfo) {
      tableInfo.textContent = `Menampilkan ${startNumber}–${endNumber} dari ${totalItems} artikel`;
    }

    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    if (!pagination) return;

    pagination.innerHTML = '';

    pagination.appendChild(
      createPageButton(
        '‹',
        currentPage === 1,
        function () {
          if (currentPage > 1) {
            currentPage -= 1;
            renderTable();
          }
        },
        'Halaman sebelumnya'
      )
    );

    getVisiblePages(totalPages, currentPage).forEach(function (page) {
      if (page === 'dots') {
        const dots = document.createElement('span');
        dots.className = 'pagination-dots';
        dots.textContent = '...';
        pagination.appendChild(dots);
        return;
      }

      const button = createPageButton(
        page,
        false,
        function () {
          currentPage = page;
          renderTable();
        },
        `Halaman ${page}`
      );

      if (page === currentPage) {
        button.classList.add('active');
        button.setAttribute('aria-current', 'page');
      }

      pagination.appendChild(button);
    });

    pagination.appendChild(
      createPageButton(
        '›',
        currentPage === totalPages,
        function () {
          if (currentPage < totalPages) {
            currentPage += 1;
            renderTable();
          }
        },
        'Halaman berikutnya'
      )
    );
  }

  function createPageButton(label, disabled, onClick, ariaLabel) {
    const button = document.createElement('button');

    button.type = 'button';
    button.textContent = label;
    button.disabled = disabled;

    if (ariaLabel) {
      button.setAttribute('aria-label', ariaLabel);
    }

    button.addEventListener('click', onClick);

    return button;
  }

  function getVisiblePages(totalPages, activePage) {
    const pages = [];

    if (totalPages <= 5) {
      for (let page = 1; page <= totalPages; page += 1) {
        pages.push(page);
      }

      return pages;
    }

    pages.push(1);

    if (activePage > 3) {
      pages.push('dots');
    }

    const start = Math.max(2, activePage - 1);
    const end = Math.min(totalPages - 1, activePage + 1);

    for (let page = start; page <= end; page += 1) {
      pages.push(page);
    }

    if (activePage < totalPages - 2) {
      pages.push('dots');
    }

    pages.push(totalPages);

    return pages;
  }

  [searchInput, categoryFilter, statusFilter].forEach(function (input) {
    if (!input) return;

    input.addEventListener('input', function () {
      currentPage = 1;
      renderTable();
    });

    input.addEventListener('change', function () {
      currentPage = 1;
      renderTable();
    });
  });

  renderTable();
}

/* ============================================================
   Table Action Menu
   ============================================================ */

function initTableActionMenu() {
  document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-action-menu-trigger]');
    const panel = event.target.closest('[data-action-menu-panel]');

    if (panel) {
      event.stopPropagation();
      return;
    }

    if (!trigger) {
      closeAllActionMenus();
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    const menu = trigger.closest('[data-action-menu]');

    if (!menu) return;

    const isOpen = menu.classList.contains('is-open');

    closeAllActionMenus();

    if (!isOpen) {
      openActionMenu(menu, trigger);
    }
  });

  window.addEventListener('resize', closeAllActionMenus);
  window.addEventListener('scroll', closeAllActionMenus, true);
}

function openActionMenu(menu, trigger) {
  const panel = menu.querySelector('[data-action-menu-panel]');

  if (!panel) return;

  menu.classList.add('is-open');
  trigger.setAttribute('aria-expanded', 'true');

  panel.style.display = 'grid';

  const triggerRect = trigger.getBoundingClientRect();
  const panelRect = panel.getBoundingClientRect();

  const gap = 8;
  const edge = 12;

  let top = triggerRect.bottom + gap;
  let left = triggerRect.right - panelRect.width;

  if (left < edge) {
    left = edge;
  }

  if (left + panelRect.width > window.innerWidth - edge) {
    left = window.innerWidth - panelRect.width - edge;
  }

  if (top + panelRect.height > window.innerHeight - edge) {
    top = triggerRect.top - panelRect.height - gap;
  }

  if (top < edge) {
    top = edge;
  }

  panel.style.top = `${top}px`;
  panel.style.left = `${left}px`;
}

function closeAllActionMenus() {
  document.querySelectorAll('[data-action-menu].is-open').forEach(function (menu) {
    const trigger = menu.querySelector('[data-action-menu-trigger]');
    const panel = menu.querySelector('[data-action-menu-panel]');

    menu.classList.remove('is-open');

    if (trigger) {
      trigger.setAttribute('aria-expanded', 'false');
    }

    if (panel) {
      panel.style.display = '';
      panel.style.top = '';
      panel.style.left = '';
    }
  });
}

/* ============================================================
   Clickable Table Rows
   ============================================================ */

function initClickableTableRows() {
  document.addEventListener('click', function (event) {
    const ignoredElement = event.target.closest(
      'a, button, input, select, textarea, label, form, [data-action-menu], [data-action-menu-panel]'
    );

    if (ignoredElement) return;

    const row = event.target.closest('tr[data-row-link]');

    if (!row || row.hidden) return;

    window.location.href = row.dataset.rowLink;
  });

  document.addEventListener('keydown', function (event) {
    const row = event.target.closest('tr[data-row-link]');

    if (!row || row.hidden) return;

    if (event.key !== 'Enter' && event.key !== ' ') return;

    event.preventDefault();
    window.location.href = row.dataset.rowLink;
  });
}

/* ============================================================
   Article Editor Page
   ============================================================ */

function initArticleEditor() {
  const form = document.getElementById('articleForm');

  if (!form) return;

  const titleInput = document.getElementById('title');
  const slugInput = document.getElementById('slug');
  const summaryInput = document.getElementById('summary');
  const summaryCounter = document.getElementById('summaryCounter');
  const contentInput = document.getElementById('content');
  const statusInput = document.getElementById('status');
  const categoryInput = document.getElementById('category');
  const authorInput = document.getElementById('author');
  const authorPreviewText = document.getElementById('authorPreviewText');

  const addCategoryBtn = document.getElementById('addCategoryBtn');
  const customCategoryBox = document.getElementById('customCategoryBox');
  const customCategoryInput = document.getElementById('customCategoryInput');
  const saveCategoryBtn = document.getElementById('saveCategoryBtn');

  const uploadBox = document.getElementById('uploadBox');
  const imageInput = document.getElementById('imageInput');
  const uploadPreview = document.getElementById('uploadPreview');
  const uploadPlaceholder = document.getElementById('uploadPlaceholder');
  const previewImg = document.getElementById('previewImg');
  const removeImageBtn = document.getElementById('removeImageBtn');
  const uploadHint = document.getElementById('uploadHint');
  const removeImageFlag = document.getElementById('removeImageFlag');
  const uploadChangeHint = uploadBox ? uploadBox.querySelector('.upload-change-hint') : null;

  const previewBtn = document.getElementById('previewBtn');
  const previewModal = document.getElementById('previewModal');
  const previewTitle = document.getElementById('previewTitle');
  const previewCategory = document.getElementById('previewCategory');
  const previewStatus = document.getElementById('previewStatus');
  const previewAuthor = document.getElementById('previewAuthor');
  const previewSummary = document.getElementById('previewSummary');
  const previewContent = document.getElementById('previewContent');
  const previewCover = document.getElementById('previewCover');
  const previewCoverImg = document.getElementById('previewCoverImg');

  const publishBtn = document.getElementById('publishBtn');
  const draftBtn = document.getElementById('draftBtn');
  const deleteArticleBtn = document.getElementById('deleteArticleBtn');
  const deleteForm = document.getElementById('deleteForm');

  let slugTouched = Boolean(slugInput?.value);

  function makeSlug(value) {
    return String(value || '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9\s-]/g, '')
      .trim()
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }

  function updateSlug() {
    if (!titleInput || !slugInput) return;

    if (slugTouched && slugInput.value.trim()) return;

    slugInput.value = makeSlug(titleInput.value);
  }

  function updateSummaryCounter() {
    if (!summaryInput || !summaryCounter) return;

    summaryCounter.textContent = summaryInput.value.length;
  }

  function updateAuthorPreview() {
    if (!authorInput || !authorPreviewText) return;

    authorPreviewText.textContent = authorInput.value.trim() || 'Admin SBS';
  }

  function updatePreviewButtonVisibility() {
    if (!previewBtn) return;

    const hasTitle = Boolean(titleInput?.value.trim());
    const hasSummary = Boolean(summaryInput?.value.trim());
    const hasContent = Boolean(contentInput?.value.trim());

    previewBtn.hidden = !(hasTitle || hasSummary || hasContent);
  }

  function addNewCategory() {
    if (!categoryInput || !customCategoryInput || !customCategoryBox) return false;

    clearFieldInvalid(customCategoryInput);

    const value = customCategoryInput.value.trim();

    if (!value) {
      showAdminToast({
        type: 'danger',
        title: 'Kategori belum diisi',
        message: 'Nama kategori baru wajib diisi sebelum ditambahkan.',
      });

      markFieldInvalid(customCategoryInput, 'Nama kategori baru wajib diisi.');
      customCategoryInput.focus();

      return false;
    }

    const exists = Array.from(categoryInput.options).some(function (option) {
      return option.value.toLowerCase() === value.toLowerCase();
    });

    if (!exists) {
      const option = new Option(value, value, true, true);
      categoryInput.add(option);
    }

    categoryInput.value = value;
    customCategoryInput.value = '';
    customCategoryBox.hidden = true;

    clearFieldInvalid(categoryInput);

    showAdminToast({
      type: 'success',
      title: 'Kategori ditambahkan',
      message: `Kategori "${value}" siap digunakan.`,
      duration: 2600,
    });

    return true;
  }

  function insertText(before, after = '', placeholder = '') {
    if (!contentInput) return;

    const start = contentInput.selectionStart;
    const end = contentInput.selectionEnd;
    const selectedText = contentInput.value.slice(start, end) || placeholder;
    const nextText = before + selectedText + after;

    contentInput.setRangeText(nextText, start, end, 'end');
    contentInput.focus();

    const cursorPosition = start + before.length + selectedText.length + after.length;
    contentInput.setSelectionRange(cursorPosition, cursorPosition);

    updatePreviewButtonVisibility();
  }

  function handleEditorAction(action) {
    const actions = {
      bold: function () {
        insertText('**', '**', 'teks tebal');
      },
      italic: function () {
        insertText('*', '*', 'teks miring');
      },
      underline: function () {
        insertText('<u>', '</u>', 'teks bergaris bawah');
      },
      ul: function () {
        insertText('- ', '', 'poin daftar');
      },
      ol: function () {
        insertText('1. ', '', 'poin daftar');
      },
      quote: function () {
        insertText('> ', '', 'kutipan');
      },
      image: function () {
        insertText('![Deskripsi gambar](', ')', 'https://contoh.com/gambar.jpg');
      },
      link: function () {
        insertText('[', '](https://contoh.com)', 'teks link');
      },
      code: function () {
        insertText('`', '`', 'kode');
      },
    };

    if (actions[action]) {
      actions[action]();
    }
  }

  function validateImage(file) {
    if (!file) return false;

    clearFieldInvalid(uploadBox);

    const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
    const maxSize = 5 * 1024 * 1024;

    if (!allowedTypes.includes(file.type)) {
      showAdminToast({
        type: 'danger',
        title: 'Format gambar tidak sesuai',
        message: 'Gunakan file PNG, JPG, JPEG, atau WebP.',
      });

      markFieldInvalid(uploadBox, 'Format gambar harus PNG, JPG, JPEG, atau WebP.');

      if (imageInput) {
        imageInput.value = '';
      }

      return false;
    }

    if (file.size > maxSize) {
      showAdminToast({
        type: 'danger',
        title: 'Ukuran gambar terlalu besar',
        message: 'Ukuran foto sampul maksimal 5MB.',
      });

      markFieldInvalid(uploadBox, 'Ukuran gambar maksimal 5MB.');

      if (imageInput) {
        imageInput.value = '';
      }

      return false;
    }

    return true;
  }

  function setUploadPreviewState(hasPreview) {
    if (!uploadBox || !uploadPreview || !uploadPlaceholder) return;

    uploadBox.classList.toggle('has-preview', hasPreview);

    uploadPreview.hidden = !hasPreview;
    uploadPlaceholder.hidden = hasPreview;

    if (uploadChangeHint) {
      uploadChangeHint.hidden = !hasPreview;
    }

    if (uploadHint) {
      uploadHint.textContent = hasPreview
        ? 'Foto sampul siap digunakan. Klik area gambar untuk mengganti.'
        : 'Belum ada file baru dipilih.';
    }
  }

  function previewImage(file) {
    if (!file || !validateImage(file)) return;

    const reader = new FileReader();

    reader.onload = function (event) {
      if (previewImg) {
        previewImg.src = event.target.result;
      }

      if (removeImageFlag) {
        removeImageFlag.value = '0';
      }

      setUploadPreviewState(true);

      if (uploadHint) {
        uploadHint.textContent = file.name;
      }

      showAdminToast({
        type: 'success',
        title: 'Gambar dipilih',
        message: 'Foto sampul berhasil dimuat sebagai preview.',
        duration: 2600,
      });
    };

    reader.readAsDataURL(file);
  }

  function clearImagePreview(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    if (imageInput) {
      imageInput.value = '';
    }

    if (previewImg) {
      previewImg.src = '';
    }

    if (removeImageFlag) {
      removeImageFlag.value = '1';
    }

    clearFieldInvalid(uploadBox);
    setUploadPreviewState(false);

    showAdminToast({
      type: 'warning',
      title: 'Gambar dihapus',
      message: 'Foto sampul akan dikosongkan setelah data disimpan.',
      duration: 3000,
    });
  }

  function openPreview() {
    if (!previewModal) return;

    const title = titleInput?.value.trim() || 'Judul berita belum diisi';
    const summary = summaryInput?.value.trim() || 'Ringkasan berita belum diisi.';
    const content = contentInput?.value.trim() || 'Isi berita belum diisi.';
    const category = categoryInput?.value.trim() || 'Tanpa kategori';
    const status = statusInput?.value || 'draft';
    const author = authorInput?.value.trim() || 'Admin SBS';

    if (previewTitle) previewTitle.textContent = title;
    if (previewSummary) previewSummary.textContent = summary;
    if (previewContent) previewContent.textContent = content;
    if (previewCategory) previewCategory.textContent = category;
    if (previewStatus) previewStatus.textContent = status === 'published' ? 'Published' : 'Draft';
    if (previewAuthor) previewAuthor.textContent = author;

    if (previewImg?.src && previewCoverImg && previewCover) {
      previewCoverImg.src = previewImg.src;
      previewCover.hidden = false;
    } else if (previewCover) {
      previewCover.hidden = true;
    }

    previewModal.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  window.closeArticlePreview = function () {
    if (!previewModal || previewModal.hidden) return;

    previewModal.hidden = true;
    document.body.style.overflow = '';
  };

  function validateArticleForm(event) {
    clearValidationState(form);

    if (customCategoryBox && !customCategoryBox.hidden && customCategoryInput?.value.trim()) {
      addNewCategory();
    }

    const requiredFields = [
      {
        input: titleInput,
        message: 'Judul berita wajib diisi.',
      },
      {
        input: summaryInput,
        message: 'Ringkasan singkat wajib diisi.',
      },
      {
        input: contentInput,
        message: 'Isi berita wajib diisi.',
      },
      {
        input: categoryInput,
        message: 'Kategori berita wajib dipilih.',
      },
    ];

    for (const field of requiredFields) {
      if (!field.input || !field.input.value.trim()) {
        event.preventDefault();

        showAdminToast({
          type: 'danger',
          title: 'Form belum lengkap',
          message: field.message,
        });

        markFieldInvalid(field.input, field.message);
        field.input?.focus();

        return false;
      }
    }

    return true;
  }

  titleInput?.addEventListener('input', function () {
    clearFieldInvalid(titleInput);
    updateSlug();
    updatePreviewButtonVisibility();
  });

  slugInput?.addEventListener('input', function () {
    slugTouched = true;
    slugInput.value = makeSlug(slugInput.value);
    clearFieldInvalid(slugInput);
  });

  summaryInput?.addEventListener('input', function () {
    clearFieldInvalid(summaryInput);
    updateSummaryCounter();
    updatePreviewButtonVisibility();
  });

  contentInput?.addEventListener('input', function () {
    clearFieldInvalid(contentInput);
    updatePreviewButtonVisibility();
  });

  categoryInput?.addEventListener('change', function () {
    clearFieldInvalid(categoryInput);
  });

  authorInput?.addEventListener('input', updateAuthorPreview);

  addCategoryBtn?.addEventListener('click', function () {
    if (!customCategoryBox || !customCategoryInput) return;

    customCategoryBox.hidden = !customCategoryBox.hidden;

    if (!customCategoryBox.hidden) {
      customCategoryInput.focus();
    }
  });

  saveCategoryBtn?.addEventListener('click', addNewCategory);

  customCategoryInput?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      addNewCategory();
    }
  });

  document.querySelectorAll('[data-editor-action]').forEach(function (button) {
    button.addEventListener('click', function () {
      handleEditorAction(button.dataset.editorAction);
    });
  });

  uploadBox?.addEventListener('click', function (event) {
    if (event.target.closest('button')) return;
    if (event.target.closest('input[type="file"]')) return;

    imageInput?.click();
  });

  uploadBox?.addEventListener('keydown', function (event) {
    if (event.key !== 'Enter' && event.key !== ' ') return;

    event.preventDefault();
    imageInput?.click();
  });

  imageInput?.addEventListener('change', function () {
    const file = imageInput.files && imageInput.files[0];

    if (file) {
      previewImage(file);
    }
  });

  ['dragenter', 'dragover'].forEach(function (eventName) {
    uploadBox?.addEventListener(eventName, function (event) {
      event.preventDefault();

      uploadBox.classList.add('is-dragging');
      uploadBox.classList.add('dragging');
    });
  });

  ['dragleave', 'drop'].forEach(function (eventName) {
    uploadBox?.addEventListener(eventName, function (event) {
      event.preventDefault();

      uploadBox.classList.remove('is-dragging');
      uploadBox.classList.remove('dragging');

      if (eventName === 'drop' && event.dataTransfer.files.length) {
        const file = event.dataTransfer.files[0];

        if (!validateImage(file)) return;

        if (window.DataTransfer && imageInput) {
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          imageInput.files = dataTransfer.files;
        }

        previewImage(file);
      }
    });
  });

  removeImageBtn?.addEventListener('click', clearImagePreview);

  previewBtn?.addEventListener('click', openPreview);

  document.querySelectorAll('[data-preview-close]').forEach(function (button) {
    button.addEventListener('click', window.closeArticlePreview);
  });

  publishBtn?.addEventListener('click', function () {
    if (statusInput) statusInput.value = 'published';
  });

  draftBtn?.addEventListener('click', function () {
    if (statusInput) statusInput.value = 'draft';
  });

  deleteArticleBtn?.addEventListener('click', function () {
    if (!deleteForm) return;

    showAdminConfirm({
      title: deleteArticleBtn.dataset.confirmTitle || 'Hapus berita?',
      message: deleteArticleBtn.dataset.confirmMessage || 'Data berita yang dihapus tidak bisa dikembalikan.',
      okText: deleteArticleBtn.dataset.confirmOk || 'Hapus',
      cancelText: deleteArticleBtn.dataset.confirmCancel || 'Batal',
      danger: true,
    }).then(function (confirmed) {
      if (!confirmed) return;

      submitFormDirectly(deleteForm);
    });
  });

  form.addEventListener('submit', validateArticleForm);

  updateSummaryCounter();
  updateAuthorPreview();
  updatePreviewButtonVisibility();

  if (previewImg?.getAttribute('src')) {
    setUploadPreviewState(true);
  }
}

/* ============================================================
   Field Validation Helpers
   ============================================================ */

function markFieldInvalid(field, message) {
  if (!field) return;

  const target = getFieldVisualTarget(field);

  if (!target) return;

  target.classList.add('is-invalid');

  const container =
    field.closest?.('.form-field') ||
    target.closest?.('.form-field') ||
    target.parentElement;

  if (!container) return;

  let error = container.querySelector('.field-error-text');

  if (!error) {
    error = document.createElement('p');
    error.className = 'field-error-text';
    container.appendChild(error);
  }

  error.textContent = message;
}

function clearFieldInvalid(field) {
  if (!field) return;

  const target = getFieldVisualTarget(field);

  if (target) {
    target.classList.remove('is-invalid');
  }

  const container =
    field.closest?.('.form-field') ||
    target?.closest?.('.form-field') ||
    target?.parentElement;

  const error = container?.querySelector('.field-error-text');

  if (error) {
    error.remove();
  }
}

function clearValidationState(root) {
  if (!root) return;

  root.querySelectorAll('.is-invalid').forEach(function (item) {
    item.classList.remove('is-invalid');
  });

  root.querySelectorAll('.field-error-text').forEach(function (item) {
    item.remove();
  });
}

function getFieldVisualTarget(field) {
  if (!field) return null;

  if (field.classList?.contains('article-upload-box')) {
    return field;
  }

  return (
    field.closest?.(
      '.input-prefix, .article-slug-input, .input-icon, .login-input, .article-upload-box, .editor-box, .article-editor-box'
    ) || field
  );
}

/* ============================================================
   Helpers
   ============================================================ */

function normalizeText(value) {
  return String(value)
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();
}

function closeArticlePreview() {
  const previewModal = document.getElementById('previewModal');

  if (!previewModal || previewModal.hidden) return;

  previewModal.hidden = true;
  document.body.style.overflow = '';
}