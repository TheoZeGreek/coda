const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const progress = document.getElementById('progress');
const result = document.getElementById('result');
const buildBtn = document.getElementById('build-btn');
const historyList = document.getElementById('history');

function updateHistory() {
  historyList.innerHTML = '';
  const items = JSON.parse(localStorage.getItem('history') || '[]');
  items.forEach((item) => {
    const li = document.createElement('li');
    const a = document.createElement('a');
    a.href = item.url;
    a.textContent = item.name;
    li.appendChild(a);
    historyList.appendChild(li);
  });
}

function upload(file) {
  const data = new FormData();
  data.append('file', file);
  progress.hidden = false;
  fetch('../routes.php?route=upload', {
    method: 'POST',
    body: data,
  })
    .then((r) => r.json())
    .then((json) => {
      result.textContent = JSON.stringify(json, null, 2);
      buildBtn.hidden = false;
      buildBtn.onclick = () => build(json);
    })
    .catch(() => alert('Erreur lors du traitement du PDF'))
    .finally(() => { progress.hidden = true; });
}

function build(data) {
  fetch('../routes.php?route=build', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  })
    .then((response) => response.blob()
      .then((blob) => ({ blob, headers: response.headers })))
    .then(({ blob, headers }) => {
      const url = URL.createObjectURL(blob);
      const filename = headers.get('X-Filename') || 'coda.cod';
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      const items = JSON.parse(localStorage.getItem('history') || '[]');
      items.unshift({ name: filename, url });
      localStorage.setItem('history', JSON.stringify(items.slice(0, 5)));
      updateHistory();
    })
    .catch(() => alert('Erreur de génération du CODA'));
}

dropZone.addEventListener('dragover', (e) => {
  e.preventDefault();
});

dropZone.addEventListener('drop', (e) => {
  e.preventDefault();
  if (e.dataTransfer.files.length) {
    upload(e.dataTransfer.files[0]);
  }
});

fileInput.addEventListener('change', (e) => {
  if (e.target.files.length) {
    upload(e.target.files[0]);
  }
});

updateHistory();
