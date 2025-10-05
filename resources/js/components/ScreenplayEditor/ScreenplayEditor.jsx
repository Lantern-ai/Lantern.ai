import React, { useState, useEffect, useRef } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import { Underline } from '@tiptap/extension-underline';
import { TextStyle } from '@tiptap/extension-text-style';
import { FontFamily } from '@tiptap/extension-font-family';
import { TextAlign } from '@tiptap/extension-text-align';
import { Placeholder } from '@tiptap/extension-placeholder';

import { SceneHeading } from '../../extensions/SceneHeading';
import { Action } from '../../extensions/Action';
import { Character } from '../../extensions/Character';
import { Dialogue } from '../../extensions/Dialogue';
import { Parenthetical } from '../../extensions/Parenthetical';
import { Transition } from '../../extensions/Transition';
import { Shot } from '../../extensions/Shot';
import { SectionAct } from '../../extensions/SectionAct';
import { Note } from '../../extensions/Note';
import { Montage } from '../../extensions/Montage';
import { DualDialogue } from '../../extensions/DualDialogue';
import { SlashCommands, slashCommandSuggestion } from '../../extensions/SlashCommands';
import { SmartDetection } from '../../extensions/SmartDetection';
import { ParagraphExtension } from '../../extensions/ParagraphExtension';
import { DragHandleExtension } from '../../extensions/DragHandleExtension';

import MenuBar from './MenuBar';
import PlusInserter from './PlusInserter';
import ChatSidebar from './ChatSidebar';
import LeftSidebar from './LeftSidebar';
import ContextMenu from './ContextMenu';
import { exportToFountain, importFromFountain } from '../../utils/fountainExport';
import { exportToPDF } from '../../utils/pdfExport';

import './screenplay.css';

