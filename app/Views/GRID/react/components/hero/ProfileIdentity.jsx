import React from 'react';
import ProfileActions from './ProfileActions.jsx';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

/**
 * ProfileIdentity Component
 * 
 * Top hero identity block:
 * - Avatar with click-to-preview (opens ProfilePhotoModal)
 * - Profile action buttons (Open to, Add Section, More)
 * - Name, pronoun, verified badge, quick edit button
 * - Headline and professional metadata
 * - Open to Work announcement banner
 */
export default function ProfileIdentity({
    user = {},
    canEdit = true,
    onNavigate,
    onEditAvatar,
    onEditProfile,
    onOpenToWork,
    onAddSection,
    onContactInfo,
    onEditViews,
    onAvatarUpdated,
    onToast
}) {
    const fullName = user.fullName || user.name || 'Member';
    const initials = user.initials || (fullName !== 'Member' ? fullName.trim().split(/\s+/).map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U');
    const avatarSrc = user.avatarSrc || user.avatar || '';
    const pronoun = user.pronoun || '';
    const isVerified = user.isVerified !== undefined ? user.isVerified : false;
    const headline = user.headline || (canEdit ? '<span style="color: var(--slate-400); font-style: italic;">No headline added yet — click ✎ to add your professional title</span>' : 'Professional Member');
    const location = user.location || '';
    const connectionsCount = user.connectionsCount || 0;
    const followersCount = user.followersCount || 0;

    const openToWork = user.openToWork || {};
    const openToWorkRoles = openToWork.roles || 'Open to opportunities';
    const openToWorkNote = openToWork.note || 'Available';
    const isOpenToWorkActive = openToWork.active !== undefined ? openToWork.active : false;

    const handleAvatarClick = () => {
        if (onEditAvatar) onEditAvatar();
    };

    return (
        <div className="g-identity-container">
            {/* 1. Avatar & Hero Action Row */}
            <div className="g-hero-top">
                {/* Centered Avatar with #OpenToWork Badge */}
                <div className="g-avatar-wrap">
                    <div 
                        className="g-avatar-lg relative group" 
                        id="gProfileAvatar" 
                        onClick={handleAvatarClick}
                        style={{ 
                            cursor: 'pointer',
                            background: avatarSrc ? 'transparent' : 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)',
                            position: 'relative'
                        }} 
                        title="View profile photo"
                    >
                        {avatarSrc ? (
                            <img src={avatarSrc} alt={fullName} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                        ) : (
                            <span style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', fontSize: '38px', fontWeight: 800, color: '#ffffff', textShadow: '0 2px 4px rgba(0,0,0,0.2)' }}>
                                {initials}
                            </span>
                        )}
                    </div>
                    {isOpenToWorkActive && (
                        <span 
                            className="g-otw-badge" 
                            onClick={canEdit ? onOpenToWork : undefined}
                            style={{ cursor: canEdit ? 'pointer' : 'default' }} 
                            title={canEdit ? "Edit Open To Work Preferences" : "#OpenToWork"}
                        >
                            <span className="g-otw-dot" /> #OpenToWork
                        </span>
                    )}
                </div>

                {/* Hero Actions: "Open to", "Add profile section", "..." */}
                <ProfileActions 
                    canEdit={canEdit}
                    user={user}
                    onNavigate={onNavigate}
                    onOpenToWork={onOpenToWork}
                    onAddSection={onAddSection}
                    onContactInfo={onContactInfo}
                    onEditViews={onEditViews}
                    onToast={onToast}
                />
            </div>

            {/* 2. Identity, Headline & Metadata Block */}
            <div className="g-identity-block">
                <div className="g-identity-name">
                    <h2 id="gUserFullName">{fullName}</h2>
                    {pronoun && (
                        <span className="g-pronoun" id="gUserPronoun">
                            {pronoun}
                        </span>
                    )}
                    {isVerified && (
                        <VerifiedBadge size="md" title="Verified AI Professional" />
                    )}
                    {canEdit && onEditProfile && (
                        <button 
                            type="button"
                            className="g-edit-btn" 
                            onClick={onEditProfile}
                            aria-label="Edit basic info" 
                            title="Edit basic info" 
                            style={{ marginLeft: '4px' }}
                        >
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path 
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth="2" 
                                />
                            </svg>
                        </button>
                    )}
                </div>

                {/* Headline: Supports HTML entities or string formatting */}
                <p 
                    className="g-headline" 
                    id="gUserHeadline"
                    dangerouslySetInnerHTML={{ __html: headline }}
                />

                {/* Meta Row */}
                <div className="g-meta">
                    <span id="gUserLocation">{location}</span>
                    <span>•</span>
                    <a 
                        href="#contact" 
                        onClick={(e) => {
                            e.preventDefault();
                            if (onContactInfo) onContactInfo();
                        }}
                    >
                        Contact info
                    </a>
                    <span>•</span>
                    <span className="g-meta-bold" id="gUserConnections">
                        {connectionsCount} connections
                    </span>
                    <span>•</span>
                    <span className="g-meta-bold" id="gUserFollowers">
                        {followersCount} followers
                    </span>
                </div>

                {/* Open To Work Card */}
                {isOpenToWorkActive && (
                    <div className="g-otw-card">
                        <div className="g-otw-card-inner">
                            <div className="g-otw-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path 
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" 
                                        strokeLinecap="round" 
                                        strokeLinejoin="round" 
                                        strokeWidth="2" 
                                    />
                                </svg>
                            </div>
                            <div>
                                <strong>Open to work:</strong> <span id="gOtwRolesText">{openToWorkRoles}</span>
                                <span className="g-otw-note" id="gOtwNoteText"> {openToWorkNote}</span>
                            </div>
                        </div>
                        {canEdit && onOpenToWork && (
                            <button 
                                type="button"
                                className="g-otw-edit" 
                                onClick={onOpenToWork}
                            >
                                Edit
                            </button>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
