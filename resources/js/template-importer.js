import { convertUnlayerTemplate } from '@templatical/import-unlayer';
import { convertBeeFreeTemplate } from '@templatical/import-beefree';
import { convertHtmlTemplate } from '@templatical/import-html';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('template-form');
    if (!form) return;

    const importFileInput = document.getElementById('import-file');
    const importSourceSelect = document.getElementById('import-source');
    const contentInput = document.getElementById('content-input');
    const importErrorDiv = document.getElementById('import-error');

    if (!importFileInput || !importSourceSelect || !contentInput) return;

    // Helper to show errors
    const showError = (msg) => {
        if (importErrorDiv) {
            importErrorDiv.textContent = msg;
            importErrorDiv.classList.remove('hidden');
        } else {
            alert(msg);
        }
    };

    // Helper to hide errors
    const hideError = () => {
        if (importErrorDiv) {
            importErrorDiv.classList.add('hidden');
        }
    };

    // Auto-detect source when file is selected
    importFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        hideError();

        if (file.name.endsWith('.json')) {
            if (importSourceSelect.value !== 'unlayer' && importSourceSelect.value !== 'beefree') {
                importSourceSelect.value = 'unlayer';
            }
        } else if (file.name.endsWith('.html') || file.name.endsWith('.htm')) {
            importSourceSelect.value = 'html';
        }
    });

    form.addEventListener('submit', async (e) => {
        // Find which mode is selected. We'll use a data attribute on the active tab.
        const activeTab = document.querySelector('[data-tab-btn].active');
        const mode = activeTab ? activeTab.getAttribute('data-tab-btn') : 'blank';

        if (mode !== 'import') return; // Let normal post handle it

        e.preventDefault();
        hideError();

        const titleInput = document.getElementById('field-name');
        if (!titleInput || !titleInput.value.trim()) {
            showError('Please enter a template title.');
            return;
        }

        const file = importFileInput.files[0];
        if (!file) {
            showError('Please select a template file to import.');
            return;
        }

        const source = importSourceSelect.value;
        const reader = new FileReader();

        reader.onload = async (evt) => {
            const rawContent = evt.target.result;
            let convertedContent = null;
            let report = null;

            try {
                if (source === 'unlayer') {
                    const json = JSON.parse(rawContent);
                    const result = convertUnlayerTemplate(json);
                    convertedContent = result.content;
                    report = result.report;
                } else if (source === 'beefree') {
                    const json = JSON.parse(rawContent);
                    const result = convertBeeFreeTemplate(json);
                    convertedContent = result.content;
                    report = result.report;
                } else if (source === 'html') {
                    const result = convertHtmlTemplate(rawContent);
                    convertedContent = result.content;
                    report = result.report;
                } else {
                    throw new Error('Unsupported import source');
                }

                if (!convertedContent) {
                    throw new Error('Conversion yielded empty content');
                }

                // Populate the hidden input field with the JSON content
                contentInput.value = JSON.stringify(convertedContent);

                // Save report details to show warnings or notices post-redirect
                if (report) {
                    sessionStorage.setItem('import_report', JSON.stringify({
                        name: titleInput.value.trim(),
                        source: source,
                        summary: report.summary,
                        warnings: report.warnings
                    }));
                }

                // Submit the form programmatically
                form.submit();

            } catch (err) {
                console.error(err);
                showError('Failed to parse or convert template: ' + err.message);
            }
        };

        reader.onerror = () => {
            showError('Error reading template file.');
        };

        reader.readAsText(file);
    });
});
