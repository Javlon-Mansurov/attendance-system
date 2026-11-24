// Лексияи 11 · JavaScript: интерактивият
// Лоиҳа: «Баҳисобгирии вақти кории кормандон»

/* ---------- 1. Ёрирасонҳо ---------- */

/** Debounce: як дархост барои як калима (closure нигоҳ медорад timerId-ро). */
export function debounce(fn, delay = 300) {
  let timerId; // байни даъватҳо зинда аст — closure
  return (...args) => {
    clearTimeout(timerId); // даъвати нав таймерро аз нав танзим мекунад
    timerId = setTimeout(() => fn(...args), delay);
  };
}

/* ---------- 2. Валидатсияи шакли ариза барои рухсатӣ ---------- */

const leaveForm = document.querySelector('#leave-form');

/** Санаи анҷом набояд пеш аз санаи оғоз бошад. */
export function checkLeaveDates(form) {
  const { start_date: start, end_date: end } = form.elements;
  end.setCustomValidity(''); // аз нав танзим, вагарна майдон абадан нодуруст мемонад
  if (start.value && end.value && end.value < start.value) {
    end.setCustomValidity('Санаи анҷом наметавонад пеш аз санаи оғоз бошад.');
  }
}

function fields(form) {
  return [...form.elements].filter((el) => el.name && el.willValidate);
}

/** Ҳолати як майдонро нишон медиҳад; true бармегардонад, агар дуруст бошад. */
function showFieldState(field) {
  const box = field.form.querySelector(`[data-error-for="${field.name}"]`);
  const ok = field.checkValidity();
  field.classList.toggle('invalid', !ok);
  field.classList.toggle('valid', ok);
  field.setAttribute('aria-invalid', String(!ok));
  if (box) box.textContent = ok ? '' : field.validationMessage; // textContent — на innerHTML
  return ok;
}

if (leaveForm) {
  // Маслиҳати фаврӣ ҳангоми аз майдон баромадан
  leaveForm.addEventListener('focusout', (event) => {
    if (!event.target.name) return;
    checkLeaveDates(leaveForm);
    showFieldState(event.target);
  });
  leaveForm.addEventListener('input', (event) => {
    if (event.target.name === 'start_date' || event.target.name === 'end_date') {
      checkLeaveDates(leaveForm);
      showFieldState(leaveForm.elements.end_date);
    }
  });

  // Ҳангоми фиристодан: хатоҳо дар назди ҳамаи майдонҳо, фокус ба аввалин
  leaveForm.addEventListener('submit', (event) => {
    checkLeaveDates(leaveForm);
    let firstInvalid = null;
    fields(leaveForm).forEach((f) => {
      if (!showFieldState(f) && !firstInvalid) firstInvalid = f;
    });
    if (firstInvalid) {
      event.preventDefault();
      firstInvalid.focus();
    }
    // Эзоҳ: сервер (Validator.php) ва пойгоҳ (CHECK) аз нав месанҷанд.
  });
}

/* ---------- 3. Ҷустуҷӯи зинда: fetch + AbortController ---------- */

const searchForm = document.querySelector('#search-form');
const input = document.querySelector('#q');
const tbody = document.querySelector('#records-body');
const table = document.querySelector('#records-table');
const statusEl = document.querySelector('#status');

let controller = new AbortController();

function setStatus(text, isError = false) {
  statusEl.textContent = text; // aria-live="polite" инро мехонад
  statusEl.classList.toggle('error', isError);
}

function cell(text) {
  const td = document.createElement('td');
  td.textContent = text; // додаҳо — ҳамчун матн, на HTML (ҳимоя аз XSS)
  return td;
}

export function renderRecords(target, records) {
  const rows = records.map((r) => {
    const tr = document.createElement('tr');
    tr.append(
      cell(r.full_name),
      cell(r.work_date),
      cell(r.check_in),
      cell(r.check_out),
      cell(String(r.hours)),
    );

    const actions = document.createElement('td');
    const del = document.createElement('button');
    del.type = 'button';
    del.className = 'danger';
    del.textContent = 'Нест кардан';
    del.dataset.deleteUrl = `/records/${encodeURIComponent(r.id)}/delete`;
    actions.append(del);
    tr.append(actions);
    return tr;
  });
  target.replaceChildren(...rows);
}

async function search(query) {
  controller.abort(); // дархости кӯҳнашударо бекор мекунем
  controller = new AbortController();

  table.setAttribute('aria-busy', 'true');
  setStatus('Ҷустуҷӯ…');
  try {
    const response = await fetch(`/api/records?q=${encodeURIComponent(query)}`, {
      signal: controller.signal,
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`); // 404/500
    const records = await response.json();

    renderRecords(tbody, records);
    setStatus(records.length ? `Ёфт шуд: ${records.length}` : 'Кормандон ёфт нашуданд');
    table.setAttribute('aria-busy', 'false');
  } catch (error) {
    if (error.name === 'AbortError') return; // бекор шуд — ин муқаррарӣ аст
    table.setAttribute('aria-busy', 'false');
    setStatus('Ҷустуҷӯро иҷро кардан муяссар нашуд.', true);
  }
}

if (searchForm && input && tbody) {
  const debouncedSearch = debounce(search, 300);
  input.addEventListener('input', () => debouncedSearch(input.value.trim()));
  // Бо JavaScript — шакл дубора саҳифаро бор намекунад
  searchForm.addEventListener('submit', (event) => {
    event.preventDefault();
    search(input.value.trim());
  });
  search(''); // бор кардани рӯйхати аввала
}

/* ---------- 4. Вогузорӣ (delegation) + тасдиқи модалӣ ---------- */

const modal = document.querySelector('#delete-modal');
const deleteForm = document.querySelector('#delete-form');

// Як коркардкунанда ба ҷои сад — барои сатрҳои баъдтар сохташуда низ кор мекунад
document.addEventListener('click', (event) => {
  const button = event.target.closest('[data-delete-url]');
  if (!button) return; // на ба тугма пахш карданд
  deleteForm.action = button.dataset.deleteUrl;
  modal.showModal();
});

document.querySelector('#delete-cancel')?.addEventListener('click', () => modal.close());
