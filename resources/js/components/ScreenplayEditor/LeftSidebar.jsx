import React from 'react';
import { Brain, BarChart3, X } from 'lucide-react';

const LeftSidebar = ({ isVisible,content, onClose, onGenerateMindMap, onGetAnalysis }) => {
  return (
    <>
      {/* Backdrop */}
      <div
        className={`left-sidebar-backdrop ${isVisible ? 'visible' : ''}`}
        onClick={onClose}
      />

      {/* Drawer */}
      <div className={`left-sidebar ${!isVisible ? 'collapsed' : ''}`}>
        {/* Header */}
        <div className="left-sidebar-header">
          <div className="left-sidebar-title">
            <Brain size={24} />
            <span>Tools</span>
          </div>
          <button
            className="left-sidebar-close"
            onClick={onClose}
            title="Close panel"
          >
            <X size={20} />
          </button>
        </div>

        {/* Options */}
        <div className="left-sidebar-content">
            <a href={`/viewmindmap/${content.id}`} target="_blank" className="left-sidebar-option" rel="noopener noreferrer">


            <div className="left-sidebar-option-icon">
              <Brain size={24} />
            </div>
            <div className="left-sidebar-option-text">
              <h3>Generate Mind Map</h3>
              <p>Visual overview of your script structure and flow</p>
            </div>
            </a>

            <a href={`/analyse-script/${content.id}`} target="_blank" className="left-sidebar-option" rel="noopener noreferrer">

            <div className="left-sidebar-option-icon">
              <BarChart3 size={24} />
            </div>
            <div className="left-sidebar-option-text">
              <h3>Script Analysis</h3>
              <p>Detailed insights on characters, plot, and pacing</p>
            </div>
          </a>
        </div>
      </div>
    </>
  );
};

export default LeftSidebar;
