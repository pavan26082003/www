import React, { useRef, useEffect } from 'react';

/**
 * ProfileBanner Component
 * 
 * Semi-oval curved contour banner with video background (Laser Lights/Code Flow),
 * image cover, or radial gradient, featuring badge tag, edit button, and clickable
 * portfolio URL pill badge.
 */
export default function ProfileBanner({ 
    tag = 'AI Post-Training & Alignment',
    title = 'Designing the Future of Generative AI',
    url = 'grid.community/in/rohan-verma-ai',
    videoSrc = '',
    imageSrc = '',
    gradient = '',
    customStyle = {},
    onEditCover
}) {
    const videoRef = useRef(null);

    // Ensure browser autoplay policy does not block video playback
    useEffect(() => {
        if (videoRef.current && videoSrc) {
            videoRef.current.muted = true;
            videoRef.current.play().catch(() => {});
        }
    }, [videoSrc]);

    const portfolioHref = (url.startsWith('http://') || url.startsWith('https://'))
        ? url
        : `https://${url}`;

    const backgroundStyle = gradient 
        ? { background: gradient, ...customStyle }
        : customStyle;

    return (
        <div className="g-banner" id="gProfileBanner" style={backgroundStyle}>
            {videoSrc ? (
                <video 
                    ref={videoRef}
                    className="g-banner-video" 
                    src={videoSrc} 
                    autoPlay 
                    muted 
                    loop 
                    playsInline 
                />
            ) : imageSrc ? (
                <img 
                    className="g-banner-img" 
                    src={imageSrc} 
                    alt="Cover banner" 
                />
            ) : null}

            <div className="g-banner-overlay" />

            {/* Top row: Badge Tag + Edit Button */}
            <div className="g-banner-top">
                <span className="g-banner-tag" id="gBannerTagText">
                    {tag}
                </span>
                <button 
                    type="button"
                    className="g-banner-edit" 
                    id="gEditCoverBtn" 
                    onClick={onEditCover}
                    aria-label="Edit cover photo or video" 
                    title="Edit cover banner"
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
            </div>

            {/* Center / Bottom: Title + Portfolio URL Pill */}
            <div className="g-banner-bottom">
                <h1 id="gBannerTitleText">{title}</h1>
                <p className="g-banner-url-wrap">
                    <a 
                        href={portfolioHref} 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        className="g-banner-url" 
                        id="gBannerUrlText" 
                        title="Open portfolio link"
                    >
                        <svg 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24" 
                            style={{ width: '13px', height: '13px', display: 'inline-block', verticalAlign: '-1px', marginRight: '4px' }}
                        >
                            <path 
                                strokeLinecap="round" 
                                strokeLinejoin="round" 
                                strokeWidth="2" 
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" 
                            />
                        </svg>
                        {url}
                    </a>
                </p>
            </div>
        </div>
    );
}
