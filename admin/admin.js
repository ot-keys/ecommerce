(function () {
    'use strict';

    const clone = value => JSON.parse(JSON.stringify(value));
    const state = clone(window.initialSiteData || { hero: {}, containers: [] });
    const undoStack = [];
    const redoStack = [];
    const sectionList = document.getElementById('sectionList');
    const inspectorFields = document.getElementById('inspectorFields');
    const inspectorTitle = document.getElementById('inspectorTitle');
    const preview = document.getElementById('pagePreview');
    const status = document.getElementById('editorStatus');
    const undoButton = document.getElementById('undoButton');
    const redoButton = document.getElementById('redoButton');
    const toast = document.getElementById('editorToast');
    let selected = { kind: 'hero' };
    let dragItem = null;
    let saveInProgress = false;
    let editTimer = null;
    let editing = false;

    const escapeHtml = value => String(value == null ? '' : value).replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[character]));

    function announce(message, isError) {
        toast.textContent = message;
        toast.classList.toggle('is-error', Boolean(isError));
        toast.classList.add('visible');
        window.clearTimeout(announce.timer);
        announce.timer = window.setTimeout(() => toast.classList.remove('visible'), 3500);
    }

    function updateHistoryButtons() {
        undoButton.disabled = undoStack.length === 0;
        redoButton.disabled = redoStack.length === 0;
    }

    function finishEditGroup() {
        window.clearTimeout(editTimer);
        editTimer = null;
        editing = false;
    }

    function rememberChange() {
        if (!editing) {
            undoStack.push(clone(state));
            if (undoStack.length > 80) undoStack.shift();
            redoStack.length = 0;
            editing = true;
        }
        window.clearTimeout(editTimer);
        editTimer = window.setTimeout(finishEditGroup, 700);
        updateHistoryButtons();
        status.textContent = 'Unsaved changes';
    }

    function undo() {
        finishEditGroup();
        if (!undoStack.length) return;
        redoStack.push(clone(state));
        replaceState(undoStack.pop());
    }

    function redo() {
        finishEditGroup();
        if (!redoStack.length) return;
        undoStack.push(clone(state));
        replaceState(redoStack.pop());
    }

    function replaceState(nextState) {
        Object.keys(state).forEach(key => delete state[key]);
        Object.assign(state, clone(nextState));
        status.textContent = 'Unsaved changes';
        updateHistoryButtons();
        render();
    }

    function commitChange(mutator) {
        finishEditGroup();
        undoStack.push(clone(state));
        if (undoStack.length > 80) undoStack.shift();
        redoStack.length = 0;
        mutator();
        status.textContent = 'Unsaved changes';
        updateHistoryButtons();
        render();
    }

    function sectionById(id) {
        return state.containers.find(section => section.id === id);
    }

    function productById(sectionId, productId) {
        const section = sectionById(sectionId);
        return section && section.products.find(product => product.id === productId);
    }

    function renderStructure() {
        sectionList.innerHTML = state.containers.map(section => {
            const sectionSelected = selected.kind === 'section' && selected.id === section.id;
            const products = section.products.map(product => {
                const productSelected = selected.kind === 'product' && selected.id === product.id && selected.sectionId === section.id;
                return '<button type="button" class="structure-item product-structure' + (productSelected ? ' selected' : '') + '" draggable="true" data-kind="product" data-id="' + escapeHtml(product.id) + '" data-section-id="' + escapeHtml(section.id) + '">' +
                    '<span class="drag-handle" aria-hidden="true">⠿</span><span><strong>' + escapeHtml(product.name || 'Untitled product') + '</strong><small>' + formatMoney(product.price) + '</small></span></button>';
            }).join('');
            return '<section class="structure-section" data-section-id="' + escapeHtml(section.id) + '">' +
                '<button type="button" class="structure-item section-structure' + (sectionSelected ? ' selected' : '') + '" draggable="true" data-kind="section" data-id="' + escapeHtml(section.id) + '">' +
                '<span class="drag-handle" aria-hidden="true">⠿</span><span><strong>' + escapeHtml(section.title || 'Untitled section') + '</strong><small>' + section.products.length + ' products</small></span></button>' +
                '<div class="product-structure-list" data-product-list="' + escapeHtml(section.id) + '">' + products + '</div>' +
                '<button class="add-product-link" type="button" data-add-product="' + escapeHtml(section.id) + '">+ Add product</button></section>';
        }).join('');
    }

    function formatMoney(value) {
        const amount = Number(value);
        return '₦' + (Number.isFinite(amount) ? amount : 0).toLocaleString('en-NG', { maximumFractionDigits: 0 });
    }

    function previewMarkup() {
        const hero = state.hero || {};
        const sections = state.containers.map(section => {
            const cards = section.products.map(product => {
                const price = Number(product.price) || 0;
                const discount = Math.min(100, Math.max(0, Number(product.discount) || 0));
                const imagePath = product.image ? String(product.image).replace(/^\.?\//, '') : '';
                const priceMarkup = price > 0
                    ? '<strong>' + formatMoney(price * (1 - discount / 100)) + '</strong>' +
                        (discount > 0 ? '<del>' + formatMoney(price) + '</del>' : '') +
                        '<button type="button">Add to cart</button>'
                    : '';
                return '<article class="product"><img src="' + escapeHtml(imagePath) + '" alt="' + escapeHtml(product.name) + '">' +
                    '<div class="product-info"><h3>' + escapeHtml(product.name) + '</h3><p>' + escapeHtml(product.description) + '</p>' +
                    priceMarkup + '</div></article>';
            }).join('');
            return '<section class="products"><header><h2>' + escapeHtml(section.title) + '</h2><p>' + escapeHtml(section.subtitle) + '</p></header><div class="grid">' + cards + '</div></section>';
        }).join('');
        const heroImage = hero.image ? String(hero.image).replace(/^\.?\//, '') : '';

        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><base href="../"><style>' +
            '*{box-sizing:border-box}body{margin:0;background:#f4f6fa;color:#15213a;font:15px Arial,sans-serif}.nav{height:58px;display:flex;align-items:center;padding:0 6%;background:#091641;color:#fff;font-weight:700}.hero{min-height:230px;padding:34px 6%;background:linear-gradient(110deg,rgba(9,22,65,.92),rgba(9,22,65,.4)),url("' + escapeHtml(heroImage) + '") center/cover;color:#fff}.hero small{color:#6de37a;font-weight:700}.hero h1{max-width:700px;margin:12px 0;font-size:clamp(26px,5vw,44px)}.hero p{max-width:600px;line-height:1.55}.products{max-width:1100px;margin:24px auto;padding:0 20px}.products header{margin:26px 0 14px}.products h2{margin:0 0 6px}.products header p{color:#647087;margin:0}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,190px),1fr));gap:14px}.product{overflow:hidden;border-radius:12px;background:white;box-shadow:0 4px 16px #14254a12}.product img{display:block;width:100%;height:150px;object-fit:contain;background:#fff}.product-info{display:flex;flex-direction:column;min-height:160px;padding:12px}.product h3{font-size:16px;margin:0 0 8px}.product p{font-size:13px;color:#647087;line-height:1.4;margin:0 0 12px}.product strong{margin-top:auto;color:#14791e}.product del{font-size:12px;color:#778195;margin-top:3px}.product button{margin-top:10px;border:0;border-radius:7px;background:#14791e;color:white;padding:10px;cursor:pointer}@media(max-width:480px){.products{padding:0 12px}.grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.product img{height:120px}.product-info{padding:10px}.hero{padding-inline:20px}}' +
            '</style></head><body><div class="nav">POWER SOLUTIONS</div><header class="hero"><small>' + escapeHtml(hero.eyebrow) + '</small><h1>' + escapeHtml(hero.title) + ' <span style="color:#65e178">' + escapeHtml(hero.highlight) + '</span></h1><p>' + escapeHtml(hero.description) + '</p></header>' + sections + '</body></html>';
    }

    function renderPreview() {
        preview.srcdoc = previewMarkup();
    }

    function field(label, name, value, options) {
        const config = options || {};
        const wide = config.wide ? ' class="wide"' : '';
        const inputType = config.type || 'text';
        const required = config.required === false ? '' : ' required';
        const control = config.multiline
            ? '<textarea data-field="' + name + '"' + required + '>' + escapeHtml(value) + '</textarea>'
            : '<input data-field="' + name + '" type="' + inputType + '" value="' + escapeHtml(value) + '"' + (config.attributes || '') + required + '>';
        return '<label' + wide + '>' + label + control + (config.help ? '<small>' + escapeHtml(config.help) + '</small>' : '') + '</label>';
    }

    function renderHeroInspector() {
        const hero = state.hero || {};
        inspectorTitle.textContent = 'Hero banner';
        inspectorFields.innerHTML = '<div class="inspector-form">' +
            field('Eyebrow', 'eyebrow', hero.eyebrow || '') +
            field('Main title', 'title', hero.title || '') +
            field('Highlighted title', 'highlight', hero.highlight || '') +
            field('Description', 'description', hero.description || '', { multiline: true, wide: true }) +
            field('Background image path', 'image', hero.image || '', { wide: true, required: false, help: 'Upload an image or enter a path such as images/Hero.png.' }) +
            imageUploadControl() +
            '</div>';
    }

    function imageUploadControl() {
        return '<label class="wide upload-label">Upload image<input type="file" class="image-upload" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, or WEBP · max 5MB</small></label>';
    }

    function renderSectionInspector(section) {
        inspectorTitle.textContent = 'Product section';
        const placement = section.placement || 'featured';
        inspectorFields.innerHTML = '<div class="inspector-form">' +
            field('Section title', 'title', section.title || '') +
            field('Subtitle', 'subtitle', section.subtitle || '', { wide: true }) +
            '<label class="wide">Card group<select data-field="placement">' +
            '<option value="categories"' + (placement === 'categories' ? ' selected' : '') + '>Product categories</option>' +
            '<option value="top-sellers"' + (placement === 'top-sellers' ? ' selected' : '') + '>Top Sellers</option>' +
            '<option value="power-plans"' + (placement === 'power-plans' ? ' selected' : '') + '>Power Plans</option>' +
            '<option value="for-you"' + (placement === 'for-you' ? ' selected' : '') + '>For You</option>' +
            '<option value="featured"' + (placement === 'featured' ? ' selected' : '') + '>Featured Products</option>' +
            '</select></label>' +
            '<button class="button danger delete-selection" type="button">Delete section and products</button></div>';
    }

    function renderProductInspector(section, product) {
        inspectorTitle.textContent = 'Product details';
        inspectorFields.innerHTML = '<div class="inspector-form">' +
            field('Product name', 'name', product.name || '') +
            field('Price (₦)', 'price', product.price || 0, { type: 'number', attributes: ' min="0" step="0.01"' }) +
            field('Discount (%)', 'discount', product.discount || 0, { type: 'number', attributes: ' min="0" max="100" step="0.01"' }) +
            field('Description', 'description', product.description || '', { multiline: true, wide: true }) +
            field('Product image path', 'image', product.image || '', { wide: true, required: false, help: 'Upload an image or enter its path, for example images/solar.jpg.' }) +
            imageUploadControl() +
            '<button class="button danger delete-selection" type="button">Delete product</button></div>';
        inspectorFields.querySelector('.delete-selection').addEventListener('click', () => {
            if (!window.confirm('Delete this product from the draft?')) return;
            commitChange(() => {
                section.products = section.products.filter(item => item.id !== product.id);
                selected = { kind: 'section', id: section.id };
            });
        });
    }

    function render() {
        renderStructure();
        if (selected.kind === 'hero') {
            renderHeroInspector();
        } else if (selected.kind === 'section') {
            const section = sectionById(selected.id);
            if (!section) {
                selected = { kind: 'hero' };
                renderHeroInspector();
            } else {
                renderSectionInspector(section);
                inspectorFields.querySelector('.delete-selection').addEventListener('click', () => {
                    if (!window.confirm('Delete this section and all its products from the draft?')) return;
                    commitChange(() => {
                        state.containers = state.containers.filter(item => item.id !== section.id);
                        selected = { kind: 'hero' };
                    });
                });
            }
        } else {
            const section = sectionById(selected.sectionId);
            const product = section && productById(selected.sectionId, selected.id);
            if (!section || !product) {
                selected = { kind: 'hero' };
                renderHeroInspector();
            } else {
                renderProductInspector(section, product);
            }
        }
        renderPreview();
        updateHistoryButtons();
    }

    function selectedObject() {
        if (selected.kind === 'hero') return state.hero;
        if (selected.kind === 'section') return sectionById(selected.id);
        return productById(selected.sectionId, selected.id);
    }

    function handleFieldChange(event) {
        const target = event.target;
        if (!target.matches('[data-field]')) return;
        const object = selectedObject();
        if (!object) return;
        const key = target.dataset.field;
        const value = key === 'price' || key === 'discount' ? Number(target.value) : target.value;
        if (object[key] === value) return;
        rememberChange();
        object[key] = value;
        renderStructure();
        renderPreview();
    }

    function newId(prefix, name) {
        const base = String(name || prefix).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || prefix;
        return base + '-' + Math.random().toString(36).slice(2, 7);
    }

    function addSection() {
        const title = window.prompt('Name this product section:');
        if (title === null) return;
        const cleanTitle = title.trim();
        if (!cleanTitle) {
            announce('Enter a section name before adding it.', true);
            return;
        }
        let sectionId;
        commitChange(() => {
            sectionId = newId('section', cleanTitle);
            state.containers.push({ id: sectionId, title: cleanTitle, subtitle: '', placement: 'featured', products: [] });
            selected = { kind: 'section', id: sectionId };
        });
        announce('Section added to your draft.');
    }

    function addProduct(sectionId) {
        const section = sectionById(sectionId);
        if (!section) return;
        let productId;
        commitChange(() => {
            productId = newId('product', 'new-product');
            section.products.push({ id: productId, name: 'New product', description: '', image: '', price: 0, discount: 0 });
            selected = { kind: 'product', id: productId, sectionId: sectionId };
        });
        announce('Product added to your draft. Fill in its details on the right.');
    }

    function moveItem(list, fromId, toId) {
        const from = list.findIndex(item => item.id === fromId);
        const to = list.findIndex(item => item.id === toId);
        if (from < 0 || to < 0 || from === to) return false;
        const moved = list.splice(from, 1)[0];
        list.splice(to, 0, moved);
        return true;
    }

    async function uploadSelectedImage(input) {
        if (!input.files || !input.files[0]) return;
        const form = new FormData();
        form.append('action', 'upload_image');
        form.append('csrf_token', window.adminCsrfToken);
        form.append('image_upload', input.files[0]);
        input.disabled = true;
        try {
            const response = await fetch('dashboard.php', { method: 'POST', body: form });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'The image could not be uploaded.');
            const imageField = inspectorFields.querySelector('[data-field="image"]');
            if (!imageField) throw new Error('Select an image field before uploading.');
            imageField.value = result.image;
            imageField.dispatchEvent(new Event('input', { bubbles: true }));
            announce('Image uploaded. Save and publish to apply it.');
        } catch (error) {
            announce(error.message, true);
        } finally {
            input.disabled = false;
            input.value = '';
        }
    }

    async function saveDraft() {
        if (saveInProgress) return;
        finishEditGroup();
        const form = new FormData();
        form.append('action', 'save_page');
        form.append('csrf_token', window.adminCsrfToken);
        form.append('site_data', JSON.stringify(state));
        saveInProgress = true;
        document.getElementById('saveButton').disabled = true;
        status.textContent = 'Publishing changes…';
        try {
            const response = await fetch('dashboard.php', { method: 'POST', body: form });
            const result = await response.json();
            if (!response.ok) throw new Error(result.error || 'Your changes could not be saved.');
            undoStack.length = 0;
            redoStack.length = 0;
            updateHistoryButtons();
            status.textContent = 'All changes are saved.';
            announce(result.message);
        } catch (error) {
            status.textContent = 'Could not save your changes.';
            announce(error.message, true);
        } finally {
            saveInProgress = false;
            document.getElementById('saveButton').disabled = false;
        }
    }

    document.addEventListener('input', handleFieldChange);
    document.addEventListener('change', event => {
        if (event.target.matches('.image-upload')) {
            uploadSelectedImage(event.target);
        } else {
            handleFieldChange(event);
            finishEditGroup();
        }
    });
    document.addEventListener('focusout', event => {
        if (event.target.matches('[data-field]')) finishEditGroup();
    });
    document.addEventListener('click', event => {
        const structure = event.target.closest('.structure-item');
        if (structure && !dragItem) {
            finishEditGroup();
            selected = structure.dataset.kind === 'hero'
                ? { kind: 'hero' }
                : structure.dataset.kind === 'section'
                    ? { kind: 'section', id: structure.dataset.id }
                    : { kind: 'product', id: structure.dataset.id, sectionId: structure.dataset.sectionId };
            render();
            return;
        }
        const addProductButton = event.target.closest('[data-add-product]');
        if (addProductButton) addProduct(addProductButton.dataset.addProduct);
    });

    sectionList.addEventListener('dragstart', event => {
        const item = event.target.closest('.structure-item[draggable="true"]');
        if (!item) return;
        dragItem = { kind: item.dataset.kind, id: item.dataset.id, sectionId: item.dataset.sectionId || '' };
        item.classList.add('dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', item.dataset.id);
    });
    sectionList.addEventListener('dragend', event => {
        const item = event.target.closest('.structure-item');
        if (item) item.classList.remove('dragging');
        sectionList.querySelectorAll('.drop-target').forEach(element => element.classList.remove('drop-target'));
        dragItem = null;
    });
    sectionList.addEventListener('dragover', event => {
        if (!dragItem) return;
        const target = event.target.closest('.structure-item[draggable="true"]');
        const productList = event.target.closest('[data-product-list]');
        const targetSection = event.target.closest('.structure-section');
        const allowed = dragItem.kind === 'section'
            ? target && target.dataset.kind === 'section'
            : (target && target.dataset.kind === 'product' && target.dataset.sectionId === dragItem.sectionId) ||
                (target && target.dataset.kind === 'product') ||
                productList ||
                targetSection;
        if (!allowed) return;
        event.preventDefault();
        sectionList.querySelectorAll('.drop-target').forEach(element => element.classList.remove('drop-target'));
        (target || productList || targetSection).classList.add('drop-target');
    });
    sectionList.addEventListener('drop', event => {
        if (!dragItem) return;
        const target = event.target.closest('.structure-item[draggable="true"]');
        if (dragItem.kind === 'section' && target && target.dataset.kind === 'section') {
            event.preventDefault();
            if (dragItem.id !== target.dataset.id) {
                commitChange(() => moveItem(state.containers, dragItem.id, target.dataset.id));
            }
        } else if (dragItem.kind === 'product') {
            const sourceSection = sectionById(dragItem.sectionId);
            const targetSectionElement = event.target.closest('.structure-section');
            const targetProductList = event.target.closest('[data-product-list]');
            const targetSectionId = target && target.dataset.kind === 'product'
                ? target.dataset.sectionId
                : targetProductList
                    ? targetProductList.dataset.productList
                    : targetSectionElement && targetSectionElement.dataset.sectionId;
            const destinationSection = sectionById(targetSectionId);
            if (!sourceSection || !destinationSection || (target && target.dataset.kind !== 'product' && target.dataset.kind !== 'section')) return;
            if (target && target.dataset.kind === 'product' && target.dataset.id === dragItem.id) return;
            event.preventDefault();
            commitChange(() => {
                const sourceIndex = sourceSection.products.findIndex(product => product.id === dragItem.id);
                if (sourceIndex < 0) return;
                const targetRect = target && target.dataset.kind === 'product' ? target.getBoundingClientRect() : null;
                const insertBefore = targetRect ? event.clientY < targetRect.top + targetRect.height / 2 : false;
                const [product] = sourceSection.products.splice(sourceIndex, 1);
                const targetIndex = targetRect && target.dataset.sectionId === destinationSection.id
                    ? destinationSection.products.findIndex(item => item.id === target.dataset.id)
                    : -1;
                if (targetIndex < 0) destinationSection.products.push(product);
                else destinationSection.products.splice(targetIndex + (insertBefore ? 0 : 1), 0, product);
                selected = { kind: 'product', id: product.id, sectionId: destinationSection.id };
            });
        }
        dragItem = null;
    });

    document.getElementById('addSectionButton').addEventListener('click', addSection);
    document.getElementById('saveButton').addEventListener('click', saveDraft);
    undoButton.addEventListener('click', undo);
    redoButton.addEventListener('click', redo);
    document.getElementById('previewModeButton').addEventListener('click', event => {
        preview.classList.toggle('mobile-preview');
        event.currentTarget.textContent = preview.classList.contains('mobile-preview') ? 'Mobile preview' : 'Desktop preview';
    });
    window.addEventListener('keydown', event => {
        const key = event.key.toLowerCase();
        if ((event.ctrlKey || event.metaKey) && key === 'z') {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (event.shiftKey) redo();
            else undo();
        } else if ((event.ctrlKey || event.metaKey) && key === 'y') {
            event.preventDefault();
            event.stopImmediatePropagation();
            redo();
        }
    }, true);

    render();
}());
