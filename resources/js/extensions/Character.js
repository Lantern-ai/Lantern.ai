import { Node, mergeAttributes } from '@tiptap/core';

// small uuid v4 generator
const genId = () => {
    if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
        const a = crypto.getRandomValues(new Uint8Array(16));
        a[6] = (a[6] & 0x0f) | 0x40;
        a[8] = (a[8] & 0x3f) | 0x80;
        const b = Array.from(a, x => x.toString(16).padStart(2, '0'));
        return `${b.slice(0,4).join('')}-${b.slice(4,6).join('')}-${b.slice(6,8).join('')}-${b.slice(8,10).join('')}-${b.slice(10).join('')}`;
    }
    return 'id-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
};

export const Character = Node.create({
    name: 'character',

    group: 'block',
    content: 'inline*',
    defining: true,

    // add a stable id attribute persisted as data-id
    addAttributes() {
        return {
            id: {
                default: null,
                parseHTML: el => el.getAttribute('data-id') || null,
                renderHTML: attrs => (attrs.id ? { 'data-id': attrs.id } : {}),
            },
        };
    },

    parseHTML() {
        return [{ tag: 'div[data-type="character"]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            mergeAttributes(HTMLAttributes, {
                'data-type': 'character',
                class: 'screenplay-character',
            }),
            0,
        ];
    },

    addKeyboardShortcuts() {
        return {
            Enter: ({ editor }) => {
                const { state } = editor;
                const { $from } = state.selection;

                if ($from.parent.type.name === 'character') {
                    return editor
                        .chain()
                        .insertContentAt($from.after(), {
                            type: 'dialogue',
                            attrs: { id: genId() }, // give the new dialogue a stable id too
                            content: [],
                        })
                        .focus($from.after() + 1)
                        .run();
                }
                return false;
            },
        };
    },
});
