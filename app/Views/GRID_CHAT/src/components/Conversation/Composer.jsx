import { useEffect, useRef, useState } from "react";
import EmojiPicker from "./EmojiPicker.jsx";
import VoiceRecorderBar from "./VoiceRecorderBar.jsx";
import useVoiceRecorder from "../../hooks/useVoiceRecorder.js";

const formatFileSize = (bytes) => {
  if (!bytes && bytes !== 0) return "";
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

const readFileAsDataUrl = (file) =>
  new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });

function Composer({
  onSend,
  onToast,
  selectedChat,
  onCreateEvent,
  attachmentAction,
  onAttachmentActionChange,
  replyingTo = null,
  onCancelReply,
  editingMessage = null,
  onCancelEdit,
  onEditMessage,
}) {
  const [message, setMessage] = useState("");
  const [emojiOpen, setEmojiOpen] = useState(false);
  const [detectedEvent, setDetectedEvent] = useState(null);
  const [pillDismissed, setPillDismissed] = useState(false);

  const attachOpen = attachmentAction === "menu";

  const voice = useVoiceRecorder({
    onLimitReached: () => onToast?.("Maximum recording length reached (5:00)"),
  });
  const voiceActive = voice.status !== "idle";

  const textareaRef = useRef(null);
  const composerRef = useRef(null);
  const documentInputRef = useRef(null);
  const mediaInputRef = useRef(null);
  const audioInputRef = useRef(null);
  const previousChatIdRef = useRef(selectedChat?.id);

  // Composer isn't remounted on conversation switch, so an in-progress recording
  // must be discarded rather than silently sent to whichever chat is now active.
  useEffect(() => {
    if (previousChatIdRef.current === selectedChat?.id) return;
    previousChatIdRef.current = selectedChat?.id;
    if (voice.status !== "idle") {
      voice.discardRecording();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedChat?.id]);

  useEffect(() => {
    const handleOutsideClick = (event) => {
      if (
        composerRef.current &&
        !composerRef.current.contains(event.target)
      ) {
        if (attachOpen) {
          onAttachmentActionChange?.(null);
        }
        setEmojiOpen(false);
      }
    };

    document.addEventListener("click", handleOutsideClick);

    return () => {
      document.removeEventListener("click", handleOutsideClick);
    };
  }, [attachOpen, onAttachmentActionChange]);

  // Natural Language Date & Time Detection (e.g. "Let's discuss the project tomorrow at 5 PM")
  const analyzeEventIntent = (text) => {
    if (!text || text.length < 8) return null;
    const lower = text.toLowerCase();

    const hasTimeKeyword = /\b(tomorrow|today|tonight|monday|tuesday|wednesday|thursday|friday|saturday|sunday|next week|at\s+\d{1,2}(?::\d{2})?\s*(?:am|pm)?|\d{1,2}\s*(?:am|pm))\b/i.test(lower);
    const hasEventKeyword = /\b(discuss|project|meeting|meet|call|sync|demo|review|stream|session|catch\s*up|lunch|dinner|chat|presentation)\b/i.test(lower);

    if (hasTimeKeyword && hasEventKeyword) {
      const timeMatch = text.match(/\b(\d{1,2}(?::\d{2})?\s*(?:am|pm))\b/i) || text.match(/\bat\s+(\d{1,2}(?::\d{2})?)\b/i);
      const timeStr = timeMatch ? timeMatch[1].toUpperCase() : "5:00 PM";

      let dateStr = "Tomorrow";
      let dateIso = "2026-09-12";
      if (lower.includes("today") || lower.includes("tonight")) {
        dateStr = "Today";
        dateIso = "2026-09-11";
      } else if (lower.includes("monday")) {
        dateStr = "Monday";
        dateIso = "2026-09-14";
      } else if (lower.includes("friday")) {
        dateStr = "Friday";
        dateIso = "2026-09-18";
      }

      let title = "Discussion";
      if (lower.includes("project")) title = "Project Discussion";
      else if (lower.includes("demo")) title = "Product Demo";
      else if (lower.includes("review")) title = "Design Review";
      else if (lower.includes("sync")) title = "Team Sync";
      else if (lower.includes("call")) title = "Call with " + (selectedChat?.name || "Friend");
      else if (lower.includes("meeting")) title = "Meeting with " + (selectedChat?.name || "Friend");

      return {
        title,
        date: dateIso,
        dateStr,
        time: timeStr.includes(":") ? timeStr : `${timeStr.replace(/\s*(AM|PM)/i, ":00 $1")}`,
        time24: "17:00",
        withPerson: selectedChat?.name || "John",
        category: "meeting",
        description: `Created automatically from chat: "${text}"`,
        rsvps: { going: 2, maybe: 0, declined: 0, userRsvp: "going" },
      };
    }
    return null;
  };

  const handleTextChange = (event) => {
    const val = event.target.value;
    setMessage(val);

    const detected = analyzeEventIntent(val);
    if (detected) {
      setDetectedEvent(detected);
      setPillDismissed(false);
    } else if (!val.trim()) {
      setDetectedEvent(null);
    }

    // Auto-expand textarea
    if (textareaRef.current) {
      textareaRef.current.style.height = "auto";
      textareaRef.current.style.height = `${Math.min(textareaRef.current.scrollHeight, 120)}px`;
    }
  };

  useEffect(() => {
    if (editingMessage) {
      setMessage(editingMessage.text || "");
      if (textareaRef.current) {
        textareaRef.current.focus();
      }
    }
  }, [editingMessage]);

  useEffect(() => {
    if (replyingTo && textareaRef.current) {
      textareaRef.current.focus();
    }
  }, [replyingTo]);

  const handleSend = () => {
    const trimmed = message.trim();
    if (!trimmed) {
      return;
    }

    if (editingMessage) {
      onEditMessage?.(editingMessage.id, trimmed);
      onCancelEdit?.();
      setMessage("");
      if (textareaRef.current) {
        textareaRef.current.style.height = "auto";
      }
      return;
    }

    if (replyingTo) {
      const sender = replyingTo.type === "outgoing" ? "You" : (replyingTo.senderName || selectedChat?.name || "Contact");
      const quoteText = replyingTo.text || replyingTo.fileName || (replyingTo.image ? "📷 Photo" : replyingTo.video ? "🎥 Video" : replyingTo.audioUrl ? "🎤 Voice message" : "Media");
      onSend({
        text: trimmed,
        replyTo: {
          id: replyingTo.id,
          text: quoteText,
          sender: sender,
        },
      });
      onCancelReply?.();
    } else {
      onSend(trimmed);
    }

    setMessage("");
    setDetectedEvent(null);

    if (textareaRef.current) {
      textareaRef.current.style.height = "auto";
    }
  };

  const handleCreateDetectedEvent = () => {
    if (!detectedEvent) return;

    if (onCreateEvent) {
      onCreateEvent(detectedEvent);
    } else {
      onSend({
        messageType: "event",
        text: `📅 Scheduled: ${detectedEvent.title}`,
        eventData: detectedEvent,
      });
      onToast?.(`Event "${detectedEvent.title}" added to Calendar!`);
    }

    setDetectedEvent(null);
    setPillDismissed(true);
  };

  const handleKeyDown = (event) => {
    if (event.key === "Enter" && !event.shiftKey) {
      event.preventDefault();
      handleSend();
    }
  };

  const toggleAttach = (event) => {
    event.stopPropagation();
    onAttachmentActionChange?.(attachOpen ? null : "menu");
    setEmojiOpen(false);
  };

  const toggleEmoji = (event) => {
    event.stopPropagation();
    setEmojiOpen((previous) => !previous);
    onAttachmentActionChange?.(null);
  };

  // Called after EmojiPicker inserts an emoji via native DOM manipulation
  // — syncs React state from textarea value
  const handleEmojiSelected = () => {
    if (textareaRef.current) {
      const val = textareaRef.current.value;
      setMessage(val);

      const detected = analyzeEventIntent(val);
      if (detected) {
        setDetectedEvent(detected);
        setPillDismissed(false);
      }
    }
    // Keep picker open (WhatsApp stays open after selecting emoji)
  };

  const handleAttachment = (title, event) => {
    event.stopPropagation();
    onAttachmentActionChange?.(null);

    if (title === "Schedule Event") {
      const defaultEvent = {
        title: `Catch-up with ${selectedChat?.name || "Friend"}`,
        date: "2026-09-12",
        dateStr: "Tomorrow",
        time: "5:00 PM",
        withPerson: selectedChat?.name || "Friend",
        category: "meeting",
        description: "Scheduled via chat attachment",
        rsvps: { going: 1, maybe: 0, declined: 0, userRsvp: "going" },
      };
      if (onCreateEvent) {
        onCreateEvent(defaultEvent);
      } else {
        onSend({
          messageType: "event",
          text: `📅 Scheduled: ${defaultEvent.title}`,
          eventData: defaultEvent,
        });
        onToast?.("Event created & added to Calendar!");
      }
      return;
    }

    if (title === "Document") {
      documentInputRef.current?.click();
      return;
    }

    if (title === "Camera") {
      onAttachmentActionChange?.("camera");
      return;
    }

    if (title === "Photos & videos") {
      mediaInputRef.current?.click();
      return;
    }

    if (title === "Audio") {
      audioInputRef.current?.click();
      return;
    }

    if (title === "Location") {
      handleShareLocation();
      return;
    }

    if (title === "Contact") {
      onAttachmentActionChange?.("contact");
      return;
    }

    if (title === "Poll") {
      onAttachmentActionChange?.("poll");
      return;
    }

    if (onToast) {
      onToast(`${title} action`);
    }
  };

  const handleDocumentChange = (event) => {
    const files = Array.from(event.target.files || []);
    files.forEach((file) => {
      onSend({
        messageType: "file",
        fileName: file.name,
        fileSize: formatFileSize(file.size),
        fileUrl: URL.createObjectURL(file),
        file,
        text: "",
      });
    });
    onToast?.(files.length > 1 ? `${files.length} documents sent` : "Document sent");
    event.target.value = "";
  };

  const handleMediaChange = async (event) => {
    const files = Array.from(event.target.files || []);

    for (const file of files) {
      try {
        if (file.type.startsWith("video/")) {
          // Object URL instead of a data URL: large videos aren't read into memory
          onSend({ messageType: "video", video: URL.createObjectURL(file), fileName: file.name, file, text: "" });
        } else {
          const dataUrl = await readFileAsDataUrl(file);
          onSend({ messageType: "image", image: dataUrl, fileName: file.name, file, text: "" });
        }
      } catch {
        onToast?.(`Could not load ${file.name}`);
      }
    }

    event.target.value = "";
  };

  const handleAudioChange = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    try {
      const dataUrl = await readFileAsDataUrl(file);
      onSend({
        messageType: "audio",
        audio: dataUrl,
        fileName: file.name,
        fileSize: formatFileSize(file.size),
        file,
        text: "",
      });
    } catch {
      onToast?.("Could not load audio file");
    }

    event.target.value = "";
  };

  const handleShareLocation = () => {
    if (!navigator.geolocation) {
      onToast?.("Geolocation is not supported on this device");
      return;
    }

    onToast?.("Fetching your location...");

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        onSend({
          messageType: "location",
          location: { lat: latitude, lng: longitude },
          text: "",
        });
      },
      () => {
        onToast?.("Unable to access location. Please allow location permission.");
      },
      { enableHighAccuracy: true, timeout: 10000 }
    );
  };

  const handleStartRecording = () => {
    setEmojiOpen(false);
    onAttachmentActionChange?.(null);
    voice.startRecording();
  };

  const handleSendVoiceMessage = () => {
    if (!voice.audioUrl) return;

    onSend({
      messageType: "voice",
      audioUrl: voice.audioUrl,
      duration: voice.recordedDuration,
      mimeType: voice.mimeType,
      file: voice.audioBlob,
      text: "",
    });

    // Hands ownership of the object URL to the sent message — the recorder must
    // not revoke it, only stop tracking it.
    voice.finalizeSend();
  };

  const attachmentOptions = [
    { title: "Schedule Event", icon: "fa-solid fa-calendar-plus", bg: "linear-gradient(135deg, #00a884, #059669)" },
    { title: "Document", icon: "fa-solid fa-file-lines", bg: "linear-gradient(135deg, #f97316, #ea580c)" },
    { title: "Camera", icon: "fa-solid fa-camera", bg: "linear-gradient(135deg, #EC407A, #D81B60)" },
    { title: "Photos & videos", icon: "fa-solid fa-image", bg: "linear-gradient(135deg, #f59e0b, #d97706)" },
    { title: "Audio", icon: "fa-solid fa-headphones", bg: "linear-gradient(135deg, #FF9800, #F57C00)" },
    { title: "Location", icon: "fa-solid fa-location-dot", bg: "linear-gradient(135deg, #20C997, #0CA678)" },
    { title: "Contact", icon: "fa-solid fa-user", bg: "linear-gradient(135deg, #0f766e, #115e59)" },
    { title: "Poll", icon: "fa-solid fa-chart-pie", bg: "linear-gradient(135deg, #00BFA5, #00897B)" },
  ];

  return (
    <div
      className="composer-area"
      ref={composerRef}
    >

      {/* Quick Attachment Menu */}
      <div
        className={`quick-attach ${attachOpen ? "open" : ""}`}
        aria-hidden={!attachOpen}
      >
        {attachmentOptions.map((item, index) => (
          <button
            key={index}
            className="attach-item-btn"
            title={item.title}
            onClick={(event) => handleAttachment(item.title, event)}
            type="button"
            tabIndex={attachOpen ? 0 : -1}
          >
            <span className="attach-item-icon" style={{ background: item.bg }}>
              <i className={item.icon}></i>
            </span>
            <span className="attach-item-label">{item.title}</span>
          </button>
        ))}
      </div>

      {/* AI Event Suggestion Banner */}
      {detectedEvent && !pillDismissed && (
        <div className="composer-ai-event-banner">
          <div className="ai-event-info">
            <div className="ai-event-icon">
              <i className="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div className="ai-event-text">
              <div className="ai-event-headline">
                <span className="ai-event-badge">AI DETECTED</span>
                <strong>{detectedEvent.title}</strong>
              </div>
              <div className="ai-event-subtext">
                <i className="fa-regular fa-clock"></i> {detectedEvent.dateStr} at {detectedEvent.time} • With {detectedEvent.withPerson}
              </div>
            </div>
          </div>
          <div className="ai-event-actions">
            <button
              type="button"
              className="ai-event-btn-create"
              onClick={handleCreateDetectedEvent}
            >
              <i className="fa-solid fa-calendar-plus"></i> Create Event
            </button>
            <button
              type="button"
              className="ai-event-btn-close"
              onClick={() => setPillDismissed(true)}
              title="Dismiss"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>
      )}

      {/* Full WhatsApp-Style Emoji Picker */}
      <EmojiPicker
        isOpen={emojiOpen}
        onClose={() => setEmojiOpen(false)}
        onEmojiSelect={handleEmojiSelected}
        textareaRef={textareaRef}
      />

      {voiceActive ? (
        <VoiceRecorderBar
          status={voice.status}
          elapsedSeconds={voice.elapsedSeconds}
          maxDurationSeconds={voice.maxDurationSeconds}
          error={voice.error}
          audioUrl={voice.audioUrl}
          recordedDuration={voice.recordedDuration}
          onStop={voice.stopRecording}
          onDiscard={voice.discardRecording}
          onSend={handleSendVoiceMessage}
          onDismissError={voice.dismissError}
        />
      ) : (
        <>
          {/* Reply Quote Banner */}
          {replyingTo && (
            <div className="composer-reply-preview">
              <div className="composer-reply-bar"></div>
              <div className="composer-reply-body">
                <span className="composer-reply-sender">
                  {replyingTo.type === "outgoing" ? "You" : (replyingTo.senderName || selectedChat?.name || "Contact")}
                </span>
                <p className="composer-reply-text">
                  {replyingTo.text || replyingTo.fileName || (replyingTo.image ? "📷 Photo" : replyingTo.video ? "🎥 Video" : replyingTo.audioUrl ? "🎤 Voice message" : "Media")}
                </p>
              </div>
              <button
                type="button"
                className="composer-reply-close-btn"
                onClick={onCancelReply}
                title="Cancel reply"
                aria-label="Cancel reply"
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </div>
          )}

          {/* Edit Message Banner */}
          {editingMessage && (
            <div className="composer-edit-banner">
              <div className="composer-edit-icon">
                <i className="fa-solid fa-pen"></i>
              </div>
              <div className="composer-edit-body">
                <strong>Editing Message</strong>
                <span>Press Enter to save, or cancel</span>
              </div>
              <button
                type="button"
                className="composer-edit-close-btn"
                onClick={() => {
                  onCancelEdit?.();
                  setMessage("");
                }}
                title="Cancel edit"
              >
                <i className="fa-solid fa-xmark"></i>
              </button>
            </div>
          )}

          {/* Input Pill Container */}
          <div className="composer-input-pill">
          {/* Emoji Button */}
          <button
            className={`emoji-btn${emojiOpen ? " active" : ""}`}
            title="Add emoji"
            onClick={toggleEmoji}
            type="button"
            aria-label="Open emoji picker"
            aria-expanded={emojiOpen}
            aria-haspopup="dialog"
          >
            <i className="fa-regular fa-face-smile"></i>
          </button>

          {/* Auto-expanding Textarea */}
          <textarea
            ref={textareaRef}
            className="composer-textarea"
            rows={1}
            placeholder="Type your message"
            value={message}
            onChange={handleTextChange}
            onKeyDown={handleKeyDown}
          />

          {/* Attachment Button */}
          <button
            className="attach-btn"
            title="Attach"
            onClick={toggleAttach}
            type="button"
          >
            <i className="fa-solid fa-paperclip"></i>
          </button>

          {/* Camera Button */}
          <button
            className="camera-btn"
            title="Camera"
            onClick={() => onAttachmentActionChange?.("camera")}
            type="button"
          >
            <i className="fa-solid fa-camera"></i>
          </button>
        </div>
        </>
      )}

      {/* Hidden File Inputs for Attachments */}
      <input
        type="file"
        ref={documentInputRef}
        onChange={handleDocumentChange}
        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.csv"
        multiple
        style={{ display: "none" }}
      />
      <input
        type="file"
        ref={mediaInputRef}
        onChange={handleMediaChange}
        accept="image/*,video/*"
        multiple
        style={{ display: "none" }}
      />
      <input
        type="file"
        ref={audioInputRef}
        onChange={handleAudioChange}
        accept="audio/*"
        style={{ display: "none" }}
      />

      {/* Send or Mic Action Button */}
      {!voiceActive && (
        <button
          className={`send-btn ${message.trim() ? "is-send" : "is-mic"}`}
          title={message.trim() ? "Send" : "Record voice message"}
          onClick={message.trim() ? handleSend : handleStartRecording}
          type="button"
        >
          {message.trim() ? (
            <i className="fa-solid fa-paper-plane"></i>
          ) : (
            <i className="fa-solid fa-microphone"></i>
          )}
        </button>
      )}

    </div>
  );
}

export default Composer;