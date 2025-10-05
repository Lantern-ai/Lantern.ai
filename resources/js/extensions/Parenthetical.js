import { Node, mergeAttributes } from '@tiptap/core';

export const Parenthetical = Node.create({
  name: 'parenthetical',

  group: 'block',

  content: 'inline*',

  parseHTML() {
    return [{ tag: 'div[data-type="parenthetical"]' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, {
      'data-type': 'parenthetical',
      class: 'screenplay-parenthetical'
    }), 0];
  },

  addKeyboardShortcuts() {
    return {
      Enter: ({ editor }) => {
        const { state } = editor;
        const { $from } = state.selection;

        if ($from.parent.type.name === 'parenthetical') {
          return editor
            .chain()
            .insertContentAt($from.after(), { type: 'dialogue', content: [] })
            .focus($from.after() + 1)
            .run();
        }
        return false;
      },
    };
  },
});