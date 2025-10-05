import React from 'react';
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
  Type,
} from 'lucide-react';

const blockTypes = [
  {
    name: 'paragraph',
    title: 'Paragraph',
    description: 'Regular text block',
    icon: Type,
  },
  {
    name: 'sceneHeading',
    title: 'Scene Heading',
    description: 'INT./EXT. LOCATION - TIME',
    icon: Heading1,
  },
  {
    name: 'action',
    title: 'Action',
    description: 'Description of action',
    icon: AlignLeft,
  },
  {
    name: 'character',
    title: 'Character',
    description: 'Character name',
    icon: User,
  },
  {
    name: 'dialogue',
    title: 'Dialogue',
    description: 'Character speech',
    icon: MessageSquare,
  },
  {
    name: 'parenthetical',
    title: 'Parenthetical',
    description: '(action or tone)',
    icon: FileText,
  },
  {
    name: 'transition',
    title: 'Transition',
    description: 'CUT TO:',
    icon: ArrowRight,
  },
  {
    name: 'shot',
    title: 'Shot',
    description: 'Camera shot',
    icon: Camera,
  },
  {
    name: 'sectionAct',
    title: 'Section/Act',
    description: 'Section or act heading',
    icon: Hash,
  },
  {
    name: 'note',
    title: 'Note',
    description: 'Non-printing note',
    icon: StickyNote,
  },
  {
    name: 'montage',
    title: 'Montage/Intercut',
    description: 'Montage or intercut sequence',
    icon: Film,
  },
  {
    name: 'dualDialogue',
    title: 'Dual Dialogue',
    description: 'Two characters speaking simultaneously',
    icon: Users,
  },
];

const BlockTypeDropdown = ({ editor, onSelect, onClose, currentType }) => {
  const handleSelect = (blockType) => {
    onSelect(blockType);
  };

  return (
    <div className="block-type-dropdown">
      <div className="block-type-dropdown-header">
        <span>Turn into</span>
      </div>
      <div className="block-type-dropdown-items">
        {blockTypes.map((type) => {
          const Icon = type.icon;
          const isActive = currentType === type.name;

          return (
            <button
              key={type.name}
              className={`block-type-dropdown-item ${isActive ? 'active' : ''}`}
              onClick={() => handleSelect(type.name)}
            >
              <div className="block-type-dropdown-icon">
                <Icon size={16} />
              </div>
              <div className="block-type-dropdown-content">
                <div className="block-type-dropdown-title">{type.title}</div>
                <div className="block-type-dropdown-description">{type.description}</div>
              </div>
              {isActive && (
                <div className="block-type-dropdown-check">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path
                      d="M13.5 4L6 11.5L2.5 8"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                  </svg>
                </div>
              )}
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default BlockTypeDropdown;
