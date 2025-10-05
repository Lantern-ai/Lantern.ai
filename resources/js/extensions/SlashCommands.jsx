import { Extension } from '@tiptap/core';
import { ReactRenderer } from '@tiptap/react';
import Suggestion from '@tiptap/suggestion';
import { PluginKey } from '@tiptap/pm/state';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import SlashCommandMenu from '../components/ScreenplayEditor/SlashCommandMenu';

export const SlashCommands = Extension.create({
  name: 'slashCommands',

  addOptions() {
    return {
      suggestion: {
        char: '/',
        pluginKey: new PluginKey('slashCommands'),
        command: ({ editor, range, props }) => {
          props.command({ editor, range });
        },
      },
    };
  },

  addProseMirrorPlugins() {
    return [
      Suggestion({
        editor: this.editor,
        ...this.options.suggestion,
      }),
    ];
  },
});

export const slashCommandItems = [
  {
    title: 'Scene Heading',
    description: 'INT./EXT. location - TIME',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('sceneHeading')
        .run();
    },
  },
  {
    title: 'Action',
    description: 'Description of action',
    command: ({ editor, range }) => {
      const { from, to } = range;
      const currentNode = editor.state.doc.nodeAt(from);

      if (currentNode && currentNode.type.name === 'paragraph') {
        editor
          .chain()
          .focus()
          .deleteRange(range)
          .setNode('action')
          .run();
      } else {
        editor
          .chain()
          .focus()
          .deleteRange(range)
          .setNode('action')
          .run();
      }
    },
  },
  {
    title: 'Character',
    description: 'Character name',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('character')
        .run();
    },
  },
  {
    title: 'Dialogue',
    description: 'Character speech',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('dialogue')
        .run();
    },
  },
  {
    title: 'Parenthetical',
    description: '(action or tone)',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('parenthetical')
        .run();
    },
  },
  {
    title: 'Transition',
    description: 'CUT TO:',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('transition')
        .run();
    },
  },
  {
    title: 'Shot',
    description: 'Camera shot',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('shot')
        .run();
    },
  },
  {
    title: 'Section/Act',
    description: 'Section or act heading',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('sectionAct')
        .run();
    },
  },
  {
    title: 'Note',
    description: 'Non-printing note',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('note')
        .run();
    },
  },
  {
    title: 'Montage/Intercut',
    description: 'Montage or intercut sequence',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('montage')
        .run();
    },
  },
  {
    title: 'Dual Dialogue',
    description: 'Two characters speaking simultaneously',
    command: ({ editor, range }) => {
      editor
        .chain()
        .focus()
        .deleteRange(range)
        .setNode('dualDialogue')
        .run();
    },
  },
];

export const slashCommandSuggestion = {
  items: ({ query }) => {
    return slashCommandItems.filter(item =>
      item.title.toLowerCase().includes(query.toLowerCase())
    );
  },

  render: () => {
    let component;
    let popup;

    return {
      onStart: props => {
        component = new ReactRenderer(SlashCommandMenu, {
          props,
          editor: props.editor,
        });

        popup = tippy('body', {
          getReferenceClientRect: props.clientRect,
          appendTo: () => document.body,
          content: component.element,
          showOnCreate: true,
          interactive: true,
          trigger: 'manual',
          placement: 'bottom-start',
        });
      },

      onUpdate(props) {
        component.updateProps(props);

        popup[0].setProps({
          getReferenceClientRect: props.clientRect,
        });
      },

      onKeyDown(props) {
        if (props.event.key === 'Escape') {
          popup[0].hide();
          return true;
        }

        return component.ref?.onKeyDown(props);
      },

      onExit() {
        popup[0].destroy();
        component.destroy();
      },
    };
  },
};