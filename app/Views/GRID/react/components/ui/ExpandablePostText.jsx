import React, { useState } from 'react';

/**
 * ExpandablePostText Component
 * 
 * Recreates LinkedIn-style expandable post content:
 * - Initially clamps to 2 lines with an inline "... more" button.
 * - When clicked, smoothly expands to reveal the full formatted text,
 *   preserving paragraphs, line breaks, hashtags (#tag), and mentions.
 * - Offers a subtle "less" toggle to collapse back if desired.
 */
export default function ExpandablePostText({
  text = '',
  truncateAfter = null,
  maxChars = 150,
  className = 'text-sm text-slate-800 dark:text-slate-200 leading-relaxed'
}) {
  const [isExpanded, setIsExpanded] = useState(false);

  if (!text) return null;

  // Format hashtags and mentions
  const renderFormatted = (content) => {
    // Split by paragraphs
    const paragraphs = content.split('\n');
    return paragraphs.map((para, pIdx) => {
      if (!para.trim()) {
        return <div key={pIdx} className="h-2" />;
      }
      
      // Tokenize paragraph by words and hashtags
      const words = para.split(/(\s+)/);
      return (
        <p key={pIdx} className="leading-relaxed">
          {words.map((word, wIdx) => {
            if (word.startsWith('#')) {
              return (
                <span
                  key={wIdx}
                  className="text-[#0a66c2] dark:text-blue-400 font-medium hover:underline cursor-pointer"
                  onClick={(e) => {
                    e.stopPropagation();
                  }}
                >
                  {word}
                </span>
              );
            }
            if (word.includes('Genesis') || word.includes('Technologies') || word.includes('Canvas') || word.includes('Shaders')) {
              return (
                <strong key={wIdx} className="font-semibold text-slate-900 dark:text-white">
                  {word}
                </strong>
              );
            }
            return word;
          })}
        </p>
      );
    });
  };

  // Determine if expansion is needed
  const lines = text.split('\n').filter(Boolean);
  const isLong = text.length > maxChars || lines.length > 2 || Boolean(truncateAfter);

  if (!isLong) {
    return (
      <div className={className}>
        {renderFormatted(text)}
      </div>
    );
  }

  // Expanded View
  if (isExpanded) {
    return (
      <div className={`${className} space-y-2 animate-in fade-in duration-200`}>
        {renderFormatted(text)}
        <button
          type="button"
          onClick={() => setIsExpanded(false)}
          className="text-slate-400 hover:text-[#0a66c2] text-xs font-semibold cursor-pointer hover:underline pt-1 inline-block"
        >
          ...less
        </button>
      </div>
    );
  }

  // Collapsed View (2 lines + "... more")
  let previewText = '';
  if (truncateAfter && text.includes(truncateAfter)) {
    previewText = text.substring(0, text.indexOf(truncateAfter) + truncateAfter.length);
  } else if (lines.length >= 2) {
    // Take first 2 lines
    previewText = `${lines[0]}\n${lines[1]}`;
    if (previewText.length > maxChars) {
      previewText = previewText.substring(0, maxChars).trim() + '...';
    }
  } else {
    // Cut off around maxChars at word boundary
    const cut = text.lastIndexOf(' ', maxChars);
    previewText = (cut > 0 ? text.substring(0, cut) : text.substring(0, maxChars)).trim();
  }

  return (
    <div className={className}>
      <span className="whitespace-pre-line">
        {previewText.replace(/\.\.\.$/, '')}
      </span>
      <span className="text-slate-500 dark:text-slate-400">... </span>
      <button
        type="button"
        onClick={() => setIsExpanded(true)}
        className="text-slate-500 dark:text-slate-400 hover:text-[#0a66c2] dark:hover:text-blue-400 font-semibold cursor-pointer hover:underline inline-block transition-colors"
        aria-label="Expand post text"
      >
        more
      </button>
    </div>
  );
}
