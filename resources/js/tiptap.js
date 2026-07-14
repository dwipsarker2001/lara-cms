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
        else if (cmd === 'setTextAlign') active = editor.isActive('textAlign', { textAlign: args[0] });
        else if (cmd === 'prompt-link') active = editor.isActive('link');

        btn.classList.toggle('active', active);
    });

    const imageControls = wrapperEl.querySelector('.tt-image-controls');
    if (imageControls) {
        imageControls.style.display = editor.isActive('image') ? '' : 'none';
    }
}

function setupImageToolbar(wrapperEl, editor) {
    const toolbar = wrapperEl.querySelector('.tt-toolbar');
    if (!toolbar) return;

    const controls = document.createElement('span');
    controls.className = 'tt-image-controls';
    controls.style.display = 'none';

    const btnLeft = document.createElement('button');
    btnLeft.type = 'button';
    btnLeft.className = 'tt-btn tt-image-controls px-2 py-1 text-sm text-gray-700 hover:bg-gray-200 hover:text-primary rounded transition-colors';
    btnLeft.style.display = 'none';
    btnLeft.title = 'Align Left';
    btnLeft.innerHTML = '&#9668; Img';

    const btnCenter = btnLeft.cloneNode();
    btnCenter.title = 'Align Center';
    btnCenter.innerHTML = '&#9640; Img';

    const btnRight = btnLeft.cloneNode();
    btnRight.title = 'Align Right';
    btnRight.innerHTML = '&#9654; Img';

    const btnNone = btnLeft.cloneNode();
    btnNone.title = 'Default';
    btnNone.innerHTML = '&#9644; Img';

    const lastSpan = toolbar.querySelector('span:last-of-type');
    if (lastSpan) {
        [controls, btnLeft, btnCenter, btnRight, btnNone].forEach(el => {
            toolbar.insertBefore(el, lastSpan);
        });
    }

    const setAlign = (align) => {
        editor.chain().focus().updateAttributes('image', { 'data-align': align }).run();
    };

    btnLeft.addEventListener('click', () => setAlign('left'));
    btnCenter.addEventListener('click', () => setAlign('center'));
    btnRight.addEventListener('click', () => setAlign('right'));
    btnNone.addEventListener('click', () => setAlign(null));

    editor.on('selectionUpdate', () => {
        const active = editor.isActive('image');
        [controls, btnLeft, btnCenter, btnRight, btnNone].forEach(el => {
            el.style.display = active ? '' : 'none';
        });
        if (active) {
            const attrs = editor.getAttributes('image');
            [btnLeft, btnCenter, btnRight, btnNone].forEach(b => b.classList.remove('active'));
            if (attrs['data-align'] === 'left') btnLeft.classList.add('active');
            else if (attrs['data-align'] === 'center') btnCenter.classList.add('active');
            else if (attrs['data-align'] === 'right') btnRight.classList.add('active');
            else btnNone.classList.add('active');
        }
    });
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
    });

    editor.on('selectionUpdate', () => {
        updateActiveButtons(wrapperEl, editor);
    });

    window.__ttEditors[fieldName] = editor;
    updateActiveButtons(wrapperEl, editor);

    setupImageToolbar(wrapperEl, editor);
    setupResizeHandle(wrapperEl, editor);

    wrapperEl.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-tt-cmd]');
        if (!btn) return;

        const cmd = btn.dataset.ttCmd;
        const args = btn.dataset.ttArgs ? JSON.parse(btn.dataset.ttArgs) : [];

        if (cmd === 'prompt-link') {
            const existing = editor.getAttributes('link').href;
            const url = prompt('Enter URL:', existing || '');
            if (url === null) return;
            editor.chain().focus()[url ? 'setLink' : 'unsetLink'](url ? { href: url } : undefined).run();
            return;
        }

        if (cmd === 'prompt-image') {
            window.dispatchEvent(new CustomEvent('open-asset-picker', {
                detail: {
                    callback: (url) => {
                        editor.chain().focus().setImage({ src: url, width: '100%' }).run();
                    },
                },
            }));
            return;
        }

        const chain = editor.chain().focus();
        if (typeof chain[cmd] === 'function') {
            chain[cmd](...args).run();
        }
    });

    return editor;
}
