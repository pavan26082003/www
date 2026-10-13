import React, { useState } from 'react';
import ModalContainer from './ModalContainer.jsx';

/**
 * EditAboutModal Component
 * 
 * Edit about narrative and comma-separated technical arsenal chips.
 */
export default function EditAboutModal({
    isOpen,
    onClose,
    about = {},
    onSave
}) {
    const getText = () => {
        if (about.text) return about.text;
        if (about.paragraphs && about.paragraphs.length > 0) return about.paragraphs.join('\n\n');
        return '';
    };

    const getSkills = () => (about.skills || []).join(', ');

    const [narrative, setNarrative] = useState(getText);
    const [skillsString, setSkillsString] = useState(getSkills);

    React.useEffect(() => {
        if (isOpen) {
            setNarrative(getText());
            setSkillsString(getSkills());
        }
    }, [isOpen, about]);

    function handleSubmit(e) {
        e.preventDefault();
        const paragraphs = narrative.split('\n\n').map(p => p.trim()).filter(Boolean);
        const skills = skillsString.split(',').map(s => s.trim()).filter(Boolean);

        onSave({
            text: narrative,
            paragraphs,
            bullets: [], // clear legacy fallback bullets so they don't append beneath
            closing: '', // clear legacy fallback closing
            skills
        });
        onClose();
    }

    return (
        <ModalContainer isOpen={isOpen} onClose={onClose} title="Edit About Summary" maxWidth="560px">
            <form onSubmit={handleSubmit}>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Summary / Narrative (Separate paragraphs with double enter)
                        </label>
                        <textarea 
                            rows="7"
                            value={narrative}
                            placeholder="Write an overview of your professional background, current focus areas, and technical interests..."
                            onChange={(e) => setNarrative(e.target.value)}
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '13px', resize: 'vertical', lineHeight: 1.5 }}
                        />
                    </div>

                    <div>
                        <label style={{ display: 'block', fontSize: '13px', fontWeight: 600, color: 'var(--slate-800)', marginBottom: '6px' }}>
                            Core Technical Arsenal (Comma-separated)
                        </label>
                        <input 
                            type="text"
                            value={skillsString}
                            onChange={(e) => setSkillsString(e.target.value)}
                            placeholder="PyTorch, DPO, FastAPI, AWS, Docker"
                            style={{ width: '100%', padding: '10px 14px', borderRadius: '8px', border: '1px solid var(--slate-300)', fontSize: '14px' }}
                        />
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
                        Save About
                    </button>
                </div>
            </form>
        </ModalContainer>
    );
}
