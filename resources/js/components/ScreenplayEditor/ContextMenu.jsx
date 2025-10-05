import React, { useEffect, useRef } from 'react';
import { Sparkles } from 'lucide-react';

const ContextMenu = ({ position, onAskAI, onClose }) => {
  const menuRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        onClose();
      }
    };

    const handleEscape = (event) => {
      if (event.key === 'Escape') {
        onClose();
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    document.addEventListener('keydown', handleEscape);

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
      document.removeEventListener('keydown', handleEscape);
    };
  }, [onClose]);

  return (
    <div
      ref={menuRef}
      className="editor-context-menu"
      style={{
        position: 'fixed',
        top: `${position.y}px`,
        left: `${position.x}px`,
      }}
    >
      <button
        className="context-menu-item"
        onClick={onAskAI}
      >
        <Sparkles size={16} />
        <span>Ask AI</span>
      </button>
    </div>
  );
};

export default ContextMenu;
