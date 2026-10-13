import React, { useState, useRef, useEffect } from 'react';

/**
 * TeamChatStream Component (Slack-Style Chat Area)
 * Supports:
 * - "My team" channel chat (# nna)
 * - Standard channels (# all-grid, # new-channel, # social)
 * - 1:1 Direct Messages (Sai Tulasi Ram, Elena Rostova, Marcus Vance)
 * - Interactive message composer with markdown formatting, emoji reactions, and instant sending
 */
export default function TeamChatStream({
    activeItem, // channel object or directMessage object
    isDirectMessage = false,
    messages = [],
    currentUser,
    onSendMessage,
    onAddReaction,
    onBack,
    onToast
}) {
    const [draftText, setDraftText] = useState('');
    const [isFormattingActive, setIsFormattingActive] = useState({ bold: false, italic: false, code: false });
    const messagesEndRef = useRef(null);
    const scrollContainerRef = useRef(null);

    // Auto-scroll to bottom on new messages
    useEffect(() => {
        if (scrollContainerRef.current) {
            scrollContainerRef.current.scrollTop = scrollContainerRef.current.scrollHeight;
        }
    }, [messages]);

    const handleSend = (e) => {
        if (e) e.preventDefault();
        const clean = draftText.trim();
        if (!clean) return;

        let content = clean;
        if (isFormattingActive.bold) content = `**${content}**`;
        if (isFormattingActive.italic) content = `*${content}*`;
        if (isFormattingActive.code) content = `\`${content}\``;

        onSendMessage(content);
        setDraftText('');
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    };

    const quickReactions = ['👍', '🔥', '🚀', '❤️', '🎉'];

    return (
        <div className="flex-1 h-full bg-white text-[#1D1C1D] flex flex-col overflow-hidden select-text">
            {/* Top Channel/DM Header Bar */}
            <header className="h-14 px-3.5 sm:px-6 border-b border-[#E2E8F0] flex items-center justify-between shrink-0 bg-white z-10">
                <div className="flex items-center gap-2 sm:gap-3 truncate">
                    {/* Mobile Back Button */}
                    {onBack && (
                        <button
                            type="button"
                            onClick={onBack}
                            className="p-1.5 -ml-1 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
                            title="Back to Channels & DMs"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    )}

                    {isDirectMessage ? (
                        <div className="relative">
                            <div className="w-8 h-8 rounded-md bg-[#1164A3] text-white text-xs font-bold flex items-center justify-center shadow-xs">
                                {activeItem?.initials || activeItem?.name?.slice(0, 2).toUpperCase() || 'U'}
                            </div>
                            <span
                                className="w-2.5 h-2.5 rounded-full absolute -bottom-0.5 -right-0.5 border-2 border-white"
                                style={{ backgroundColor: activeItem?.statusColor || '#2BAC76' }}
                            />
                        </div>
                    ) : (
                        <div className="text-lg font-bold text-[#616061]">#</div>
                    )}

                    <div className="truncate">
                        <div className="flex items-center gap-2">
                            <span className="font-bold text-base text-[#1D1C1D] truncate">
                                {activeItem?.name || 'conversation'}
                            </span>
                            {!isDirectMessage && (
                                <button
                                    type="button"
                                    onClick={() => onToast && onToast('Starred channel')}
                                    className="text-[#616061] hover:text-[#ECB22E] transition-colors cursor-pointer"
                                    title="Star channel"
                                >
                                    ★
                                </button>
                            )}
                        </div>
                        <div className="text-xs text-[#616061] truncate">
                            {isDirectMessage
                                ? `${activeItem?.status === 'online' ? 'Active now' : 'Away'} • Direct conversation`
                                : (activeItem?.topic || activeItem?.description || 'General team sync')}
                        </div>
                    </div>
                </div>

                {/* Right Channel Tools */}
                <div className="flex items-center gap-2 text-[#616061]">
                    <button
                        type="button"
                        onClick={() => onToast && onToast('Starting team huddle 🎧')}
                        className="px-2.5 py-1 text-xs font-semibold bg-white hover:bg-[#F8F8F8] text-[#1D1C1D] rounded-md border border-slate-300 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs"
                    >
                        <span>🎧</span>
                        <span className="hidden sm:inline">Huddle</span>
                    </button>

                    {!isDirectMessage && (
                        <div className="hidden md:flex items-center gap-1 px-2 py-1 text-xs text-[#616061] bg-[#F8F8F8] rounded-md border border-[#E2E8F0]">
                            <svg className="w-3.5 h-3.5 text-[#616061]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{activeItem?.memberCount || 2}</span>
                        </div>
                    )}

                    <button
                        type="button"
                        onClick={() => onToast && onToast('Channel details & files')}
                        className="p-1.5 hover:bg-[#F8F8F8] rounded-md transition-colors text-[#616061] hover:text-[#1D1C1D] cursor-pointer"
                        title="Conversation Details"
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
            </header>

            {/* Scrollable Message History */}
            <div
                ref={scrollContainerRef}
                className="flex-1 overflow-y-auto px-6 py-5 space-y-4 scrollbar-thin scrollbar-thumb-slate-200"
            >
                {/* Channel Welcome Banner */}
                <div className="pt-2 pb-4 border-b border-[#E2E8F0]">
                    <div className="w-12 h-12 rounded-xl bg-blue-50 text-[#1164A3] text-xl font-bold flex items-center justify-center mb-2">
                        {isDirectMessage ? (activeItem?.initials || 'DM') : '#'}
                    </div>
                    <h2 className="text-xl font-bold text-[#1D1C1D]">
                        {isDirectMessage ? `This is your direct message history with ${activeItem?.name}` : `Welcome to #${activeItem?.name}!`}
                    </h2>
                    <p className="text-xs text-[#616061] mt-1 max-w-lg">
                        {isDirectMessage
                            ? 'Messages sent here are 1-on-1 and private to both of you.'
                            : (activeItem?.description || 'This channel is for project synchronization, development updates, and questions.')}
                    </p>
                </div>

                {/* Date Divider */}
                <div className="relative flex py-3 items-center">
                    <div className="flex-grow border-t border-[#E2E8F0]" />
                    <span className="flex-shrink mx-4 text-[11px] font-bold text-[#616061] bg-white border border-[#E2E8F0] px-3 py-0.5 rounded-full shadow-2xs uppercase tracking-wider">
                        Today
                    </span>
                    <div className="flex-grow border-t border-[#E2E8F0]" />
                </div>

                {/* Messages List */}
                {messages.map(msg => {
                    const isMe = msg.senderId === currentUser?.id || msg.senderName === currentUser?.name;
                    return (
                        <div
                            key={msg.id}
                            className="flex items-start gap-3 group hover:bg-[#F8F8F8] -mx-4 px-4 py-1.5 rounded-md transition-colors relative"
                        >
                            {/* Avatar */}
                            <div className="shrink-0 mt-0.5">
                                {msg.avatar ? (
                                    <img src={msg.avatar} alt={msg.senderName} className="w-9 h-9 rounded-md object-cover" />
                                ) : (
                                    <div className="w-9 h-9 rounded-md bg-[#0F172A] text-white text-xs font-bold flex items-center justify-center shadow-2xs">
                                        {msg.initials || msg.senderName.slice(0, 2).toUpperCase()}
                                    </div>
                                )}
                            </div>

                            {/* Message Body */}
                            <div className="flex-1 min-w-0">
                                <div className="flex items-baseline gap-2 mb-0.5">
                                    <span className="font-bold text-[15px] text-[#1D1C1D] hover:underline cursor-pointer">
                                        {msg.senderName}
                                    </span>
                                    {isMe && <span className="text-[11px] text-[#616061]">you</span>}
                                    <span className="text-[12px] text-[#616061]">{msg.time}</span>
                                </div>

                                <div className="text-[15px] text-[#1D1C1D] leading-[1.46667] break-words">
                                    {msg.content}
                                </div>

                                {/* Reactions Row */}
                                {msg.reactions && msg.reactions.length > 0 && (
                                    <div className="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        {msg.reactions.map((r, rIdx) => {
                                            const hasReacted = r.users?.includes(currentUser?.id);
                                            return (
                                                <button
                                                    key={rIdx}
                                                    type="button"
                                                    onClick={() => onAddReaction(msg.id, r.emoji)}
                                                    className={`px-2 py-0.5 rounded-full text-xs flex items-center gap-1 border transition-all cursor-pointer ${
                                                        hasReacted
                                                            ? 'bg-[#E8F5FA] border-[#1D9BD1] text-[#1164A3] font-bold shadow-2xs'
                                                            : 'bg-[#F8F8F8] border-[#E2E8F0] hover:border-[#868686] text-[#1D1C1D]'
                                                    }`}
                                                >
                                                    <span>{r.emoji}</span>
                                                    <span>{r.count}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>

                            {/* Hover Quick Actions Bar */}
                            <div className="absolute right-4 -top-3 hidden group-hover:flex items-center gap-0.5 bg-white border border-[#E2E8F0] rounded-lg shadow-sm px-1 py-0.5 text-xs text-[#616061] z-10">
                                {quickReactions.slice(0, 3).map(emoji => (
                                    <button
                                        key={emoji}
                                        type="button"
                                        onClick={() => onAddReaction(msg.id, emoji)}
                                        className="p-1 hover:bg-slate-100 rounded transition-colors cursor-pointer"
                                        title={`React with ${emoji}`}
                                    >
                                        {emoji}
                                    </button>
                                ))}
                                <button
                                    type="button"
                                    onClick={() => onToast && onToast('Thread replies')}
                                    className="p-1 hover:bg-slate-100 rounded text-slate-500 cursor-pointer"
                                    title="Reply in thread"
                                >
                                    💬
                                </button>
                            </div>
                        </div>
                    );
                })}

                <div ref={messagesEndRef} />
            </div>

            {/* Bottom Slack-Style Rich Message Composer */}
            <div className="p-4 border-t border-[#E2E8F0] bg-white shrink-0">
                <form
                    onSubmit={handleSend}
                    className="border border-[#868686]/60 focus-within:border-[#1D1C1D] rounded-lg bg-white shadow-2xs overflow-hidden transition-colors"
                >
                    {/* Formatting Toolbar */}
                    <div className="flex items-center gap-1 px-3 py-1 border-b border-[#E2E8F0] text-[#616061] text-xs bg-[#F8F8F8]">
                        <button
                            type="button"
                            onClick={() => setIsFormattingActive(p => ({ ...p, bold: !p.bold }))}
                            className={`p-1 rounded font-bold transition-colors cursor-pointer ${isFormattingActive.bold ? 'bg-slate-200 text-[#1D1C1D]' : 'hover:bg-white hover:text-[#1D1C1D]'}`}
                            title="Bold"
                        >
                            B
                        </button>
                        <button
                            type="button"
                            onClick={() => setIsFormattingActive(p => ({ ...p, italic: !p.italic }))}
                            className={`p-1 rounded italic transition-colors cursor-pointer ${isFormattingActive.italic ? 'bg-slate-200 text-[#1D1C1D]' : 'hover:bg-white hover:text-[#1D1C1D]'}`}
                            title="Italic"
                        >
                            I
                        </button>
                        <button
                            type="button"
                            onClick={() => setIsFormattingActive(p => ({ ...p, code: !p.code }))}
                            className={`p-1 rounded font-mono transition-colors cursor-pointer ${isFormattingActive.code ? 'bg-slate-200 text-[#1D1C1D]' : 'hover:bg-white hover:text-[#1D1C1D]'}`}
                            title="Code snippet"
                        >
                            &lt;&gt;
                        </button>
                        <span className="w-px h-3.5 bg-slate-300 mx-1" />
                        <button
                            type="button"
                            onClick={() => onToast && onToast('Inserted link')}
                            className="p-1 hover:bg-white hover:text-[#1D1C1D] rounded cursor-pointer"
                            title="Link"
                        >
                            🔗
                        </button>
                        <button
                            type="button"
                            onClick={() => onToast && onToast('Bullet list')}
                            className="p-1 hover:bg-white hover:text-[#1D1C1D] rounded cursor-pointer"
                            title="Bullet list"
                        >
                            •
                        </button>
                        <button
                            type="button"
                            onClick={() => onToast && onToast('Numbered list')}
                            className="p-1 hover:bg-white hover:text-[#1D1C1D] rounded font-semibold cursor-pointer"
                            title="Numbered list"
                        >
                            1.
                        </button>
                    </div>

                    {/* Input Field */}
                    <textarea
                        rows={2}
                        value={draftText}
                        onChange={(e) => setDraftText(e.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder={isDirectMessage ? `Message ${activeItem?.name}...` : `Message #${activeItem?.name}...`}
                        className="w-full px-3.5 py-2.5 text-[15px] text-[#1D1C1D] placeholder-[#616061] outline-none resize-none bg-transparent"
                    />

                    {/* Bottom Action Bar */}
                    <div className="flex items-center justify-between px-3 py-1.5 bg-white">
                        <div className="flex items-center gap-1 text-[#616061] text-sm">
                            <button
                                type="button"
                                onClick={() => setDraftText(prev => prev + ' 👍')}
                                className="p-1.5 hover:bg-slate-100 rounded hover:text-[#1D1C1D] transition-colors cursor-pointer"
                                title="Add emoji"
                            >
                                😀
                            </button>
                            <button
                                type="button"
                                onClick={() => setDraftText(prev => prev + ' @')}
                                className="p-1.5 hover:bg-slate-100 rounded hover:text-[#1D1C1D] font-bold transition-colors cursor-pointer"
                                title="Mention teammate"
                            >
                                @
                            </button>
                            <button
                                type="button"
                                onClick={() => onToast && onToast('Audio recording ready')}
                                className="p-1.5 hover:bg-slate-100 rounded hover:text-[#1D1C1D] transition-colors cursor-pointer"
                                title="Voice note"
                            >
                                🎙️
                            </button>
                        </div>

                        {/* Send Button */}
                        <button
                            type="submit"
                            disabled={!draftText.trim()}
                            className={`p-1.5 rounded-lg transition-all flex items-center justify-center cursor-pointer ${
                                draftText.trim()
                                    ? 'bg-[#007A5A] text-white hover:bg-[#006C4E] shadow-xs'
                                    : 'bg-[#F8F8F8] text-[#868686] cursor-not-allowed'
                            }`}
                            title="Send message (Enter)"
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
