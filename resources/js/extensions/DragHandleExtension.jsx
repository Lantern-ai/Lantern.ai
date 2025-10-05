import { Extension } from '@tiptap/core';
import { Plugin, PluginKey } from '@tiptap/pm/state';
import { Decoration, DecorationSet } from '@tiptap/pm/view';
import { ReactRenderer } from '@tiptap/react';
import BlockHandle from '../components/ScreenplayEditor/BlockHandle';

const DragHandlePluginKey = new PluginKey('dragHandle');

export const DragHandleExtension = Extension.create({
  name: 'dragHandle',

  addProseMirrorPlugins() {
    const editor = this.editor;
    let currentComponent = null;

    return [
      new Plugin({
        key: DragHandlePluginKey,
        state: {
          init() {
            return {
              decorations: DecorationSet.empty,
            };
          },
          apply(tr, state, oldState, newState) {
            const { doc, selection } = newState;
            const decorations = [];

            // Find the block containing the cursor
            const { $from } = selection;
            let depth = $from.depth;
            let blockPos = null;
            let blockNode = null;

            // Find the top-level block node
            while (depth > 0) {
              const node = $from.node(depth);
              if (node.type.isBlock && depth === 1) {
                blockPos = $from.before(depth);
                blockNode = node;
                break;
              }
              depth--;
            }

            // Only create decoration if we found a block
            if (blockPos !== null && blockNode) {
              // Cleanup previous component
              if (currentComponent) {
                try {
                  currentComponent.destroy();
                } catch (e) {
                  // Ignore cleanup errors
                }
                currentComponent = null;
              }

              // Create a widget decoration for the drag handle
              const widget = Decoration.widget(blockPos, (view) => {
                const handleWrapper = document.createElement('div');
                handleWrapper.className = 'block-handle-wrapper';
                handleWrapper.setAttribute('data-block-handle-wrapper', 'true');
                handleWrapper.contentEditable = 'false';

                // Defer ReactRenderer creation to avoid flushSync during render
                queueMicrotask(() => {
                  const component = new ReactRenderer(BlockHandle, {
                    props: {
                      editor,
                      node: blockNode,
                      pos: blockPos,
                    },
                    editor,
                  });

                  currentComponent = component;
                  handleWrapper.appendChild(component.element);
                });

                return handleWrapper;
              }, {
                side: -1,
                key: `drag-handle-${blockPos}`,
              });

              decorations.push(widget);
            }

            return {
              decorations: DecorationSet.create(doc, decorations),
            };
          },
        },
        props: {
          decorations(state) {
            return this.getState(state).decorations;
          },

          handleDOMEvents: {
            dragover: (view, event) => {
              event.preventDefault();
              event.dataTransfer.dropEffect = 'move';
              return false;
            },

            drop: (view, event) => {
              event.preventDefault();

              const posStr = event.dataTransfer.getData('application/x-tiptap-node-pos');
              if (!posStr) return false;

              const draggedPos = parseInt(posStr, 10);
              const dropPos = view.posAtCoords({ left: event.clientX, top: event.clientY });

              if (!dropPos || draggedPos === dropPos.pos) return false;

              const { state, dispatch } = view;
              const { tr, doc } = state;

              try {
                // Get the node being dragged
                const $draggedPos = doc.resolve(draggedPos);
                const draggedNode = $draggedPos.nodeAfter;

                if (!draggedNode) return false;

                const draggedNodeSize = draggedNode.nodeSize;
                let targetPos = dropPos.pos;

                // Find the target block position
                const $targetPos = doc.resolve(targetPos);
                let targetDepth = $targetPos.depth;

                while (targetDepth > 0) {
                  const node = $targetPos.node(targetDepth);
                  if (node.type.isBlock && targetDepth === 1) {
                    targetPos = $targetPos.before(targetDepth);
                    break;
                  }
                  targetDepth--;
                }

                // Don't move if same position
                if (draggedPos === targetPos) return false;

                // Adjust target position if dropping after the dragged node
                if (targetPos > draggedPos) {
                  targetPos -= draggedNodeSize;
                }

                // Delete the node from original position and insert at new position
                tr.delete(draggedPos, draggedPos + draggedNodeSize);
                tr.insert(targetPos, draggedNode);

                // Set selection to the moved node
                const newPos = targetPos + 1;
                if (newPos < tr.doc.content.size) {
                  tr.setSelection(state.selection.constructor.near(tr.doc.resolve(newPos)));
                }

                dispatch(tr.scrollIntoView());
                return true;
              } catch (error) {
                console.error('Error during drop:', error);
                return false;
              }
            },
          },
        },
      }),
    ];
  },
});
