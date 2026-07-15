import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';

window.__ttEditors = window.__ttEditors || {};

const ResizableImage = Image.extend({
    addAttributes() {
        return {
            src: { default: null },
            alt: { default: null },
            title: { default: null },
            width: {
                default: null,
                parseHTML: (el) => {
                    const style = el.getAttribute('style') || '';
                    const m = style.match(/width:\s*([^;]+)/);
                    if (m) return m[1].trim();
                    return el.getAttribute('width');
                },
            },
            'data-align': { default: null },
        };
    },
    renderHTML({ HTMLAttributes }) {
        const { width, style, ...attrs } = HTMLAttributes;
        const styles = [];
        if (style) styles.push(style);
        if (width != null && width !== '') styles.push(`width:${width}`);
        return ['img', { ...attrs, style: styles.join(';'), width: null }];
    },
});

function updateActiveButtons(wrapperEl, editor) {
    wrapperEl.querySelectorAll('[data-tt-cmd]').forEach((btn) => {
        const cmd = btn.dataset.ttCmd;
        const args = btn.dataset.ttArgs ? JSON.parse(btn.dataset.ttArgs) : [];
        let active = false;

        if (cmd === 'toggleBold') active = editor.isActive('bold');
        else if (cmd === 'toggleItalic') active = editor.isActive('italic');
        else if (cmd === 'toggleUnderline') active = editor.isActive('underline');
        else if (cmd === 'toggleStrike') active = editor.isActive('strike');
        else if (cmd === 'toggleBulletList') active = editor.isActive('bulletList');
        else if (cmd === 'toggleOrderedList') active = editor.isActive('orderedList');
        else if (cmd === 'toggleBlockquote') active = editor.isActive('blockquote');
        else if (cmd === 'toggleCodeBlock') active = editor.isActive('codeBlock');
        else if (cmd === 'toggleHeading') active = editor.isActive('heading', { level: args[0] });
        else if (cmd === 'setTextAlign') {
            if (editor.isActive('image')) {
                active = editor.getAttributes('image')['data-align'] === args[0];
            } else {
                active = editor.isActive('textAlign', { textAlign: args[0] });
            }
        }
        else if (cmd === 'prompt-link') active = editor.isActive('link');

        btn.classList.toggle('active', active);
    });

    const imageControls = wrapperEl.querySelector('.tt-image-controls');
    if (imageControls) {
        imageControls.style.display = editor.isActive('image') ? '' : 'none';
    }
}

function setupImageToolbar(_wrapperEl, _editor) {
}

function setupToolbarOverflow(wrapperEl) {
    const toolbar = wrapperEl.querySelector('.tt-toolbar');
    if (!toolbar) return;
    const overflowDd = toolbar.querySelector('.tt-overflow-dd');
    if (!overflowDd) return;
    const overflowPanel = overflowDd.querySelector('[data-tt-panel="overflow"]');

    const update = () => {
        // Restore every moved item back to the toolbar (before the "..." toggle)
        while (overflowPanel.firstChild) {
            toolbar.insertBefore(overflowPanel.firstChild, overflowDd);
        }
        toolbar.querySelectorAll('span.w-px').forEach((s) => { s.style.display = ''; });
        overflowDd.style.display = 'none';

        // Everything fits — nothing to do.
        if (toolbar.scrollWidth <= toolbar.clientWidth) return;

        // Reveal the "..." toggle and reserve its width before measuring.
        overflowDd.style.display = '';

        // Move trailing single-action buttons into the overflow menu until it fits.
        // Group dropdowns (wrapped in .tt-dropdown) are never moved — only bare .tt-btn actions.
        let guard = 100;
        while (toolbar.scrollWidth > toolbar.clientWidth && guard-- > 0) {
            const last = overflowDd.previousElementSibling;
            if (!last) break;
            if (last.matches('span.w-px')) { last.style.display = 'none'; continue; }
            if (!last.matches('button.tt-btn')) break; // stop at group dropdowns
            overflowPanel.prepend(last);
        }

        // Hide a now-dangling separator left at the toolbar's right edge.
        let sib = overflowDd.previousElementSibling;
        while (sib && sib.matches('span.w-px')) {
            sib.style.display = 'none';
            sib = sib.previousElementSibling;
        }

        if (!overflowPanel.querySelector('.tt-btn')) overflowDd.style.display = 'none';
    };

    const ro = new ResizeObserver(update);
    ro.observe(toolbar);
    setTimeout(update, 0);
    setTimeout(update, 150); // re-run once layout/fonts settle
}

