import React, { useState, useRef } from 'react';
import gridApi from '../../gridApi.js';

/**
 * PostJobModal Component
 * Allows recruiters & hiring managers to publish new roles directly to the GRID Jobs Hub.
 */
export default function PostJobModal({ isOpen, onClose, onJobCreated, onToast }) {
    if (!isOpen) return null;

    const [title, setTitle] = useState('');
    const [companyName, setCompanyName] = useState('');
    const [discipline, setDiscipline] = useState('Engineering');
    const [workplaceType, setWorkplaceType] = useState('remote');
    const [location, setLocation] = useState('');
    const [salaryRange, setSalaryRange] = useState('');
    const [description, setDescription] = useState('');
    
    // Skills tags
    const [skills, setSkills] = useState(['Python', 'React.js']);
    const [skillInput, setSkillInput] = useState('');
    const [skillSuggestions, setSkillSuggestions] = useState([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const searchTimeoutRef = useRef(null);

    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errorMsg, setErrorMsg] = useState('');

    const disciplines = [
        'Engineering',
        'AI & Data',
        'Product & Design',
        'Cloud & DevOps',
        'Marketing & Growth',
        'Sales & Business',
        'Operations & Finance'
    ];

    const workplaceOptions = [
        { value: 'remote', label: '🌐 Remote' },
        { value: 'hybrid', label: '🏢 Hybrid' },
        { value: 'onsite', label: '📍 On-site' }
    ];

    const handleSkillInputChange = (val) => {
        setSkillInput(val);
        if (searchTimeoutRef.current) clearTimeout(searchTimeoutRef.current);

        if (val.trim().length >= 1) {
            searchTimeoutRef.current = setTimeout(async () => {
                const results = await gridApi.suggestSkills(val.trim());
                if (Array.isArray(results) && results.length > 0) {
                    setSkillSuggestions(results.filter(r => !skills.includes(r.name)));
                    setShowSuggestions(true);
                } else {
                    setSkillSuggestions([]);
                    setShowSuggestions(false);
                }
            }, 180);
        } else {
            setSkillSuggestions([]);
            setShowSuggestions(false);
        }
    };

    const addSkill = (skillName) => {
        const trimmed = skillName.trim();
        if (trimmed && !skills.includes(trimmed) && skills.length < 10) {
            setSkills([...skills, trimmed]);
        }
        setSkillInput('');
        setSkillSuggestions([]);
        setShowSuggestions(false);
    };

    const removeSkill = (skillToRemove) => {
        setSkills(skills.filter(s => s !== skillToRemove));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrorMsg('');

        if (!title.trim()) {
            setErrorMsg('Job title is required.');
            return;
        }
        if (!companyName.trim()) {
            setErrorMsg('Company name is required.');
            return;
        }
        if (!location.trim()) {
            setErrorMsg('Location is required (e.g. San Francisco or Remote).');
            return;
        }

        setIsSubmitting(true);
        try {
            const payload = {
                title: title.trim(),
                company_name: companyName.trim(),
                discipline,
                workplace_type: workplaceType,
                location: location.trim(),
                salary_range: salaryRange.trim(),
                description: description.trim(),
                skills_required: skills
            };

            const res = await gridApi.createJob(payload);
            if (res && res.success) {
                onToast && onToast('Job listing published successfully! 🚀');
                onJobCreated && onJobCreated(res.data || payload);
                onClose();
            } else {
                setErrorMsg(res?.message || 'Failed to publish job. Please try again.');
            }
        } catch (err) {
            setErrorMsg(err.message || 'An unexpected error occurred.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[160] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 animate-fadeIn">
            <div className="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                
                {/* Header */}
                <div className="p-5 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-purple-500/20">
                            💼
                        </div>
                        <div>
                            <h2 className="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">
                                Post a New Job Opportunity
                            </h2>
                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                Reach top verified engineering &amp; AI talent across the GRID network.
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                {/* Form Body */}
                <form onSubmit={handleSubmit} className="p-5 sm:p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    {errorMsg && (
                        <div className="p-3 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900 rounded-xl text-rose-700 dark:text-rose-300 text-xs font-semibold">
                            ⚠️ {errorMsg}
                        </div>
                    )}

                    {/* Job Title & Company */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Job Title *
                            </label>
                            <input
                                type="text"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                placeholder="e.g. Senior Machine Learning Engineer"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Company Name *
                            </label>
                            <input
                                type="text"
                                value={companyName}
                                onChange={(e) => setCompanyName(e.target.value)}
                                placeholder="e.g. Acme AI Labs"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                            />
                        </div>
                    </div>

                    {/* Discipline & Workplace Type */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Discipline / Domain
                            </label>
                            <select
                                value={discipline}
                                onChange={(e) => setDiscipline(e.target.value)}
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500"
                            >
                                {disciplines.map((d) => (
                                    <option key={d} value={d}>{d}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Workplace Setup
                            </label>
                            <div className="grid grid-cols-3 gap-2">
                                {workplaceOptions.map((opt) => (
                                    <button
                                        key={opt.value}
                                        type="button"
                                        onClick={() => setWorkplaceType(opt.value)}
                                        className={`py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer ${
                                            workplaceType === opt.value
                                                ? 'bg-purple-600 text-white border-purple-600 shadow-xs'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-300'
                                        }`}
                                    >
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Location & Compensation */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Location *
                            </label>
                            <input
                                type="text"
                                value={location}
                                onChange={(e) => setLocation(e.target.value)}
                                placeholder="e.g. San Francisco, CA / Bengaluru"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                                Salary / Equity Range
                            </label>
                            <input
                                type="text"
                                value={salaryRange}
                                onChange={(e) => setSalaryRange(e.target.value)}
                                placeholder="e.g. $150k - $190k • 0.1% Equity"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                            />
                        </div>
                    </div>

                    {/* Skills Required */}
                    <div>
                        <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                            Skills Required ({skills.length}/10)
                        </label>
                        <div className="relative">
                            <input
                                type="text"
                                value={skillInput}
                                onChange={(e) => handleSkillInputChange(e.target.value)}
                                onKeyDown={(e) => {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        if (skillInput.trim()) addSkill(skillInput.trim());
                                    }
                                }}
                                placeholder="Type skill name and press Enter..."
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500"
                            />

                            {showSuggestions && skillSuggestions.length > 0 && (
                                <div className="absolute z-20 top-full left-0 right-0 mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-h-40 overflow-y-auto">
                                    {skillSuggestions.map((s) => (
                                        <div
                                            key={s.id || s.name}
                                            onClick={() => addSkill(s.name)}
                                            className="px-3.5 py-2 text-xs hover:bg-purple-50 dark:hover:bg-purple-950/50 cursor-pointer flex items-center justify-between text-slate-800 dark:text-slate-200"
                                        >
                                            <span className="font-semibold">{s.name}</span>
                                            <span className="text-[10px] text-slate-400">{s.category}</span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Selected Skills Chips */}
                        <div className="flex flex-wrap gap-1.5 mt-2">
                            {skills.map((s) => (
                                <span
                                    key={s}
                                    className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold text-xs border border-purple-200 dark:border-purple-900/50"
                                >
                                    {s}
                                    <button
                                        type="button"
                                        onClick={() => removeSkill(s)}
                                        className="hover:text-purple-950 dark:hover:text-white cursor-pointer"
                                    >
                                        ×
                                    </button>
                                </span>
                            ))}
                        </div>
                    </div>

                    {/* Description */}
                    <div>
                        <label className="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">
                            Role Description &amp; Responsibilities
                        </label>
                        <textarea
                            rows={4}
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            placeholder="Provide details about the mission, responsibilities, technical challenges, and growth opportunities..."
                            className="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 leading-relaxed"
                        />
                    </div>

                    {/* Modal Footer Actions */}
                    <div className="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={isSubmitting}
                            className="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-purple-500/25 transition-all cursor-pointer disabled:opacity-50"
                        >
                            {isSubmitting ? 'Publishing Role...' : 'Publish Job Listing 🚀'}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    );
}
