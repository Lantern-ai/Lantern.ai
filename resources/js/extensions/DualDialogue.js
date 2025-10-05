import { Node, mergeAttributes } from '@tiptap/core';

export const DualDialogue = Node.create({
  name: 'dualDialogue',

  group: 'block',

  content: 'block+',

  parseHTML() {
    return [{ tag: 'div[data-type="dual-dialogue"]' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, {
      'data-type': 'dual-dialogue',
      class: 'screenplay-dual-dialogue'
    }), 0];
  },
});