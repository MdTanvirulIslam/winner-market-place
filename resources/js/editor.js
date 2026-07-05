// Rich-text editing for the product form. Loaded only on pages that
// @vite this entry — the storefront bundle never pays for Quill.
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const TOOLBARS = {
    minimal: [['bold', 'italic']],
    list: [['bold', 'italic'], [{ list: 'bullet' }, { list: 'ordered' }]],
    full: [
        [{ header: [2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'bullet' }, { list: 'ordered' }],
        ['link', 'blockquote', 'code-block'],
        ['clean'],
    ],
};

const isEmptyHtml = (html) => html.replace(/<[^>]*>|&nbsp;|\s/g, '') === '';

const mountEditor = (textarea) => {
    const preset = TOOLBARS[textarea.dataset.quill] ? textarea.dataset.quill : 'full';

    const wrapper = document.createElement('div');
    wrapper.className = 'quill-wrap mt-1';
    const editorHost = document.createElement('div');
    wrapper.appendChild(editorHost);
    textarea.after(wrapper);
    textarea.classList.add('hidden');

    const quill = new Quill(editorHost, {
        theme: 'snow',
        placeholder: textarea.placeholder || '',
        modules: { toolbar: TOOLBARS[preset] },
    });

    if (textarea.value.trim() !== '') {
        // Legacy plain-text values keep their line breaks as paragraphs.
        if (textarea.value.includes('<')) {
            quill.clipboard.dangerouslyPasteHTML(textarea.value);
        } else {
            quill.setText(textarea.value);
        }
    }

    const sync = () => {
        const html = quill.getSemanticHTML();
        textarea.value = isEmptyHtml(html) ? '' : html;
    };

    quill.on('text-change', sync);
    textarea.closest('form')?.addEventListener('submit', sync);
};

// AJAX upload with a live progress bar for forms marked data-ajax-upload
// (release zips run to 200 MB — a silent POST is unusable). XMLHttpRequest
// because fetch exposes no upload progress.
const mountAjaxUpload = (form) => {
    const progressWrap = form.querySelector('[data-upload-progress]');
    const progressFill = progressWrap?.querySelector('.progress-fill');
    const percentLabel = progressWrap?.querySelector('[data-upload-percent]');
    const errorBox = form.querySelector('[data-upload-errors]');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        errorBox?.classList.add('hidden');
        if (errorBox) errorBox.textContent = '';
        progressWrap?.classList.remove('hidden');
        if (submitButton) submitButton.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open(form.method || 'POST', form.action);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        const setPercent = (value) => {
            if (progressFill) progressFill.style.width = `${value}%`;
            if (percentLabel) percentLabel.textContent = `${value}%`;
        };

        xhr.upload.addEventListener('progress', (progress) => {
            if (progress.lengthComputable) {
                const percent = Math.round((progress.loaded / progress.total) * 100);
                setPercent(percent);
                if (percent >= 100 && percentLabel) {
                    percentLabel.textContent = '100% — processing…';
                }
            }
        });

        const fail = (messages) => {
            progressWrap?.classList.add('hidden');
            setPercent(0);
            if (submitButton) submitButton.disabled = false;
            if (errorBox) {
                errorBox.textContent = messages.join(' ');
                errorBox.classList.remove('hidden');
            }
        };

        xhr.addEventListener('load', () => {
            let body = {};
            try {
                body = JSON.parse(xhr.responseText || '{}');
            } catch {
                // fall through to the generic error below
            }

            if (xhr.status >= 200 && xhr.status < 300 && body.redirect) {
                window.location.assign(body.redirect);
                return;
            }

            if (xhr.status === 422 && body.errors) {
                fail(Object.values(body.errors).flat());
                return;
            }

            fail([body.message || `Upload failed (HTTP ${xhr.status}). Please try again.`]);
        });

        xhr.addEventListener('error', () => fail(['Network error — the upload did not complete. Please try again.']));

        xhr.send(new FormData(form));
    });
};

const init = () => {
    document.querySelectorAll('textarea[data-quill]').forEach(mountEditor);
    document.querySelectorAll('form[data-ajax-upload]').forEach(mountAjaxUpload);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
    init();
}