const ScreenplayEditor = ({content}) => {
  const [language, setLanguage] = useState('english');
  const [previewMode, setPreviewMode] = useState(false);
  const [showSceneNumbers, setShowSceneNumbers] = useState(false);
  const [showPlusMenu, setShowPlusMenu] = useState(false);
  const [contextMenu, setContextMenu] = useState(null);
  const [selectedText, setSelectedText] = useState(null);

  // Initialize sidebar state based on screen size
  const getInitialSidebarState = () => {
    if (typeof window !== 'undefined') {
      return window.innerWidth >= 1200;
    }
    return true; // Default to open for SSR
  };

  const [showChatSidebar, setShowChatSidebar] = useState(getInitialSidebarState);
  const [showLeftSidebar, setShowLeftSidebar] = useState(false);
  const fileInputRef = useRef(null);
  const chatSidebarRef = useRef(null);

  const editor = useEditor({
    extensions: [
      StarterKit.configure({
        heading: false,
        strike: false,
        history: {
          depth: 100,
          newGroupDelay: 500,
        },
      }),
      Underline,
      TextStyle,
      FontFamily,
      TextAlign.configure({
        types: ['paragraph', 'sceneHeading', 'action', 'character', 'dialogue', 'parenthetical', 'transition', 'shot', 'sectionAct', 'note', 'montage'],
      }),
      Placeholder.configure({
        placeholder: ({ node }) => {
          const placeholders = {
            sceneHeading: 'INT./EXT. LOCATION - TIME',
            action: 'Describe the action...',
            character: 'CHARACTER NAME',
            dialogue: 'Character dialogue...',
            parenthetical: '(action or tone)',
            transition: 'CUT TO:',
            shot: 'Camera shot description',
            sectionAct: 'Section or Act heading',
            note: 'Note (non-printing)',
            montage: 'Montage/Intercut description',
            paragraph: 'Type / for commands...',
          };
          return placeholders[node.type.name] || 'Type / for commands...';
        },
      }),
      SceneHeading,
      Action,
      Character,
      Dialogue,
      Parenthetical,
      Transition,
      Shot,
      SectionAct,
      Note,
      Montage,
      DualDialogue,
      SlashCommands.configure({
        suggestion: slashCommandSuggestion,
      }),
      SmartDetection,
      ParagraphExtension,
      DragHandleExtension,
    ],
    content: '<p></p>',
    editorProps: {
      attributes: {
        class: 'tiptap-editor',
      },
    },
  });

  // Load content from props
  useEffect(() => {
    if (editor && content?.content) {
      console.log('Loading content:', content);
      editor.commands.setContent(content.content);
    }
  }, [editor, content]);

  useEffect(() => {
    if (editor) {
      const editorElement = editor.view.dom;
      if (editorElement) {
        editorElement.setAttribute('data-language', language);
        editorElement.setAttribute('data-preview', previewMode);
        editorElement.setAttribute('data-scene-numbers', showSceneNumbers);
      }
    }
  }, [language, previewMode, showSceneNumbers, editor]);

  // Handle responsive sidebar on window resize
  useEffect(() => {
    const handleResize = () => {
      const shouldShowSidebar = window.innerWidth >= 1200;
      // Only auto-close on resize to smaller screen, don't auto-open
      if (!shouldShowSidebar && showChatSidebar) {
        setShowChatSidebar(false);
      }
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, [showChatSidebar]);

  const handleExport = () => {
    const fountain = exportToFountain(editor);
    const blob = new Blob([fountain], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'screenplay.fountain';
    a.click();
    URL.revokeObjectURL(url);
  };

  const handleImport = () => {
    fileInputRef.current?.click();
  };

  const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
      const content = e.target.result;
      const json = importFromFountain(content);
      editor.commands.setContent(json);
    };
    reader.readAsText(file);
  };

  const handleExportPDF = () => {
    exportToPDF();
  };

  const handlePlusClick = () => {
    setShowPlusMenu(!showPlusMenu);
  };

  const handleContextMenu = (event) => {
    event.preventDefault();

    if (!editor) return;

    // Check if there's selected text
    const selection = window.getSelection();
    const selectedContent = selection.toString().trim();

    if (selectedContent) {
      // Get the current block node that contains the selection
      const { state } = editor;
      const { $from } = state.selection;
      const blockNode = $from.node($from.depth);
      const blockPos = $from.before($from.depth);

      // Get the entire block content as text
      const blockText = blockNode.textContent;
      const blockType = blockNode.type.name;
      const blockId = blockNode.attrs.id;

      setSelectedText({
        selectedText: selectedContent, // The text user actually selected
        blockText: blockText,          // The entire block content
        blockType: blockType,          // Type of block (action, dialogue, etc.)
        blockPos: blockPos,            // Position of block in document
        blockId: blockId,              // Block ID if available
      });

      setContextMenu({
        x: event.clientX,
        y: event.clientY,
      });
    }
  };

  const handleAskAI = () => {
    // Open chat sidebar and pass selected text
    setShowChatSidebar(true);

    // Send selected text to chat sidebar
    if (chatSidebarRef.current && selectedText) {
      chatSidebarRef.current.setSelectedText(selectedText);
    }

    // Close context menu
    setContextMenu(null);
  };

  const handleCloseContextMenu = () => {
    setContextMenu(null);
  };

  const handleGenerateMindMap = () => {
    // TODO: Implement mind map generation
    console.log('Generate Mind Map clicked');
    alert('Mind map generation coming soon!');
  };

  const handleGetAnalysis = () => {
    // TODO: Implement script analysis
      //
      let script_id = content.id;
      window.open('/analyse-script/'+script_id, '_blank', 'noopener,noreferrer');
      console.log("text");
      console.log(script_id);
    console.log('Get Script Analysis clicked');
    alert('Script analysis coming soon!');
  };
  const handleSaveContent = async () => {
      console.log("dfghfhi");
      console.log(editor.getJSON());

      const bodyJSON = editor.getJSON(); // avoid calling twice
      console.log('Saving…', bodyJSON);
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      console.log("content id", content.id);
      console.log("content",content);
      try {
          const res = await fetch('/editor/save', {

              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
              },
              body: JSON.stringify({
                  id: content.id,      // <-- make sure `content` exists or replace with your own id
                  body: bodyJSON,
              }),
              // credentials: 'include', // uncomment if you rely on cookies/CSRF
          });

          if (!res.ok) {
              // Try to get a useful error message
              const errText = await res.text().catch(() => '');
              throw new Error(errText || `Save failed with ${res.status}`);
          }

          // If your API returns JSON:
          // const data = await res.json();
          // console.log('Saved!', data);

          console.log('Saved!');
      } catch (err) {
          console.error('Save error:', err);
      }
  }

  // Calculate wrapper classes
  const getWrapperClass = () => {
    const classes = ['editor-wrapper'];
    if (showChatSidebar) {
      classes.push('with-right-sidebar');
    }
    return classes.join(' ');
  };

  return (
    <div className="editor-container">
      <div className="editor-layout">
        <LeftSidebar
          isVisible={showLeftSidebar}
          content={content}
          onClose={() => setShowLeftSidebar(false)}
          onGenerateMindMap={handleGenerateMindMap}
          onGetAnalysis={handleGetAnalysis}
        />

        <div className={getWrapperClass()}>
          <MenuBar
            editor={editor}
            language={language}
            setLanguage={setLanguage}
            previewMode={previewMode}
            setPreviewMode={setPreviewMode}
            showSceneNumbers={showSceneNumbers}
            setShowSceneNumbers={setShowSceneNumbers}
            onImport={handleImport}
            onExport={handleExport}
            onExportPDF={handleExportPDF}
            saveContent={handleSaveContent}
            onPlusClick={handlePlusClick}
            showChatSidebar={showChatSidebar}
            setShowChatSidebar={setShowChatSidebar}
            showLeftSidebar={showLeftSidebar}
            setShowLeftSidebar={setShowLeftSidebar}
          />

          <div className="editor-main-content" onContextMenu={handleContextMenu}>
            <div className={`editor-content ${language === 'english' ? 'editor-letter' : 'editor-a4'}`}>
              <EditorContent editor={editor} />
            </div>
          </div>

          {showPlusMenu && (
            <PlusInserter
              editor={editor}
              onClose={() => setShowPlusMenu(false)}
            />
          )}

          {contextMenu && (
            <ContextMenu
              position={contextMenu}
              onAskAI={handleAskAI}
              onClose={handleCloseContextMenu}
            />
          )}

          <input
            ref={fileInputRef}
            type="file"
            accept=".fountain,.txt"
            onChange={handleFileChange}
            style={{ display: 'none' }}
          />
        </div>

        <ChatSidebar
          ref={chatSidebarRef}
          editor={editor}
          content={content}
          isVisible={showChatSidebar}
          onClose={() => setShowChatSidebar(false)}
        />
      </div>
    </div>
  );
};

export default ScreenplayEditor;
