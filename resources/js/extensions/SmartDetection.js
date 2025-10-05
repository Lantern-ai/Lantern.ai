import { Extension } from '@tiptap/core';
import { Plugin, PluginKey } from '@tiptap/pm/state';

export const SmartDetection = Extension.create({
  name: 'smartDetection',

  addProseMirrorPlugins() {
    return [
      new Plugin({
        key: new PluginKey('smartDetection'),
        appendTransaction: (transactions, oldState, newState) => {
          // Only process if there's actual content change
          const docChanges = transactions.some(tr => tr.docChanged);
          if (!docChanges) return null;

          const { doc, selection } = newState;
          const { $from } = selection;
          const node = $from.node();
          const nodeText = node.textContent.trim();

          let tr = null;

          // Check if we should auto-convert based on text patterns

          // INT./EXT. detection for Scene Heading
          if (
            (nodeText.startsWith('INT.') ||
             nodeText.startsWith('EXT.') ||
             nodeText.startsWith('INT/EXT') ||
             nodeText.startsWith('I/E')) &&
            node.type.name !== 'sceneHeading' &&
            (node.type.name === 'paragraph' || node.type.name === 'action')
          ) {
            tr = newState.tr.setNodeMarkup($from.before(),
              newState.schema.nodes.sceneHeading
            );
          }

          // Lines ending with TO: become Transition
          if (
            nodeText.match(/TO:\s*$/) &&
            node.type.name !== 'transition' &&
            (node.type.name === 'paragraph' || node.type.name === 'action')
          ) {
            tr = newState.tr.setNodeMarkup($from.before(),
              newState.schema.nodes.transition
            );
          }

          // Lines starting with ( become Parenthetical (if under character/dialogue context)
          if (
            nodeText.startsWith('(') &&
            nodeText.endsWith(')') &&
            node.type.name !== 'parenthetical'
          ) {
            // Check if previous node is character or dialogue
            const prevNode = $from.node(-1);
            if (prevNode && (prevNode.type.name === 'character' || prevNode.type.name === 'dialogue')) {
              tr = newState.tr.setNodeMarkup($from.before(),
                newState.schema.nodes.parenthetical
              );
            }
          }

          // UPPERCASE lines before dialogue could be Character
          if (
            nodeText === nodeText.toUpperCase() &&
            nodeText.length > 0 &&
            nodeText.length < 50 && // Reasonable character name length
            !nodeText.includes('.') &&
            node.type.name !== 'character' &&
            node.type.name !== 'sceneHeading' &&
            (node.type.name === 'paragraph' || node.type.name === 'action')
          ) {
            // Look ahead to see if next line exists and could be dialogue
            // This is a simple heuristic
            const nextPos = $from.after();
            const nextNode = doc.nodeAt(nextPos);
            if (nextNode && nextNode.textContent.length > 0) {
              tr = newState.tr.setNodeMarkup($from.before(),
                newState.schema.nodes.character
              );
            }
          }

          return tr;
        },
      }),
    ];
  },
});