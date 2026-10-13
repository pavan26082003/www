import React, { useState, useEffect, useRef } from 'react';

/**
 * AnimatedThumbsUp Component
 * 
 * Recreates the animated Lucide ThumbsUp icon from:
 * https://animateicons.in/icons/lucide/thumbs-up
 * 
 * - Empty outline when unliked (stroke: currentColor, fill: none)
 * - On click, fills up with vibrant active blue and triggers the signature rock, lift, and bounce animation
 * - Micro particle sparkles and expanding ripple ring on like
 * - Subtle hover wiggle preview
 * - Crisp white cuff separation line when filled
 */
export default function AnimatedThumbsUp({
  isLiked = false,
  size = 19,
  color = 'currentColor',
  activeColor = '#0a66c2', // Professional LinkedIn / Grid blue
  className = ''
}) {
  const [isAnimating, setIsAnimating] = useState(false);
  const [showParticles, setShowParticles] = useState(false);
  const prevLikedRef = useRef(isLiked);

  useEffect(() => {
    // Trigger animation only when transitioning from unliked to liked
    if (isLiked && !prevLikedRef.current) {
      setIsAnimating(true);
      setShowParticles(true);

      const animTimer = setTimeout(() => {
        setIsAnimating(false);
      }, 850);

      const particleTimer = setTimeout(() => {
        setShowParticles(false);
      }, 650);

      prevLikedRef.current = isLiked;
      return () => {
        clearTimeout(animTimer);
        clearTimeout(particleTimer);
      };
    }
    prevLikedRef.current = isLiked;
  }, [isLiked]);

  return (
    <span 
      className={`g-thumbs-up-container ${className} ${isLiked ? 'is-liked' : ''}`}
      style={{ width: `${size + 4}px`, height: `${size + 4}px` }}
    >
      <style>{`
        .g-thumbs-up-container {
          position: relative;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          user-select: none;
        }

        .g-thumbs-up-svg {
          display: block;
          overflow: visible;
          transform-origin: 7px 18px;
          transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Hover wiggle on unliked state (matching animateicons.in) */
        .g-thumbs-up-container:hover .g-thumbs-up-svg:not(.animating):not(.is-liked) {
          animation: gThumbsUpHoverWiggle 0.65s ease-in-out;
        }

        @keyframes gThumbsUpHoverWiggle {
          0% { transform: scale(1) rotate(0deg); }
          22% { transform: scale(1.08) rotate(-7deg); }
          48% { transform: scale(1.04) rotate(5deg); }
          75% { transform: scale(1.02) rotate(-2deg); }
          100% { transform: scale(1) rotate(0deg); }
        }

        /* Signature AnimateIcons Rock, Lift & Bounce */
        .g-thumbs-up-svg.animating {
          animation: gThumbsUpAnimateIcons 0.85s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes gThumbsUpAnimateIcons {
          0% {
            transform: scale(1) rotate(0deg) translateY(0);
          }
          18% {
            transform: scale(1.18) rotate(-13deg) translateY(-5px);
          }
          38% {
            transform: scale(1.24) rotate(9deg) translateY(-7px);
          }
          60% {
            transform: scale(0.96) rotate(-4deg) translateY(-2px);
          }
          80% {
            transform: scale(1.05) rotate(1.5deg) translateY(0);
          }
          100% {
            transform: scale(1) rotate(0deg) translateY(0);
          }
        }

        /* Stem compression & expansion */
        .g-thumbs-up-stem.animating {
          animation: gThumbsUpStemAnim 0.65s ease-in-out 0.06s forwards;
          transform-origin: center;
        }

        @keyframes gThumbsUpStemAnim {
          0% { transform: scaleY(1); opacity: 1; }
          28% { transform: scaleY(0.9); opacity: 0.85; }
          65% { transform: scaleY(1.06); opacity: 1; }
          100% { transform: scaleY(1); opacity: 1; }
        }

        /* Hand Fill-Up Pop Animation */
        .g-thumbs-up-hand {
          transform-origin: center;
          transition: fill 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), stroke 0.25s ease;
        }

        .g-thumbs-up-hand.fill-animating {
          animation: gThumbsUpFillPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes gThumbsUpFillPop {
          0% {
            fill-opacity: 0;
            transform: scale(0.7);
          }
          45% {
            fill-opacity: 0.85;
            transform: scale(1.16);
          }
          75% {
            fill-opacity: 1;
            transform: scale(0.98);
          }
          100% {
            fill-opacity: 1;
            transform: scale(1);
          }
        }

        /* Expanding Particle Bursts */
        .g-thumbs-burst {
          position: absolute;
          top: 50%;
          left: 50%;
          width: 0;
          height: 0;
          pointer-events: none;
        }

        .g-thumbs-particle {
          position: absolute;
          width: 3.5px;
          height: 3.5px;
          border-radius: 50%;
          background: #0a66c2;
          opacity: 0;
          animation: gParticleBurst 0.6s ease-out forwards;
        }

        .g-thumbs-particle:nth-child(1) { --tx: 0px; --ty: -15px; background: #2563eb; }
        .g-thumbs-particle:nth-child(2) { --tx: 13px; --ty: -10px; background: #38bdf8; }
        .g-thumbs-particle:nth-child(3) { --tx: 15px; --ty: 4px; background: #0a66c2; }
        .g-thumbs-particle:nth-child(4) { --tx: 8px; --ty: 14px; background: #60a5fa; }
        .g-thumbs-particle:nth-child(5) { --tx: -11px; --ty: 12px; background: #818cf8; }
        .g-thumbs-particle:nth-child(6) { --tx: -14px; --ty: -4px; background: #0284c7; }

        @keyframes gParticleBurst {
          0% {
            transform: translate(0, 0) scale(1.2);
            opacity: 1;
          }
          50% {
            opacity: 0.9;
          }
          100% {
            transform: translate(var(--tx), var(--ty)) scale(0);
            opacity: 0;
          }
        }

        /* Ripple ring */
        .g-thumbs-ring {
          position: absolute;
          top: 50%;
          left: 50%;
          width: 24px;
          height: 24px;
          margin-top: -12px;
          margin-left: -12px;
          border-radius: 50%;
          border: 2px solid rgba(10, 102, 194, 0.5);
          pointer-events: none;
          animation: gThumbsRing 0.55s ease-out forwards;
        }

        @keyframes gThumbsRing {
          0% {
            transform: scale(0.4);
            opacity: 0.9;
          }
          100% {
            transform: scale(1.8);
            opacity: 0;
          }
        }
      `}</style>

      {showParticles && (
        <>
          <span className="g-thumbs-ring" />
          <span className="g-thumbs-burst">
            <span className="g-thumbs-particle" />
            <span className="g-thumbs-particle" />
            <span className="g-thumbs-particle" />
            <span className="g-thumbs-particle" />
            <span className="g-thumbs-particle" />
            <span className="g-thumbs-particle" />
          </span>
        </>
      )}

      <svg
        xmlns="http://www.w3.org/2000/svg"
        width={size}
        height={size}
        viewBox="0 0 24 24"
        fill={isLiked ? activeColor : 'none'}
        stroke={isLiked ? activeColor : color}
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        className={`g-thumbs-up-svg ${isAnimating ? 'animating' : ''} ${isLiked ? 'is-liked' : ''}`}
      >
        {/* Stem / Cuff Divider */}
        <path
          d="M7 10v12"
          stroke={isLiked ? '#ffffff' : color}
          strokeWidth={isLiked ? 2 : 2}
          strokeLinecap="round"
          className={`g-thumbs-up-stem ${isAnimating ? 'animating' : ''}`}
        />
        {/* Hand contour */}
        <path
          d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"
          className={`g-thumbs-up-hand ${isAnimating ? 'fill-animating' : ''}`}
        />
      </svg>
    </span>
  );
}
