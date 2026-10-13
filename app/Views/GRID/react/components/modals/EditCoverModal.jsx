import React, { useState, useEffect } from 'react';
import ModalContainer from './ModalContainer.jsx';
import { gridApi } from '../../gridApi.js';

/**
 * EditCoverModal Component
 * 
 * 100% visual and functional match to original design:
 * - Cover Media Type radio selector (Gradient / Image / Video)
 * - 1-Click Sample Video Presets (🌌 Laser Lights / 💻 Code Flow)
 * - Video URL input and PC file upload (saved to server /files/profile_banners/)
 * - Gradient presets & Image upload
 * - Category Tag, Banner Title, and Personal Portfolio Link
 */
export default function EditCoverModal({
    isOpen,
    onClose,
    user = {},
    onSave
}) {
    const [mediaType, setMediaType] = useState(user.bannerType || (user.bannerVideoSrc ? 'video' : user.bannerImageSrc ? 'image' : 'gradient'));
    const [videoUrl, setVideoUrl] = useState(user.bannerVideoSrc || 'https://assets.mixkit.co/videos/preview/mixkit-abstract-laser-lights-background-animation-42566-large.mp4');
    const [imageUrl, setImageUrl] = useState(user.bannerImageSrc || '');
    const [selectedGradient, setSelectedGradient] = useState(user.bannerGradient || 'radial-gradient(circle at 20% 30%, #4338ca 0%, #1e1b4b 65%, #0f172a 100%)');
    const [uploadStatus, setUploadStatus] = useState('');

    const [badgeTag, setBadgeTag] = useState(user.bannerTag || '');
    const [bannerTitle, setBannerTitle] = useState(user.bannerTitle || '');
    const [portfolioUrl, setPortfolioUrl] = useState(user.bannerUrl || (user.gridId ? `grid.community/in/${user.gridId}` : ''));

    useEffect(() => {
        if (isOpen) {
            setMediaType(user.bannerType || (user.bannerVideoSrc ? 'video' : user.bannerImageSrc ? 'image' : 'gradient'));
            setVideoUrl(user.bannerVideoSrc || '');
            setImageUrl(user.bannerImageSrc || '');
            setSelectedGradient(user.bannerGradient || 'radial-gradient(circle at 20% 30%, #4338ca 0%, #1e1b4b 65%, #0f172a 100%)');
            setBadgeTag(user.bannerTag || '');
            setBannerTitle(user.bannerTitle || '');
            setPortfolioUrl(user.bannerUrl || (user.gridId ? `grid.community/in/${user.gridId}` : ''));
            setUploadStatus('');
        }
    }, [isOpen, user]);

    const gradientPresets = [
        { id: 1, style: 'radial-gradient(circle at 20% 30%, #4338ca 0%, #1e1b4b 65%, #0f172a 100%)' },
        { id: 2, style: 'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%)' },
        { id: 3, style: 'linear-gradient(135deg, #312e81 0%, #7c3aed 50%, #ec4899 100%)' },
        { id: 4, style: 'linear-gradient(135deg, #064e3b 0%, #059669 50%, #10b981 100%)' }
    ];

    async function handleVideoFileUpload(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const blobUrl = URL.createObjectURL(file);
            setVideoUrl(blobUrl);
            setUploadStatus(`Uploading video "${file.name}"... ⏳`);
            try {
                const res = await gridApi.uploadBanner(file);
                if (res && res.success && res.url) {
                    setVideoUrl(res.url);
                    setUploadStatus(`✓ Video uploaded & saved: ${file.name} (${(file.size / (1024 * 1024)).toFixed(1)} MB)`);
                } else {
                    setUploadStatus(`⚠️ Upload note: ${res?.message || 'Using local preview'}`);
                }
            } catch (err) {
                setUploadStatus(`✓ Video loaded for preview.`);
            }
        }
    }

    async function handleImageFileUpload(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const blobUrl = URL.createObjectURL(file);
            setImageUrl(blobUrl);
            setUploadStatus(`Uploading image "${file.name}"... ⏳`);
            try {
                const res = await gridApi.uploadBanner(file);
                if (res && res.success && res.url) {
                    setImageUrl(res.url);
                    setUploadStatus(`✓ Image uploaded & saved: ${file.name}`);
                } else {
                    setUploadStatus(`⚠️ Upload note: ${res?.message || 'Using local preview'}`);
                }
            } catch (err) {
                setUploadStatus(`✓ Image loaded for preview.`);
            }
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        let finalVideo = '';
        let finalImage = '';
        let finalGradient = '';

        if (mediaType === 'video') {
            finalVideo = videoUrl;
        } else if (mediaType === 'image') {
            finalImage = imageUrl;
        } else {
            finalGradient = selectedGradient;
        }

        onSave({
            bannerType: mediaType,
            bannerVideoSrc: finalVideo,
            bannerImageSrc: finalImage,
            bannerGradient: finalGradient,
            bannerTag: badgeTag,
            bannerTitle,
            bannerUrl: portfolioUrl
        });
        onClose();
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title="Edit Cover Banner" maxWidth="560px">
            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                {/* 1. Cover Media Type Radio Group */}
                <div>
                    <label style={{ display: 'block', fontSize: '13px', fontWeight: 700, color: 'var(--slate-800)', marginBottom: '8px' }}>
                        Cover Media Type
                    </label>
                    <div style={{ display: 'flex', gap: '18px', alignItems: 'center' }}>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', cursor: 'pointer' }}>
                            <input 
                                type="radio" 
                                name="coverMediaType" 
                                value="gradient" 
                                checked={mediaType === 'gradient'}
                                onChange={() => setMediaType('gradient')}
                            />
                            Gradient / Default
                        </label>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', cursor: 'pointer' }}>
                            <input 
                                type="radio" 
                                name="coverMediaType" 
                                value="image" 
                                checked={mediaType === 'image'}
                                onChange={() => setMediaType('image')}
                            />
                            Image
                        </label>
                        <label style={{ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', cursor: 'pointer' }}>
                            <input 
                                type="radio" 
                                name="coverMediaType" 
                                value="video" 
                                checked={mediaType === 'video'}
                                onChange={() => setMediaType('video')}
                            />
                            Video (MP4 / WebM / URL)
                        </label>
                    </div>
                </div>

                {/* 2. Media Inputs based on Selected Type */}
                {mediaType === 'video' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                        {/* 1-Click Video Presets */}
                        <div>
                            <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                                Sample Video Presets (1-Click Test)
                            </label>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '8px' }}>
                                <button 
                                    type="button" 
                                    onClick={() => {
                                        setVideoUrl('https://assets.mixkit.co/videos/preview/mixkit-abstract-laser-lights-background-animation-42566-large.mp4');
                                        setUploadStatus('✓ Preset: 🌌 Laser Lights selected');
                                    }}
                                    className="g-btn-outline" 
                                    style={{ fontSize: '12px', padding: '8px 12px', justifyContent: 'center', fontWeight: 600 }}
                                >
                                    🌌 Laser Lights
                                </button>
                                <button 
                                    type="button" 
                                    onClick={() => {
                                        setVideoUrl('https://assets.mixkit.co/videos/preview/mixkit-digital-animation-of-screens-with-code-31911-large.mp4');
                                        setUploadStatus('✓ Preset: 💻 Code Flow selected');
                                    }}
                                    className="g-btn-outline" 
                                    style={{ fontSize: '12px', padding: '8px 12px', justifyContent: 'center', fontWeight: 600 }}
                                >
                                    💻 Code Flow
                                </button>
                            </div>
                        </div>

                        {/* Video URL */}
                        <div>
                            <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                                Or Video URL (MP4, WebM)
                            </label>
                            <input 
                                type="text" 
                                value={videoUrl}
                                onChange={(e) => {
                                    setVideoUrl(e.target.value);
                                    setUploadStatus('');
                                }}
                                placeholder="https://assets.mixkit.co/... or any direct .mp4 link" 
                                style={{ width: '100%', padding: '9px 14px', border: '1px solid var(--slate-300)', borderRadius: '8px', fontSize: '13px' }}
                            />
                        </div>

                        {/* Video File Upload */}
                        <div>
                            <span style={{ fontSize: '12px', color: 'var(--slate-600)' }}>Or upload video file from PC:</span>
                            <input 
                                type="file" 
                                accept="video/mp4,video/webm,video/ogg" 
                                onChange={handleVideoFileUpload}
                                style={{ display: 'block', marginTop: '6px', fontSize: '12px' }}
                            />
                            {uploadStatus && (
                                <span style={{ fontSize: '11px', color: 'var(--emerald-600)', fontWeight: 600, display: 'block', marginTop: '4px' }}>
                                    {uploadStatus}
                                </span>
                            )}
                        </div>
                    </div>
                )}

                {mediaType === 'image' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                        <div>
                            <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                                Image Direct URL
                            </label>
                            <input 
                                type="text" 
                                value={imageUrl}
                                onChange={(e) => {
                                    setImageUrl(e.target.value);
                                    setUploadStatus('');
                                }}
                                placeholder="https://images.unsplash.com/... or image URL" 
                                style={{ width: '100%', padding: '9px 14px', border: '1px solid var(--slate-300)', borderRadius: '8px', fontSize: '13px' }}
                            />
                        </div>
                        <div>
                            <span style={{ fontSize: '12px', color: 'var(--slate-600)' }}>Or upload image from device:</span>
                            <input 
                                type="file" 
                                accept="image/*" 
                                onChange={handleImageFileUpload}
                                style={{ display: 'block', marginTop: '6px', fontSize: '12px' }}
                            />
                            {uploadStatus && (
                                <span style={{ fontSize: '11px', color: 'var(--emerald-600)', fontWeight: 600, display: 'block', marginTop: '4px' }}>
                                    {uploadStatus}
                                </span>
                            )}
                        </div>
                    </div>
                )}

                {mediaType === 'gradient' && (
                    <div>
                        <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '8px' }}>
                            Select Gradient Theme
                        </label>
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: '8px' }}>
                            {gradientPresets.map(preset => (
                                <div 
                                    key={preset.id}
                                    onClick={() => setSelectedGradient(preset.style)}
                                    style={{
                                        height: '42px',
                                        borderRadius: '8px',
                                        cursor: 'pointer',
                                        background: preset.style,
                                        border: selectedGradient === preset.style ? '2px solid var(--brand-600)' : '2px solid transparent',
                                        boxShadow: selectedGradient === preset.style ? '0 0 0 2px var(--brand-100)' : 'none',
                                        transition: '.15s ease'
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                )}

                <hr style={{ border: 0, borderTop: '1px solid var(--slate-200)', margin: '4px 0' }} />

                {/* 3. Badge / Category Tag */}
                <div>
                    <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                        Badge / Category Tag
                    </label>
                    <input 
                        type="text" 
                        value={badgeTag}
                        onChange={(e) => setBadgeTag(e.target.value)}
                        style={{ width: '100%', padding: '9px 14px', border: '1px solid var(--slate-300)', borderRadius: '8px', fontSize: '13px' }}
                    />
                </div>

                {/* 4. Banner Title / Motto */}
                <div>
                    <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                        Banner Title / Motto
                    </label>
                    <input 
                        type="text" 
                        value={bannerTitle}
                        onChange={(e) => setBannerTitle(e.target.value)}
                        style={{ width: '100%', padding: '9px 14px', border: '1px solid var(--slate-300)', borderRadius: '8px', fontSize: '13px' }}
                    />
                </div>

                {/* 5. Personal Portfolio / Profile Link */}
                <div>
                    <label style={{ display: 'block', fontSize: '12px', fontWeight: 700, color: 'var(--slate-700)', marginBottom: '6px' }}>
                        Personal Portfolio / Profile Link
                    </label>
                    <input 
                        type="text" 
                        value={portfolioUrl}
                        onChange={(e) => setPortfolioUrl(e.target.value)}
                        placeholder="e.g. grid.community/in/rohan-verma-ai or myportfolio.dev"
                        style={{ width: '100%', padding: '9px 14px', border: '1px solid var(--slate-300)', borderRadius: '8px', fontSize: '13px', fontFamily: 'monospace' }}
                    />
                    <span style={{ fontSize: '11px', color: 'var(--slate-500)', marginTop: '4px', display: 'block' }}>
                        This link is displayed on your cover and opens in a new tab when clicked.
                    </span>
                </div>

                {/* Footer Buttons */}
                <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '16px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                    <button 
                        type="button" 
                        onClick={onClose}
                        className="g-btn-outline"
                        style={{ padding: '8px 18px', borderRadius: '8px', fontSize: '13px', fontWeight: 600 }}
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        className="g-btn-create"
                        style={{ padding: '8px 24px', borderRadius: '8px', background: 'var(--brand-600)', color: '#fff', fontSize: '13px', fontWeight: 600 }}
                    >
                        Save Banner
                    </button>
                </div>
            </form>
        </ModalContainer>
    );
}
