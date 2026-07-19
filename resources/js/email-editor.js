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

    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            if (!editor || !dirty) return;

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            try {
                const content = editor.exportJson();
                const res = await fetch(window.templateSaveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ content }),
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