function setupResizeHandle(wrapperEl, editor) {
    const editorEl = wrapperEl.querySelector('.tt-editor');
    const handle = document.createElement('div');
    handle.className = 'tt-resize-handle';
    handle.style.cssText = 'display:none;position:absolute;bottom:-6px;right:-6px;width:14px;height:14px;background:#16a34a;border:2px solid #fff;border-radius:2px;cursor:se-resize;z-index:10;box-shadow:0 1px 3px rgba(0,0,0,0.3);';
    editorEl.parentElement.style.position = 'relative';
    editorEl.parentElement.appendChild(handle);

    let resizing = false;
    let startX = 0;
    let startW = 0;
    let currentImg = null;

    const updateHandle = () => {
        const img = editorEl.querySelector('.ProseMirror-selectednode');
        if (img && img.tagName === 'IMG') {
            currentImg = img;
            const rect = img.getBoundingClientRect();
            const editorRect = editorEl.parentElement.getBoundingClientRect();
            handle.style.display = 'block';
            handle.style.left = (rect.right - editorRect.left - 7) + 'px';
            handle.style.top = (rect.bottom - editorRect.top - 7) + 'px';
        } else {
            handle.style.display = 'none';
            currentImg = null;
        }
    };

    handle.addEventListener('mousedown', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!currentImg) return;
        resizing = true;
        startX = e.clientX;
        startW = currentImg.offsetWidth;
        document.body.style.cursor = 'se-resize';
        document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', (e) => {
        if (!resizing || !currentImg) return;
        const diff = e.clientX - startX;
        const newW = Math.max(50, startW + diff);
        const maxW = Math.min(editorEl.clientWidth, newW);
        const pct = Math.round((maxW / editorEl.clientWidth) * 100);
        currentImg.style.width = pct + '%';

        const pos = editor.view.posAtDOM(currentImg, 0);
        if (pos) {
            const node = editor.view.state.doc.nodeAt(pos);
            if (node && node.type.name === 'image') {
                editor.chain().setMeta('addToHistory', false).updateAttributes('image', { width: pct + '%' }).run();
            }
        }

        updateHandle();
        handle.style.left = (currentImg.getBoundingClientRect().right - editorEl.parentElement.getBoundingClientRect().left - 7) + 'px';
        handle.style.top = (currentImg.getBoundingClientRect().bottom - editorEl.parentElement.getBoundingClientRect().top - 7) + 'px';
    });

    document.addEventListener('mouseup', () => {
        if (resizing) {
            resizing = false;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
        }
    });

    editor.on('selectionUpdate', updateHandle);
    const observer = new MutationObserver(() => updateHandle());
    observer.observe(editorEl, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style'] });
}

