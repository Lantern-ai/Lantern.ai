import React, { useState, useRef, useEffect, forwardRef, useImperativeHandle } from 'react';
import { Send, Bot, User, Sparkles, Edit3, X, FileText } from 'lucide-react';

const ChatSidebar = forwardRef(({ editor, content, isVisible, onClose }, ref) => {
  const [messages, setMessages] = useState([]);
  const [inputValue, setInputValue] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [mode, setMode] = useState('ask'); // 'ask' or 'edit'
  const [selectedText, setSelectedTextState] = useState(null);
  const messagesEndRef = useRef(null);
  const inputRef = useRef(null);

  // Expose methods to parent component
  useImperativeHandle(ref, () => ({
    setSelectedText: (textData) => {
      setSelectedTextState(textData);
      setMode('ask'); // Switch to ask mode when text is selected
    },
  }));

  // Helper sleep function for typewriter effect
  const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

  // Pull usable text out of any AI response shape
  const extractAIText = (resp) => {
    if (!resp) return '';
    if (typeof resp === 'string') return resp;
    return resp.output ?? resp.text ?? resp.answer ?? resp.message ?? '';
  };

// Make sure text fits `inline*` blocks: strip HTML, collapse newlines
    const normalizeInlineText = (value) => {
        let s = extractAIText(value);
        console.log('Extracted AI Text:', s);
        console.log('Extracted AI Text  value:', value);

        if (!s) return '';
        // convert <br> to \n then strip tags
        s = s.replace(/<br\s*\/?>/gi, '\n').replace(/<\/?[^>]+>/g, '');
        // inline blocks can't contain block breaks → replace newlines with spaces
        s = s.replace(/\r?\n|\r/g, ' ').replace(/\s+/g, ' ').trim();
        return s;
    };

    const replaceBlockInline = (blockData, text) => {
        if (!editor || !blockData) return false;

        const { state } = editor;
        const pos = blockData.blockPos;
        if (typeof pos !== 'number') return false;

        const $pos = state.doc.resolve(pos);
        const blockNode = $pos.nodeAfter || state.doc.nodeAt(pos);
        if (!blockNode) return false;

        const from = pos + 1;
        const to   = pos + blockNode.nodeSize - 1;

        const safe = normalizeInlineText(text);
        if (!safe) return false;

        return editor
            .chain()
            .focus()
            .insertContentAt({ from, to }, safe) // replace inner content in one validated op
            .run();
    };
  // Function to replace block content with AI-generated content
    const typewriterReplaceBlock = async (blockData, text, msPerChar = 16) => {
        if (!editor || !blockData) {
          console.error('typewriterReplaceBlock: Missing editor or blockData');
          return;
        }

        const { state } = editor;
        const pos = blockData.blockPos;

        // Resolve position to find the block
        const $pos = state.doc.resolve(pos);
        const blockNode = $pos.nodeAfter || state.doc.nodeAt(pos);

        if (!blockNode) {
          console.error('typewriterReplaceBlock: Block node not found at position', pos);
          return;
        }

        const originalBlockType = blockNode.type.name;
        const blockAttrs = { ...blockNode.attrs };
        const blockEnd = pos + blockNode.nodeSize;

        console.log('typewriterReplaceBlock - Original block:', {
          pos,
          blockEnd,
          blockSize: blockNode.nodeSize,
          blockType: originalBlockType,
          blockAttrs
        });

        const safe = normalizeInlineText(text);
        console.log('Normalized text:', safe);

        if (!safe) {
          console.error('typewriterReplaceBlock: No safe text after normalization');
          return;
        }

        // Find the position of the block BEFORE the target block (if it exists)
        let insertAfterPos = null;

        // Navigate up to find the block before this one
        if (pos > 0) {
          const $before = state.doc.resolve(pos - 1);
          // Get the depth where blocks live (usually depth 1 for top-level blocks)
          const depth = $pos.depth;
          const indexInParent = $pos.index(depth - 1);

          if (indexInParent > 0) {
            // There's a block before this one
            const beforeBlock = $pos.node(depth - 1).child(indexInParent - 1);
            insertAfterPos = $pos.before(depth) - beforeBlock.nodeSize;
            console.log('Found previous block at position:', insertAfterPos);
          }
        }

        // 1) Delete the old block
        console.log('Deleting block from', pos, 'to', blockEnd);
        editor
          .chain()
          .focus()
          .deleteRange({ from: pos, to: blockEnd })
          .run();

        // 2) Determine where to insert the new block
        let insertPos;
        if (insertAfterPos !== null) {
          // Insert after the previous block
          const afterState = editor.state;
          const $after = afterState.doc.resolve(insertAfterPos);
          const prevBlock = $after.nodeAfter || afterState.doc.nodeAt(insertAfterPos);
          insertPos = insertAfterPos + (prevBlock ? prevBlock.nodeSize : 0);
          console.log('Inserting after previous block at position:', insertPos);
        } else {
          // No previous block, insert at the beginning
          insertPos = pos;
          console.log('Inserting at beginning, position:', insertPos);
        }

        // 3) Create and insert a new block with a space as placeholder
        const newBlockContent = {
          type: originalBlockType,
          attrs: blockAttrs,
          content: [{
            type: 'text',
            text: ' ' // Space as placeholder to avoid empty text node error
          }]
        };

        console.log('Block attrs being used:', blockAttrs);
        console.log('Inserting new block:', newBlockContent);

        const insertResult = editor
          .chain()
          .focus()
          .insertContentAt(insertPos, newBlockContent)
          .run();

        console.log('Insert result:', insertResult);

        // 4) Find the newly inserted block
        const finalState = editor.state;
        const $final = finalState.doc.resolve(insertPos);
        const newBlock = $final.nodeAfter || finalState.doc.nodeAt(insertPos);

        if (!newBlock) {
          console.error('Failed to find newly inserted block');
          return;
        }

        console.log('Newly inserted block:', {
          type: newBlock.type.name,
          attrs: newBlock.attrs,
          expectedType: originalBlockType
        });

        // 5) Clear the placeholder space and type character by character
        const blockStart = insertPos;
        const blockContentStart = blockStart + 1;
        const blockContentEnd = blockStart + newBlock.nodeSize - 1;

        // Delete the placeholder space
        editor.chain().deleteRange({ from: blockContentStart, to: blockContentEnd }).run();

        // Set cursor at the beginning of the block content
        editor.chain().setTextSelection(blockContentStart).run();

        // 6) Type character by character with typewriter effect
        for (const ch of safe) {
            editor.chain().insertContent(ch).run();
            await sleep(msPerChar);
        }

        console.log('Typewriter effect completed');
    };
  const editBlock = async (blockData, newContent) => {
    if (!editor || !blockData) {
      console.error('Missing editor or blockData:', { editor: !!editor, blockData });
      return;
    }

    try {
      const { blockPos, blockType, blockId } = blockData;
      console.log('Block Data:', blockData);
      console.log('New Content:', newContent);

      // Find the block by position and replace its content
      const { state } = editor;
      const blockNode = state.doc.nodeAt(blockPos);

      if (!blockNode) {
        console.error('Block not found at position:', blockPos);
        return;
      }

      console.log('Found block node:', {
        type: blockNode.type.name,
        size: blockNode.nodeSize,
        content: blockNode.textContent
      });

      // Replace the block content while preserving the block type and attributes
      await typewriterReplaceBlock(blockData, newContent);

      console.log('Block updated successfully');
    } catch (error) {
      console.error('Error updating block:', error);
    }
  };

  // Auto-scroll to bottom when new messages arrive
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  // Auto-resize textarea
  useEffect(() => {
    const textarea = inputRef.current;
    if (textarea) {
      textarea.style.height = 'auto';
      textarea.style.height = `${Math.min(textarea.scrollHeight, 200)}px`;
    }
  }, [inputValue]);

  /**
   * BACKEND COMMUNICATION FUNCTION - ASK MODE
   *
   * This function handles sending prompts to the backend in ASK mode
   * and receiving responses to display in the chat.
   *
   * @param {string} prompt - The user's question/prompt
   * @returns {Promise<string>} - The AI response text
   *
   * INSTRUCTIONS:
   * 1. Get the CSRF token from the meta tag:
   *    document.querySelector('meta[name="csrf-token"]').content
   *
   * 2. Send POST request to your Laravel backend endpoint (e.g., '/api/chat/ask')
   *    Include in the request body:
   *    - prompt: the user's question
   *    - context: optional screenplay context from editor.getHTML() or editor.getText()
   *
   * 3. Handle the response:
   *    - If successful, return the response text
   *    - If error, throw an error with a user-friendly message
   *
   * 4. Example structure:
   *    const response = await fetch('/api/chat/ask', {
   *      method: 'POST',
   *      headers: {
   *        'Content-Type': 'application/json',
   *        'X-CSRF-TOKEN': csrfToken,
   *      },
   *      body: JSON.stringify({ prompt, context }),
   *    });
   *
   * 5. Return the parsed response text
   */
  const sendAskRequest = async (prompt, selectedBlockData = null) => {
    // TODO: Implement backend API call for ASK mode

    try {
      // Get CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const script_id = content.id;

      // Prepare the text to send - use blockText if available, otherwise selectedText
      const textToSend = selectedBlockData?.blockText || selectedBlockData?.selectedText || null;

      // Send request to backend
      const response = await fetch('/api/ai/ask', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          prompt: prompt,
          script_id: script_id,
          selected_text: textToSend, // Send the entire block text
          block_type: selectedBlockData?.blockType, // Include block type for context
          // context: context,
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to get response from server');
      }

      const data = await response.json();
      console.log('Response from backend:', data.output);
      return data; // Return the full data object (should contain response.output)

    } catch (error) {
      console.error('Error in sendAskRequest:', error);
      throw error;
    }
  };

  /**
   * BACKEND COMMUNICATION FUNCTION - EDIT MODE
   *
   * This function handles sending edit requests to the backend and
   * applying the changes to the screenplay editor.
   *
   * @param {string} prompt - The user's edit instruction
   * @returns {Promise<Object>} - Object containing edit instructions or new content
   *
   * INSTRUCTIONS:
   * 1. Get the CSRF token from the meta tag
   *
   * 2. Get the current screenplay content:
   *    const currentContent = editor.getHTML(); // or editor.getJSON()
   *
   * 3. Send POST request to your backend endpoint (e.g., '/api/chat/edit')
   *    Include in the request body:
   *    - prompt: the edit instruction
   *    - content: the current screenplay content
   *    - format: 'html' or 'json' (depending on what your backend expects)
   *
   * 4. Backend should return one of:
   *    a) Complete new content to replace
   *    b) Specific edits with positions
   *    c) Suggestions with change tracking
   *
   * 5. Apply changes to editor based on response type:
   *    - Full replacement: editor.commands.setContent(newContent)
   *    - Specific changes: Use editor.chain() commands
   *    - Find and replace: Use editor commands with proper positioning
   *
   * 6. Return response for chat display (confirmation message)
   */
  const sendEditRequest = async (prompt) => {
    // TODO: Implement backend API call for EDIT mode

    try {
      // Get CSRF token
      // const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      // Get current screenplay content
      // const currentContent = editor.getHTML();
      // // Or use JSON format:
      // // const currentContent = editor.getJSON();

      // Send request to backend
      // const response = await fetch('/api/chat/edit', {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json',
      //     'X-CSRF-TOKEN': csrfToken,
      //   },
      //   body: JSON.stringify({
      //     prompt: prompt,
      //     content: currentContent,
      //     format: 'html', // or 'json'
      //   }),
      // });

      // if (!response.ok) {
      //   throw new Error('Failed to get edit response from server');
      // }

      // const data = await response.json();

      // Apply changes to editor based on response type
      // Option 1: Full content replacement
      // if (data.newContent) {
      //   editor.commands.setContent(data.newContent);
      //   return { success: true, message: data.message || 'Content updated successfully' };
      // }

      // Option 2: Specific edits with positions
      // if (data.edits && Array.isArray(data.edits)) {
      //   data.edits.forEach(edit => {
      //     // Apply each edit based on your backend response structure
      //     // Example: editor.commands.insertContentAt(edit.position, edit.content)
      //   });
      //   return { success: true, message: data.message || 'Edits applied successfully' };
      // }

      // Option 3: Find and replace operations
      // if (data.replacements && Array.isArray(data.replacements)) {
      //   data.replacements.forEach(replacement => {
      //     // Find and replace text
      //     // You may need to implement custom logic for this
      //   });
      //   return { success: true, message: data.message || 'Replacements applied successfully' };
      // }

      // return data;

      // TEMPORARY: Simulate API call and edit
      await new Promise(resolve => setTimeout(resolve, 1500));

      // Example: Add a note to the editor (simulated edit)
      // Uncomment when you want to test:
      // const { from, to } = editor.state.selection;
      // editor.chain().focus().insertContentAt(to, {
      //   type: 'note',
      //   content: [{ type: 'text', text: `AI Edit: ${prompt}` }]
      // }).run();

      return {
        success: true,
        message: `Simulated edit applied for: "${prompt}". Replace this with actual backend integration.`,
      };

    } catch (error) {
      console.error('Error in sendEditRequest:', error);
      throw error;
    }
  };

  const handleSendMessage = async () => {
    if (!inputValue.trim() || isLoading) return;

    const userMessage = inputValue.trim();
    const currentSelectedBlockData = selectedText; // Capture current selected block data

    setInputValue('');
    setSelectedTextState(null); // Clear selected text after sending

    // Add user message to chat
    const newUserMessage = {
      id: Date.now(),
      type: 'user',
      content: userMessage,
      timestamp: new Date(),
      selectedText: currentSelectedBlockData?.blockText || currentSelectedBlockData?.selectedText, // Store block text with message
    };

    setMessages(prev => [...prev, newUserMessage]);
    setIsLoading(true);

    try {
      let response;

      if (mode === 'ask') {
        // Call ASK mode function with selected block data
        let aiResponse = await sendAskRequest(userMessage, currentSelectedBlockData);
        aiResponse = aiResponse.response;
        console.log('AI Response:', aiResponse);
        console.log('AI resposne output : ', aiResponse.response)
        // console.log('AI resposne output : ', JSON.parse(aiResponse))
        console.log('Current Selected Block Data:', currentSelectedBlockData);

        // If there's selected block data and AI returned new content, replace the block
        if (currentSelectedBlockData) {
          console.log('Replacing block with AI response:', aiResponse.output);
          await editBlock(currentSelectedBlockData, aiResponse.output);
        } else {
          console.log('Skipping block update:', {
            hasBlockData: !!currentSelectedBlockData,
            blockPos: currentSelectedBlockData?.blockPos,
            hasResponse: !!aiResponse,
            hasOutput: !!aiResponse?.output
          });
        }

        response = {
          id: Date.now() + 1,
          type: 'ai',
          content: aiResponse.output,
          timestamp: new Date(),
          mode: 'ask',
        };
      } else {
        // Call EDIT mode function
        const editResult = await sendEditRequest(userMessage);

        response = {
          id: Date.now() + 1,
          type: 'ai',
          content: editResult.message || 'Edit applied successfully',
          timestamp: new Date(),
          mode: 'edit',
          success: editResult.success,
        };
      }

      setMessages(prev => [...prev, response]);
    } catch (error) {
      // Add error message to chat
      const errorMessage = {
        id: Date.now() + 1,
        type: 'error',
        content: error.message || 'Something went wrong. Please try again.',
        timestamp: new Date(),
      };
      setMessages(prev => [...prev, errorMessage]);
    } finally {
      setIsLoading(false);
      inputRef.current?.focus();
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
  };

  return (
    <div className={`chat-sidebar ${!isVisible ? 'collapsed' : ''}`}>
      {/* Header */}
      <div className="chat-sidebar-header">
        <div className="chat-sidebar-title">
          <Bot size={20} />
          <span>AI Assistant</span>
        </div>


      </div>
        {/* Close button - mobile only */}
        <button
            className="chat-sidebar-close"
            onClick={onClose}
            title="Close sidebar"
        >
            <X size={20} />
        </button>

        {/* Mode Toggle */}
        <div className="chat-mode-toggle">
            <button
                className={`chat-mode-btn ${mode === 'ask' ? 'active' : ''}`}
                onClick={() => setMode('ask')}
                title="Ask questions about your screenplay"
            >
                <Sparkles size={16} />
                <span>Ask</span>
            </button>
            <button
                className={`chat-mode-btn ${mode === 'edit' ? 'active' : ''}`}
                onClick={() => setMode('edit')}
                title="Request edits to your screenplay"
            >
                <Edit3 size={16} />
                <span>Edit</span>
            </button>
        </div>

      {/* Messages Container */}
      <div className="chat-messages-container">
        {messages.length === 0 ? (
          <div className="chat-empty-state">
            <Bot size={48} />
            <h3>How can I help you today?</h3>
            <p>
              {mode === 'ask'
                ? 'Ask me anything about your screenplay or scriptwriting in general.'
                : 'Tell me what edits you\'d like to make to your screenplay.'}
            </p>
          </div>
        ) : (
          <div className="chat-messages">
            {messages.map((message) => (
              <div
                key={message.id}
                className={`chat-message ${message.type === 'user' ? 'user-message' : 'ai-message'} ${message.type === 'error' ? 'error-message' : ''}`}
              >
                <div className="chat-message-avatar">
                  {message.type === 'user' ? (
                    <User size={18} />
                  ) : (
                    <Bot size={18} />
                  )}
                </div>
                <div className="chat-message-content">
                  {message.mode === 'edit' && message.success && (
                    <div className="chat-message-badge">
                      <Edit3 size={12} />
                      <span>Edit Applied</span>
                    </div>
                  )}
                  {message.selectedText && (
                    <div className="chat-message-selected-text">
                      <FileText size={12} />
                      <span>{message.selectedText.length > 50 ? `${message.selectedText.substring(0, 50)}...` : message.selectedText}</span>
                    </div>
                  )}
                  <div className="chat-message-text">{message.content}</div>
                  <div className="chat-message-time">
                    {message.timestamp.toLocaleTimeString([], {
                      hour: '2-digit',
                      minute: '2-digit'
                    })}
                  </div>
                </div>
              </div>
            ))}
            {isLoading && (
              <div className="chat-message ai-message">
                <div className="chat-message-avatar">
                  <Bot size={18} />
                </div>
                <div className="chat-message-content">
                  <div className="chat-loading">
                    <div className="chat-loading-dot"></div>
                    <div className="chat-loading-dot"></div>
                    <div className="chat-loading-dot"></div>
                  </div>
                </div>
              </div>
            )}
            <div ref={messagesEndRef} />
          </div>
        )}
      </div>

      {/* Input Area */}
      <div className="chat-input-container">
        {selectedText && (
          <div className="chat-selected-text-preview">
            <div className="chat-selected-text-header">
              <FileText size={14} />
              <span>Selected Block ({selectedText.blockType})</span>
              <button
                className="chat-selected-text-close"
                onClick={() => setSelectedTextState(null)}
                title="Clear selection"
              >
                <X size={14} />
              </button>
            </div>
            <div className="chat-selected-text-content">
              {selectedText.blockText && selectedText.blockText.length > 100
                ? `${selectedText.blockText.substring(0, 100)}...`
                : selectedText.blockText}
            </div>
          </div>
        )}

        <div className="chat-input-wrapper">
          <textarea
            ref={inputRef}
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onKeyDown={handleKeyDown}
            placeholder={mode === 'ask' ? 'Ask me anything...' : 'Describe the edit you want to make...'}
            className="chat-input"
            rows={1}
            disabled={isLoading}
          />
          <button
            onClick={handleSendMessage}
            disabled={!inputValue.trim() || isLoading}
            className="chat-send-btn"
            title="Send message"
          >
            <Send size={18} />
          </button>
        </div>
        <div className="chat-input-hint">
          {mode === 'ask' ? (
            <span>Ask questions about screenwriting, characters, or plot</span>
          ) : (
            <span>Request changes and the AI will edit your screenplay</span>
          )}
        </div>
      </div>
    </div>
  );
});

export default ChatSidebar;
