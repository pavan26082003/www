import React from 'react';
import ProfileBanner from './ProfileBanner.jsx';
import ProfileIdentity from './ProfileIdentity.jsx';
import ProfileProgress from './ProfileProgress.jsx';

/**
 * ProfileHero Component
 * 
 * Composes:
 * - ProfileBanner (curved banner, video/image, portfolio link)
 * - ProfileIdentity (avatar, #OpenToWork, actions popover, headline, meta)
 * - ProfileProgress (profile strength, percentage bar, tip)
 */
export default function ProfileHero({
    user = {},
    canEdit = true,
    onNavigate,
    onEditCover,
    onEditAvatar,
    onEditProfile,
    onOpenToWork,
    onAddSection,
    onContactInfo,
    onEditViews,
    onRequestRecommendation,
    onToast
}) {
    const bannerTag = user.bannerTag || '';
    const bannerTitle = user.bannerTitle || '';
    const bannerUrl = user.bannerUrl || (user.gridId ? `grid.community/in/${user.gridId}` : '');
    const bannerVideoSrc = user.bannerVideoSrc || '';
    const bannerImageSrc = user.bannerImageSrc || '';
    const bannerGradient = user.bannerGradient || '';

    const strength = user.profileStrength || {};
    const profileLevel = strength.level ? `Profile Level: ${strength.level} (${strength.percent || 40}%)` : 'Profile Level: Beginner (20%)';
    const profilePercent = strength.percent || 20;
    const profileTipText = strength.tip || 'Complete your profile setup to increase visibility across the community.';

    return (
        <section className="g-card" id="gHeroCard" style={{ marginBottom: '24px' }}>
            {/* 1. Curved Cover Banner */}
            <ProfileBanner 
                tag={bannerTag}
                title={bannerTitle}
                url={bannerUrl}
                videoSrc={bannerVideoSrc}
                imageSrc={bannerImageSrc}
                gradient={bannerGradient}
                onEditCover={canEdit ? onEditCover : undefined}
            />

            {/* 2. Hero Details & Identity */}
            <div className="g-hero-details">
                <ProfileIdentity 
                    user={user}
                    canEdit={canEdit}
                    onNavigate={onNavigate}
                    onEditAvatar={canEdit ? onEditAvatar : undefined}
                    onAvatarUpdated={onEditAvatar}
                    onEditProfile={canEdit ? onEditProfile : undefined}
                    onOpenToWork={canEdit ? onOpenToWork : undefined}
                    onAddSection={canEdit ? onAddSection : undefined}
                    onContactInfo={onContactInfo}
                    onEditViews={canEdit ? onEditViews : undefined}
                    onToast={onToast}
                />
            </div>

            {/* 3. Strength Progress Section (Only shown to owner) */}
            {canEdit && (
                <ProfileProgress 
                    level={profileLevel}
                    percent={profilePercent}
                    tipText={profileTipText}
                    onRequestRecommendation={onRequestRecommendation}
                />
            )}
        </section>
    );
}
