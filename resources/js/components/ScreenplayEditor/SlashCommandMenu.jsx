import React, { useState, useEffect, forwardRef, useImperativeHandle } from 'react';
import {
  Heading1,
  AlignLeft,
  User,
  MessageSquare,
  FileText,
  ArrowRight,
  Camera,
  Hash,
  StickyNote,
  Film,
  Users,
} from 'lucide-react';

const iconMap = {
  'Scene Heading': Heading1,
  'Action': AlignLeft,
  'Character': User,
  'Dialogue': MessageSquare,
  'Parenthetical': FileText,
  'Transition': ArrowRight,
  'Shot': Camera,
  'Section/Act': Hash,
  'Note': StickyNote,
  'Montage/Intercut': Film,
  'Dual Dialogue': Users,
};

const SlashCommandMenu = forwardRef((props, ref) => {
  const [selectedIndex, setSelectedIndex] = useState(0);

  const selectItem = index => {
    const item = props.items[index];
    if (item) {
      props.command(item);
    }
  };

  const upHandler = () => {
    setSelectedIndex((selectedIndex + props.items.length - 1) % props.items.length);
  };

  const downHandler = () => {
    setSelectedIndex((selectedIndex + 1) % props.items.length);
  };

  const enterHandler = () => {
    selectItem(selectedIndex);
  };

  useEffect(() => setSelectedIndex(0), [props.items]);

  useImperativeHandle(ref, () => ({
    onKeyDown: ({ event }) => {
      if (event.key === 'ArrowUp') {
        upHandler();
        return true;
      }

      if (event.key === 'ArrowDown') {
        downHandler();
        return true;
      }

      if (event.key === 'Enter') {
        enterHandler();
        return true;
      }

      return false;
    },
  }));

  return (
    <div className="notion-slash-menu">
      {props.items.length > 0 ? (
        props.items.map((item, index) => {
          const Icon = iconMap[item.title] || AlignLeft;
          return (
            <button
              key={index}
              className={`notion-menu-item ${
                index === selectedIndex ? 'notion-menu-item-selected' : ''
              }`}
              onClick={() => selectItem(index)}
              onMouseEnter={() => setSelectedIndex(index)}
            >
              <div className="notion-menu-icon">
                <Icon size={16} />
              </div>
              <div className="notion-menu-content">
                <div className="notion-menu-title">{item.title}</div>
                <div className="notion-menu-description">{item.description}</div>
              </div>
            </button>
          );
        })
      ) : (
        <div className="notion-menu-empty">No results</div>
      )}
    </div>
  );
});

SlashCommandMenu.displayName = 'SlashCommandMenu';

export default SlashCommandMenu;