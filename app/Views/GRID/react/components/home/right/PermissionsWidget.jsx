import React from 'react';

/**
 * PermissionsWidget Component
 * Explains the 4-tier privacy and interactive access matrix used by GRID Visiting Cards.
 * Includes platform footer links & copyright mark.
 */
export default function PermissionsWidget() {
  const tiers = [
    {
      level: 'Level 1',
      name: 'Public',
      icon: 'public',
      color: 'bg-emerald-50 text-emerald-600 border-emerald-200/70',
      description: 'Anyone can view and comment'
    },
    {
      level: 'Level 2',
      name: 'eFactor + Connections only',
      icon: 'group',
      color: 'bg-blue-50 text-blue-600 border-blue-200/70',
      description: 'Verified network access'
    },
    {
      level: 'Level 3',
      name: 'Guest / QR Scan',
      icon: 'qr_code_scanner',
      color: 'bg-amber-50 text-amber-600 border-amber-200/70',
      description: 'Read-only access to visiting card'
    },
    {
      level: 'Level 4',
      name: 'eFactor Members',
      icon: 'verified_user',
      color: 'bg-indigo-50 text-indigo-600 border-indigo-200/70',
      description: 'Full interactive reply permissions'
    }
  ];

  return (
    <div className="space-y-4">
      <div className="g-glass-card rounded-2xl border border-white/70 p-4 shadow-sm space-y-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className="material-symbols-outlined text-[17px] text-slate-700">
              admin_panel_settings
            </span>
            <h3 className="font-display text-xs font-bold uppercase tracking-wider text-slate-600">
              Permission Levels Used
            </h3>
          </div>
          <span
            className="material-symbols-outlined text-[15px] text-slate-400"
            title="Verified Access Protocol"
          >
            lock
          </span>
        </div>

        <div className="space-y-2 text-xs">
          {tiers.map((t, idx) => (
            <div
              key={idx}
              className="flex items-start gap-2.5 p-2 rounded-xl bg-slate-50/50 border border-slate-200/60"
            >
              <div
                className={`w-6 h-6 rounded-lg border flex items-center justify-center shrink-0 mt-0.5 ${t.color}`}
              >
                <span className="material-symbols-outlined text-[14px]">{t.icon}</span>
              </div>
              <div className="min-w-0">
                <div className="font-semibold text-slate-800 text-xs flex items-center gap-1.5">
                  <span>{t.name}</span>
                  <span className="text-[10px] font-mono font-normal text-slate-400">
                    • {t.level}
                  </span>
                </div>
                <p className="text-[11px] text-slate-500 leading-tight mt-0.5">
                  {t.description}
                </p>
              </div>
            </div>
          ))}
        </div>

        <p className="text-[10px] text-slate-400 leading-relaxed pt-1 border-t border-slate-100 italic">
          Visiting card and profile access dynamically adapts based on visitor permissions.
        </p>
      </div>

      {/* Footer Telemetry & System Links */}
      <div className="px-2 text-center text-[11px] text-slate-400 space-y-2 font-normal">
        <div className="flex flex-wrap items-center justify-center gap-x-3 gap-y-1">
          <a className="hover:text-slate-600 transition-colors" href="#">About</a>
          <a className="hover:text-slate-600 transition-colors" href="#">Accessibility</a>
          <a className="hover:text-slate-600 transition-colors" href="#">Help Center</a>
          <a className="hover:text-slate-600 transition-colors" href="#">Privacy &amp; Terms</a>
          <a className="hover:text-slate-600 transition-colors" href="#">Ad Choices</a>
        </div>
        <div className="flex items-center justify-center gap-1.5 font-mono text-[10px] text-slate-400">
          <div className="w-3.5 h-3.5 rounded bg-blue-600 text-white flex items-center justify-center text-[7px] font-bold">
            G
          </div>
          <span>Grid Corporation © 2025</span>
        </div>
      </div>
    </div>
  );
}
