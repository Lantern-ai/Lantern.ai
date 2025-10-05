import { Node, mergeAttributes } from '@tiptap/core';

export const Montage = Node.create({
  name: 'montage',

  group: 'block',

  content: 'inline*',

  parseHTML() {
    return [{ tag: 'div[data-type="montage"]' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, {
      'data-type': 'montage',
      class: 'screenplay-montage'
    }), 0];
  },

  addKeyboardShortcuts() {
    return {
      Enter: ({ editor }) => {
        const { state } = editor;
        const { $from } = state.selection;

        if ($from.parent.type.name === 'montage') {
          return editor
            .chain()
            .insertContentAt($from.after(), { type: 'action', content: [] })
            .focus($from.after() + 1)
            .run();
        }
        return false;
      },
    };
  },
});