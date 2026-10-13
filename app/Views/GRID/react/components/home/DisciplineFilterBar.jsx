import React from 'react';

/**
 * DisciplineFilterBar Component
 * Horizontal discipline navigation rail matching Stitch:
 * - Graphic Designer, Web Designer, Figma, Web Developer, UI Designer
 * - Uses proper material-symbols-outlined glyph rendering
 * - Active discipline highlighted cleanly
 */
export default function DisciplineFilterBar({
  disciplines = [],
  selectedDiscipline = 'all',
  selected,
  onSelectDiscipline,
  onSelect
}) {
  const activeItem = selectedDiscipline || selected || 'all';
  const handleSelect = (id) => {
    if (onSelectDiscipline) onSelectDiscipline(id);
    if (onSelect) onSelect(id);
  };

  return (
    <div className="flex items-center gap-6 overflow-x-auto px-4 sm:px-0 sm:pt-3 pb-3 text-slate-500 font-medium text-xs border-b border-slate-200/60 dark:border-slate-800 scrollbar-none">
      {disciplines.map((item) => {
        const isSelected = activeItem === item.id;

        return (
          <button
            key={item.id}
            type="button"
            onClick={() => handleSelect(item.id)}
            className={`flex items-center gap-1.5 transition-colors shrink-0 cursor-pointer ${
              isSelected
                ? 'text-slate-900 font-semibold border-b-2 border-slate-900 -mb-[13px] pb-3'
                : 'hover:text-slate-900'
            }`}
          >
            {item.isBrand ? (
              <span className="inline-flex items-center justify-center w-4 h-4">
                <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 38 57">
                  <path d="M19 28.5C19 23.2533 23.2533 19 28.5 19C33.7467 19 38 23.2533 38 28.5C38 33.7467 33.7467 38 28.5 38C23.2533 38 19 33.7467 19 28.5Z" fill="#1ABCFE" />
                  <path d="M0 47.5C0 42.2533 4.25329 38 9.5 38H19V47.5C19 52.7467 14.7467 57 9.5 57C4.25329 57 0 52.7467 0 47.5Z" fill="#0ACF83" />
                  <path d="M19 0V19H28.5C33.7467 19 38 14.7467 38 9.5C38 4.25329 33.7467 0 28.5 0H19Z" fill="#FF7262" />
                  <path d="M0 9.5C0 14.7467 4.25329 19 9.5 19H19V0H9.5C4.25329 0 0 4.25329 0 9.5Z" fill="#F24E1E" />
                  <path d="M0 28.5C0 33.7467 4.25329 38 9.5 38H19V19H9.5C4.25329 19 0 23.2533 0 28.5Z" fill="#A259FF" />
                </svg>
              </span>
            ) : item.icon ? (
              <span className="material-symbols-outlined text-[18px] text-slate-400">
                {item.icon}
              </span>
            ) : null}
            <span>{item.label}</span>
          </button>
        );
      })}
    </div>
  );
}
