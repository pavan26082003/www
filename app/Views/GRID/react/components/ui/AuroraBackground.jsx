import React from 'react';

/**
 * AmbientBackground / AuroraBackground Component
 * Executive Ambient Spotlight & Precision Micro-Grid
 * Inspired by Linear.app, Stripe, and Vercel.
 *
 * Provides an understated, high-contrast, professional atmospheric canvas:
 * - Obsidian Dark: Deep obsidian void + subtle indigo ambient spotlight + precision engineering micro-grid
 * - Executive Light: Crisp titanium slate + calm ambient soft glow
 *
 * @param {Object} props
 * @param {React.ReactNode} props.children - Child elements to render above the ambient canvas
 * @param {string} [props.className=''] - Optional additional CSS classes
 * @param {'dark' | 'light'} [props.theme='dark'] - Current active theme
 * @param {boolean} [props.isFixedViewport=false] - Whether this view is locked to full viewport height (e.g. Messaging)
 */
export default function AuroraBackground({
    children,
    className = '',
    theme = 'dark',
    isFixedViewport = false,
    ...props
}) {
    return (
        <div className={`g-ambient-wrapper g-aurora-wrapper ${theme === 'dark' ? 'g-theme-dark' : 'g-theme-light'} ${isFixedViewport ? 'g-ambient-fixed' : ''} ${className}`} {...props}>
            {/* 1. Fixed ambient lighting & precision grid canvas */}
            <div className="g-ambient-viewport g-aurora-viewport" aria-hidden="true">
                {/* Precision engineering micro-grid */}
                <div className="g-ambient-grid" />
                {/* Calm, monochromatic ambient spotlight */}
                <div className="g-ambient-spotlight" />
                {/* Secondary gentle diffuse flare */}
                <div className="g-ambient-flare" />
            </div>

            {/* 2. Elevated content layer */}
            <div className={`g-ambient-content g-aurora-content ${isFixedViewport ? 'g-content-fixed' : ''}`}>
                {children}
            </div>
        </div>
    );
}

