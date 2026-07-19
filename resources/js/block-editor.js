import { EditorView, basicSetup } from 'codemirror';
import { json } from '@codemirror/lang-json';
import { html } from '@codemirror/lang-html';
import { indentSelection } from '@codemirror/commands';

window.__cmViews = {};

document.querySelectorAll('[data-cm-editor]').forEach(function (el) {
    var textarea = document.getElementById(el.dataset.cmTextarea);
    var lang = el.dataset.cmLang;
    var ext = lang === 'json' ? json() : html();

    var view = new EditorView({
        doc: textarea.value,
        extensions: [basicSetup, ext],
        parent: el,
    });

    window.__cmViews[el.dataset.cmTextarea] = view;

    el.closest('form').addEventListener('submit', function () {
        textarea.value = view.state.doc.toString();
    });
});

window.updateEditorContent = function (textareaId, content) {
    var view = window.__cmViews[textareaId];
    if (view) {
        view.dispatch({
            changes: { from: 0, to: view.state.doc.length, insert: content }
        });
    }
    var textarea = document.getElementById(textareaId);
    if (textarea) {
        textarea.value = content;
    }
};

window.renderPreview = function () {
    var cmViews = window.__cmViews || {};
    if (cmViews['field-fields']) document.getElementById('field-fields').value = cmViews['field-fields'].state.doc.toString();
    if (cmViews['field-template']) document.getElementById('field-template').value = cmViews['field-template'].state.doc.toString();

    var fieldsText = document.getElementById('field-fields').value;
    var templateText = document.getElementById('field-template').value;
    var fields = {};
    try {
        var parsed = JSON.parse(fieldsText);
        if (Array.isArray(parsed)) {
            parsed.forEach(function (f) { if (f.name) fields[f.name] = f.defaultValue ?? ''; });
        }
    } catch (_) {}

    function resolve(obj, path) {
        return path.split('.').reduce(function (acc, key) { return acc && acc[key] !== undefined ? acc[key] : ''; }, obj);
    }

    var rendered = templateText.replace(/\{\{\s*(.+?)\s*\}\}/g, function (_, key) {
        return String(resolve(fields, key.trim()));
    });

    var cssLinks = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(function (l) { return l.outerHTML; }).join('');
    rendered = '<!DOCTYPE html><html><head><meta charset="utf-8">' + cssLinks + '</head><body>' + rendered + '</body></html>';

    var iframe = document.getElementById('preview-frame');
    if (!iframe) { console.error('preview-frame not found'); return; }
    var doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write(rendered);
    doc.close();
};

window.formatDocument = function () {
    var cmViews = window.__cmViews || {};
    Object.keys(cmViews).forEach(function (key) {
        var view = cmViews[key];
        if (!view) return;
        var all = { from: 0, to: view.state.doc.length };
        view.dispatch({ selection: { anchor: 0, head: view.state.doc.length } });
        indentSelection({ state: view.state, dispatch: view.dispatch });
        view.dispatch({ selection: { anchor: 0 } });
    });
};
