import React, { useState } from 'react';

/**
 * VisitingCardWidget Component
 * Renders the Praful Shah's Visiting Card card with:
 * - Deterministic SVG QR Matrix
 * - Card ID chip (#GRID-PS-2025)
 * - Permission-based explanation
 * - Save PNG & Share Card action buttons
 */
export default function VisitingCardWidget({ cardData, onShare, onSavePng }) {
  const [copied, setCopied] = useState(false);

  const handleShare = () => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(window.location.origin + '/grid/profile');
      setCopied(true);
      setTimeout(() => setCopied(false), 2500);
    }
    if (onShare) onShare();
  };

  const handleDownload = () => {
    // Generate a simple client-side SVG download
    const svgEl = document.getElementById('grid-visiting-qr');
    if (!svgEl) return;
    const serializer = new XMLSerializer();
    const source = serializer.serializeToString(svgEl);
    const svgBlob = new Blob([source], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);
    const downloadLink = document.createElement('a');
    downloadLink.href = url;
    downloadLink.download = 'praful_shah_visiting_card.svg';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    URL.revokeObjectURL(url);
    if (onSavePng) onSavePng();
  };

  return (
    <div className="g-glass-card rounded-2xl border border-white/70 p-4 shadow-sm space-y-3.5">
      <div className="flex items-center justify-between pb-2 border-b border-slate-100">
        <div className="flex items-center gap-2">
          <span className="material-symbols-outlined text-[18px] text-blue-600">badge</span>
          <h3 className="font-display text-xs font-bold text-slate-900 tracking-tight">
            {cardData?.owner || "Praful Shah's"} Visiting Card
          </h3>
        </div>
        <button
          className="p-1 text-slate-400 hover:text-slate-600 rounded-md hover:bg-slate-100 transition-colors cursor-pointer"
          title="Share options"
          type="button"
        >
          <span className="material-symbols-outlined text-[16px]">more_vert</span>
        </button>
      </div>

      <div className="flex flex-col items-center justify-center p-3 bg-white/40 backdrop-blur-xs rounded-xl border border-white/60">
        <div className="p-2.5 bg-white/90 rounded-lg border border-white/80 shadow-2xs relative group">
          <svg
            id="grid-visiting-qr"
            className="w-32 h-32 text-slate-900"
            fill="currentColor"
            viewBox="0 0 120 120"
          >
            {/* Corners Position Markers */}
            <rect fill="#0f172a" height="32" rx="4" width="32" x="4" y="4" />
            <rect fill="#ffffff" height="20" rx="2" width="20" x="10" y="10" />
            <rect fill="#2563eb" height="12" rx="1.5" width="12" x="14" y="14" />
            <rect fill="#0f172a" height="32" rx="4" width="32" x="84" y="4" />
            <rect fill="#ffffff" height="20" rx="2" width="20" x="90" y="10" />
            <rect fill="#2563eb" height="12" rx="1.5" width="12" x="94" y="14" />
            <rect fill="#0f172a" height="32" rx="4" width="32" x="4" y="84" />
            <rect fill="#ffffff" height="20" rx="2" width="20" x="10" y="90" />
            <rect fill="#2563eb" height="12" rx="1.5" width="12" x="14" y="94" />

            {/* QR Data Modules Matrix */}
            <rect fill="#0f172a" height="6" rx="1" width="6" x="44" y="6" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="54" y="6" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="66" y="6" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="44" y="18" />
            <rect fill="#2563eb" height="6" rx="1" width="6" x="60" y="18" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="70" y="18" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="44" y="30" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="54" y="30" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="66" y="30" />

            {/* Row 4-7 */}
            <rect fill="#0f172a" height="6" rx="1" width="6" x="6" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="18" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="30" y="44" />
            <rect fill="#2563eb" height="8" rx="2" width="8" x="44" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="56" y="44" />
            <rect fill="#2563eb" height="8" rx="2" width="8" x="68" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="84" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="96" y="44" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="108" y="44" />

            {/* Row 8-10 */}
            <rect fill="#0f172a" height="6" rx="1" width="6" x="6" y="56" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="24" y="56" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="36" y="56" />
            <rect fill="#0f172a" height="16" rx="3" width="16" x="52" y="56" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="84" y="56" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="102" y="56" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="6" y="68" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="18" y="68" />
            <rect fill="#2563eb" height="6" rx="1" width="6" x="30" y="68" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="76" y="68" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="92" y="68" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="108" y="68" />

            {/* Bottom Half */}
            <rect fill="#0f172a" height="6" rx="1" width="6" x="44" y="84" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="56" y="84" />
            <rect fill="#2563eb" height="6" rx="1" width="6" x="68" y="84" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="84" y="84" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="98" y="84" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="108" y="84" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="44" y="96" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="60" y="96" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="74" y="96" />
            <rect fill="#2563eb" height="6" rx="1" width="6" x="90" y="96" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="104" y="96" />
            <rect fill="#2563eb" height="6" rx="1" width="6" x="44" y="108" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="56" y="108" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="68" y="108" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="82" y="108" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="98" y="108" />
            <rect fill="#0f172a" height="6" rx="1" width="6" x="110" y="108" />

            {/* Center Logo Mark */}
            <rect fill="#2563eb" height="12" rx="2" width="12" x="54" y="58" />
            <rect fill="#ffffff" height="6" rx="1" width="6" x="57" y="61" />
          </svg>
        </div>
        <div className="mt-2.5 text-center">
          <span className="font-mono text-[10px] text-slate-500 font-medium px-2 py-0.5 rounded bg-white border border-slate-200/80">
            CARD ID {cardData?.cardId || '#GRID-PS-2025'}
          </span>
        </div>
      </div>

      <p className="text-[11px] text-slate-500 text-center leading-relaxed px-1">
        Scan this QR to view {cardData?.owner?.split(' ')[0] || "Praful's"} interactive card with permission-based commenting.
      </p>

      <div className="grid grid-cols-2 gap-2 pt-1">
        <button
          onClick={handleDownload}
          className="h-8 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer"
          type="button"
        >
          <span className="material-symbols-outlined text-[15px] text-slate-500">download</span>
          <span>Save PNG</span>
        </button>
        <button
          onClick={handleShare}
          className="h-8 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
          type="button"
        >
          <span className="material-symbols-outlined text-[15px]">share</span>
          <span>{copied ? 'Copied!' : 'Share Card'}</span>
        </button>
      </div>
    </div>
  );
}
