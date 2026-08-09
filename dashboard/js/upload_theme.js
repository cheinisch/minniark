/**
 * upload_theme.js
 * Upload Theme modal:
 *   - Drag & drop / file picker
 *   - Live image.png preview from ZIP (FileReader + JSZip fallback-free, pure ZipArchive)
 *   - XHR upload with progress bar + X-Requested-With header
 *   - Success toast on page reload (via ?uploaded= query param)
 *   - Error feedback inline
 */

(function () {
    'use strict';

    // ── Element refs ──────────────────────────────────────────────────────
    const openBtn      = document.getElementById('openUploadThemeBtn');
    const modal        = document.getElementById('uploadThemeModal');

    if (!openBtn || !modal) return;

    const closeBtns    = modal.querySelectorAll('[data-close-upload-modal]');
    const dropZone     = document.getElementById('uploadThemeDropZone');
    const fileInput    = document.getElementById('uploadThemeFileInput');
    const browseBtn    = document.getElementById('uploadThemeBrowseBtn');
    const fileNameEl   = document.getElementById('uploadThemeFileName');
    const progressWrap = document.getElementById('uploadThemeProgress');
    const progressBar  = document.getElementById('uploadThemeProgressBar');
    const progressPct  = document.getElementById('uploadThemeProgressPct');
    const submitBtn    = document.getElementById('uploadThemeSubmit');
    const feedback     = document.getElementById('uploadThemeFeedback');
    const feedbackMsg  = document.getElementById('uploadThemeFeedbackMsg');
    const previewWrap  = document.getElementById('uploadThemePreview');
    const previewImg   = document.getElementById('uploadThemePreviewImg');

    let selectedFile = null;

    // ── Toast on page load (after successful upload + reload) ─────────────

    (function showUploadedToast() {
        const params = new URLSearchParams(location.search);
        const name   = params.get('uploaded');
        if (!name) return;

        // Remove param from URL without reload
        const clean = location.pathname + location.search.replace(/[?&]uploaded=[^&]*/g, '').replace(/^&/, '?');
        history.replaceState(null, '', clean || location.pathname);

        showToast('✓ Theme "' + decodeURIComponent(name) + '" installed successfully.', 'success');
    })();

    // ── Modal open / close ────────────────────────────────────────────────

    function openModal() {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        resetForm();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        resetForm();
    }

    openBtn.addEventListener('click', openModal);
    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    modal.addEventListener('click', function (e) {
        if (e.target === modal || e.target.hasAttribute('data-upload-modal-backdrop')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // ── File selection ────────────────────────────────────────────────────

    browseBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) setFile(this.files[0]);
    });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('border-cyan-500', 'bg-cyan-50', 'dark:bg-cyan-900/20');
    });

    ['dragleave', 'dragend'].forEach(evt => {
        dropZone.addEventListener(evt, () =>
            dropZone.classList.remove('border-cyan-500', 'bg-cyan-50', 'dark:bg-cyan-900/20')
        );
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('border-cyan-500', 'bg-cyan-50', 'dark:bg-cyan-900/20');
        const file = e.dataTransfer.files[0];
        if (file) setFile(file);
    });

    function setFile(file) {
        if (!file.name.toLowerCase().endsWith('.zip')) {
            showFeedback(false, 'Only .zip files are accepted.');
            return;
        }
        selectedFile = file;
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
        submitBtn.disabled = false;
        hideFeedback();
        extractPreview(file);
    }

    // ── ZIP preview: extract image.png client-side ────────────────────────
    // Uses the native DecompressionStream (Chrome 80+, FF 113+, Safari 16.4+)
    // with a simple ZIP central-directory parser — no external lib required.

    async function extractPreview(file) {
        if (!previewWrap || !previewImg) return;

        try {
            const buffer  = await file.arrayBuffer();
            const bytes   = new Uint8Array(buffer);
            const pngData = findFileInZip(bytes, 'image.png');
            if (!pngData) return;

            const blob = new Blob([pngData], { type: 'image/png' });
            previewImg.src = URL.createObjectURL(blob);
            previewWrap.classList.remove('hidden');

            // Clean up object URL once image is loaded
            previewImg.onload = () => URL.revokeObjectURL(previewImg.src);
        } catch {
            // Preview is best-effort — silently skip on failure
        }
    }

    /**
     * Minimal ZIP local-file parser to extract a specific file by name.
     * Supports STORED and DEFLATE (via DecompressionStream).
     * Returns Uint8Array or null.
     */
    function findFileInZip(bytes, targetName) {
        const view    = new DataView(bytes.buffer);
        let   offset  = 0;

        while (offset + 30 < bytes.length) {
            const sig = view.getUint32(offset, true);
            if (sig !== 0x04034b50) break; // Local file header signature

            const compression   = view.getUint16(offset + 8,  true);
            const compressedSz  = view.getUint32(offset + 18, true);
            const fileNameLen   = view.getUint16(offset + 26, true);
            const extraLen      = view.getUint16(offset + 28, true);

            const nameBytes = bytes.slice(offset + 30, offset + 30 + fileNameLen);
            const name      = new TextDecoder().decode(nameBytes);
            const dataStart = offset + 30 + fileNameLen + extraLen;
            const dataEnd   = dataStart + compressedSz;

            if (name === targetName || name.endsWith('/' + targetName)) {
                const compressedData = bytes.slice(dataStart, dataEnd);
                if (compression === 0) return compressedData; // STORED
                // DEFLATE — decompress synchronously isn't possible; return raw for async caller
                // We resolve this by returning a tagged object
                return { deflate: compressedData };
            }

            offset = dataEnd;
        }
        return null;
    }

    // Override extractPreview to handle async deflate via DecompressionStream
    async function extractPreview(file) {
        if (!previewWrap || !previewImg) return;

        try {
            const buffer  = await file.arrayBuffer();
            const bytes   = new Uint8Array(buffer);
            const result  = findFileInZip(bytes, 'image.png');
            if (!result) return;

            let pngData;
            if (result instanceof Uint8Array) {
                pngData = result; // STORED
            } else if (result?.deflate && typeof DecompressionStream !== 'undefined') {
                // DEFLATE — wrap in raw deflate stream
                const ds     = new DecompressionStream('deflate-raw');
                const writer = ds.writable.getWriter();
                writer.write(result.deflate);
                writer.close();
                const chunks = [];
                const reader = ds.readable.getReader();
                let   done   = false;
                while (!done) {
                    const { value, done: d } = await reader.read();
                    if (value) chunks.push(value);
                    done = d;
                }
                const total = chunks.reduce((n, c) => n + c.length, 0);
                pngData = new Uint8Array(total);
                let pos = 0;
                for (const chunk of chunks) { pngData.set(chunk, pos); pos += chunk.length; }
            } else {
                return; // No DecompressionStream support — skip preview
            }

            const blob = new Blob([pngData], { type: 'image/png' });
            previewImg.src = URL.createObjectURL(blob);
            previewWrap.classList.remove('hidden');
            previewImg.onload = () => URL.revokeObjectURL(previewImg.src);
        } catch {
            // Best-effort — silently skip
        }
    }

    // ── Upload ────────────────────────────────────────────────────────────

    submitBtn.addEventListener('click', function () {
        if (!selectedFile) return;
        upload(selectedFile);
    });

    function upload(file) {
        const formData = new FormData();
        formData.append('theme_zip', file);

        const xhr = new XMLHttpRequest();

        progressWrap.classList.remove('hidden');
        submitBtn.disabled = true;
        hideFeedback();

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                const pct = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = pct + '%';
                progressPct.textContent = pct + '%';
            }
        });

        xhr.addEventListener('load', function () {
            progressWrap.classList.add('hidden');
            progressBar.style.width = '0%';

            let data;
            try { data = JSON.parse(xhr.responseText); }
            catch { showFeedback(false, 'Server returned an invalid response.'); submitBtn.disabled = false; return; }

            if (data.success) {
                showFeedback(true, data.message || 'Theme installed successfully.');
                setTimeout(() => {
                    window.location.href = '?uploaded=' + encodeURIComponent(data.name || '');
                }, 900);
            } else {
                showFeedback(false, data.message || 'Installation failed.');
                submitBtn.disabled = false;
            }
        });

        xhr.addEventListener('error', function () {
            progressWrap.classList.add('hidden');
            showFeedback(false, 'Network error. Please try again.');
            submitBtn.disabled = false;
        });

        xhr.open('POST', 'backend_api/upload_theme.php', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    }

    // ── Toast ─────────────────────────────────────────────────────────────

    function showToast(message, type) {
        const toast = document.createElement('div');
        const isSuccess = type === 'success';

        toast.className = [
            'fixed bottom-6 right-6 z-[9999] flex items-center gap-3',
            'rounded-lg px-4 py-3 shadow-lg text-sm font-medium',
            'transition-all duration-300 translate-y-2 opacity-0',
            isSuccess
                ? 'bg-emerald-600 text-white dark:bg-emerald-500'
                : 'bg-red-600 text-white dark:bg-red-500',
        ].join(' ');

        toast.innerHTML = `
            <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
              ${isSuccess
                ? '<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>'
                : '<path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>'
              }
            </svg>
            <span>${message}</span>`;

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    function resetForm() {
        selectedFile = null;
        fileInput.value = '';
        fileNameEl.textContent = '';
        fileNameEl.classList.add('hidden');
        if (previewWrap) { previewWrap.classList.add('hidden'); previewImg.src = ''; }
        progressWrap.classList.add('hidden');
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        submitBtn.disabled = true;
        hideFeedback();
        dropZone.classList.remove('border-cyan-500', 'bg-cyan-50', 'dark:bg-cyan-900/20');
    }

    function showFeedback(success, message) {
        feedback.classList.remove(
            'hidden',
            'bg-green-50', 'text-green-800', 'dark:bg-green-900/20', 'dark:text-green-300',
            'bg-red-50',   'text-red-800',   'dark:bg-red-900/20',   'dark:text-red-300'
        );
        if (success) {
            feedback.classList.add('bg-green-50', 'text-green-800', 'dark:bg-green-900/20', 'dark:text-green-300');
        } else {
            feedback.classList.add('bg-red-50', 'text-red-800', 'dark:bg-red-900/20', 'dark:text-red-300');
        }
        feedbackMsg.textContent = message;
    }

    function hideFeedback() {
        feedback.classList.add('hidden');
        feedbackMsg.textContent = '';
    }
})();
