import React, { useState } from 'react';

/**
 * EasyApplyModal Component
 * Interactive modal for viewing job details and submitting an Easy Apply application.
 */
export default function EasyApplyModal({
    job,
    userProfile,
    isOpen,
    onClose,
    onSubmitApplication
}) {
    const [step, setStep] = useState(1);
    const [phone, setPhone] = useState('+91 98765 43210');
    const [notes, setNotes] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    if (!isOpen || !job) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setTimeout(() => {
            setIsSubmitting(false);
            onSubmitApplication && onSubmitApplication(job.id);
            setStep(1);
            onClose();
        }, 600);
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                {/* Modal Header */}
                <div className="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div
                            className="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-white shadow-xs shrink-0"
                            style={{ backgroundColor: job.logoBg || '#2563eb' }}
                        >
                            {job.logoText || job.company?.slice(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <h3 className="text-base font-bold text-slate-900 dark:text-white truncate">
                                Easy Apply to {job.company}
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400">
                                {job.title}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="p-1.5 rounded-full text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* Modal Form Body */}
                <form onSubmit={handleSubmit} className="p-5 overflow-y-auto flex-1 space-y-4 text-xs sm:text-sm">
                    {step === 1 ? (
                        <>
                            <div className="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <h4 className="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-2">
                                    Contact Information
                                </h4>
                                <div className="space-y-2">
                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Full Name</label>
                                        <input
                                            type="text"
                                            readOnly
                                            value={userProfile?.name || 'K. Pavan Kumar'}
                                            className="w-full mt-1 p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Email Address</label>
                                        <input
                                            type="email"
                                            readOnly
                                            value="pavankumar@grid.network"
                                            className="w-full mt-1 p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Mobile Phone</label>
                                        <input
                                            type="tel"
                                            value={phone}
                                            onChange={(e) => setPhone(e.target.value)}
                                            className="w-full mt-1 p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 outline-hidden"
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Job Requirements Snapshot */}
                            <div>
                                <h4 className="font-bold text-slate-900 dark:text-white mb-1.5">Required Skills:</h4>
                                <div className="flex flex-wrap gap-1.5">
                                    {(job.skills || ['Python', 'Problem Solving']).map((sk, idx) => (
                                        <span key={idx} className="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-bold border border-blue-200 dark:border-blue-800">
                                            ✓ {sk}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </>
                    ) : (
                        <>
                            {/* Step 2: Resume & Note */}
                            <div className="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                <h4 className="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-2">
                                    Resume & Portfolio
                                </h4>
                                <div className="flex items-center justify-between p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div className="flex items-center gap-2.5">
                                        <span className="text-xl">📄</span>
                                        <div>
                                            <p className="font-bold text-slate-900 dark:text-white text-xs">Pavan_Kumar_Software_Resume.pdf</p>
                                            <p className="text-[10px] text-slate-400">Uploaded from Verified GRID Profile</p>
                                        </div>
                                    </div>
                                    <span className="text-xs text-emerald-600 dark:text-emerald-400 font-bold">Attached ✓</span>
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Message to Hiring Manager (Optional)
                                </label>
                                <textarea
                                    rows={3}
                                    placeholder="Highlight why your skills make you a strong fit for this role..."
                                    value={notes}
                                    onChange={(e) => setNotes(e.target.value)}
                                    className="w-full p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 outline-hidden"
                                />
                            </div>
                        </>
                    )}

                    {/* Footer Actions */}
                    <div className="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        {step === 2 ? (
                            <button
                                type="button"
                                onClick={() => setStep(1)}
                                className="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
                            >
                                Back
                            </button>
                        ) : (
                            <div />
                        )}

                        {step === 1 ? (
                            <button
                                type="button"
                                onClick={() => setStep(2)}
                                className="px-5 py-2 text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors cursor-pointer"
                            >
                                Next Step &rarr;
                            </button>
                        ) : (
                            <button
                                type="submit"
                                disabled={isSubmitting}
                                className="px-6 py-2 text-xs sm:text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-full transition-colors cursor-pointer flex items-center gap-2"
                            >
                                {isSubmitting ? 'Submitting...' : 'Submit Application 🚀'}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
}
