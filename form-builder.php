<!DOCTYPE html>
<html>

<head>
    <title>Buat Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <form id="builderForm" method="POST" action="save_form.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Form</label>
                        <input type="text" name="form_name" class="form-control" required placeholder="Contoh: Formulir Pendaftaran">
                    </div>
                    <div id="questionContainer"></div>
                    <button type="button" class="btn btn-sm btn-success mt-3" onclick="addQuestion()">+ Tambah Pertanyaan</button>
                    <button type="submit" class="btn btn-sm btn-primary mt-3">Simpan Form</button>
                </form>
            </div>

            <div class="col-md-6">
                <h4 class="mb-3">Preview</h4>
                <form class="form-preview border p-3 bg-light rounded" id="previewForm">
                    <!-- Preview form muncul di sini -->
                </form>
            </div>
        </div>
    </div>

    <script>
        let questionIndex = 0;

        function addQuestion() {
            const builderContainer = document.getElementById('questionContainer');
            const questionId = `q${questionIndex}`;

            const card = document.createElement('div');
            card.className = 'card mb-3 p-3 position-relative';
            card.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2"
                onclick="removeQuestion('${questionId}', this)">
            <i class="fas fa-trash"></i>
        </button>
        <div class="mb-2">
            <label class="form-label">Pertanyaan</label>
            <input type="text" name="questions[${questionIndex}][text]" class="form-control"
                   oninput="updatePreview('${questionId}')" data-preview-id="${questionId}-label" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Tipe Jawaban</label>
            <select name="questions[${questionIndex}][type]" class="form-select"
                    onchange="toggleOptions('${questionId}'); updatePreview('${questionId}', true)"
                    data-preview-id="${questionId}-input" required>
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="checkbox">Checkbox</option>
                <option value="select">Select</option>
                <option value="file">File Upload</option>
            </select>
        </div>
        <div class="mb-2" id="options-${questionId}" style="display: none;">
            <label class="form-label">Opsi (pisahkan dengan koma)</label>
            <input type="text" name="questions[${questionIndex}][options]" class="form-control"
                   oninput="updatePreview('${questionId}')" data-preview-id="${questionId}-options">
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="questions[${questionIndex}][required]" value="1"
                   onchange="updatePreview('${questionId}')" id="required-${questionId}">
            <label class="form-check-label">Wajib Diisi</label>
        </div>
    `;

            builderContainer.appendChild(card);
            createPreview(questionId);
            updatePreview(questionId);
            questionIndex++;
        }

        function toggleOptions(qid) {
            const type = document.querySelector(`[data-preview-id="${qid}-input"]`).value;
            const optionsDiv = document.getElementById(`options-${qid}`);
            if (type === 'file') {
                optionsDiv.style.display = 'block';
                optionsDiv.querySelector('label').textContent = 'Tipe File yang diperbolehkan (pisahkan dengan koma)';
                optionsDiv.querySelector('input').placeholder = 'Contoh: png, jpg, jpeg, gif, pdf, doc, docx';
            } else if (type === 'text' || type === 'textarea') {} else {
                optionsDiv.style.display = ['checkbox', 'select'].includes(type) ? 'block' : 'none';
                optionsDiv.querySelector('label').textContent = 'Opsi (pisahkan dengan koma)';
                optionsDiv.querySelector('input').placeholder = '';
            }
        }

        function createPreview(qid) {
            const previewForm = document.getElementById('previewForm');
            if (!document.getElementById(`preview-${qid}`)) {
                const field = document.createElement('div');
                field.id = `preview-${qid}`;
                field.className = 'mb-3';
                previewForm.appendChild(field);
            }
        }

        function updatePreview(qid) {
            const labelInput = document.querySelector(`[data-preview-id="${qid}-label"]`);
            const typeSelect = document.querySelector(`[data-preview-id="${qid}-input"]`);
            const optionsInput = document.querySelector(`[data-preview-id="${qid}-options"]`);
            const isRequired = document.getElementById(`required-${qid}`).checked;
            const previewField = document.getElementById(`preview-${qid}`);

            if (!labelInput || !typeSelect || !previewField) return;

            const label = labelInput.value || 'Pertanyaan';
            const type = typeSelect.value;
            const options = optionsInput?.value.split(',').map(opt => opt.trim()) || [];

            let inputField = '';

            switch (type) {
                case 'text':
                    inputField = `<input type="text" class="form-control" name="answers[${qid}]" placeholder="Jawaban Anda" ${isRequired ? 'required' : ''}>`;
                    break;
                case 'textarea':
                    inputField = `<textarea class="form-control" name="answers[${qid}]" placeholder="Jawaban Anda" ${isRequired ? 'required' : ''}></textarea>`;
                    break;
                case 'checkbox':
                    inputField = options.map((opt, i) => `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="answers[${qid}][]" id="${qid}-check-${i}" value="${opt}" ${isRequired ? 'required' : ''}>
                    <label class="form-check-label" for="${qid}-check-${i}">${opt}</label>
                </div>
            `).join('');
                    break;
                case 'select':
                    inputField = `
                <select class="form-select" name="answers[${qid}]" ${isRequired ? 'required' : ''}>
                    ${options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                </select>
            `;
                    break;
                case 'file':
                    inputField = `
                <input type="file" class="form-control" name="answers[${qid}]" ${isRequired ? 'required' : ''}>
                <small>Tipe file yang diperbolehkan: png, jpg, jpeg, gif, pdf, doc, docx</small>
            `;
                    break;
            }

            previewField.innerHTML = `
        <label class="form-label">${label}${isRequired ? ' *' : ''}</label>
        ${inputField}
    `;
        }


        function removeQuestion(qid, btn) {
            const card = btn.closest('.card');
            const preview = document.getElementById(`preview-${qid}`);
            if (card) card.remove();
            if (preview) preview.remove();
        }
    </script>
</body>

</html>