import React, { useState } from 'react';

export default function VisitingCardPanel({
    conversation,
    isOpen = true,
    onClose,
}) {
    const [selectedLevel, setSelectedLevel] = useState(2);
    const [copied, setCopied] = useState(false);

    if (!isOpen || !conversation) return null;

    const handleShare = () => {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    return (
        <>
            {/* Mobile backdrop overlay */}
            <div 
                className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 xl:hidden"
                onClick={onClose}
                aria-hidden="true"
            />
            <aside className="g-msg-visiting-card-pane fixed inset-y-0 right-0 z-50 w-[320px] max-w-[85vw] shadow-2xl xl:shadow-none xl:static xl:w-80 shrink-0 border-l border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 overflow-y-auto flex flex-col">
            {/* Panel Header */}
            <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                    <svg className="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <h3 className="text-xs font-bold text-slate-900 dark:text-slate-100">
                        {(conversation?.name || 'Member').split(' ')[0]}'s Visiting Card
                    </h3>
                </div>
                {onClose && (
                    <button 
                        type="button" 
                        onClick={onClose}
                        className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded"
                        title="Close panel"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                )}
            </div>

            {/* Interactive Visiting Card QR Container */}
            <div className="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col items-center text-center shadow-2xs">
                {/* SVG QR Matrix */}
                <div className="p-3 bg-white dark:bg-slate-900 rounded-xl shadow-xs border border-slate-200/80 dark:border-slate-700 mb-3">
                    <svg className="w-36 h-36" fill="none" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                        {/* Corner 1 */}
                        <rect x="6" y="6" width="30" height="30" rx="6" fill="#1E3A8A" />
                        <rect x="12" y="12" width="18" height="18" rx="2" fill="white" />
                        <rect x="16" y="16" width="10" height="10" rx="2" fill="#1E3A8A" />
                        {/* Corner 2 */}
                        <rect x="84" y="6" width="30" height="30" rx="6" fill="#1E3A8A" />
                        <rect x="90" y="12" width="18" height="18" rx="2" fill="white" />
                        <rect x="94" y="16" width="10" height="10" rx="2" fill="#1E3A8A" />
                        {/* Corner 3 */}
                        <rect x="6" y="84" width="30" height="30" rx="6" fill="#1E3A8A" />
                        <rect x="12" y="90" width="18" height="18" rx="2" fill="white" />
                        <rect x="16" y="94" width="10" height="10" rx="2" fill="#1E3A8A" />
                        {/* Center Matrix Dots */}
                        <rect x="46" y="10" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="64" y="10" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="46" y="26" width="8" height="8" rx="2" fill="#3B82F6" />
                        <rect x="64" y="26" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="10" y="46" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="26" y="46" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="46" y="46" width="10" height="10" rx="2" fill="#1D4ED8" />
                        <rect x="64" y="46" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="84" y="46" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="102" y="46" width="8" height="8" rx="2" fill="#3B82F6" />
                        <rect x="46" y="64" width="8" height="8" rx="2" fill="#3B82F6" />
                        <rect x="64" y="64" width="10" height="10" rx="2" fill="#1E3A8A" />
                        <rect x="84" y="64" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="10" y="64" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="46" y="84" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="64" y="84" width="8" height="8" rx="2" fill="#1D4ED8" />
                        <rect x="84" y="84" width="8" height="8" rx="2" fill="#2563EB" />
                        <rect x="102" y="84" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="46" y="102" width="8" height="8" rx="2" fill="#1E3A8A" />
                        <rect x="64" y="102" width="8" height="8" rx="2" fill="#3B82F6" />
                        <rect x="84" y="102" width="8" height="8" rx="2" fill="#2563EB" />
                    </svg>
                </div>

                <span className="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono text-[10px] rounded font-semibold mb-2">
                    {conversation.cardId || 'CARD ID #GRID-ER-9042'}
                </span>

                <p className="text-[11px] text-slate-500 dark:text-slate-400 mb-3 leading-snug">
                    {conversation.cardDescription || 'Scan to inspect verified credentials, hardware pipelines & live shader benchmarks.'}
                </p>

                <div className="grid grid-cols-2 gap-2 w-full">
                    <button 
                        type="button" 
                        onClick={() => {}}
                        className="py-1.5 px-2 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg flex items-center justify-center gap-1 transition-colors"
                    >
                        <svg className="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Save PNG</span>
                    </button>
                    <button 
                        type="button" 
                        onClick={handleShare}
                        className="py-1.5 px-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg flex items-center justify-center gap-1 shadow-2xs transition-colors"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span>{copied ? 'Copied!' : 'Share Card'}</span>
                    </button>
                </div>
            </div>

            {/* Permission Levels Block */}
            <div className="mt-5">
                <div className="flex items-center justify-between mb-3">
                    <h4 className="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                        <svg className="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Permission Levels</span>
                    </h4>
                    <span className="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800/40">
                        Level {selectedLevel} Active
                    </span>
                </div>

                <div className="space-y-2">
                    {/* Level 1 */}
                    <div 
                        onClick={() => setSelectedLevel(1)}
                        className={`p-2.5 rounded-xl border cursor-pointer flex items-center justify-between text-xs transition-all ${
                            selectedLevel === 1 
                                ? 'border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/40 font-medium' 
                                : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-100/60'
                        }`}
                    >
                        <div className="flex items-center gap-2">
                            <span className={`w-2 h-2 rounded-full ${selectedLevel === 1 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-600'}`} />
                            <span className={selectedLevel === 1 ? 'text-blue-900 dark:text-blue-200' : 'text-slate-600 dark:text-slate-400'}>
                                Public • Level 1
                            </span>
                        </div>
                        <span className="text-[10px] text-slate-400">Read only</span>
                    </div>

                    {/* Level 2 (Selected by default) */}
                    <div 
                        onClick={() => setSelectedLevel(2)}
                        className={`p-2.5 rounded-xl border cursor-pointer flex items-center justify-between text-xs transition-all ${
                            selectedLevel === 2 
                                ? 'border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/40 font-medium' 
                                : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-100/60'
                        }`}
                    >
                        <div className="flex items-center gap-2">
                            <span className={`w-2 h-2 rounded-full ${selectedLevel === 2 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-600'}`} />
                            <span className={selectedLevel === 2 ? 'text-blue-900 dark:text-blue-200 font-semibold' : 'text-slate-600 dark:text-slate-400'}>
                                eFactor + Connections • Level 2
                            </span>
                        </div>
                        <span className="text-[10px] text-blue-600 dark:text-blue-400 font-bold">Enabled</span>
                    </div>

                    {/* Level 3 */}
                    <div 
                        onClick={() => setSelectedLevel(3)}
                        className={`p-2.5 rounded-xl border cursor-pointer flex items-center justify-between text-xs transition-all ${
                            selectedLevel === 3 
                                ? 'border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-950/40 font-medium' 
                                : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-slate-100/60'
                        }`}
                    >
                        <div className="flex items-center gap-2">
                            <span className={`w-2 h-2 rounded-full ${selectedLevel === 3 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-600'}`} />
                            <span className={selectedLevel === 3 ? 'text-blue-900 dark:text-blue-200' : 'text-slate-600 dark:text-slate-400'}>
                                Guest / QR Scan • Level 3
                            </span>
                        </div>
                        <span className="text-[10px] text-slate-400">Visiting Card</span>
                    </div>
                </div>
            </div>

            {/* Mutual Connections */}
            {conversation.mutualCount > 0 && (
                <div className="mt-5 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <h4 className="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2">
                        Mutual Connections ({conversation.mutualCount})
                    </h4>
                    <div className="flex items-center gap-1">
                        <div className="flex -space-x-2 overflow-hidden">
                            <img 
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmLay1Vc7J9U8g_b0GDitvM8F9kEE44Tt-DnlcsrD5rR8HBOwlr1xqg2ZPKh6LbSg2fszrsxNmGmaQQB20j9DMX2wfiJT7lChqOVbqgg-v7cJzxyxBbZwPLaScbfdKGTzAaC6-h6t-dY0yFjJIERT6-250kwizy9cJzoKEAN4NIH0uvzK8Lso-LmuMJsWm8WSYhxAoGVbG492WaO--jGijVXbdEBsUZryI7w43pwhVU248LrljtIYQ" 
                                alt="Mutual connection" 
                                className="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-slate-900" 
                                onError={(e) => {
                                    e.currentTarget.onerror = null;
                                    e.currentTarget.src = '/assets/images/avatar.jpg';
                                }}
                            />
                            <img 
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoXmW3jICWuIEsBNNVsJu07wYyknhhgnD2B8CjdWB3EQTFCjNMxGGihxrnRLjljHGvpn-FekfIqzajKWWVTtvlnq8ozyAIqD4pVNyWTOyAKtVnUonGs-Ns4CL8xpz9u0rQzpGruzkgUwHoCHM6dTY3dCShWAtdEXl-mnUL72PKIfQnhV9srHIghYGRQaYON9deK4Jrraesv6PqecZ_4z7wMC_TJ4ZBw_q7lC9VgmTY8QhWPm_uluo5" 
                                alt="Mutual connection" 
                                className="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-slate-900" 
                                onError={(e) => {
                                    e.currentTarget.onerror = null;
                                    e.currentTarget.src = '/assets/images/avatar.jpg';
                                }}
                            />
                            <img 
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBEDBE9FEdMHO7NmFMjOTBnlipXJnkmb7ehRpbsaam5j_DTIjRT4XeMdy7dkmPx54ihr3EngPL3FjIzDJbcTAArjbwSA6xJoKIG6jQx3b2bsKvh6KxTBSkUp5zkskFk-cW6bizi_KQuNw_ew3hPxsGEeuDJRWh6OQ0yRp6OIydCNPj7NkXrOzO_OqK4cXMlWD-nSwVV60gLXfVo-sevXvky3ZwIEHztRxc9satD4ZnAnNFRAbYd7pN3" 
                                alt="Mutual connection" 
                                className="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-slate-900" 
                                onError={(e) => {
                                    e.currentTarget.onerror = null;
                                    e.currentTarget.src = '/assets/images/avatar.jpg';
                                }}
                            />
                        </div>
                        <span className="text-xs text-slate-500 dark:text-slate-400 ml-2 truncate">
                            {conversation.mutualNames}
                        </span>
                    </div>
                </div>
            )}

            {/* Footer Policy Note */}
            <div className="mt-auto pt-6 text-[10px] text-slate-400 dark:text-slate-500 text-center leading-relaxed">
                Visiting card and message permissions dynamically sync based on eFactor reputation.
            </div>
        </aside>
        </>
    );
}
