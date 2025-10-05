import React, { useRef, useEffect } from 'react';
import { slashCommandItems } from '../../extensions/SlashCommands';

const PlusInserter = ({ editor, onClose }) => {
  const menuRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        onClose();
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [onClose]);

  const handleItemClick = (item) => {
    const { from, to } = editor.state.selection;
    item.command({ editor, range: { from, to } });
    onClose();
  };

  return (
    <div className="plus-inserter-overlay">
      <div className="plus-inserter-menu" ref={menuRef}>
        <div className="plus-inserter-header">
          <h3>Insert Screenplay Block</h3>
        </div>
        <div className="plus-inserter-items">
          {slashCommandItems.map((item, index) => (
            <button
              key={index}
              className="plus-inserter-item"
              onClick={() => handleItemClick(item)}
            >
              <div className="plus-inserter-item-content">
                <span className="plus-inserter-item-title">{item.title}</span>
                <span className="plus-inserter-item-description">{item.description}</span>
              </div>
            </button>
          ))}
        </div>
      </div>
    </div>
  );
};

export default PlusInserter;