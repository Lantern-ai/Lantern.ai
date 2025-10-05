import { Extension } from '@tiptap/core';

// Extension to handle paragraph behavior in screenplay context
export const ParagraphExtension = Extension.create({
  name: 'paragraphExtension',

  addKeyboardShortcuts() {
    return {
      Enter: ({ editor }) => {
        const { state } = editor;
        const { $from } = state.selection;

        // If in paragraph, create new paragraph
        if ($from.parent.type.name === 'paragraph') {
          return editor
            .chain()
            .insertContentAt($from.after(), { type: 'paragraph', content: [] })
            .focus($from.after() + 1)
            .run();
        }
        return false;
      },
    };
  },
});