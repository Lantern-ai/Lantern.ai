import React from 'react';
import {
  Bold,
  Italic,
  Underline,
  Plus,
  Eye,
  EyeOff,
  FileDown,
  FileUp,
  Hash,
  Languages,
  Undo,
  Redo,
  MessageSquare,
  PanelLeftClose,
  PanelLeftOpen,
} from 'lucide-react';

const MenuBar = ({
  editor,
  language,
  setLanguage,
  previewMode,
  setPreviewMode,
  showSceneNumbers,
  setShowSceneNumbers,
  onImport,
  onExport,
  onExportPDF,
  saveContent,
  onPlusClick,
  showChatSidebar,
  setShowChatSidebar,
  showLeftSidebar,
  setShowLeftSidebar,
}) => {
  if (!editor) {
    return null;
  }

  return (
    <div className="menu-bar">
      <div className="menu-bar-section">
        <button
          onClick={() => setShowLeftSidebar(!showLeftSidebar)}
          className={`menu-bar-button ${showLeftSidebar ? 'is-active' : ''}`}
          title={showLeftSidebar ? "Hide tools sidebar" : "Show tools sidebar"}
        >
          {showLeftSidebar ? <PanelLeftClose size={18} /> : <PanelLeftOpen size={18} />}
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={onPlusClick}
          className="menu-bar-button"
          title="Insert block"
        >
          <Plus size={18} />
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={() => editor.chain().focus().toggleBold().run()}
          className={`menu-bar-button ${editor.isActive('bold') ? 'is-active' : ''}`}
          title="Bold (Ctrl+B)"
        >
          <Bold size={18} />
        </button>

        <button
          onClick={() => editor.chain().focus().toggleItalic().run()}
          className={`menu-bar-button ${editor.isActive('italic') ? 'is-active' : ''}`}
          title="Italic (Ctrl+I)"
        >
          <Italic size={18} />
        </button>

        <button
          onClick={() => editor.chain().focus().toggleUnderline().run()}
          className={`menu-bar-button ${editor.isActive('underline') ? 'is-active' : ''}`}
          title="Underline (Ctrl+U)"
        >
          <Underline size={18} />
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={() => editor.chain().focus().undo().run()}
          disabled={!editor.can().undo()}
          className="menu-bar-button"
          title="Undo (Ctrl+Z)"
        >
          <Undo size={18} />
        </button>

        <button
          onClick={() => editor.chain().focus().redo().run()}
          disabled={!editor.can().redo()}
          className="menu-bar-button"
          title="Redo (Ctrl+Shift+Z)"
        >
          <Redo size={18} />
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={() => setLanguage(language === 'english' ? 'malayalam' : 'english')}
          className="menu-bar-button menu-bar-button-language"
          title="Toggle language"
        >
          <Languages size={18} />
          <span>{language === 'english' ? 'English' : 'മലയാളം'}</span>
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={() => setShowSceneNumbers(!showSceneNumbers)}
          className={`menu-bar-button ${showSceneNumbers ? 'is-active' : ''}`}
          title="Toggle scene numbers"
        >
          <Hash size={18} />
        </button>

        <button
          onClick={() => setPreviewMode(!previewMode)}
          className={`menu-bar-button ${previewMode ? 'is-active' : ''}`}
          title="Toggle preview mode"
        >
          {previewMode ? <EyeOff size={18} /> : <Eye size={18} />}
        </button>

        <button
          onClick={() => setShowChatSidebar(!showChatSidebar)}
          className={`menu-bar-button ${showChatSidebar ? 'is-active' : ''}`}
          title="Toggle AI Assistant"
        >
          <MessageSquare size={18} />
        </button>
      </div>

      <div className="menu-bar-divider" />

      <div className="menu-bar-section">
        <button
          onClick={onImport}
          className="menu-bar-button"
          title="Import Fountain"
        >
          <FileUp size={18} />
        </button>

        <button
          onClick={onExport}
          className="menu-bar-button"
          title="Export Fountain"
        >
          <FileDown size={18} />
        </button>

        <button
          onClick={onExportPDF}
          className="menu-bar-button menu-bar-button-primary"
          title="Export PDF"
        >
          PDF
        </button>
          <button
              onClick={saveContent}
              className="menu-bar-button menu-bar-button-primary"
              title="Save"
          >
              Save
          </button>

      </div>
    </div>
  );
};

export default MenuBar;
