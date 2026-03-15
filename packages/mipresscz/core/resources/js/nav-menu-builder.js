import Sortable from 'sortablejs';

// Expose Sortable globally so it can be used in Alpine components
window.Sortable = Sortable;

// ============================================================
// Navigation Menu Builder — Frontend Script
// ============================================================

// Helper: extract ordered nested tree from the DOM
function extractTree(container) {
    const result = [];
    const children = container.querySelectorAll(':scope > .fmm-item-row');
    children.forEach(function (el) {
        const id = parseInt(el.dataset.id, 10);
        if (!id) { return; }

        const nestedList = el.querySelector(':scope > .fmm-nested-list');
        result.push({
            id: id,
            children: nestedList ? extractTree(nestedList) : [],
        });
    });
    return result;
}

// Alpine.js component for a single sortable level
function menuSortable(wire, maxDepth) {
    return {
        sortable: null,

        init() {
            this.$nextTick(() => this.initSortable());
        },

        initSortable() {
            const el = this.$el;
            if (!el || el._sortable) { return; }

            this.sortable = Sortable.create(el, {
                group: {
                    name: 'menu-items',
                    pull: true,
                    put: () => true,
                },
                animation: 150,
                handle: '.fmm-drag-handle',
                ghostClass: 'fmm-ghost',
                chosenClass: 'fmm-chosen',
                dragClass: 'fmm-dragging',
                fallbackOnBody: true,
                swapThreshold: 0.65,

                onMove: (evt) => {
                    if (maxDepth === null) { return true; }

                    let destDepth = 0;
                    let p = evt.to;
                    while (p && !p.classList.contains('fmm-root-list')) {
                        if (p.classList.contains('fmm-nested-list')) { destDepth++; }
                        p = p.parentElement;
                    }

                    const getSubtreeDepth = (element) => {
                        let maxChildDepth = 0;
                        const nested = element.querySelector(':scope > .fmm-nested-list');
                        if (nested) {
                            nested.querySelectorAll(':scope > .fmm-item-row').forEach(function (child) {
                                const d = getSubtreeDepth(child) + 1;
                                if (d > maxChildDepth) { maxChildDepth = d; }
                            });
                        }
                        return maxChildDepth;
                    };

                    const subtreeDepth = getSubtreeDepth(evt.dragged);

                    if ((destDepth + subtreeDepth) > maxDepth) { return false; }

                    return true;
                },

                onEnd: () => {
                    const rootContainer =
                        document.getElementById('fmm-root-list') ||
                        el.closest('.fmm-root-list') ||
                        el;
                    const tree = extractTree(rootContainer);

                    if (wire) { wire.updateOrder(tree); }
                },
            });

            el._sortable = this.sortable;
        },

        destroy() {
            if (this.sortable) {
                this.sortable.destroy();
                this.sortable = null;
                this.$el._sortable = null;
            }
        },
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('menuSortable', menuSortable);
});

document.addEventListener('livewire:initialized', () => {
    Livewire.on('menu-saved', () => {
        const flash = document.getElementById('fmm-autosave-flash');
        if (flash) {
            flash.classList.add('fmm-flash-visible');
            setTimeout(() => flash.classList.remove('fmm-flash-visible'), 2000);
        }
    });
});
