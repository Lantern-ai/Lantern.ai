import { Node, mergeAttributes } from '@tiptap/core';

export const Note = Node.create({
  name: 'note',

  group: 'block',

  content: 'inline*',

  parseHTML() {
    return [{ tag: 'div[data-type="note"]' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, {
      'data-type': 'note',
      class: 'screenplay-note'
    }), 0];
  },

  addKeyboardShortcuts() {
    return {
      Enter: ({ editor }) => {
        const { state } = editor;
        const { $from } = state.selection;

        if ($from.parent.type.name === 'note') {
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