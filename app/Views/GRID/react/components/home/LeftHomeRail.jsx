import React from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * LeftHomeRail Component
 * Left column structural sidebar matching the Stitch design:
 * - Mini Profile Card with blue cosmic gradient cover, overlapping avatar, active pulse dot
 * - Verified badge and "Update your profile" link
 * - Dashed "+ Add/Edit Experience" CTA
 * - Bold "4" GRIDLINKS metric box
 * - Profile Settings link
 * - Navigation links: Home (active), My visiting card, eFactor network, Scan QR, Privacy & permissions
 */
export default function LeftHomeRail({
  user,
  profileData,
  activeNav = 'home',
  onNavSelect,
  onAddExperience,
  onProfileSettings,
  onNavigate
}) {
  // Support either user or profileData prop
  const profile = user || profileData || {};
  const fullName = profile.name || profile.fullName || 'User';
  const rawAvatar = profile.avatar || profile.avatarSrc || '';
  const parsedAvatar = (typeof rawAvatar === 'string' && rawAvatar.includes('file_name'))
    ? `/files/profile_images/${rawAvatar.match(/"file_name";s:\d+:"([^"]+)"/)?.[1] || ''}`
    : rawAvatar;
  const fallbackAvatar = '/assets/images/avatar.jpg';
  const avatarUrl = parsedAvatar || fallbackAvatar;
  const skillCount = profile.skillEnds || profile.skillsCount || 4;

  const handleProfileClick = (e) => {
    e.preventDefault();
    if (onProfileSettings) {
      onProfileSettings();
    } else if (onNavigate) {
      onNavigate('profile');
    } else {
      window.location.href = '/index.php/grid/profile';
    }
  };

  return (
    <div className="space-y-4">
      {/* 1. Profile & Gridlinks Card */}
      <div className="g-glass-card rounded-2xl border border-white/70 shadow-sm overflow-hidden">
        {/* Gradient Top Cover */}
        <div className="h-16 bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 relative">
          <div className="absolute inset-0 bg-[radial-gradient(#60a5fa_1px,transparent_1px)] [background-size:10px_10px] opacity-25" />
        </div>

        {/* Profile Content */}
        <div className="px-5 pb-5 pt-0 relative">
          {/* Avatar & Badge */}
          <div className="flex flex-col items-center justify-center -mt-9 mb-3">
            <div
              className="relative inline-block cursor-pointer group"
              onClick={handleProfileClick}
              title="View Profile"
            >
              <img
                alt={fullName}
                className="w-16 h-16 rounded-full object-cover ring-4 ring-white shadow-sm bg-white transition-transform group-hover:scale-105"
                src={avatarUrl}
                onError={(e) => {
                  e.currentTarget.onerror = null;
                  e.currentTarget.src = fallbackAvatar;
                }}
              />
              <span
                className="absolute bottom-0.5 right-0.5 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white"
                title="Active on Network"
              />
            </div>
          </div>

          {/* Name & Update link */}
          <div className="text-center">
            <div
              className="flex items-center gap-1.5 justify-center cursor-pointer"
              onClick={handleProfileClick}
            >
              <h2 className="font-display text-lg font-bold text-slate-900 leading-snug hover:text-blue-600 transition-colors">
                {fullName}
              </h2>
              <VerifiedBadge size="md" title="Verified Identity" />
            </div>
            <a
              className="text-xs text-slate-500 hover:text-blue-600 transition-colors inline-block mt-0.5"
              href="/index.php/grid/profile"
              onClick={handleProfileClick}
            >
              Update your profile
            </a>
          </div>

          {/* Dashed border button */}
          <button
            className="mt-3.5 w-full py-2 px-3 rounded-xl border border-dashed border-blue-400/80 hover:border-blue-600 bg-blue-50/40 hover:bg-blue-50 text-blue-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition-all group cursor-pointer"
            type="button"
            onClick={onAddExperience || handleProfileClick}
          >
            <span className="material-symbols-outlined text-[16px] group-hover:scale-110 transition-transform">
              add_circle
            </span>
            <span>+ Add/Edit Experience</span>
          </button>

          {/* Key Metric Container: Large bold "4" GRIDLINKS */}
          <div className="mt-3.5 p-3 rounded-xl bg-white/50 backdrop-blur-xs border border-white/70 text-center">
            <div className="font-display text-2xl font-bold text-slate-900 leading-none">
              {skillCount}
            </div>
            <div className="font-mono text-[10px] tracking-widest uppercase font-semibold text-slate-500 mt-1">
              GRIDLINKS
            </div>
          </div>

          {/* Profile Action Row */}
          <div className="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <a
              className="flex items-center gap-1.5 text-slate-600 hover:text-blue-600 font-medium transition-colors"
              href="/index.php/grid/profile"
              onClick={handleProfileClick}
            >
              <span className="material-symbols-outlined text-[17px] text-slate-400">public</span>
              <span>Profile Settings</span>
            </a>
            <span className="material-symbols-outlined text-[15px] text-slate-400">chevron_right</span>
          </div>
        </div>
      </div>

      {/* 2. Left Navigation Links Card */}
      <div className="g-glass-card rounded-2xl border border-white/70 p-2 shadow-sm space-y-1">
        {/* Home (Active state with subtle blue highlight) */}
        <a
          className="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-blue-50 text-blue-700 font-semibold text-xs border-l-4 border-blue-600 transition-colors shadow-2xs"
          href="/index.php/grid"
          onClick={(e) => {
            e.preventDefault();
            if (onNavSelect) onNavSelect('home');
          }}
        >
          <span className="material-symbols-outlined text-[19px] fill-current-solid">home</span>
          <span>Home</span>
        </a>

        {/* My visiting card */}
        <a
          className="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-medium text-xs transition-colors cursor-pointer"
          href="#"
          onClick={(e) => {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('grid:open-visiting-card'));
          }}
        >
          <span className="material-symbols-outlined text-[19px] text-slate-400">badge</span>
          <span>My visiting card</span>
        </a>

        {/* eFactor network */}
        <a
          className="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-medium text-xs transition-colors cursor-pointer"
          href="/index.php/grid/network"
          onClick={(e) => {
            e.preventDefault();
            if (onNavigate) {
              onNavigate('network');
            } else {
              window.location.href = '/index.php/grid/network';
            }
          }}
        >
          <div className="flex items-center gap-3">
            <span className="material-symbols-outlined text-[19px] text-slate-400">group</span>
            <span>eFactor network</span>
          </div>
          <span className="w-2 h-2 rounded-full bg-emerald-500" title="Live Network Graph" />
        </a>

        {/* Scan QR */}
        <a
          className="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-medium text-xs transition-colors cursor-pointer"
          href="#"
          onClick={(e) => {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('grid:open-scan-qr'));
          }}
        >
          <span className="material-symbols-outlined text-[19px] text-slate-400">qr_code_scanner</span>
          <span>Scan QR</span>
        </a>

        {/* Privacy & permissions */}
        <a
          className="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-medium text-xs transition-colors"
          href="#"
          onClick={(e) => {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('grid:open-section-visibility'));
          }}
        >
          <span className="material-symbols-outlined text-[19px] text-slate-400">shield</span>
          <span>Privacy &amp; permissions</span>
        </a>
      </div>
    </div>
  );
}