export function mountTipTap(fieldName, wrapperEl, initialContent, onUpdate) {
    if (window.__ttEditors[fieldName]) {
        window.__ttEditors[fieldName].destroy();
        delete window.__ttEditors[fieldName];
    }

    const editorEl = wrapperEl.querySelector('.tt-editor');
    if (!editorEl) return null;

    const editor = new Editor({
        element: editorEl,
        extensions: [
            StarterKit.configure({
                heading: { levels: [1, 2, 3, 4, 5, 6] },
            }),
            Underline,
            Link.configure({
                openOnClick: false,
                HTMLAttributes: { class: 'text-primary underline' },
            }),
            ResizableImage.configure({ allowBase64: true }),
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            Placeholder.configure({ placeholder: 'Start writing...' }),
        ],
        content: initialContent || '',
        onUpdate: ({ editor: ed }) => {
            if (onUpdate) onUpdate(ed.getHTML());
            updateActiveButtons(wrapperEl, ed);
        },
        onFocus: () => {
            wrapperEl.classList.add('tt-focused');
        },
        onBlur: () => {
            wrapperEl.classList.remove('tt-focused');
        },
    });

    editor.on('selectionUpdate', () => {
        updateActiveButtons(wrapperEl, editor);
    });

    window.__ttEditors[fieldName] = editor;
    updateActiveButtons(wrapperEl, editor);

    // Set initial heading dropdown label (no-op if label element removed)

    setupImageToolbar(wrapperEl, editor);
    setupResizeHandle(wrapperEl, editor);
    setupToolbarOverflow(wrapperEl);

    // Dropdown toggle handler via wrapper click delegation
    wrapperEl.addEventListener('click', (e) => {
        // Close dropdowns when clicking outside any dropdown
        const dd = e.target.closest('.tt-dropdown');
        if (!dd) {
            wrapperEl.querySelectorAll('[data-tt-panel]').forEach(p => {
                p.classList.add('hidden');
                p.style.position = '';
                p.style.top = '';
                p.style.left = '';
                p.style.zIndex = '';
            });
        }

        const btn = e.target.closest('[data-tt-cmd]');
        if (!btn) return;

        const cmd = btn.dataset.ttCmd;
        const args = btn.dataset.ttArgs ? JSON.parse(btn.dataset.ttArgs) : [];

        if (cmd === 'toggle-dropdown') {
            const name = btn.dataset.ttDropdown;
            if (!name) return;
            const panel = wrapperEl.querySelector(`[data-tt-panel="${name}"]`);
            if (!panel) return;
            const wasOpen = !panel.classList.contains('hidden');
            // Hide + reset every panel (including this one) so a re-click closes it
            // cleanly instead of leaving it position:static inside the toolbar.
            wrapperEl.querySelectorAll('[data-tt-panel]').forEach(p => {
                p.classList.add('hidden');
                p.style.position = '';
                p.style.top = '';
                p.style.left = '';
                p.style.zIndex = '';
            });
            if (wasOpen) return;
            const rect = btn.getBoundingClientRect();
            panel.style.position = 'fixed';
            panel.style.top = rect.bottom + 4 + 'px';
            panel.style.zIndex = '9999';
            panel.style.left = '0px';
            panel.classList.remove('hidden');
            // Clamp within the viewport (menus near the right edge would clip otherwise).
            const pw = panel.getBoundingClientRect().width;
            let left = rect.left;
            if (left + pw > window.innerWidth - 8) left = window.innerWidth - pw - 8;
            panel.style.left = Math.max(8, left) + 'px';
            return;
        }

        const closeParentPanel = () => {
            const parentPanel = btn.closest('[data-tt-panel]');
            if (!parentPanel) return;
            parentPanel.classList.add('hidden');
            parentPanel.style.position = '';
            parentPanel.style.top = '';
            parentPanel.style.left = '';
            parentPanel.style.zIndex = '';
        };

        if (cmd === 'prompt-link') {
            closeParentPanel();
            const existing = editor.getAttributes('link').href;
            const url = prompt('Enter URL:', existing || '');
            if (url === null) return;
            editor.chain().focus()[url ? 'setLink' : 'unsetLink'](url ? { href: url } : undefined).run();
            return;
        }

        if (cmd === 'prompt-image') {
            closeParentPanel();
            window.dispatchEvent(new CustomEvent('open-asset-picker', {
                detail: {
                    callback: (url) => {
                        editor.chain().focus().setImage({ src: url, width: '100%' }).run();
                    },
                },
            }));
            return;
        }

        if (cmd === 'setTextAlign' && editor.isActive('image')) {
            editor.chain().focus().updateAttributes('image', { 'data-align': args[0] }).run();
            closeParentPanel();
            return;
        }

        if (cmd === 'toggleHeading') {
            // TipTap expects { level }, not a bare number.
            editor.chain().focus().toggleHeading({ level: args[0] }).run();
            closeParentPanel();
            return;
        }

        const chain = editor.chain().focus();
        if (typeof chain[cmd] === 'function') {
            chain[cmd](...args).run();
        }

        closeParentPanel();
    });

    // Close any open toolbar menu when clicking outside the whole editor.
    document.addEventListener('mousedown', (e) => {
        if (wrapperEl.contains(e.target)) return;
        wrapperEl.querySelectorAll('[data-tt-panel]:not(.hidden)').forEach((p) => {
            p.classList.add('hidden');
            p.style.position = '';
            p.style.top = '';
            p.style.left = '';
            p.style.zIndex = '';
        });
    });

    // Update heading label on selection change
    editor.on('selectionUpdate', () => {
        const label = wrapperEl.querySelector('[data-tt-dd-label="heading"]');
        if (!label) return;
        if (editor.isActive('heading', { level: 1 })) label.textContent = 'H1';
        else if (editor.isActive('heading', { level: 2 })) label.textContent = 'H2';
        else if (editor.isActive('heading', { level: 3 })) label.textContent = 'H3';
        else label.textContent = 'P';
    });

    return editor;
}
