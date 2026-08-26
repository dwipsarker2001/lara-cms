function pageEditor() {
        return {
            sections: [],
            schemas: {},
            blockList: [],
            slug: '',
            pages: [],
            entryData: {},
            siteSettings: window.editorSettingsCustomValues || {},
            collectionFields: window.editorCollectionFields || [],
            groupedCollectionFields: window.editorGroupedCollectionFields || [],
            allCollections: window.editorAllCollections || [],
            availableForms: window.editorForms || [],
            selectedCollectionGroup: (window.editorGroupedCollectionFields && window.editorGroupedCollectionFields.length > 0) ? window.editorGroupedCollectionFields[0].collection_id : null,
            linkModes: {},
            colModes: {},
            active: null,
            crumbs: [],
            homeGlobals: {},
            originalSections: [],
            _focusField: null,
            dirty: false,
            isSaving: false,
            previewTimer: null,
            iconPickerOpen: false,
            iconSearch: '',
            iconLoading: false,
            faIcons: window.FA_ICONS || [],
            sidebarOpen: true,
            sidebarWidth: 420,

            startSidebarResize(e) {
                e.preventDefault();

                const root = document.getElementById('page-editor-root');
                const iframe = document.getElementById('preview-iframe');
                const startX = e.clientX;
                const startWidth = this.sidebarWidth;
                const maxSidebarWidth = root.offsetWidth - 320 - 10; // keep 320px for preview + 10px handle

                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'col-resize';
                if (iframe) { iframe.style.pointerEvents = 'none'; }

                const onMove = (moveEvt) => {
                    const w = Math.max(280, Math.min(maxSidebarWidth, startWidth + moveEvt.clientX - startX));
                    root.style.setProperty('--sb-w', w + 'px');
                };

                const onEnd = (moveEvt) => {
                    const w = Math.max(280, Math.min(maxSidebarWidth, startWidth + moveEvt.clientX - startX));
                    this.sidebarWidth = Math.round(w);
                    root.style.setProperty('--sb-w', w + 'px');

                    document.body.style.userSelect = '';
                    document.body.style.cursor = '';
                    if (iframe) { iframe.style.pointerEvents = ''; }
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onEnd);
                };

                window.addEventListener('mousemove', onMove, { passive: true });
                window.addEventListener('mouseup', onEnd);
            },

            init(sections, schemas, blockList, slug, pages, homeGlobals) {
                if (!sections) sections = [];
                if (!schemas) schemas = {};
                if (!blockList) blockList = [];
                if (!slug) slug = '';
                if (!pages) pages = [];
                if (!homeGlobals) homeGlobals = {};
                this.sections = JSON.parse(JSON.stringify(sections));
                this.ensureSectionKeys();
                this.originalSections = JSON.parse(JSON.stringify(this.sections));
                this.schemas = schemas;
                this.blockList = blockList;
                this.slug = slug;
                this.pages = pages;
                this.allCollections = window.editorAllCollections || [];
                this.homeGlobals = homeGlobals;
                this.entryData = window.editorEntryData || {};
                window.__pageEditor = this;
                this.$nextTick(() => this.initSectionSortable());
                this.refreshPreview();
            },

            ensureSectionKeys() {
                const addKeys = (obj) => {
                    if (!obj || typeof obj !== 'object') return;
                    if (Array.isArray(obj)) {
                        obj.forEach(item => {
                            if (item && typeof item === 'object') {
                                if (!item._key) {
                                    item._key = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);
                                }
                                addKeys(item);
                            }
                        });
                    } else {
                        Object.values(obj).forEach(val => addKeys(val));
                    }
                };

                for (const section of this.sections) {
                    if (!section._key) {
                        section._key = crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2);
                    }
                    if (section.data) {
                        addKeys(section.data);
                    }
                }
            },

            initSectionSortable() {
                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }
                const el = this.$refs?.sectionList;
                if (!el) return;
                this._sectionSortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    onStart: (evt) => {
                        evt.item._prevSibling = evt.item.previousElementSibling;
                    },
                    onEnd: (evt) => {
                        const cleanup = () => {
                            delete evt.item._prevSibling;
                            setTimeout(() => this.initSectionSortable(), 0);
                        };

                        if (evt.oldIndex === evt.newIndex || evt.oldIndex === undefined || evt.newIndex === undefined) {
                            cleanup();
                            return;
                        }

                        // Revert Sortable DOM changes so Alpine can handle the DOM update
                        const itemEl = evt.item;
                        if (itemEl._prevSibling && itemEl._prevSibling.parentElement === evt.from) {
                            itemEl._prevSibling.after(itemEl);
                        } else if (evt.from) {
                            evt.from.prepend(itemEl);
                        }

                        let oldIdx = evt.oldDraggableIndex;
                        let newIdx = evt.newDraggableIndex;
                        if (oldIdx === undefined || newIdx === undefined) {
                            const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                            oldIdx = evt.oldIndex - offset;
                            newIdx = evt.newIndex - offset;
                        }

                        if (oldIdx >= 0 && oldIdx < this.sections.length && newIdx >= 0 && newIdx < this.sections.length) {
                            const item = this.sections.splice(oldIdx, 1)[0];
                            if (item !== undefined) {
                                this.sections.splice(newIdx, 0, item);
                                this.sections = [...this.sections];
                                this.dirty = true;
                                this.schedulePreview();
                            }
                        }
                        cleanup();
                    },
                });
            },

            initListSortable(el) {
                if (!el) return;
                if (el._sortable) {
                    try { el._sortable.destroy(); } catch (e) {}
                    delete el._sortable;
                }
                el._sortable = new Sortable(el, {
                    handle: '.cursor-grab',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    onStart: (evt) => {
                        evt.item._prevSibling = evt.item.previousElementSibling;
                    },
                    onEnd: (evt) => {
                        const cleanup = () => {
                            delete evt.item._prevSibling;
                            setTimeout(() => this.initListSortable(el), 0);
                        };

                        if (evt.oldIndex === evt.newIndex || evt.oldIndex === undefined || evt.newIndex === undefined) {
                            cleanup();
                            return;
                        }

                        // Revert Sortable DOM changes so Alpine can handle the DOM update
                        const itemEl = evt.item;
                        if (itemEl._prevSibling && itemEl._prevSibling.parentElement === evt.from) {
                            itemEl._prevSibling.after(itemEl);
                        } else if (evt.from) {
                            evt.from.prepend(itemEl);
                        }

                        const name = el.dataset.fieldName;
                        if (!name) {
                            cleanup();
                            return;
                        }

                        let oldIdx = evt.oldDraggableIndex;
                        let newIdx = evt.newDraggableIndex;
                        if (oldIdx === undefined || newIdx === undefined) {
                            const offset = (evt.from.children[0] && evt.from.children[0].tagName === 'TEMPLATE') ? 1 : 0;
                            oldIdx = evt.oldIndex - offset;
                            newIdx = evt.newIndex - offset;
                        }

                        const list = this.getList(name);
                        if (oldIdx < 0 || oldIdx >= list.length || newIdx < 0 || newIdx >= list.length) {
                            cleanup();
                            return;
                        }

                        const item = list.splice(oldIdx, 1)[0];
                        if (item === undefined) {
                            cleanup();
                            return;
                        }
                        list.splice(newIdx, 0, item);

                        // Re-assign list array reference to force Alpine reactivity update
                        if (this.active !== null && this.sections[this.active] && this.sections[this.active].data) {
                            let d = this.sections[this.active].data;
                            let valid = true;
                            for (const crumb of this.crumbs) {
                                if (!d || !crumb || !crumb.key) { valid = false; break; }
                                d = d[crumb.key];
                                if (!d) { valid = false; break; }
                                if (crumb.index !== undefined) {
                                    d = d[crumb.index];
                                    if (!d) { valid = false; break; }
                                }
                            }
                            if (valid && d) {
                                d[name] = [...list];
                            }
                        }

                        this.dirty = true;
                        this.schedulePreview();
                        cleanup();
                    },
                });
            },

            initListSortables() {
                setTimeout(() => {
                    document.querySelectorAll('[data-sortable-list]').forEach(el => {
                        this.initListSortable(el);
                    });
                }, 0);
            },

            moveSection(from, to) {
                if (from < 0 || from >= this.sections.length) return;
                if (to < 0 || to >= this.sections.length) return;

                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }

                const item = this.sections.splice(from, 1)[0];
                this.sections.splice(to, 0, item);
                this.sections = [...this.sections];
                this.dirty = true;
                this.schedulePreview();
                this.$nextTick(() => this.initSectionSortable());
            },

            sectionLabel(section) {
                if (!section) return '';
                if (section.data?.headline) return section.data.headline;
                if (section.data?.title) return section.data.title;
                if (section.data?.heading) return section.data.heading;
                const block = (this.blockList || []).find(b => b.name === section.name);
                if (block && block.label) return block.label;
                const s = this.schemas[section.name];
                if (s && s.label) return s.label;
                return (section.name || '')
                    .replace(/([A-Z])/g, ' $1')
                    .replace(/^./, str => str.toUpperCase())
                    .trim();
            },

            editorTitle() {
                if (this.crumbs.length > 0) {
                    return this.crumbs[this.crumbs.length - 1].key;
                }
                const section = this.sections[this.active];
                if (!section) return '';
                return this.sectionLabel(section);
            },

            isGlobal(section) {
                const name = typeof section === 'string' ? section : (section?.name || '');
                const block = (this.blockList || []).find(b => b.name === name);
                return Boolean(block?.global);
            },

            isChecked(val) {
                return val === 'true' || val === true;
            },

            currentFields() {
                if (this.active === null) return [];
                const section = this.sections[this.active];
                if (!section) return [];
                let fields = this.schemas[section.name] || [];
                let current = { fields, data: section.data };
                for (const crumb of this.crumbs) {
                    const f = (current.fields || []).find(f2 => f2.name === crumb.key);
                    if (!f || f.type !== 'object') return [];
                    current = { fields: f.fields || [], data: (current.data || {})[crumb.key] };
                    if (crumb.index !== undefined && current.data) {
                        current.data = current.data[crumb.index] || {};
                    }
                }
                return current.fields || [];
            },

            /**
             * Filters Field::select() options for card slot selectors on ListBlock-based blocks.
             *
             * Options tagged with a 'collection' slug are filtered to match the currently
             * selected listCollection value. Options with an empty 'collection' (e.g. "-- Not Mapped --")
             * are always shown.
             *
             * For non-tagged option arrays (regular blocks), all options are returned unchanged.
             */
            filteredSelectOptions(field) {
                const opts = field.options || [];
                if (!opts.length) return opts;

                // Only filter if options carry collection tagging
                const isTagged = opts.some(o => o && typeof o.collection === 'string');
                if (!isTagged) return opts;

                // Find the listCollection value in this section's data
                const sectionData = (this.active !== null && this.sections[this.active])
                    ? (this.sections[this.active].data || {})
                    : {};
                const selectedCol = sectionData['listCollection'] || '';

                if (!selectedCol) {
                    // No collection chosen — show only the "Not Mapped" option
                    return opts.filter(o => o.collection === '');
                }

                // Show options belonging to the selected collection + universal options (collection === '')
                return opts.filter(o => o.collection === '' || o.collection === selectedCol);
            },

            getFormKeyOptions(field) {
                const formFieldKey = field.formFieldKey || 'formId';
                const selectedFormId = this.getField(formFieldKey);
                if (!selectedFormId) return [];
                const form = (this.availableForms || []).find(f => String(f.id) === String(selectedFormId));
                if (!form || !Array.isArray(form.fields)) return [];
                return form.fields.map(f => ({
                    value: f.name,
                    label: (f.label ? (f.label + ' (' + f.name + ')') : f.name) + (f.type ? ' [' + f.type + ']' : ''),
                }));
            },

            currentData() {
                if (this.active === null) return {};
                let d = this.sections[this.active].data || {};
                for (const crumb of this.crumbs) {
                    d = d[crumb.key] || {};
                    if (crumb.index !== undefined) {
                        d = d[crumb.index] || {};
                    }
                }
                return d;
            },

            fieldPath(name) {
                let path = '';
                for (const crumb of (this.crumbs || [])) {
                    path += (path ? '.' : '') + crumb.key;
                    if (crumb.index !== undefined && crumb.index !== null) {
                        path += '.' + crumb.index;
                    }
                }
                return path ? (path + '.' + name) : name;
            },

            getSourceKey(name) {
                if (this.active === null || !this.sections[this.active]) return null;
                const data = this.sections[this.active].data || {};
                const sources = data._sources || {};
                const path = this.fieldPath(name);

                if (sources[path] === '__none__') {
                    return null;
                }
                if (sources[path]) {
                    return sources[path];
                }
                const genericPath = path.replace(/\.\d+\./g, '.');
                if (sources[genericPath] && sources[genericPath] !== '__none__') {
                    return sources[genericPath];
                }
                if (sources[name] && sources[name] !== '__none__') {
                    return sources[name];
                }

                const fields = this.currentFields();
                const fieldDef = fields?.find(f => f.name === name);
                return fieldDef?.source || null;
            },

            formatToString(val) {
                if (val === null || val === undefined) return '';
                if (typeof val === 'string') {
                    if (val.trim().startsWith('{') && val.trim().endsWith('}')) {
                        try {
                            const parsed = JSON.parse(val);
                            if (parsed && typeof parsed === 'object') {
                                return this.formatToString(parsed);
                            }
                        } catch (e) {}
                    }
                    return val;
                }
                if (typeof val === 'number' || typeof val === 'boolean') return String(val);
                if (Array.isArray(val)) {
                    if (val.length === 0) return '';
                    return this.formatToString(val[0]);
                }
                if (typeof val === 'object') {
                    if (val.formatted && typeof val.formatted === 'string') {
                        return val.formatted;
                    }
                    const parts = [val.city, val.state, val.country].filter(Boolean);
                    if (parts.length > 0) {
                        return parts.join(', ');
                    }
                    if (val.url && typeof val.url === 'string') return val.url;
                    if (val.name && typeof val.name === 'string') return val.name;
                    if (val.title && typeof val.title === 'string') return val.title;
                    if (val.label && typeof val.label === 'string') return val.label;
                    return '';
                }
                return String(val);
            },

            getSourceKeyLabel(name) {
                const sourceKey = this.getSourceKey(name);
                if (!sourceKey) return '';
                if (sourceKey.startsWith('entry:') || sourceKey.startsWith('term:')) {
                    const parts = sourceKey.split(':');
                    const key = parts[2] || 'field';
                    return key.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
                return sourceKey;
            },

            getSourceKeyTitle(name) {
                const sourceKey = this.getSourceKey(name);
                if (!sourceKey) return '';
                if (sourceKey.startsWith('entry:')) {
                    const parts = sourceKey.split(':');
                    const entryId = parts[1];
                    const key = parts[2];
                    let entry = (this.pages || []).find(p => String(p.id) === String(entryId));
                    if (!entry && (window.editorAllCollections || this.allCollections)) {
                        const collections = window.editorAllCollections || this.allCollections || [];
                        for (const c of collections) {
                            if (c.entries) {
                                const found = c.entries.find(e => String(e.id) === String(entryId));
                                if (found) { entry = found; break; }
                            }
                        }
                    }
                    return 'Linked to ' + (entry ? entry.title : 'Item') + ' > ' + key;
                }
                if (sourceKey.startsWith('term:')) {
                    const parts = sourceKey.split(':');
                    const termId = parts[1];
                    const key = parts[2];
                    let term = null;
                    const allTaxes = window.editorAllTaxonomies || [];
                    for (const t of allTaxes) {
                        if (t.terms) {
                            const found = t.terms.find(item => String(item.id) === String(termId));
                            if (found) { term = found; break; }
                        }
                    }
                    return 'Linked to ' + (term ? term.title : 'Term') + ' > ' + key;
                }
                return 'Linked to field: ' + sourceKey;
            },

            getEntrySourceFields(entryId) {
                if (!entryId) return [];
                let entry = (this.pages || []).find(p => String(p.id) === String(entryId));
                if (!entry && (window.editorAllCollections || this.allCollections)) {
                    const collections = window.editorAllCollections || this.allCollections || [];
                    for (const c of collections) {
                        if (c.entries) {
                            const found = c.entries.find(e => String(e.id) === String(entryId));
                            if (found) { entry = found; break; }
                        }
                    }
                }
                if (!entry) return [];

                const fields = [
                    { key: 'title', label: 'Title' },
                    { key: 'slug', label: 'Slug' },
                    { key: 'link', label: 'Link / URL' },
                    { key: 'route', label: 'Route / Link' },
                    { key: 'created_at', label: 'Created At' },
                    { key: 'author', label: 'Author' },
                ];

                // 1. Add all fields defined on this collection schema
                const collections = window.editorAllCollections || this.allCollections || [];
                const col = collections.find(c => c.slug === entry.collection_slug || String(c.id) === String(entry.collection_id) || (c.entries && c.entries.some(e => String(e.id) === String(entry.id))));
                if (col && Array.isArray(col.fields)) {
                    for (const f of col.fields) {
                        const key = f.template || f.key || f.name || '';
                        if (key && !fields.some(existing => existing.key === key)) {
                            const label = f.title || f.label || key.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            fields.push({ key: key, label: label });
                        }
                    }
                }

                // 2. Add any additional keys present in entry.data
                const ed = entry.data || {};
                for (const [k, v] of Object.entries(ed)) {
                    if (k && !k.startsWith('_') && !fields.some(existing => existing.key === k)) {
                        const cleanLabel = k.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        fields.push({ key: k, label: cleanLabel });
                    }
                }
                return fields;
            },

            getTermSourceFields(termId) {
                if (!termId) return [];
                let term = null;
                let taxonomy = null;
                const allTaxes = window.editorAllTaxonomies || [];
                for (const t of allTaxes) {
                    if (t.terms) {
                        const found = t.terms.find(item => String(item.id) === String(termId));
                        if (found) { term = found; taxonomy = t; break; }
                    }
                }
                if (!term) return [];

                const fields = [
                    { key: 'title', label: 'Name / Title' },
                    { key: 'slug', label: 'Slug' },
                    { key: 'route', label: 'Route / Link' },
                    { key: 'image', label: 'Image' },
                ];

                if (taxonomy && Array.isArray(taxonomy.fields)) {
                    for (const f of taxonomy.fields) {
                        const key = f.template || f.key || f.name || '';
                        if (key && !fields.some(existing => existing.key === key)) {
                            const label = f.title || f.label || key.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            fields.push({ key: key, label: label });
                        }
                    }
                }

                const td = term.data || {};
                for (const [k, v] of Object.entries(td)) {
                    if (k && !k.startsWith('_') && !fields.some(existing => existing.key === k)) {
                        const cleanLabel = k.replace(/[_-]+/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        fields.push({ key: k, label: cleanLabel });
                    }
                }
                return fields;
            },

            getField(name) {
                const sourceKey = this.getSourceKey(name);
                let val;
                if (sourceKey) {
                    if (sourceKey.startsWith('entry:')) {
                        const parts = sourceKey.split(':');
                        const entryId = parts[1];
                        const key = parts[2];
                        let entry = (this.pages || []).find(p => String(p.id) === String(entryId));
                        if (!entry && (window.editorAllCollections || this.allCollections)) {
                            const collections = window.editorAllCollections || this.allCollections || [];
                            for (const c of collections) {
                                if (c.entries) {
                                    const found = c.entries.find(e => String(e.id) === String(entryId));
                                    if (found) { entry = found; break; }
                                }
                            }
                        }
                        if (entry) {
                            if (key === 'title') {
                                val = entry.title;
                            } else if (key === 'link' || key === 'route') {
                                val = entry.route;
                            } else if (key === 'slug') {
                                val = entry.slug;
                            } else if (entry.data && entry.data[key] !== undefined && entry.data[key] !== null) {
                                val = entry.data[key];
                            }
                        }
                    } else if (sourceKey.startsWith('term:')) {
                        const parts = sourceKey.split(':');
                        const termId = parts[1];
                        const key = parts[2];
                        let term = null;
                        let taxonomy = null;
                        const allTaxes = window.editorAllTaxonomies || [];
                        for (const t of allTaxes) {
                            if (t.terms) {
                                const found = t.terms.find(item => String(item.id) === String(termId));
                                if (found) { term = found; taxonomy = t; break; }
                            }
                        }
                        if (term) {
                            if (key === 'title' || key === 'name') {
                                val = term.title;
                            } else if (key === 'link' || key === 'route' || key === 'url') {
                                const pattern = taxonomy?.route_pattern || '';
                                if (pattern) {
                                    val = pattern.replace('{slug}', term.slug).replace('{id}', term.id).replace('{title}', encodeURIComponent(term.title));
                                } else if (term.route) {
                                    val = term.route;
                                } else {
                                    const taxSlug = taxonomy?.slug || 'destinations';
                                    val = '/' + taxSlug + '/' + term.slug;
                                }
                            } else if (key === 'slug') {
                                val = term.slug;
                            } else if (key === 'image') {
                                val = term.data?.image || term.data?.featured_image || term.data?.cover_image || term.data?.photo || '';
                            } else if (term.data && term.data[key] !== undefined && term.data[key] !== null) {
                                val = term.data[key];
                            }
                        }
                    } else {
                        if (this.entryData && this.entryData[sourceKey] !== undefined && this.entryData[sourceKey] !== null && this.entryData[sourceKey] !== '') {
                            val = this.entryData[sourceKey];
                        } else if (this.siteSettings && this.siteSettings[sourceKey] !== undefined && this.siteSettings[sourceKey] !== null && this.siteSettings[sourceKey] !== '') {
                            val = this.siteSettings[sourceKey];
                        } else if (this.homeGlobals && this.homeGlobals[sourceKey] !== undefined && this.homeGlobals[sourceKey] !== null && this.homeGlobals[sourceKey] !== '') {
                            val = this.homeGlobals[sourceKey];
                        } else {
                            val = '';
                        }
                    }
                } else {
                    val = this.currentData()[name] ?? '';
                }

                const fields = this.currentFields();
                const fieldDef = fields?.find(f => f.name === name);
                if (fieldDef && (fieldDef.type === 'object' || fieldDef.type === 'location' || fieldDef.type === 'devices')) {
                    if (fieldDef.type === 'devices') {
                        if (!val || typeof val !== 'object' || Object.keys(val).length === 0) {
                            return { laptop: true, tablet: true, mobile: true };
                        }
                    }
                    return val;
                }

                if (typeof val === 'object' && val !== null) {
                    return this.formatToString(val);
                }

                return val;
            },

            isSourceField(name) {
                const sourceKey = this.getSourceKey(name);
                return !!sourceKey;
            },

            setFieldSource(name, sourceKey) {
                if (this.active === null || !this.sections[this.active]) return;
                if (!this.sections[this.active].data) this.sections[this.active].data = {};
                if (!this.sections[this.active].data._sources) this.sections[this.active].data._sources = {};

                const path = this.fieldPath(name);
                this.sections[this.active].data._sources[path] = sourceKey;
                this.sections[this.active].data = { ...this.sections[this.active].data };
                this.sections = [...this.sections];
                this.dirty = true;
                this.schedulePreview();
            },

            clearFieldSource(name) {
                if (this.active === null || !this.sections[this.active]) return;
                if (!this.sections[this.active].data) this.sections[this.active].data = {};
                if (!this.sections[this.active].data._sources) this.sections[this.active].data._sources = {};

                const path = this.fieldPath(name);
                this.sections[this.active].data._sources[path] = '__none__';
                this.sections[this.active].data = { ...this.sections[this.active].data };
                this.sections = [...this.sections];
                this.dirty = true;
                this.schedulePreview();
            },

            getLinkMode(name) {
                if (!(name in this.linkModes)) {
                    const val = this.getField(name);
                    if (!val) {
                        const firstCol = this.getLinkCollections()[0]?.slug;
                        this.linkModes[name] = firstCol || 'custom';
                    } else {
                        const collections = this.getLinkCollections();
                        let matchedSlug = null;
                        for (const col of collections) {
                            const entries = this.getLinkEntries(col.slug);
                            if (entries && entries.some(e => e.route === val || String(e.id) === String(val) || e.slug === val)) {
                                matchedSlug = col.slug;
                                break;
                            }
                        }
                        if (matchedSlug) {
                            this.linkModes[name] = matchedSlug;
                        } else {
                            const matched = this.pages?.find(p => p.route === val || p.slug === val);
                            if (matched) {
                                this.linkModes[name] = matched.collection_slug || 'pages';
                            } else {
                                this.linkModes[name] = 'custom';
                            }
                        }
                    }
                }
                return this.linkModes[name];
            },

            setLinkMode(name, mode) {
                this.linkModes[name] = mode;
            },

            getLinkCollections() {
                const collections = (this.allCollections && this.allCollections.length > 0)
                    ? this.allCollections
                    : (window.editorAllCollections || []);
                if (collections && collections.length > 0) {
                    return collections.filter(c => {
                        const slug = (c.slug || '').toLowerCase();
                        const name = (c.name || '').toLowerCase();
                        return slug !== 'layout' && slug !== 'layouts' && name !== 'layout';
                    });
                }
                const map = new Map();
                (this.pages || []).forEach(p => {
                    const slug = p.collection_slug || 'pages';
                    const name = p.collection_name || 'Pages';
                    if (name.toLowerCase() === 'layout' || slug.toLowerCase() === 'layout' || slug.toLowerCase() === 'layouts') {
                        return;
                    }
                    if (!map.has(slug)) {
                        map.set(slug, { slug, name });
                    }
                });
                return Array.from(map.values());
            },

            getLinkEntries(collectionSlug) {
                const collections = (this.allCollections && this.allCollections.length > 0)
                    ? this.allCollections
                    : (window.editorAllCollections || []);
                if (collections && collections.length > 0) {
                    const found = collections.find(c => String(c.slug) === String(collectionSlug) || String(c.id) === String(collectionSlug));
                    if (found && found.entries && found.entries.length > 0) {
                        return found.entries;
                    }
                }
                return (this.pages || []).filter(p => (p.collection_slug || 'pages') === collectionSlug);
            },

            getLinkModeLabel(mode) {
                if (mode === 'custom') return 'Custom';
                const col = this.getLinkCollections().find(c => c.slug === mode);
                return col ? col.name : 'Custom';
            },

            linkFieldValue(name, linkValue) {
                this.setField(name, linkValue);
            },

            getCollectionFieldMode(name, field) {
                if (field && field.collection) return field.collection;
                if (!(name in this.colModes)) {
                    const val = this.getField(name);
                    if (val) {
                        const matched = (this.pages || []).find(p => String(p.id) === String(val));
                        if (matched && matched.collection_slug) {
                            this.colModes[name] = matched.collection_slug;
                        }
                    }
                    if (!this.colModes[name]) {
                        const firstCol = this.getLinkCollections()[0]?.slug;
                        this.colModes[name] = firstCol || '';
                    }
                }
                return this.colModes[name];
            },

            setCollectionFieldMode(name, mode) {
                this.colModes[name] = mode;
            },

            getCollectionModeLabel(slug) {
                if (!slug) return 'Collection';
                const col = this.getLinkCollections().find(c => c.slug === slug);
                return col ? col.name : (slug.charAt(0).toUpperCase() + slug.slice(1));
            },

            getCollectionEntryTitle(field) {
                const val = this.getField(field.name);
                if (!val) return '';
                const matched = (this.pages || []).find(p => String(p.id) === String(val));
                return matched ? matched.title : String(val);
            },

            selectCollectionEntry(field, item) {
                if (!item) {
                    this.clearCollectionEntry(field);
                    return;
                }

                this.setField(field.name, item.id);
                this.dirty = true;
                this.schedulePreview();
            },

            clearCollectionEntry(field) {
                this.setField(field.name, '');
                this.dirty = true;
                this.schedulePreview();
            },

            syncPreviewField(name, value) {
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return false;

                const doc = iframe.contentDocument || iframe.contentWindow?.document;
                if (!doc) return false;

                let context = doc;
                const container = doc.getElementById('preview-content');
                if (container && this.active !== null && container.children[this.active]) {
                    context = container.children[this.active];
                }

                // ── Configuration / Background group special handling ──────────────
                // When we are editing inside the 'configuration' or 'background' group (crumbs contain
                // {key:'configuration'} or {key:'background'} with no index)...
                const isInsideConfigGroup = this.crumbs.length > 0 &&
                    (this.crumbs[this.crumbs.length - 1].key === 'configuration' || this.crumbs[this.crumbs.length - 1].key === 'background') &&
                    this.crumbs[this.crumbs.length - 1].index === undefined;

                if (isInsideConfigGroup) {
                    if (name === 'devices') {
                        this.schedulePreview();
                        return true;
                    }

                    // context = the outer [data-section-index] wrapper div.
                    // We need the actual block element ([data-block]) inside it.
                    const sectionWrapper = (context !== doc) ? context : null;
                    const blockEl = sectionWrapper
                        ? (sectionWrapper.querySelector('[data-block]') || sectionWrapper)
                        : null;

                    if (blockEl) {
                        // Read the current full background data from Alpine state
                        const bgData = this.currentData();
                        const currentImage = name === 'image' ? value : (bgData['image'] ?? '');
                        const currentColor = name === 'color' ? value : (bgData['color'] ?? '');
                        const currentOpacity = name === 'opacity' ? value : (bgData['opacity'] ?? 100);

                        // Ensure the section is positioned so absolute children work
                        if (getComputedStyle(blockEl).position === 'static') {
                            blockEl.style.position = 'relative';
                        }

                        // Find or create the background image div.
                        // Also detect the Blade-rendered background div (has background-image
                        // in its inline style but no data attribute) and adopt it.
                        let bgImgDiv = blockEl.querySelector('[data-bg-img-layer]');
                        if (!bgImgDiv) {
                            // Blade renders: <div class="absolute inset-0 bg-cover ..." style="background-image: url(...)">
                            const candidates = Array.from(blockEl.children).filter(el =>
                                el.style && el.style.backgroundImage && el.style.backgroundImage !== 'none'
                            );
                            if (candidates.length > 0) {
                                bgImgDiv = candidates[0];
                                bgImgDiv.setAttribute('data-bg-img-layer', '');
                            }
                        }

                        let bgColorDiv = blockEl.querySelector('[data-bg-color-layer]');
                        if (!bgColorDiv) {
                            const candidates = Array.from(blockEl.children).filter(el =>
                                el.style && el.style.backgroundColor && el.style.backgroundColor !== '' &&
                                !el.hasAttribute('data-bg-img-layer')
                            );
                            if (candidates.length > 0) {
                                bgColorDiv = candidates[0];
                                bgColorDiv.setAttribute('data-bg-color-layer', '');
                            }
                        }

                        if (currentImage) {
                            if (!bgImgDiv) {
                                bgImgDiv = doc.createElement('div');
                                bgImgDiv.setAttribute('data-bg-img-layer', '');
                                blockEl.insertBefore(bgImgDiv, blockEl.firstChild);
                            }
                            bgImgDiv.style.cssText = `
                                position: absolute;
                                inset: 0;
                                background-image: url(${currentImage});
                                background-size: cover;
                                background-position: center;
                                background-repeat: no-repeat;
                                opacity: ${Number(currentOpacity) / 100};
                                pointer-events: none;
                                z-index: 0;
                            `;
                        } else {
                            // No image — update opacity on existing layer if present,
                            // otherwise remove the stale layer.
                            if (bgImgDiv) {
                                bgImgDiv.remove();
                                bgImgDiv = null;
                            }
                        }

                        if (currentColor) {
                            if (!bgColorDiv) {
                                bgColorDiv = doc.createElement('div');
                                bgColorDiv.setAttribute('data-bg-color-layer', '');
                                blockEl.insertBefore(bgColorDiv, blockEl.firstChild);
                            }
                            bgColorDiv.style.cssText = `
                                position: absolute;
                                inset: 0;
                                background-color: ${currentColor};
                                pointer-events: none;
                                z-index: 0;
                            `;
                        } else if (bgColorDiv) {
                            bgColorDiv.remove();
                        }

                        // Ensure all direct children that are NOT background layers
                        // have position:relative and z-index:1 so they appear above
                        Array.from(blockEl.children).forEach(child => {
                            if (!child.hasAttribute('data-bg-img-layer') && !child.hasAttribute('data-bg-color-layer')) {
                                if (getComputedStyle(child).position === 'static') {
                                    child.style.position = 'relative';
                                }
                                if (!child.style.zIndex) {
                                    child.style.zIndex = '1';
                                }
                            }
                        });
                    }
                    // Also trigger a full preview refresh so the server-rendered
                    // background (via Blade @@if checks) is always in sync.
                    this.schedulePreview();
                    return true;
                }

                // ──────────────────────────────────────────────────────────────────

                if (name === 'devices') {
                    this.schedulePreview();
                    return true;
                }

                if (this.crumbs && this.crumbs.length > 0) {
                    for (const crumb of this.crumbs) {
                        const listElements = Array.from(context.querySelectorAll(`[data-list="${crumb.key}"]`));
                        if (crumb.index !== undefined) {
                            const byExplicitIndex = listElements.find(el => el.getAttribute('data-list-index') === String(crumb.index));
                            if (byExplicitIndex) {
                                context = byExplicitIndex;
                            } else if (listElements[crumb.index]) {
                                context = listElements[crumb.index];
                            }
                        } else if (listElements.length > 0) {
                            context = listElements[0];
                        }
                    }
                }

                const target = context.querySelector(`[data-edit="${name}"]`);
                if (!target) return false;

                const isMapField = name.toLowerCase().includes('map') || (value && typeof value === 'string' && (value.includes('google.com/maps') || value.includes('maps.app') || value.includes('<iframe')));
                if (isMapField) {
                    this.schedulePreview();
                    return true;
                }

                const text = value == null || value === undefined ? '' : String(value);
                const looksLikeHtml = typeof text === 'string' && /<\/?[a-z][\s\S]*>/i.test(text);

                const fields = this.currentFields();
                const fieldDef = fields?.find(f => f.name === name);
                const isRichtextField = fieldDef?.type === 'richtext' || looksLikeHtml;

                const isLikelyImageSrc = typeof value === 'string' && !looksLikeHtml && !value.includes('<') && !value.includes('>') && (value.startsWith('/') || value.startsWith('http://') || value.startsWith('https://') || value.startsWith('data:image/'));

                const isImgTag = target.tagName === 'IMG';
                let imgEl = isImgTag ? target : (fieldDef?.type === 'image' ? target.querySelector('img') : null);
                const isImageField = !isRichtextField && isLikelyImageSrc && (fieldDef?.type === 'image' || isImgTag || (name.toLowerCase().includes('image') && !looksLikeHtml));

                if (isImageField) {
                    if (!imgEl && !isImgTag) {
                        imgEl = doc.createElement('img');
                        imgEl.className = 'absolute inset-0 w-full h-full object-cover';
                        target.appendChild(imgEl);
                    }

                    const activeImg = imgEl || target;
                    if (value && typeof value === 'string') {
                        activeImg.setAttribute('src', value);
                        activeImg.classList.remove('hidden');
                        activeImg.style.display = '';
                        target.classList.remove('hidden');
                        target.style.display = '';
                        if (!isImgTag) {
                            target.querySelectorAll('span, p, label').forEach(el => el.classList.add('hidden'));
                        }
                    } else {
                        activeImg.classList.add('hidden');
                        if (!isImgTag) {
                            target.querySelectorAll('span, p, label').forEach(el => el.classList.remove('hidden'));
                        }
                    }
                    return true;
                }

                if (looksLikeHtml) {
                    if (window.morphdom) {
                        const wrapper = doc.createElement('div');
                        wrapper.innerHTML = text;
                        window.morphdom(target, wrapper, { childrenOnly: true });
                    } else {
                        target.innerHTML = text;
                    }
                } else {
                    target.textContent = text;
                }

                return true;
            },

            setField(name, value) {
                this.setNested(name, value);
                this.dirty = true;
                const synced = this.syncPreviewField(name, value);
                if (!synced) {
                    this.schedulePreview();
                }
            },

            setNested(name, value) {
                if (this.active === null) return;
                if (!this.sections[this.active].data) {
                    this.sections[this.active].data = {};
                }
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    if (!crumb || !crumb.key) return;
                    if (d[crumb.key] === undefined || d[crumb.key] === null) {
                        d[crumb.key] = (crumb.index !== undefined) ? [] : {};
                    }
                    d = d[crumb.key];
                    if (crumb.index !== undefined) {
                        if (!Array.isArray(d)) d = [];
                        if (d[crumb.index] === undefined || d[crumb.index] === null) {
                            d[crumb.index] = {};
                        }
                        d = d[crumb.index];
                    }
                }
                if (d && typeof d === 'object') {
                    d[name] = value;
                }
            },

            getList(name) {
                const val = this.currentData()[name];
                return Array.isArray(val) ? val : [];
            },

            drillIn(key, index) {
                const val = this.currentData()[key];
                if (typeof val === 'string') {
                    try {
                        const parsed = JSON.parse(val);
                        if (typeof parsed === 'object' && parsed !== null) {
                            this.setNested(key, parsed);
                        }
                    } catch (e) {}
                }
                this.crumbs.push({ key, index: index ?? undefined });
                this.initListSortables();
            },

            currentSection() {
                if (this.active === null) return null;
                return this.sections[this.active] ?? null;
            },

            toggleEnabled() {
                const section = this.currentSection();
                if (!section) return;
                section.enabled = section.enabled === false ? true : false;
                this.dirty = true;
                this.schedulePreview();
            },

            exit() {
                if (this.crumbs.length > 0) {
                    this.destroyNestedSortables();
                    this.crumbs.pop();
                } else {
                    this.active = null;
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            destroyNestedSortables() {
                document.querySelectorAll('[data-sortable-list]').forEach(el => {
                    if (el._sortable) {
                        el._sortable.destroy();
                        delete el._sortable;
                    }
                });
            },

            edit(i, focusField) {
                this.active = i;
                this.crumbs = [];
                this._focusField = focusField || null;
                if (this._focusField) {
                    this.$nextTick(() => this.focusFirstField());
                }
            },

            focusFirstField() {
                const targetName = this._focusField ? this.resolveFieldName(this._focusField) : null;
                const candidates = document.querySelectorAll('[data-field-target]');

                if (targetName) {
                    const el = document.querySelector(`[data-field-target="${targetName}"]`);
                    if (el) {
                        const proseMirror = el.classList.contains('ProseMirror') ? el : el.querySelector('.ProseMirror');
                        if (proseMirror) { proseMirror.focus(); } else { try { el.focus(); } catch {} }
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                }

                for (const el of candidates) {
                    const proseMirror = el.classList.contains('ProseMirror') ? el : el.querySelector('.ProseMirror');
                    if (proseMirror) {
                        proseMirror.focus();
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                    if (el.matches('input, textarea, button, select, [tabindex]')) {
                        try { el.focus(); } catch {}
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        return;
                    }
                }
            },

            resolveFieldName(type) {
                if (this.active === null) return null;
                const section = this.sections[this.active];
                if (!section) return null;
                const schema = this.schemas[section.name] || [];
                if (type === 'title') {
                    for (const name of ['headline', 'title', 'heading']) {
                        if (schema.some(f => f.name === name)) return name;
                    }
                    const f = schema.find(f => f.type === 'string' && !f.multiline);
                    return f ? f.name : null;
                }
                if (type === 'description') {
                    for (const name of ['description', 'subheading']) {
                        if (schema.some(f => f.name === name)) return name;
                    }
                    const f = schema.find(f => (f.type === 'string' && f.multiline) || f.type === 'text');
                    return f ? f.name : null;
                }
                return null;
            },

            sectionDescription(section) {
                return section.data?.description || section.data?.subheading || '';
            },

            addSection(name) {
                const section = this.createDefault(name);
                if (section) {
                    this.sections.push(section);
                    this.sections = [...this.sections];
                    this.dirty = true;
                    this.schedulePreview();
                    this.$nextTick(() => this.initSectionSortable());
                }
            },

            createDefault(name) {
                const schema = this.schemas[name];
                if (!schema) return null;
                const homeGlobal = this.homeGlobals.find?.(g => g.name === name);
                return {
                    _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2),
                    name: name,
                    enabled: true,
                    data: homeGlobal ? JSON.parse(JSON.stringify(homeGlobal.data)) : this.buildDefaultData(schema),
                };
            },

            buildDefaultData(fields) {
                const out = {};
                for (const f of fields) {
                    if (f.type === 'object') {
                        if (f.list) {
                            out[f.name] = [];
                            const count = f.defaultCount || 0;
                            for (let i = 0; i < count; i++) {
                                out[f.name].push(this.buildListItem(f));
                            }
                        } else {
                            out[f.name] = this.buildDefaultData(f.fields || []);
                        }
                    } else {
                        out[f.name] = f.defaultValue ?? '';
                    }
                }
                return out;
            },

            buildListItem(field) {
                return { _key: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2), ...this.buildDefaultData(field.fields || []) };
            },

            reset() {
                this.sections = JSON.parse(JSON.stringify(this.originalSections));
                this.ensureSectionKeys();
                this.active = null;
                this.crumbs = [];
                this.dirty = false;
                this.closeAllSelects();
                this.$nextTick(() => {
                    this.initSectionSortable();
                    this.refreshPreview();
                });
            },

            removeSection(i) {
                // Destroy Sortable BEFORE mutating the array so it doesn't
                // interfere with Alpine's DOM diffing (otherwise the wrong
                // element gets removed from the DOM).
                if (this._sectionSortable) {
                    try { this._sectionSortable.destroy(); } catch (e) {}
                    this._sectionSortable = null;
                }
                this.sections.splice(i, 1);
                this.sections = [...this.sections];
                if (this.active === i || this.active >= this.sections.length) { this.active = null; this.crumbs = []; }
                this.dirty = true;
                this.schedulePreview();
                this.$nextTick(() => this.initSectionSortable());
            },

            addListItem(name) {
                const field = this.findField(name);
                if (!field) return;
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                if (!Array.isArray(d[name])) d[name] = [];
                d[name].push(this.buildListItem(field));
                d[name] = [...d[name]];
                this.dirty = true;
                this.schedulePreview();
                this.initListSortables();
            },

            removeListItem(name, index) {
                if (this.active === null) return;
                let d = this.sections[this.active].data;
                for (const crumb of this.crumbs) {
                    d = d[crumb.key];
                    if (crumb.index !== undefined) d = d[crumb.index];
                }
                if (Array.isArray(d[name])) {
                    d[name] = d[name].filter((_, idx) => idx !== index);
                    this.dirty = true;
                    this.schedulePreview();
                    this.initListSortables();
                }
            },

            findField(name) {
                if (this.active === null) return null;
                const section = this.sections[this.active];
                let fields = this.schemas[section.name] || [];
                for (const crumb of this.crumbs) {
                    const f = fields.find(f2 => f2.name === crumb.key);
                    if (!f) return null;
                    fields = f.fields || [];
                }
                return fields.find(f => f.name === name) || null;
            },

            cardLabel(item, field, index) {
                if (item && typeof item === 'object') {
                    if (item.name && typeof item.name === 'string' && item.name.trim() !== '' && item.name !== 'Destination Name') {
                        return item.name.trim().slice(0, 40);
                    }
                    if (item.title && typeof item.title === 'string' && item.title.trim() !== '' && item.title !== 'Paris Getaway' && item.title !== 'Untitled') {
                        return item.title.trim().slice(0, 40);
                    }
                    if (item.term_id && window.editorAllTaxonomies) {
                        for (const tax of window.editorAllTaxonomies) {
                            const found = tax.terms?.find(t => String(t.id) === String(item.term_id));
                            if (found) return found.title.slice(0, 40);
                        }
                    }
                    if (item.package_id && window.editorAllCollections) {
                        for (const col of window.editorAllCollections) {
                            const found = col.entries?.find(e => String(e.id) === String(item.package_id));
                            if (found) return found.title.slice(0, 40);
                        }
                    }
                    const candidates = ['title', 'label', 'name', 'heading', 'text', 'platform', 'caption', 'url', 'link', 'value'];
                    for (const c of candidates) {
                        if (item[c] && typeof item[c] === 'string' && item[c].trim() !== '') {
                            return item[c].trim().slice(0, 40);
                        }
                    }
                }
                const num = (typeof index === 'number') ? ` #${index + 1}` : '';
                return (field?.itemLabel || field?.label || 'Item') + num;
            },

            parseTags(val) {
                if (!val) return [];
                try { return JSON.parse(val); } catch { return []; }
            },

            addTag(name, value) {
                const v = value.trim();
                if (!v) return;
                const current = this.parseTags(this.getField(name));
                current.push(v);
                this.setField(name, JSON.stringify(current));
            },

            removeTag(name, index) {
                const current = this.parseTags(this.getField(name));
                current.splice(index, 1);
                this.setField(name, JSON.stringify(current));
            },

            get filteredIcons() {
                let icons = this.faIcons;
                if (this.iconSearch.trim()) {
                    const q = this.iconSearch.toLowerCase();
                    icons = icons.filter(i => i.l.toLowerCase().includes(q) || i.c.toLowerCase().includes(q));
                }
                return icons.slice(0, 2000);
            },

            iconLabel(cls) {
                const found = this.faIcons.find(i => i.c === cls);
                return found ? found.l : cls;
            },

            schedulePreview(immediate = false) {
                if (window.__isResizingImage || window.__skipNextPreviewRefresh) {
                    return;
                }
                if (this._previewTimer) {
                    clearTimeout(this._previewTimer);
                    this._previewTimer = null;
                }
                if (immediate) {
                    this.refreshPreview();
                } else {
                    this._previewTimer = setTimeout(() => {
                        this.refreshPreview();
                    }, 120);
                }
            },

            refreshPreview() {
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return;
                if (this.sections.length === 0) {
                    const doc = iframe.contentDocument || iframe.contentWindow?.document;
                    if (doc) doc.body.innerHTML = '';
                    return;
                }
                const payload = { sections: this.sections, entry_data: this.entryData };
                if (window.editorPostId) payload.post_id = window.editorPostId;
                fetch(window.editorPreviewRoute, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.editorCsrfToken },
                    body: JSON.stringify(payload),
                })
                .then(r => { if (!r.ok) throw new Error('Preview response ' + r.status); return r.json(); })
                .then(data => { this.updateIframeContent(iframe, data.html); })
                .catch(e => console.error('Preview fetch failed:', e));
            },

            updateIframeContent(iframe, html) {
                const doc = iframe.contentDocument || iframe.contentWindow?.document;
                if (!doc) return;

                doc.body.className = 'antialiased text-gray-800 m-0 p-0 min-h-full bg-white preview-shell';
                let container = doc.getElementById('preview-content');
                if (!container) {
                    doc.body.innerHTML = '<div id="preview-content">' + html + '</div>';
                } else {
                    if (window.morphdom) {
                        window.morphdom(container, '<div id="preview-content">' + html + '</div>');
                    } else {
                        const parser = new DOMParser();
                        const newDoc = parser.parseFromString(html, 'text/html');
                        const newNodes = Array.from(newDoc.body.children);
                        const currentNodes = Array.from(container.children);

                        if (newNodes.length === currentNodes.length && newNodes.length > 0) {
                            newNodes.forEach((node, idx) => {
                                const cur = currentNodes[idx];
                                if (cur && cur.outerHTML !== node.outerHTML) {
                                    cur.replaceWith(doc.importNode(node, true));
                                }
                            });
                        } else if (container.innerHTML !== html) {
                            container.innerHTML = html;
                        }
                    }
                }

                if (!doc.head.querySelector('[data-preview-styles]')) {
                    const styleMarker = doc.createElement('meta');
                    styleMarker.setAttribute('data-preview-styles', 'true');
                    doc.head.appendChild(styleMarker);

                    const vp = doc.createElement('meta');
                    vp.name = 'viewport';
                    vp.content = 'width=device-width, initial-scale=1';
                    doc.head.appendChild(vp);

                    document.querySelectorAll('link[rel="stylesheet"], style').forEach(el => {
                        doc.head.appendChild(el.cloneNode(true));
                    });

                    const baseStyle = doc.createElement('style');
                    baseStyle.textContent = 'html, body { margin: 0; padding: 0; min-height: 100%; background-color: #ffffff; }\n' +
                        '::-webkit-scrollbar { width: 6px; height: 6px; }\n' +
                        '::-webkit-scrollbar-track { background: transparent; }\n' +
                        '::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 9999px; }\n' +
                        '::-webkit-scrollbar-thumb:hover { background: rgba(107, 114, 128, 0.7); }\n' +
                        '* { scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.4) transparent; }\n' +
                        '[data-edit], [data-list], [data-section-index] { cursor: pointer !important; }\n' +
                        '[data-edit]:hover { outline: 1px dashed #3b82f6 !important; position: relative; z-index: 50; }\n' +
                        '[data-edit]:not(img):not([data-edit-button]):hover { background-color: rgba(59, 130, 246, 0.07) !important; }\n' +
                        '[data-section-index]:hover { outline: 1px dashed #3b82f6 !important; border-radius: 4px; }\n' +
                        '[data-list]:hover { outline: 1px dashed #3b82f6 !important; }\n' +
                        '[data-package-block]:hover { outline: 1px dashed #3b82f6 !important; }';
                    doc.head.appendChild(baseStyle);
                }

                this.attachPreviewListeners(doc);
            },

            attachPreviewListeners(doc) {
                doc.addEventListener('click', (e) => {
                    const sectionEl = e.target.closest('[data-section-index]');
                    if (!sectionEl) return;
                    const link = e.target.closest('a');
                    if (link) { e.preventDefault(); e.stopPropagation(); }

                    const idx = parseInt(sectionEl.getAttribute('data-section-index'), 10);
                    if (isNaN(idx) || idx < 0 || idx >= this.sections.length) return;

                    // Always open the sidebar when clicking the preview
                    this.sidebarOpen = true;

                    const path = this.buildFieldPath(e.target);
                    this.focusField(path, idx);
                });
            },

            buildFieldPath(target) {
                const fieldEl = target.closest('[data-edit]');
                const listEl = !fieldEl ? target.closest('[data-list]') : null;

                if (!fieldEl && !listEl) return '_root';

                let leaf = '';
                let startEl = fieldEl;
                if (fieldEl) {
                    leaf = fieldEl.getAttribute('data-edit') || '';
                    if (!leaf) { startEl = null; leaf = ''; }
                } else if (listEl) {
                    startEl = listEl;
                }

                const listParts = [];
                let current = startEl;
                while (current) {
                    const listName = current.getAttribute('data-list');
                    if (listName) {
                        const explicitIndex = current.getAttribute('data-list-index');
                        if (explicitIndex !== null && explicitIndex !== '') {
                            listParts.unshift(`${listName}:${explicitIndex}`);
                        } else {
                            const sectionContainer = current.closest('[data-section-index]') || current.closest('[data-block]') || document.body;
                            const allSameList = Array.from(sectionContainer.querySelectorAll(`[data-list="${listName}"]`));
                            const index = allSameList.indexOf(current);
                            if (index >= 0) {
                                listParts.unshift(`${listName}:${index}`);
                            } else {
                                const parent = current.parentElement;
                                if (parent) {
                                    const siblings = Array.from(parent.querySelectorAll(`[data-list="${listName}"]`));
                                    const sIndex = siblings.indexOf(current);
                                    if (sIndex >= 0) listParts.unshift(`${listName}:${sIndex}`);
                                }
                            }
                        }
                    }
                    current = current.parentElement?.closest('[data-list]') ?? null;
                }

                if (leaf) {
                    return [...listParts, leaf].join('/');
                }
                return listParts.join('/') + '/';
            },

            scrollToIframeSection(sectionIdx, noScroll = false) {
                if (sectionIdx === undefined || sectionIdx === null) return;
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return;
                const doc = iframe.contentDocument || iframe.contentWindow?.document;
                if (!doc) return;
                const container = doc.getElementById('preview-content');
                if (!container) return;

                const targetEl = container.children[sectionIdx];
                if (targetEl) {
                    try {
                        if (!noScroll) {
                            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        targetEl.style.transition = 'outline 0.3s ease, box-shadow 0.3s ease';
                        targetEl.style.outline = '3px solid rgba(59, 130, 246, 0.65)';
                        targetEl.style.outlineOffset = '2px';
                        clearTimeout(targetEl._highlightTimer);
                        targetEl._highlightTimer = setTimeout(() => {
                            targetEl.style.outline = '';
                            targetEl.style.outlineOffset = '';
                        }, 2500);
                    } catch (e) {}
                }
            },

            focusField(cmd, sectionIdx, noScroll = false) {
                if (sectionIdx !== undefined) {
                    this.active = sectionIdx;
                    this.scrollToIframeSection(sectionIdx, noScroll);
                }

                const raw = cmd.split('#')[0];
                if (raw === '_root') { this.crumbs = []; return; }

                const tokens = String(raw)
                    .replace(/\[(\w+)\]/g, '.$1')
                    .replace(/[:\/]/g, '.')
                    .replace(/^\.+|\.+$/g, '')
                    .split('.')
                    .filter(Boolean);

                const leaf = tokens.length > 0 ? tokens[tokens.length - 1] : '';
                const newCrumbs = [];
                let curFields = this.schemas[this.sections[this.active]?.name] || [];
                let curData = this.sections[this.active]?.data || {};

                let i = 0;
                while (i < tokens.length - 1) {
                    const key = tokens[i];
                    const next = tokens[i + 1];
                    const isNextIndex = /^\d+$/.test(next);

                    const def = curFields.find(f => f.name === key && (f.type === 'object' || f.type === 'list'));
                    if (def) {
                        if (isNextIndex) {
                            const index = parseInt(next, 10);
                            newCrumbs.push({ key, index });
                            curData = curData?.[key]?.[index] || {};
                            i += 2;
                        } else {
                            newCrumbs.push({ key });
                            curData = curData?.[key] || {};
                            i += 1;
                        }
                        curFields = def.fields || [];
                    } else {
                        i++;
                    }
                }

                this.crumbs = newCrumbs;

                this.$nextTick(() => {
                    const fieldEl = document.querySelector(`[data-field-target="${leaf}"]`);
                    if (fieldEl && fieldEl.offsetParent !== null) {
                        const proseMirror = fieldEl.classList.contains('ProseMirror') ? fieldEl : fieldEl.querySelector('.ProseMirror');
                        if (proseMirror) {
                            proseMirror.focus();
                        } else if (fieldEl.matches('input, textarea, button, select, [tabindex]')) {
                            try { fieldEl.focus(); } catch {}
                        }
                        this.highlightField(fieldEl);
                        if (!noScroll) {
                            try { fieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                        }
                    } else if (leaf) {
                        const scrollEl = document.querySelector(`[data-field-scroll="${leaf}"]`);
                        if (scrollEl) {
                            if (!noScroll) {
                                try { scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch {}
                            }
                            const target = document.querySelector(`[data-field-target="${leaf}"]`);
                            if (target) this.highlightField(target);
                        }
                    }
                });
            },

            highlightField(el) {
                if (!el) return;
                el.style.transition = 'box-shadow 0.2s ease, border-color 0.2s ease';
                el.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.55)';
                el.style.borderColor = 'rgb(59, 130, 246)';
                clearTimeout(el._highlightTimer);
                el._highlightTimer = setTimeout(() => {
                    el.style.boxShadow = '';
                    el.style.borderColor = '';
                }, 2500);
            },

            selects: {},

            getSelect(id) {
                if (!this.selects[id]) this.selects[id] = { open: false, search: '', highlight: -1 };
                return this.selects[id];
            },

            toggleSelect(id) {
                const s = this.getSelect(id);
                const wasOpen = s.open;
                this.closeAllSelects();
                if (!wasOpen) {
                    s.open = true;
                    this.$nextTick(() => {
                        if (this.selects[id]?.searchable) {
                            const input = document.getElementById('sel-search-' + id);
                            input?.focus();
                        }
                    });
                }
            },

            closeSelect(id) {
                const s = this.selects[id];
                if (s) { s.open = false; s.search = ''; s.highlight = -1; }
            },

            closeAllSelects() {
                Object.keys(this.selects).forEach(k => this.closeSelect(k));
            },

            selectKeydown(e, id) {
                const s = this.getSelect(id);
                if (!s.open) {
                    if (['Enter', ' ', 'ArrowDown'].includes(e.key)) { e.preventDefault(); s.open = true; }
                    return;
                }
                const list = s.searchable ? s.filtered : s.options;
                switch (e.key) {
                    case 'Escape': e.preventDefault(); s.open = false; break;
                    case 'ArrowDown': e.preventDefault(); s.highlight = Math.min(s.highlight + 1, (list?.length || 1) - 1); break;
                    case 'ArrowUp': e.preventDefault(); s.highlight = Math.max(s.highlight - 1, 0); break;
                    case 'Enter': e.preventDefault(); if (list?.[s.highlight]) { s.onSelect(list[s.highlight].value); s.open = false; } break;
                }
            },

            async save() {
                this.isSaving = true;
                const route = window.editorSaveRoute;
                try {
                    const r = await fetch(route, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.editorCsrfToken },
                        body: JSON.stringify({ sections: this.sections }),
                    });
                    if (r.ok) {
                        this.originalSections = JSON.parse(JSON.stringify(this.sections));
                        this.dirty = false;
                        this.isSaving = false;
                    } else {
                        this.isSaving = false;
                        alert('Save failed.');
                    }
                } catch {
                    this.isSaving = false;
                    alert('Save failed.');
                }
        }
    }
}

document.addEventListener('alpine:init', function () {
    if (window.Alpine) {
        window.Alpine.data('pageEditor', pageEditor);
    }
});
