import React, { useState, useRef, useEffect } from 'react';
import { gridApi } from '../../gridApi.js';

/**
 * ProfilePhotoModal — LinkedIn-Style Profile Photo & Crop Editor
 * 
 * Supports two distinct views:
 * 1. 'preview' — LinkedIn Dark Modal with circular avatar, #OPENTOWORK frame, visibility pill, and actions
 * 2. 'edit'    — LinkedIn "Edit image" screen with Circular Crop viewport, Zoom slider, Rotate slider, Flip tools, and "Save changes"
 */
export default function ProfilePhotoModal({ isOpen, onClose, user = {}, canEdit = true, onAvatarUpdated, onToast }) {
    const fileInputRef = useRef(null);
    const [view, setView] = useState('preview'); // 'preview' | 'edit'
    const [activeTab, setActiveTab] = useState('crop'); // 'crop' | 'filter' | 'adjust'
    
    // Crop & Transform state
    const [zoom, setZoom] = useState(1);
    const [rotation, setRotation] = useState(0);
    const [flipH, setFlipH] = useState(false);
    const [flipV, setFlipV] = useState(false);
    const [pan, setPan] = useState({ x: 0, y: 0 });
    const [isDragging, setIsDragging] = useState(false);
    const dragStartRef = useRef({ x: 0, y: 0, panX: 0, panY: 0 });
    const [isSaving, setIsSaving] = useState(false);

    // Active image being edited (holds uploaded file DataURL or current avatar)
    const [editImageSrc, setEditImageSrc] = useState(null);
    const [imageDimensions, setImageDimensions] = useState({ width: 280, height: 280 });

    // Frame toggle
    const [hasFrame, setHasFrame] = useState(() => {
        return user.openToWork?.active !== false;
    });

    // Reset editor state when opened or closed
    useEffect(() => {
        if (!isOpen) {
            setView('preview');
            setZoom(1);
            setRotation(0);
            setFlipH(false);
            setFlipV(false);
            setPan({ x: 0, y: 0 });
            setEditImageSrc(null);
        }
    }, [isOpen]);

    if (!isOpen) return null;

    const fullName = user.fullName || user.name || 'Member';
    const avatarSrc = user.avatarSrc || user.avatar || '';
    const initials = user.initials || (fullName !== 'Member' ? fullName.trim().split(/\s+/).map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'U');
    const activeImageSrc = editImageSrc || avatarSrc;

    const handleBackdropClick = (e) => {
        if (e.target === e.currentTarget) {
            if (view === 'edit') {
                setEditImageSrc(null);
                setView('preview');
            } else {
                onClose();
            }
        }
    };

    const handleUpdatePhoto = () => {
        if (fileInputRef.current) fileInputRef.current.click();
    };

    const handleStartEdit = () => {
        const srcToEdit = activeImageSrc;
        if (!srcToEdit) {
            handleUpdatePhoto();
            return;
        }
        const tempImg = new Image();
        tempImg.crossOrigin = 'anonymous';
        tempImg.onload = () => {
            setImageDimensions({
                width: tempImg.naturalWidth || 280,
                height: tempImg.naturalHeight || 280
            });
            setEditImageSrc(srcToEdit);
            setZoom(1);
            setRotation(0);
            setFlipH(false);
            setFlipV(false);
            setPan({ x: 0, y: 0 });
            setActiveTab('crop');
            setView('edit');
        };
        tempImg.onerror = () => {
            setEditImageSrc(srcToEdit);
            setZoom(1);
            setRotation(0);
            setFlipH(false);
            setFlipV(false);
            setPan({ x: 0, y: 0 });
            setActiveTab('crop');
            setView('edit');
        };
        tempImg.src = srcToEdit;
    };

    const handleToggleFrame = () => {
        setHasFrame(prev => {
            const next = !prev;
            onToast?.(next ? '#OPENTOWORK frame applied! 🟢' : 'Photo frame removed');
            return next;
        });
    };

    // When a user selects a file from device, IMMEDIATELY open the edit screen with full original image
    const handleFileChange = (e) => {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            onToast?.('Please select a valid image file (JPG, PNG, WebP).');
            return;
        }
        if (file.size > 20 * 1024 * 1024) {
            onToast?.('Image is too large. Please select a photo under 20MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            const dataUrl = event.target?.result;
            if (dataUrl) {
                const tempImg = new Image();
                tempImg.onload = () => {
                    const natW = tempImg.naturalWidth || 280;
                    const natH = tempImg.naturalHeight || 280;
                    setImageDimensions({ width: natW, height: natH });
                    setEditImageSrc(dataUrl);
                    setZoom(1);
                    setRotation(0);
                    setFlipH(false);
                    setFlipV(false);
                    setPan({ x: 0, y: 0 });
                    setActiveTab('crop');
                    setView('edit'); // DIRECTLY open edit screen!
                    onToast?.('Photo selected! Adjust position, zoom, or rotate below ✂️');
                };
                tempImg.src = dataUrl;
            }
        };
        reader.readAsDataURL(file);
        if (e.target) e.target.value = '';
    };

    const handleImageLoad = (e) => {
        const { naturalWidth, naturalHeight } = e.currentTarget;
        if (naturalWidth && naturalHeight) {
            setImageDimensions({ width: naturalWidth, height: naturalHeight });
        }
    };

    const handleDeletePhoto = async () => {
        if (!avatarSrc) {
            onToast?.('No custom profile photo to delete.');
            return;
        }

        if (window.__GRID_DATA__?.user) {
            window.__GRID_DATA__.user.avatar = '';
        }
        window.dispatchEvent(new CustomEvent('grid:avatar-updated', { detail: '' }));
        onAvatarUpdated?.('');
        onToast?.('Profile photo removed.');
        onClose();
    };

    // Pan / Drag Handlers for Crop Canvas
    const handleMouseDown = (e) => {
        setIsDragging(true);
        dragStartRef.current = {
            x: e.clientX,
            y: e.clientY,
            panX: pan.x,
            panY: pan.y
        };
    };

    const viewportSize = 280; // 280px circular crop viewport diameter
    const imgW = imageDimensions.width || 280;
    const imgH = imageDimensions.height || 280;

    let baseWidth = viewportSize;
    let baseHeight = viewportSize;

    if (imgW / imgH >= 1) {
        // Landscape or Square: Height matches viewportSize (280), Width expands so circle is 100% COVERED!
        baseHeight = viewportSize;
        baseWidth = Math.round(viewportSize * (imgW / imgH));
    } else {
        // Portrait: Width matches viewportSize (280), Height expands so circle is 100% COVERED!
        baseWidth = viewportSize;
        baseHeight = Math.round(viewportSize * (imgH / imgW));
    }

    const clampPan = (x, y, z = zoom) => {
        const renderedW = baseWidth * z;
        const renderedH = baseHeight * z;
        const maxPanX = Math.max(0, (renderedW - viewportSize) / 2);
        const maxPanY = Math.max(0, (renderedH - viewportSize) / 2);
        return {
            x: Math.min(Math.max(x, -maxPanX), maxPanX),
            y: Math.min(Math.max(y, -maxPanY), maxPanY)
        };
    };

    const handleMouseMove = (e) => {
        if (!isDragging) return;
        const dx = e.clientX - dragStartRef.current.x;
        const dy = e.clientY - dragStartRef.current.y;
        const rawX = dragStartRef.current.panX + dx;
        const rawY = dragStartRef.current.panY + dy;
        setPan(clampPan(rawX, rawY));
    };

    const handleMouseUp = () => {
        setIsDragging(false);
    };

    // Touch Drag Handlers for Mobile Cropping
    const handleTouchStart = (e) => {
        if (e.touches && e.touches.length === 1) {
            setIsDragging(true);
            dragStartRef.current = {
                x: e.touches[0].clientX,
                y: e.touches[0].clientY,
                panX: pan.x,
                panY: pan.y
            };
        }
    };

    const handleTouchMove = (e) => {
        if (!isDragging || !e.touches || e.touches.length !== 1) return;
        if (e.cancelable) e.preventDefault();
        const dx = e.touches[0].clientX - dragStartRef.current.x;
        const dy = e.touches[0].clientY - dragStartRef.current.y;
        const rawX = dragStartRef.current.panX + dx;
        const rawY = dragStartRef.current.panY + dy;
        setPan(clampPan(rawX, rawY));
    };

    const handleTouchEnd = () => {
        setIsDragging(false);
    };

    // Save Crop Result
    const handleSaveCrop = async () => {
        if (!activeImageSrc) {
            setView('preview');
            return;
        }

        setIsSaving(true);
        try {
            const canvas = document.createElement('canvas');
            const outputSize = 560; // 2x crisp HD resolution
            canvas.width = outputSize;
            canvas.height = outputSize;
            const ctx = canvas.getContext('2d');

            // Circular clipping path
            ctx.beginPath();
            ctx.arc(outputSize / 2, outputSize / 2, outputSize / 2, 0, Math.PI * 2);
            ctx.closePath();
            ctx.clip();

            // Background fill
            ctx.fillStyle = '#1d2226';
            ctx.fill();

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = activeImageSrc;

            await new Promise((resolve) => {
                if (img.complete && img.naturalWidth) {
                    resolve();
                } else {
                    img.onload = resolve;
                    img.onerror = resolve;
                }
            });

            // Exact scale factor between canvas and screen (560 / 280 = 2)
            const S = outputSize / viewportSize;
            const clamped = clampPan(pan.x, pan.y, zoom);

            ctx.translate(outputSize / 2 + clamped.x * S, outputSize / 2 + clamped.y * S);
            ctx.rotate((rotation * Math.PI) / 180);
            ctx.scale(flipH ? -zoom : zoom, flipV ? -zoom : zoom);

            const drawW = baseWidth * S;
            const drawH = baseHeight * S;
            ctx.drawImage(img, -drawW / 2, -drawH / 2, drawW, drawH);

            canvas.toBlob(async (blob) => {
                if (blob) {
                    const file = new File([blob], 'cropped_avatar.jpg', { type: 'image/jpeg' });
                    const res = await gridApi.uploadAvatar(file);
                    if (res?.success && res.avatar_url) {
                        if (window.__GRID_DATA__?.user) {
                            window.__GRID_DATA__.user.avatar = res.avatar_url;
                        }
                        window.dispatchEvent(new CustomEvent('grid:avatar-updated', { detail: res.avatar_url }));
                        onAvatarUpdated?.(res.avatar_url);
                        setEditImageSrc(res.avatar_url);
                        onToast?.('Profile photo cropped and saved! 📸');
                    } else {
                        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                        onAvatarUpdated?.(dataUrl);
                        setEditImageSrc(dataUrl);
                        onToast?.('Changes applied! 📸');
                    }
                }
                setIsSaving(false);
                setView('preview');
            }, 'image/jpeg', 0.92);
        } catch (err) {
            console.warn('Failed to crop photo:', err);
            setIsSaving(false);
            setView('preview');
        }
    };

    return (
        <div 
            className="g-photo-modal-backdrop" 
            onClick={handleBackdropClick}
            style={{
                position: 'fixed',
                inset: 0,
                zIndex: 9999,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                backgroundColor: 'rgba(0, 0, 0, 0.75)',
                backdropFilter: 'blur(8px)',
                WebkitBackdropFilter: 'blur(8px)',
                animation: 'gPhotoModalFadeIn 0.2s ease-out'
            }}
        >
            {/* Hidden File Input */}
            <input
                type="file"
                ref={fileInputRef}
                accept="image/png,image/jpeg,image/jpg,image/webp"
                style={{ display: 'none' }}
                onChange={handleFileChange}
            />

            {/* ================================================================= */}
            {/* VIEW 1: LINKEDIN PROFILE PHOTO PREVIEW (DARK THEME)               */}
            {/* ================================================================= */}
            {view === 'preview' && (
                <div 
                    className="g-photo-modal-card"
                    style={{
                        background: '#1d2226',
                        borderRadius: '10px',
                        width: '100%',
                        maxWidth: '560px',
                        margin: '0 16px',
                        overflow: 'hidden',
                        boxShadow: '0 25px 60px rgba(0,0,0,0.6)',
                        color: '#ffffff',
                        border: '1px solid rgba(255,255,255,0.1)',
                        animation: 'gPhotoModalSlideUp 0.22s cubic-bezier(0.16, 1, 0.3, 1)'
                    }}
                >
                    {/* Header */}
                    <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        padding: '16px 24px',
                        borderBottom: '1px solid rgba(255,255,255,0.08)'
                    }}>
                        <h3 style={{
                            fontSize: '19px',
                            fontWeight: 600,
                            color: '#ffffff',
                            margin: 0
                        }}>
                            Profile photo
                        </h3>
                        <button
                            type="button"
                            onClick={onClose}
                            style={{
                                width: '32px',
                                height: '32px',
                                borderRadius: '50%',
                                border: 'none',
                                background: 'transparent',
                                cursor: 'pointer',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '20px',
                                color: 'rgba(255,255,255,0.7)',
                                transition: 'all 0.15s ease'
                            }}
                            onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.1)'; e.currentTarget.style.color = '#ffffff'; }}
                            onMouseLeave={(e) => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = 'rgba(255,255,255,0.7)'; }}
                            aria-label="Close"
                        >
                            ✕
                        </button>
                    </div>

                    {/* Photo Preview Area */}
                    <div style={{
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '36px 24px 20px',
                        background: '#1d2226'
                    }}>
                        <div style={{
                            position: 'relative',
                            width: '260px',
                            height: '260px',
                        }}>
                            {/* Main Circular Avatar */}
                            <div style={{
                                width: '260px',
                                height: '260px',
                                borderRadius: '50%',
                                overflow: 'hidden',
                                boxShadow: '0 8px 32px rgba(0,0,0,0.4)',
                                background: avatarSrc ? '#24292e' : 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)',
                                position: 'relative'
                            }}>
                                {avatarSrc ? (
                                    <img 
                                        src={avatarSrc} 
                                        alt={fullName} 
                                        style={{ 
                                            width: '100%', 
                                            height: '100%', 
                                            objectFit: 'cover' 
                                        }} 
                                    />
                                ) : (
                                    <span style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        height: '100%',
                                        fontSize: '84px',
                                        fontWeight: 800,
                                        color: '#ffffff',
                                        textShadow: '0 3px 10px rgba(0,0,0,0.3)'
                                    }}>
                                        {initials}
                                    </span>
                                )}
                            </div>

                            {/* LinkedIn #OPENTOWORK Arc Frame */}
                            {hasFrame && (
                                <svg 
                                    viewBox="0 0 260 260" 
                                    style={{ 
                                        position: 'absolute', 
                                        inset: 0, 
                                        width: '260px', 
                                        height: '260px', 
                                        pointerEvents: 'none' 
                                    }}
                                >
                                    <defs>
                                        <linearGradient id="otwGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stopColor="#057642" />
                                            <stop offset="60%" stopColor="#0a884e" />
                                            <stop offset="100%" stopColor="#03542f" />
                                        </linearGradient>
                                        <clipPath id="avatarCircleClip">
                                            <circle cx="130" cy="130" r="130" />
                                        </clipPath>
                                        <path id="otwTextArc" d="M 18,124 A 113,113 0 0,0 206,198" fill="none" />
                                    </defs>
                                    <g clipPath="url(#avatarCircleClip)">
                                        <path 
                                            d="M 18,124 A 113,113 0 0,0 206,198" 
                                            fill="none" 
                                            stroke="url(#otwGradient)" 
                                            strokeWidth="34" 
                                            strokeLinecap="round" 
                                        />
                                        <text 
                                            fill="#ffffff" 
                                            fontFamily="'Plus Jakarta Sans', system-ui, -apple-system, sans-serif" 
                                            fontSize="13.5" 
                                            fontWeight="800" 
                                            letterSpacing="2"
                                        >
                                            <textPath href="#otwTextArc" startOffset="50%" textAnchor="middle">
                                                #OPENTOWORK
                                            </textPath>
                                        </text>
                                    </g>
                                </svg>
                            )}
                        </div>
                    </div>

                    {/* Visibility Indicator (Left-aligned) */}
                    <div style={{
                        padding: '8px 24px 12px',
                        display: 'flex',
                        alignItems: 'center'
                    }}>
                        <button
                            type="button"
                            onClick={() => { onToast?.('Visibility is set to Anyone (Public)'); }}
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '8px',
                                padding: '6px 14px',
                                borderRadius: '20px',
                                border: '1px solid rgba(255,255,255,0.3)',
                                background: 'rgba(255,255,255,0.06)',
                                color: '#ffffff',
                                fontSize: '13px',
                                fontWeight: 600,
                                cursor: 'pointer',
                                transition: 'all 0.15s ease'
                            }}
                            onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.12)'; }}
                            onMouseLeave={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.06)'; }}
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Anyone
                        </button>
                    </div>

                    {/* Action Bar */}
                    {canEdit && (
                        <div style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            padding: '14px 24px 18px',
                            borderTop: '1px solid rgba(255,255,255,0.08)',
                            background: '#1d2226'
                        }}>
                            {/* Left: Edit, Update, Frames */}
                            <div style={{ display: 'flex', gap: '20px' }}>
                                {/* Edit -> Switches to Crop View */}
                                <button
                                    type="button"
                                    onClick={handleStartEdit}
                                    style={{
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '6px',
                                        background: 'none',
                                        border: 'none',
                                        cursor: 'pointer',
                                        color: 'rgba(255,255,255,0.78)',
                                        padding: '6px 12px',
                                        borderRadius: '8px',
                                        transition: 'all 0.15s ease'
                                    }}
                                    onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.08)'; e.currentTarget.style.color = '#ffffff'; }}
                                    onMouseLeave={(e) => { e.currentTarget.style.background = 'none'; e.currentTarget.style.color = 'rgba(255,255,255,0.78)'; }}
                                >
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span style={{ fontSize: '12px', fontWeight: 600 }}>Edit</span>
                                </button>

                                {/* Update */}
                                <button
                                    type="button"
                                    onClick={handleUpdatePhoto}
                                    style={{
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '6px',
                                        background: 'none',
                                        border: 'none',
                                        cursor: 'pointer',
                                        color: 'rgba(255,255,255,0.78)',
                                        padding: '6px 12px',
                                        borderRadius: '8px',
                                        transition: 'all 0.15s ease'
                                    }}
                                    onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.08)'; e.currentTarget.style.color = '#ffffff'; }}
                                    onMouseLeave={(e) => { e.currentTarget.style.background = 'none'; e.currentTarget.style.color = 'rgba(255,255,255,0.78)'; }}
                                >
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                        <circle cx="12" cy="13" r="4"/>
                                    </svg>
                                    <span style={{ fontSize: '12px', fontWeight: 600 }}>Update</span>
                                </button>

                                {/* Frames */}
                                <button
                                    type="button"
                                    onClick={handleToggleFrame}
                                    style={{
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '6px',
                                        background: hasFrame ? 'rgba(5, 118, 66, 0.2)' : 'none',
                                        border: 'none',
                                        cursor: 'pointer',
                                        color: hasFrame ? '#4ade80' : 'rgba(255,255,255,0.78)',
                                        padding: '6px 12px',
                                        borderRadius: '8px',
                                        transition: 'all 0.15s ease'
                                    }}
                                    onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(255,255,255,0.08)'; e.currentTarget.style.color = '#ffffff'; }}
                                    onMouseLeave={(e) => { 
                                        e.currentTarget.style.background = hasFrame ? 'rgba(5, 118, 66, 0.2)' : 'none'; 
                                        e.currentTarget.style.color = hasFrame ? '#4ade80' : 'rgba(255,255,255,0.78)'; 
                                    }}
                                >
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                    <span style={{ fontSize: '12px', fontWeight: 600 }}>Frames</span>
                                </button>
                            </div>

                            {/* Right: Delete */}
                            <button
                                type="button"
                                onClick={handleDeletePhoto}
                                style={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    gap: '6px',
                                    background: 'none',
                                    border: 'none',
                                    cursor: 'pointer',
                                    color: 'rgba(255,255,255,0.78)',
                                    padding: '6px 12px',
                                    borderRadius: '8px',
                                    transition: 'all 0.15s ease'
                                }}
                                onMouseEnter={(e) => { e.currentTarget.style.background = 'rgba(239, 68, 68, 0.15)'; e.currentTarget.style.color = '#f87171'; }}
                                onMouseLeave={(e) => { e.currentTarget.style.background = 'none'; e.currentTarget.style.color = 'rgba(255,255,255,0.78)'; }}
                            >
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                <span style={{ fontSize: '12px', fontWeight: 600 }}>Delete</span>
                            </button>
                        </div>
                    )}
                </div>
            )}

            {/* ================================================================= */}
            {/* VIEW 2: LINKEDIN "EDIT IMAGE" (CROP / ZOOM / ROTATE SCREEN)       */}
            {/* ================================================================= */}
            {view === 'edit' && (
                <div 
                    className="g-photo-edit-card"
                    style={{
                        background: '#ffffff',
                        borderRadius: '10px',
                        width: '100%',
                        maxWidth: '820px',
                        margin: '0 16px',
                        overflow: 'hidden',
                        boxShadow: '0 25px 60px rgba(0,0,0,0.6)',
                        color: '#0f172a',
                        animation: 'gPhotoModalSlideUp 0.22s cubic-bezier(0.16, 1, 0.3, 1)',
                        display: 'flex',
                        flexDirection: 'column',
                        maxHeight: '94vh',
                        overflowY: 'auto'
                    }}
                >
                    {/* Header */}
                    <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        padding: '16px 24px',
                        borderBottom: '1px solid #e2e8f0'
                    }}>
                        <h3 style={{
                            fontSize: '19px',
                            fontWeight: 600,
                            color: '#1e293b',
                            margin: 0
                        }}>
                            Edit image
                        </h3>
                        <button
                            type="button"
                            onClick={() => {
                                setEditImageSrc(null);
                                setView('preview');
                            }}
                            style={{
                                width: '32px',
                                height: '32px',
                                borderRadius: '50%',
                                border: 'none',
                                background: 'transparent',
                                cursor: 'pointer',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '20px',
                                color: '#64748b',
                                transition: 'all 0.15s ease'
                            }}
                            onMouseEnter={(e) => { e.currentTarget.style.background = '#f1f5f9'; e.currentTarget.style.color = '#0f172a'; }}
                            onMouseLeave={(e) => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = '#64748b'; }}
                            aria-label="Back to preview"
                        >
                            ✕
                        </button>
                    </div>

                    {/* Editor Body: Left Canvas + Right Tools Panel */}
                    <div style={{
                        display: 'flex',
                        flexDirection: 'row',
                        minHeight: '420px',
                        flexWrap: 'wrap'
                    }}>
                        {/* Left Side: Gray Canvas with Circular Crop Viewport */}
                        <div 
                            style={{
                                flex: '1 1 400px',
                                background: '#8d9499',
                                position: 'relative',
                                overflow: 'hidden',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                userSelect: 'none',
                                cursor: isDragging ? 'grabbing' : 'grab',
                                minHeight: '340px',
                                touchAction: 'none'
                            }}
                            onMouseDown={handleMouseDown}
                            onMouseMove={handleMouseMove}
                            onMouseUp={handleMouseUp}
                            onMouseLeave={handleMouseUp}
                            onTouchStart={handleTouchStart}
                            onTouchMove={handleTouchMove}
                            onTouchEnd={handleTouchEnd}
                            onTouchCancel={handleTouchEnd}
                        >
                            {/* Inner Transformed Image Layer */}
                            <div 
                                style={{
                                    position: 'absolute',
                                    width: `${baseWidth}px`,
                                    height: `${baseHeight}px`,
                                    transform: `translate(${pan.x}px, ${pan.y}px) rotate(${rotation}deg) scale(${flipH ? -zoom : zoom}, ${flipV ? -zoom : zoom})`,
                                    transformOrigin: 'center center',
                                    transition: isDragging ? 'none' : 'transform 0.1s ease-out',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center'
                                }}
                            >
                                {activeImageSrc ? (
                                    <img 
                                        src={activeImageSrc} 
                                        alt={fullName} 
                                        draggable={false}
                                        onLoad={handleImageLoad}
                                        style={{ 
                                            width: '100%', 
                                            height: '100%', 
                                            objectFit: 'contain', 
                                            pointerEvents: 'none',
                                            userSelect: 'none'
                                        }} 
                                    />
                                ) : (
                                    <div style={{
                                        width: '100%',
                                        height: '100%',
                                        background: 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        fontSize: '96px',
                                        fontWeight: 800,
                                        color: '#ffffff'
                                    }}>
                                        {initials}
                                    </div>
                                )}
                            </div>

                            {/* Circular Mask Overlay with 2px Crisp White Border */}
                            <div 
                                style={{
                                    position: 'absolute',
                                    width: '280px',
                                    height: '280px',
                                    borderRadius: '50%',
                                    border: '2px solid #ffffff',
                                    boxShadow: '0 0 0 9999px rgba(0, 0, 0, 0.42)',
                                    pointerEvents: 'none'
                                }}
                            />
                        </div>

                        {/* Right Side: Tools Control Panel */}
                        <div style={{
                            flex: '0 0 280px',
                            background: '#ffffff',
                            padding: '24px 20px',
                            display: 'flex',
                            flexDirection: 'column',
                            justifyContent: 'space-between',
                            borderLeft: '1px solid #e2e8f0'
                        }}>
                            <div>
                                {/* Tabs Row: Crop (Active), Filter, Adjust */}
                                <div style={{
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '24px',
                                    borderBottom: '1px solid #e2e8f0',
                                    paddingBottom: '8px',
                                    marginBottom: '24px'
                                }}>
                                    <button
                                        type="button"
                                        onClick={() => setActiveTab('crop')}
                                        style={{
                                            background: 'none',
                                            border: 'none',
                                            borderBottom: activeTab === 'crop' ? '2px solid #0f172a' : '2px solid transparent',
                                            paddingBottom: '6px',
                                            fontSize: '14px',
                                            fontWeight: 600,
                                            color: activeTab === 'crop' ? '#0f172a' : '#64748b',
                                            cursor: 'pointer'
                                        }}
                                    >
                                        Crop
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => onToast?.('Filters coming soon')}
                                        style={{
                                            background: 'none',
                                            border: 'none',
                                            paddingBottom: '6px',
                                            fontSize: '14px',
                                            fontWeight: 500,
                                            color: '#94a3b8',
                                            cursor: 'not-allowed'
                                        }}
                                    >
                                        Filter
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => onToast?.('Adjustments coming soon')}
                                        style={{
                                            background: 'none',
                                            border: 'none',
                                            paddingBottom: '6px',
                                            fontSize: '14px',
                                            fontWeight: 500,
                                            color: '#94a3b8',
                                            cursor: 'not-allowed'
                                        }}
                                    >
                                        Adjust
                                    </button>
                                </div>

                                {/* Crop Tools: Rotate & Flip Buttons */}
                                <div style={{
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '16px',
                                    marginBottom: '24px'
                                }}>
                                    {/* Rotate Left -90deg */}
                                    <button
                                        type="button"
                                        onClick={() => setRotation(r => (r - 90) % 360)}
                                        title="Rotate left"
                                        style={{
                                            width: '36px',
                                            height: '36px',
                                            borderRadius: '50%',
                                            border: '1px solid #e2e8f0',
                                            background: '#f8fafc',
                                            cursor: 'pointer',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            color: '#334155'
                                        }}
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                            <path d="M3 3v5h5"/>
                                        </svg>
                                    </button>

                                    {/* Rotate Right +90deg */}
                                    <button
                                        type="button"
                                        onClick={() => setRotation(r => (r + 90) % 360)}
                                        title="Rotate right"
                                        style={{
                                            width: '36px',
                                            height: '36px',
                                            borderRadius: '50%',
                                            border: '1px solid #e2e8f0',
                                            background: '#f8fafc',
                                            cursor: 'pointer',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            color: '#334155'
                                        }}
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="M21 12a9 9 0 1 1-9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                            <path d="M21 3v5h-5"/>
                                        </svg>
                                    </button>

                                    {/* Flip Horizontal */}
                                    <button
                                        type="button"
                                        onClick={() => setFlipH(f => !f)}
                                        title="Flip horizontally"
                                        style={{
                                            width: '36px',
                                            height: '36px',
                                            borderRadius: '50%',
                                            border: '1px solid #e2e8f0',
                                            background: flipH ? '#e2e8f0' : '#f8fafc',
                                            cursor: 'pointer',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            color: '#334155'
                                        }}
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="m3 7 5 5-5 5V7z"/>
                                            <path d="m21 7-5 5 5 5V7z"/>
                                            <line x1="12" y1="3" x2="12" y2="21"/>
                                        </svg>
                                    </button>

                                    {/* Flip Vertical */}
                                    <button
                                        type="button"
                                        onClick={() => setFlipV(f => !f)}
                                        title="Flip vertically"
                                        style={{
                                            width: '36px',
                                            height: '36px',
                                            borderRadius: '50%',
                                            border: '1px solid #e2e8f0',
                                            background: flipV ? '#e2e8f0' : '#f8fafc',
                                            cursor: 'pointer',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            color: '#334155'
                                        }}
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="m7 3 5 5 5-5H7z"/>
                                            <path d="m7 21 5-5 5 5H7z"/>
                                            <line x1="3" y1="12" x2="21" y2="12"/>
                                        </svg>
                                    </button>
                                </div>

                                {/* Zoom Slider */}
                                <div style={{ marginBottom: '20px' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '8px' }}>
                                        <label style={{ fontSize: '13px', fontWeight: 600, color: '#334155' }}>
                                            Zoom
                                        </label>
                                        <span style={{ fontSize: '12px', color: '#64748b' }}>
                                            {Math.round(zoom * 100)}%
                                        </span>
                                    </div>
                                    <input 
                                        type="range" 
                                        min="1" 
                                        max="3" 
                                        step="0.05" 
                                        value={zoom} 
                                        onChange={(e) => {
                                            const newZoom = parseFloat(e.target.value);
                                            setZoom(newZoom);
                                            setPan(prevPan => clampPan(prevPan.x, prevPan.y, newZoom));
                                        }}
                                        style={{
                                            width: '100%',
                                            accentColor: '#0a66c2',
                                            cursor: 'pointer'
                                        }}
                                    />
                                </div>

                                {/* Rotate Slider */}
                                <div style={{ marginBottom: '20px' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '8px' }}>
                                        <label style={{ fontSize: '13px', fontWeight: 600, color: '#334155' }}>
                                            Rotate
                                        </label>
                                        <span style={{ fontSize: '12px', color: '#64748b' }}>
                                            {rotation}°
                                        </span>
                                    </div>
                                    <input 
                                        type="range" 
                                        min="-180" 
                                        max="180" 
                                        step="1" 
                                        value={rotation} 
                                        onChange={(e) => setRotation(parseInt(e.target.value))}
                                        style={{
                                            width: '100%',
                                            accentColor: '#0a66c2',
                                            cursor: 'pointer'
                                        }}
                                    />
                                </div>

                                {/* Reset Transform */}
                                {(zoom !== 1 || rotation !== 0 || flipH || flipV || pan.x !== 0 || pan.y !== 0) && (
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setZoom(1);
                                            setRotation(0);
                                            setFlipH(false);
                                            setFlipV(false);
                                            setPan({ x: 0, y: 0 });
                                        }}
                                        style={{
                                            background: 'none',
                                            border: 'none',
                                            color: '#0a66c2',
                                            fontSize: '12px',
                                            fontWeight: 600,
                                            cursor: 'pointer',
                                            padding: 0
                                        }}
                                    >
                                        Reset changes
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Footer with "Save changes" Button */}
                    <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        gap: '12px',
                        padding: '16px 24px',
                        borderTop: '1px solid #e2e8f0',
                        background: '#ffffff'
                    }}>
                        <button
                            type="button"
                            onClick={handleUpdatePhoto}
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: '6px',
                                padding: '8px 14px',
                                borderRadius: '24px',
                                border: '1px solid #cbd5e1',
                                background: '#ffffff',
                                color: '#0a66c2',
                                fontSize: '13px',
                                fontWeight: 600,
                                cursor: 'pointer',
                                transition: 'all 0.15s ease'
                            }}
                            onMouseEnter={(e) => { e.currentTarget.style.background = '#f0f7ff'; }}
                            onMouseLeave={(e) => { e.currentTarget.style.background = '#ffffff'; }}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                            <span>Change photo</span>
                        </button>

                        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                            <button
                                type="button"
                                onClick={() => {
                                    setEditImageSrc(null);
                                    setView('preview');
                                }}
                                style={{
                                    padding: '8px 18px',
                                    borderRadius: '24px',
                                    border: '1px solid #cbd5e1',
                                    background: '#ffffff',
                                    color: '#475569',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    cursor: 'pointer',
                                    transition: 'all 0.15s ease'
                                }}
                                onMouseEnter={(e) => { e.currentTarget.style.background = '#f1f5f9'; }}
                                onMouseLeave={(e) => { e.currentTarget.style.background = '#ffffff'; }}
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                onClick={handleSaveCrop}
                                disabled={isSaving}
                                style={{
                                    padding: '8px 24px',
                                    borderRadius: '24px',
                                    border: 'none',
                                    background: isSaving ? '#93c5fd' : '#0a66c2',
                                    color: '#ffffff',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    cursor: isSaving ? 'not-allowed' : 'pointer',
                                    transition: 'all 0.15s ease',
                                    boxShadow: '0 2px 8px rgba(10,102,194,0.3)'
                                }}
                                onMouseEnter={(e) => { if (!isSaving) e.currentTarget.style.background = '#004182'; }}
                                onMouseLeave={(e) => { if (!isSaving) e.currentTarget.style.background = '#0a66c2'; }}
                            >
                                {isSaving ? 'Saving...' : 'Save changes'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal Animations */}
            <style>{`
                @keyframes gPhotoModalFadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes gPhotoModalSlideUp {
                    from { opacity: 0; transform: translateY(24px) scale(0.97); }
                    to { opacity: 1; transform: translateY(0) scale(1); }
                }
            `}</style>
        </div>
    );
}
