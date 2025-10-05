import React, { useState, useRef, useEffect } from 'react';
import { GripVertical } from 'lucide-react';
import BlockTypeDropdown from './BlockTypeDropdown';

const BlockHandle = ({ editor, node, pos }) => {
  const [showDropdown, setShowDropdown] = useState(false);
  const [isDragging, setIsDragging] = useState(false);
  const handleRef = useRef(null);
  const dropdownRef = useRef(null);
  const dragStartPos = useRef(null);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (
        dropdownRef.current &&
        !dropdownRef.current.contains(event.target) &&
        handleRef.current &&
        !handleRef.current.contains(event.target)
      ) {
        setShowDropdown(false);
      }
    };

    if (showDropdown) {
      document.addEventListener('mousedown', handleClickOutside);
      return () => {
        document.removeEventListener('mousedown', handleClickOutside);
      };
    }
  }, [showDropdown]);

  const handleClick = (e) => {
    e.preventDefault();
    e.stopPropagation();
    setShowDropdown(!showDropdown);
  };

  const handleDragStart = (e) => {
    setIsDragging(true);
    dragStartPos.current = pos;

    // Set drag data
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', node.innerHTML || '');
    e.dataTransfer.setData('application/x-tiptap-node-pos', pos.toString());

    // Create a drag image
    const dragImage = e.currentTarget.closest('[data-block-handle-wrapper]');
    if (dragImage) {
      e.dataTransfer.setDragImage(dragImage, 0, 0);
    }
  };

  const handleDragEnd = () => {
    setIsDragging(false);
    dragStartPos.current = null;
  };

  const handleBlockTypeChange = (blockType) => {
    // Focus the node first
    editor.commands.focus(pos);
    editor.commands.setTextSelection(pos);

    // Change the node type
    editor.chain().focus().setNode(blockType).run();

    setShowDropdown(false);
  };

  return (
    <>
      <div
        ref={handleRef}
        className={`block-handle ${isDragging ? 'dragging' : ''}`}
        onClick={handleClick}
        draggable="true"
        onDragStart={handleDragStart}
        onDragEnd={handleDragEnd}
        title="Drag to move, click to change type"
      >
        <GripVertical size={18} />
      </div>

      {showDropdown && (
        <div ref={dropdownRef}>
          <BlockTypeDropdown
            editor={editor}
            onSelect={handleBlockTypeChange}
            onClose={() => setShowDropdown(false)}
            currentType={node?.type?.name}
          />
        </div>
      )}
    </>
  );
};

export default BlockHandle;
