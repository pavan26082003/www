import React, { useState, useRef, useEffect } from 'react';
import VerifiedBadge from '../ui/VerifiedBadge.jsx';

export default function ActiveChatPane({
    conversation,
    currentUser,
    onSendMessage,
    isVisitingCardOpen,
    onToggleVisitingCard,
    onBackToList,
    className = '',
}) {
    const [messageInput, setMessageInput] = useState('');
    const [isStarred, setIsStarred] = useState(false);
    const messagesEndRef = useRef(null);
    const streamRef = useRef(null);

    const scrollToBottom = () => {
        if (streamRef.current) {
            streamRef.current.scrollTop = streamRef.current.scrollHeight;
        }
    };

    useEffect(() => {
        scrollToBottom();
    }, [conversation?.messages, conversation?.id]);

    if (!conversation) {
        return (
            <section className={`flex-1 flex flex-col items-center justify-center bg-white dark:bg-slate-900 min-w-0 p-8 text-center text-slate-400 ${className}`}>
                <svg className="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p className="text-sm font-semibold text-slate-700 dark:text-slate-300">Select a conversation to start messaging</p>
                <p className="text-xs text-slate-400 mt-1">Chat in real-time with verified professionals on the GRID network.</p>
            </section>
        );
    }

    const handleSend = (e) => {
        if (e) e.preventDefault();
        const trimmed = messageInput.trim();
        if (!trimmed) return;
        onSendMessage(conversation.id, trimmed);
        setMessageInput('');
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    return (
        <section className={`g-msg-chat-pane flex-1 flex flex-col min-w-0 bg-white dark:bg-slate-900 ${className}`}>
            {/* Active Chat Header */}
            <header className="h-16 border-b border-slate-200/80 dark:border-slate-800 px-3 sm:px-6 flex items-center justify-between flex-none">
                <div className="flex items-center gap-2 sm:gap-3 min-w-0">
                    {onBackToList && (
                        <button
                            type="button"
                            onClick={onBackToList}
                            className="md:hidden p-1.5 -ml-1 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors flex items-center justify-center shrink-0"
                            title="Back to messages"
                            aria-label="Back to messages"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    )}

                    <div className="relative shrink-0">
                        {conversation.avatar ? (
                            <img 
                                src={conversation.avatar} 
                                alt={conversation.name} 
                                className="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700" 
                            />
                        ) : (
                            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                                {conversation.groupBadge || conversation.name.slice(0, 2)}
                            </div>
                        )}
                        {conversation.isOnline && (
                            <span 
                                className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-900" 
                                title="Active now" 
                            />
                        )}
                    </div>

                    <div className="min-w-0">
                        <div className="flex items-center gap-1.5">
                            <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                                {conversation.name}
                            </h2>
                            {conversation.verified && (
                                <VerifiedBadge size="sm" title="Verified Creator" />
                            )}
                            {conversation.connectionDegree && (
                                <span className="hidden sm:inline-block px-2 py-0.5 bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[10px] font-semibold rounded-full">
                                    {conversation.connectionDegree}
                                </span>
                            )}
                            {conversation.grid_id && (
                                <span className="font-mono text-[10px] text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 rounded border border-purple-200 dark:border-purple-800">
                                    @{conversation.grid_id}
                                </span>
                            )}
                        </div>
                        <p className="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {conversation.headline}
                        </p>
                    </div>
                </div>

                {/* Right Action Buttons */}
                <div className="flex items-center gap-1.5 sm:gap-2">
                    <button 
                        type="button" 
                        onClick={onToggleVisitingCard}
                        className={`inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
                            isVisitingCardOpen 
                                ? 'bg-blue-600 text-white shadow-xs' 
                                : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'
                        }`}
                        title="Toggle Visiting Card Inspector"
                    >
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span className="hidden sm:inline">Visiting Card</span>
                    </button>

                    <button 
                        type="button" 
                        onClick={() => setIsStarred(!isStarred)}
                        className={`p-2 rounded-lg transition-colors ${
                            isStarred 
                                ? 'text-amber-500 bg-amber-50 dark:bg-amber-950/40' 
                                : 'text-slate-400 hover:text-amber-500 hover:bg-slate-50 dark:hover:bg-slate-800'
                        }`} 
                        title={isStarred ? 'Unstar conversation' : 'Star conversation'}
                    >
                        <svg className="w-4 h-4" fill={isStarred ? 'currentColor' : 'none'} stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>

                    <button 
                        type="button" 
                        className="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition-colors" 
                        title="Thread Options"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                </div>
            </header>

            {/* Message History Feed */}
            <div ref={streamRef} className="g-msg-stream flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 sm:space-y-5 bg-slate-50/50 dark:bg-slate-950/40 min-h-0">
                {/* Date Separator */}
                <div className="flex items-center justify-center my-2">
                    <span className="px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-full text-[11px] font-semibold text-slate-500 dark:text-slate-400 shadow-2xs">
                        Today, Sep 8
                    </span>
                </div>

                {/* Messages Rendering */}
                {conversation.messages?.map((msg) => {
                    const isMe = msg.sender === 'me';
                    return (
                        <div 
                            key={msg.id} 
                            className={`flex gap-3 max-w-2xl ${isMe ? 'ml-auto flex-row-reverse' : ''}`}
                        >
                            {/* Avatar */}
                            {msg.avatar ? (
                                <img 
                                    src={msg.avatar} 
                                    alt={msg.senderName} 
                                    className="w-8 h-8 rounded-full shrink-0 object-cover mt-0.5 border border-slate-200 dark:border-slate-700" 
                                />
                            ) : (
                                <div className="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    {msg.senderName?.slice(0, 2) || 'U'}
                                </div>
                            )}

                            {/* Message Body & Metadata */}
                            <div className={`space-y-1.5 ${isMe ? 'items-end flex flex-col' : ''}`}>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-xs font-bold text-slate-900 dark:text-slate-100">
                                        {msg.senderName}
                                    </span>
                                    <span className="text-[10px] text-slate-400">
                                        {msg.time}
                                    </span>
                                </div>

                                {/* Speech Bubble */}
                                <div className={`p-4 text-xs leading-relaxed shadow-2xs rounded-2xl ${
                                    isMe 
                                        ? 'bg-blue-600 text-white rounded-tr-sm shadow-sm' 
                                        : 'bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-tl-sm'
                                }`}>
                                    {msg.content}
                                </div>

                                {/* Embedded Artifact Card (e.g. GitHub Repo / Three.js Pipeline) */}
                                {msg.repoArtifact && (
                                    <div className="bg-[#0B0F17] rounded-xl p-3 text-white border border-slate-800 shadow-card max-w-lg mt-2">
                                        <div className="flex items-center justify-between text-[11px] text-slate-400 pb-2 border-b border-slate-800/80">
                                            <div className="flex items-center gap-1.5">
                                                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                                                <span className="font-mono text-xs text-slate-200">{msg.repoArtifact.repoUrl}</span>
                                            </div>
                                            <span className="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">
                                                {msg.repoArtifact.version}
                                            </span>
                                        </div>
                                        <div className="pt-2.5 flex items-start justify-between gap-3">
                                            <div>
                                                <h4 className="text-xs font-bold text-white mb-1">
                                                    {msg.repoArtifact.title}
                                                </h4>
                                                <p className="text-[11px] text-slate-400 leading-normal">
                                                    {msg.repoArtifact.description}
                                                </p>
                                                <div className="flex items-center gap-2 mt-2">
                                                    {msg.repoArtifact.tags?.map((tag) => (
                                                        <span key={tag} className="px-2 py-0.5 text-[10px] bg-slate-800 text-slate-300 rounded font-medium">
                                                            {tag}
                                                        </span>
                                                    ))}
                                                </div>
                                            </div>
                                            <div className="text-right shrink-0">
                                                <span className="inline-flex items-center gap-1 px-2 py-1 bg-amber-500/10 text-amber-300 rounded text-xs font-bold">
                                                    {msg.repoArtifact.stars}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Permission Granted Chip */}
                                {msg.permissionBadge && (
                                    <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-lg text-[11px] font-semibold text-emerald-700 dark:text-emerald-300 mt-1">
                                        <svg className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span>{msg.permissionBadge}</span>
                                    </div>
                                )}

                                {/* Read Receipt Status */}
                                {isMe && msg.readStatus && (
                                    <div className="flex items-center gap-1 text-[10px] text-slate-400">
                                        <span>{msg.readStatus}</span>
                                        <svg className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                )}
                            </div>
                        </div>
                    );
                })}
                <div ref={messagesEndRef} />
            </div>

            {/* Rich Composer Footer */}
            <footer className="p-2.5 sm:p-4 border-t border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 flex-none">
                <form onSubmit={handleSend} className="border border-slate-200/90 dark:border-slate-700 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 focus-within:bg-white dark:focus-within:bg-slate-900 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/10 transition-all">
                    {/* Text Area */}
                    <textarea 
                        rows={2}
                        value={messageInput}
                        onChange={(e) => setMessageInput(e.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder={`Write a message to ${conversation.name.split(' ')[0]}...`}
                        className="w-full bg-transparent p-2.5 sm:p-3 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none resize-none"
                    />

                    {/* Bottom Actions Row: Formatting Tools, eFactor Pill & Send CTA */}
                    <div className="flex items-center justify-between px-3 py-2 bg-white dark:bg-slate-900 rounded-b-xl border-t border-slate-100 dark:border-slate-800 gap-2">
                        {/* Formatting Toolbar & eFactor Pill */}
                        <div className="flex items-center gap-1 sm:gap-2 flex-wrap min-w-0">
                            <div className="flex items-center gap-0.5 text-slate-500 dark:text-slate-400">
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors" title="Bold text">
                                    <span className="font-bold text-xs px-0.5">B</span>
                                </button>
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors" title="Italic text">
                                    <span className="italic font-serif text-xs px-0.5">I</span>
                                </button>
                                <div className="h-3.5 w-px bg-slate-200 dark:bg-slate-700 mx-1" />
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors" title="Attach Image or Asset">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors" title="Embed Code Snippet">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                </button>
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors" title="Insert Emoji">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button type="button" className="p-1.5 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors text-blue-600 dark:text-blue-400" title="Share GRID Visiting Card">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </button>
                            </div>

                            {/* Security / eFactor Permission Pill */}
                            <div className="hidden sm:inline-flex items-center gap-1.5 text-[11px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                <svg className="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                                </svg>
                                <span>Level 2 (eFactor Verified)</span>
                            </div>
                        </div>

                        {/* Right: Send Action */}
                        <button 
                            type="submit"
                            disabled={!messageInput.trim()}
                            className="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition-all shrink-0"
                        >
                            <span>Send</span>
                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </footer>
        </section>
    );
}
