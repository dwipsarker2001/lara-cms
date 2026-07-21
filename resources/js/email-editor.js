import { init } from '@templatical/editor';
import '@templatical/editor/style.css';

document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('email-editor-container');
    if (!container) return;

    const saveBtn = document.getElementById('btn-save-template');
    let dirty = false;
    let editor;

    editor = await init({
        container,
        content: window.templateContent || null,
        uiTheme: 'light',
        onChange() {
            dirty = true;
        },
    });

    const integrateToolbar = () => {
        const shadowRoot = container.shadowRoot;
        if (!shadowRoot) return;

        const findHeaderAndIntegrate = () => {
            const header = shadowRoot.querySelector('header');
            if (header && header.children.length >= 3) {
                // Add custom styles to the shadow DOM for the moved buttons
                const style = document.createElement('style');
                style.textContent = `
                    #btn-back {
                        display: inline-flex !important;
                        align-items: center;
                        justify-content: center;
                        width: 28px;
                        height: 28px;
                        border-radius: 9999px;
                        border: 1px solid #d1d5db;
                        background-color: #ffffff;
                        color: #374151;
                        transition: background-color 0.2s;
                        cursor: pointer;
                        text-decoration: none;
                    }
                    #btn-back:hover {
                        background-color: #f3f4f6;
                    }
                    #btn-back svg {
                        width: 12px;
                        height: 12px;
                    }
                    #template-name {
                        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        font-size: 14px;
                        font-weight: 600;
                        color: #1f2937;
                        margin-left: 8px;
                        white-space: nowrap;
                    }
                    #btn-save-template {
                        display: inline-flex !important;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        font-weight: 500;
                        font-size: 14px;
                        padding: 0 16px;
                        height: 36px;
                        border-radius: 8px;
                        border: none;
                        background-color: #16a34a; /* CMS primary green color */
                        color: #ffffff;
                        cursor: pointer;
                        transition: opacity 0.2s;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                        margin-right: 8px;
                    }
                    #btn-save-template:hover:not(:disabled) {
                        opacity: 0.9;
                    }
                    #btn-save-template:disabled {
                        opacity: 0.6;
                        cursor: not-allowed;
                    }
                    header {
                        box-shadow: none !important;
                        border-bottom: 1px solid #e2e8f0 !important;
                    }
                    .tpl-design-dropzone {
                        border: 1px solid #e2e8f0 !important;
                    }
                    .tpl-canvas-stage {
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03) !important;
                    }
                `;
                shadowRoot.appendChild(style);

                const children = header.children;
                const leftContainer = children[0];
                const rightContainer = children[children.length - 1];

                const backBtn = document.getElementById('btn-back');
                const templateName = document.getElementById('template-name');
                const saveBtn = document.getElementById('btn-save-template');

                if (leftContainer) {
                    leftContainer.style.paddingLeft = '16px';
                    if (backBtn) leftContainer.appendChild(backBtn);
                    if (templateName) leftContainer.appendChild(templateName);
                }

                if (rightContainer) {
                    rightContainer.style.paddingRight = '16px';
                    if (saveBtn) rightContainer.appendChild(saveBtn);
                }
            } else {
                requestAnimationFrame(findHeaderAndIntegrate);
            }
        };

        findHeaderAndIntegrate();
    };

    integrateToolbar();

    if (saveBtn) {
        saveBtn.addEventListener('click', async (e) => {
            e.stopPropagation();
            if (!editor) return;

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            try {
                const content = JSON.stringify(editor.getContent());
                const payload = { content };
                if (window.templateId) {
                    payload.template_id = window.templateId;
                }
                const res = await fetch(window.templateSaveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                if (!res.ok) throw new Error('Save failed');

                dirty = false;
                saveBtn.textContent = 'Saved';
                setTimeout(() => { saveBtn.textContent = 'Save Template'; }, 2000);
            } catch (e) {
                saveBtn.textContent = 'Save Failed';
                setTimeout(() => { saveBtn.textContent = 'Save Template'; }, 3000);
            } finally {
                saveBtn.disabled = false;
            }
        });
    }

    window.addEventListener('beforeunload', (e) => {
        if (dirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
