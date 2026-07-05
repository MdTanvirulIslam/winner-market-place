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

const init = () => {
    document.querySelectorAll('textarea[data-quill]').forEach(mountEditor);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
} else {
    init();
}
