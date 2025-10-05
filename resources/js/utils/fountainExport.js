// Convert editor content to Fountain format
export const exportToFountain = (editor) => {
  if (!editor) return '';

  const json = editor.getJSON();
  let fountain = '';

  const processNode = (node) => {
    const text = node.content
      ? node.content.map(child => child.text || '').join('')
      : '';

    switch (node.type) {
      case 'sceneHeading':
        fountain += `${text}\n\n`;
        break;
      case 'action':
        fountain += `${text}\n\n`;
        break;
      case 'character':
        fountain += `${text}\n`;
        break;
      case 'dialogue':
        fountain += `${text}\n\n`;
        break;
      case 'parenthetical':
        fountain += `${text}\n`;
        break;
      case 'transition':
        fountain += `> ${text}\n\n`;
        break;
      case 'note':
        fountain += `[[${text}]]\n\n`;
        break;
      case 'sectionAct':
        fountain += `# ${text}\n\n`;
        break;
      case 'shot':
        fountain += `${text}\n\n`;
        break;
      case 'montage':
        fountain += `${text}\n\n`;
        break;
      default:
        if (node.content) {
          node.content.forEach(child => processNode(child));
        }
    }
  };

  if (json.content) {
    json.content.forEach(node => processNode(node));
  }

  return fountain;
};

// Parse Fountain format and convert to editor JSON
export const importFromFountain = (fountainText) => {
  const lines = fountainText.split('\n');
  const nodes = [];
  let currentNode = null;

  lines.forEach((line, index) => {
    const trimmedLine = line.trim();

    // Skip empty lines
    if (!trimmedLine && !currentNode) return;

    // Check for scene heading
    if (trimmedLine.match(/^(INT|EXT|INT\/EXT|I\/E)[\.\s]/i)) {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'sceneHeading',
        content: [{ type: 'text', text: trimmedLine }],
      };
      return;
    }

    // Check for transition (starts with >)
    if (trimmedLine.startsWith('>')) {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'transition',
        content: [{ type: 'text', text: trimmedLine.substring(1).trim() }],
      };
      return;
    }

    // Check for note (wrapped in [[]])
    if (trimmedLine.startsWith('[[') && trimmedLine.endsWith(']]')) {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'note',
        content: [{ type: 'text', text: trimmedLine.substring(2, trimmedLine.length - 2) }],
      };
      return;
    }

    // Check for section/act (starts with #)
    if (trimmedLine.startsWith('#')) {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'sectionAct',
        content: [{ type: 'text', text: trimmedLine.substring(1).trim() }],
      };
      return;
    }

    // Check for character (all caps, followed by dialogue)
    if (trimmedLine === trimmedLine.toUpperCase() && trimmedLine.length > 0 && !trimmedLine.includes('.')) {
      const nextLine = lines[index + 1];
      if (nextLine && nextLine.trim().length > 0 && !nextLine.trim().startsWith('[[')) {
        if (currentNode) nodes.push(currentNode);
        currentNode = {
          type: 'character',
          content: [{ type: 'text', text: trimmedLine }],
        };
        return;
      }
    }

    // Check for parenthetical
    if (trimmedLine.startsWith('(') && trimmedLine.endsWith(')')) {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'parenthetical',
        content: [{ type: 'text', text: trimmedLine }],
      };
      return;
    }

    // Empty line - finalize current node
    if (!trimmedLine) {
      if (currentNode) {
        nodes.push(currentNode);
        currentNode = null;
      }
      return;
    }

    // Default to dialogue if after character, otherwise action
    if (currentNode && currentNode.type === 'character') {
      nodes.push(currentNode);
      currentNode = {
        type: 'dialogue',
        content: [{ type: 'text', text: trimmedLine }],
      };
    } else {
      if (currentNode) nodes.push(currentNode);
      currentNode = {
        type: 'action',
        content: [{ type: 'text', text: trimmedLine }],
      };
    }
  });

  // Add last node
  if (currentNode) nodes.push(currentNode);

  return {
    type: 'doc',
    content: nodes.length > 0 ? nodes : [{ type: 'action', content: [] }],
  };
};