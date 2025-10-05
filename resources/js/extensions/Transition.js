import { Node, mergeAttributes } from '@tiptap/core';

export const Transition = Node.create({
  name: 'transition',

  group: 'block',

  content: 'inline*',

  parseHTML() {
    return [{ tag: 'div[data-type="transition"]' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, {
      'data-type': 'transition',
      class: 'screenplay-transition'
    }), 0];
  },

  addKeyboardShortcuts() {
    return {
      Enter: ({ editor }) => {
        const { state } = editor;
        const { $from } = state.selection;

        if ($from.parent.type.name === 'transition') {
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