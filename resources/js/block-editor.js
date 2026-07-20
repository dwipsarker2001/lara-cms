import { EditorView, basicSetup } from 'codemirror';
import { json } from '@codemirror/lang-json';
import { html } from '@codemirror/lang-html';

window.__cmViews = window.__cmViews || {};

function initEditors() {
    document.querySelectorAll('[data-cm-editor]').forEach(function (el) {
        var textareaId = el.dataset.cmTextarea;
        if (window.__cmViews[textareaId]) return;

        var textarea = document.getElementById(textareaId);
        if (!textarea) return;

        var lang = el.dataset.cmLang;
        var ext = lang === 'json' ? json() : html();

        var view = new EditorView({
            doc: textarea.value,
            extensions: [basicSetup, ext],
            parent: el,
        });

        window.__cmViews[textareaId] = view;

        var form = el.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                textarea.value = view.state.doc.toString();
            });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditors);
} else {
    initEditors();
}

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

window.refreshEditors = function () {
    initEditors();
    var cmViews = window.__cmViews || {};
    Object.keys(cmViews).forEach(function (key) {
        var view = cmViews[key];
        if (view && typeof view.requestMeasure === 'function') {
            view.requestMeasure();
        }
    });
};

window.formatDocument = function () {
    initEditors();
    var cmViews = window.__cmViews || {};
    var fieldsView = cmViews['field-fields'];
    if (fieldsView) {
        try {
            var raw = fieldsView.state.doc.toString();
            var parsed = JSON.parse(raw);
            var formatted = JSON.stringify(parsed, null, 2);
            window.updateEditorContent('field-fields', formatted);
            window.showToast?.('✓ fields.json formatted.', 'success');
        } catch (err) {
            window.showToast?.('✗ Invalid JSON syntax: ' + err.message, 'danger');
        }
    }
};

window.renderPreview = function () {
    initEditors();
    var cmViews = window.__cmViews || {};
    if (cmViews['field-fields']) document.getElementById('field-fields').value = cmViews['field-fields'].state.doc.toString();
    if (cmViews['field-template']) document.getElementById('field-template').value = cmViews['field-template'].state.doc.toString();

    var fieldsText = document.getElementById('field-fields')?.value || '[]';
    var templateText = document.getElementById('field-template')?.value || '';
    var fields = {
        background: {
            color: '#ffffff',
            image: '',
            opacity: 100
        }
    };

    function getDefaultValue(field) {
        if (field.type === 'object') {
            if (field.list) {
                var count = parseInt(field.defaultCount) || 1;
                var list = [];
                for (var i = 0; i < count; i++) {
                    var item = {};
                    if (Array.isArray(field.fields)) {
                        field.fields.forEach(function (sub) {
                            item[sub.name] = getDefaultValue(sub);
                        });
                    }
                    list.push(item);
                }
                return list;
            } else {
                var obj = {};
                if (Array.isArray(field.fields)) {
                    field.fields.forEach(function (sub) {
                        obj[sub.name] = getDefaultValue(sub);
                    });
                }
                return obj;
            }
        }
        return field.defaultValue ?? '';
    }

    function resolve(obj, path) {
        return path.split('.').reduce(function (acc, key) { return acc && acc[key] !== undefined ? acc[key] : ''; }, obj);
    }

    try {
        var parsed = JSON.parse(fieldsText);
        if (Array.isArray(parsed)) {
            parsed.forEach(function (f) {
                if (f.name) {
                    fields[f.name] = getDefaultValue(f);
                }
            });
        }
    } catch (_) {}

    var rendered = templateText;

    // 1. Process {% for ... %} loops
    rendered = rendered.replace(/\{%\s*for\s+(\w+)\s+in\s+(.+?)\s*%\}([\s\S]*?)\{%\s*endfor\s*%\}/g, function (_, loopVar, expr, body) {
        var items = resolve(fields, expr.trim());
        if (!Array.isArray(items)) items = [];
        return items.map(function (item) {
            var itemContext = Object.assign({}, fields);
            itemContext[loopVar] = item;
            var itemHtml = body;

            itemHtml = itemHtml.replace(/\{%\s*if\s+(.+?)\s*%\}([\s\S]*?)\{%\s*endif\s*%\}/g, function (_, cond, ifBody) {
                var val = resolve(itemContext, cond.trim().split('!=')[0].split('==')[0].trim());
                if (cond.includes('!=')) {
                    var expected = cond.split('!=')[1].trim().replace(/^['"]|['"]$/g, '');
                    return val != expected ? ifBody : '';
                }
                return val ? ifBody : '';
            });

            return itemHtml.replace(/\{\{\s*(.+?)\s*\}\}/g, function (_, path) {
                return resolve(itemContext, path.trim());
            });
        }).join('');
    });

    // 2. Process {% if ... %} conditionals
    rendered = rendered.replace(/\{%\s*if\s+(.+?)\s*%\}([\s\S]*?)\{%\s*endif\s*%\}/g, function (_, cond, body) {
        var val = resolve(fields, cond.trim().split('!=')[0].split('==')[0].trim());
        if (cond.includes('!=')) {
            var expected = cond.split('!=')[1].trim().replace(/^['"]|['"]$/g, '');
            return val != expected ? body : '';
        }
        return val ? body : '';
    });

    // 3. Process remaining {{ ... }} variables
    rendered = rendered.replace(/\{\{\s*(.+?)\s*\}\}/g, function (_, path) {
        return resolve(fields, path.trim());
    });

    var iframe = document.getElementById('preview-frame');
    if (iframe) {
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write('<!DOCTYPE html><html><head><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-50 p-6">' + rendered + '</body></html>');
        doc.close();
    }
};
