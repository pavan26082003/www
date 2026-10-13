import React, { useState } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * CreatePostModal Component
 * 
 * Create a new feed post with title, body narrative, and attachment.
 */
export default function CreatePostModal({
    isOpen,
    onClose,
    authorName = 'Member',
    authorRole = 'Community Member',
    onPublish
}) {
    const [title, setTitle] = useState('');
    const [body, setBody] = useState('');
    const [attachmentName, setAttachmentName] = useState('');
    const [attachmentDesc, setAttachmentDesc] = useState('');

    const authorInitials = authorName && authorName !== 'Member'
        ? authorName.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
        : 'U';

    function handleSubmit(e) {
        e.preventDefault();
        onPublish({
            id: `post-${Date.now()}`,
            authorName,
            authorInitials,
            authorRole,
            timeAgo: 'Just now',
            title,
            body,
            attachmentName,
            attachmentDesc,
            likesCount: 0,
            commentsCount: 0
        });
        setTitle('');
        setBody('');
        setAttachmentName('');
        setAttachmentDesc('');
        onClose();
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title="Create a Post" maxWidth="560px">
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Post Title
                        </label>
                        <input 
                            type="text" 
                            placeholder="What do you want to talk about?"
                            value={title} 
                            onChange={(e) => setTitle(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Post Content *
                        </label>
                        <textarea 
                            rows="4"
                            required
                            placeholder="Share an insight, research paper breakdown, or milestone..."
                            value={body} 
                            onChange={(e) => setBody(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical' }}
                        />
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }}>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Attachment Name (Optional)
                            </label>
                            <input 
                                type="text" 
                                placeholder="e.g. DPO_Research_Slides.pdf"
                                value={attachmentName} 
                                onChange={(e) => setAttachmentName(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
                        <div>
                            <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                                Attachment Subtitle
                            </label>
                            <input 
                                type="text" 
                                placeholder="18 pages • Presentation deck"
                                value={attachmentDesc} 
                                onChange={(e) => setAttachmentDesc(e.target.value)}
                                style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                            />
                        </div>
                    </div>
                </div>

                <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--slate-200)' }}>
                    <button 
                        type="button" 
                        onClick={onClose}
                        style={{ padding: '8px 18px', borderRadius: '9999px', border: '1px solid var(--slate-300)', background: '#fff', fontSize: '13px', fontWeight: 600, cursor: 'pointer' }}
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        className="g-btn-create"
                        style={{ padding: '8px 22px' }}
                    >
                        Post
                    </button>
                </div>
            </form>
        </ModalContainer>
    );
}
