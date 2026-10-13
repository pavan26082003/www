import { useState, useEffect, useRef } from "react";
import { giftsCatalog } from "../../data/liveStreamsData";
import "./LiveChat.css";

function LiveChat({ stream, onToast }) {
  const [messages, setMessages] = useState(stream.chatMessages || []);
  const [inputText, setInputText] = useState("");
  const [showGifts, setShowGifts] = useState(false);
  const [recentGift, setRecentGift] = useState(null);
  const messagesStreamRef = useRef(null);

  // Auto-scroll to bottom of chat without scrolling ancestor containers
  const scrollToBottom = () => {
    if (messagesStreamRef.current) {
      messagesStreamRef.current.scrollTop = messagesStreamRef.current.scrollHeight;
    }
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages, recentGift]);

  // Simulated live audience comments arriving periodically
  useEffect(() => {
    const audienceQuotes = [
      { user: "Sarah Connor", avatar: 14, text: "Can you explain that hook again? 🔥", badge: "" },
      { user: "Michael Chang", avatar: 18, text: "This workflow is so fast!", badge: "VIP" },
      { user: "Elena Rostova", avatar: 21, text: "Sent 50 stars! Keep it up ⭐", badge: "" },
      { user: "David Miller", avatar: 25, text: "Subscribed! Love your streams 💜", badge: "Sub" },
      { user: "CryptoDev", avatar: 28, text: "Cleanest architecture I've seen in a while 🚀", badge: "Mod" },
    ];

    let timer = setInterval(() => {
      const randomQuote = audienceQuotes[Math.floor(Math.random() * audienceQuotes.length)];
      const newMsg = {
        id: Date.now() + Math.random(),
        user: randomQuote.user,
        avatar: randomQuote.avatar,
        time: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }),
        text: randomQuote.text,
        badge: randomQuote.badge,
      };

      setMessages((prev) => [...prev.slice(-30), newMsg]);
    }, 9000);

    return () => clearInterval(timer);
  }, []);

  const handleSendMessage = (e) => {
    e.preventDefault();
    if (!inputText.trim()) return;

    const userMessage = {
      id: Date.now(),
      user: "You",
      avatar: 12,
      time: "Just now",
      text: inputText.trim(),
      badge: "You",
      isSelf: true,
    };

    setMessages((prev) => [...prev, userMessage]);
    setInputText("");
    onToast?.("Comment sent to live chat! 💬");
  };

  const handleSendGift = (gift) => {
    const giftAnnouncement = {
      id: crypto.randomUUID(),
      sender: "You",
      giftName: gift.name,
      giftIcon: gift.icon,
      giftColor: gift.color,
    };

    setRecentGift(giftAnnouncement);
    setShowGifts(false);
    onToast?.(`Sent a ${gift.name} ${gift.icon} to the streamer!`);

    // Add celebration message to chat
    const giftMsg = {
      id: crypto.randomUUID(),
      user: "You",
      avatar: 12,
      time: "Just now",
      text: `Sent ${gift.name} ${gift.icon}!`,
      badge: "Gift",
      isGift: true,
      giftIcon: gift.icon,
    };
    setMessages((prev) => [...prev, giftMsg]);

    setTimeout(() => {
      setRecentGift(null);
    }, 4500);
  };

  return (
    <div className="live-chat-panel">
      {/* Header */}
      <div className="live-chat-header">
        <div className="chat-title-box">
          <i className="fa-solid fa-comments"></i>
          <h4>Live Stream Chat</h4>
        </div>
        <div className="chat-stats-badge">
          <span className="live-dot-green"></span>
          <span>Slow Mode: 3s</span>
        </div>
      </div>

      {/* Gift Celebration Pop Banner */}
      {recentGift && (
        <div className="gift-celebration-banner" style={{ borderColor: recentGift.giftColor }}>
          <span className="celebration-icon">{recentGift.giftIcon}</span>
          <div className="celebration-text">
            <strong>{recentGift.sender}</strong> sent <strong>{recentGift.giftName}</strong>!
          </div>
        </div>
      )}

      {/* Message List */}
      <div className="live-messages-stream" ref={messagesStreamRef}>
        <div className="chat-welcome-box">
          <i className="fa-solid fa-shield-halved"></i>
          <span>Welcome to the live chat! Remember to keep comments respectful and adhere to community guidelines.</span>
        </div>

        {messages.map((msg) => (
          <div
            key={msg.id}
            className={`live-chat-row ${msg.isSelf ? "is-self" : ""} ${msg.isGift ? "is-gift-row" : ""}`}
          >
            <img
              src={`https://i.pravatar.cc/80?img=${msg.avatar}`}
              alt={msg.user}
              className="chat-user-avatar"
            />
            <div className="chat-msg-body">
              <div className="chat-user-line">
                <span className="chat-user-name">{msg.user}</span>
                {msg.badge && (
                  <span className={`chat-badge ${msg.badge.toLowerCase()}`}>
                    {msg.badge}
                  </span>
                )}
                <span className="chat-msg-time">{msg.time}</span>
              </div>
              <p className="chat-msg-text">
                {msg.isGift && <span className="gift-inline-icon">{msg.giftIcon}</span>}
                {msg.text}
              </p>
            </div>
          </div>
        ))}
      </div>

      {/* Gifts Drawer Popup */}
      {showGifts && (
        <div className="gifts-drawer-overlay">
          <div className="gifts-drawer-header">
            <span>Send Streamer Super Gift</span>
            <button
              className="close-drawer-btn"
              onClick={() => setShowGifts(false)}
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div className="gifts-grid">
            {giftsCatalog.map((gift) => (
              <button
                key={gift.id}
                className="gift-card-btn"
                onClick={() => handleSendGift(gift)}
              >
                <span className="gift-card-icon">{gift.icon}</span>
                <span className="gift-card-name">{gift.name}</span>
                <span className="gift-card-cost">{gift.cost}</span>
              </button>
            ))}
          </div>
        </div>
      )}

      {/* Chat Composer */}
      <form className="live-chat-composer" onSubmit={handleSendMessage}>
        <button
          type="button"
          className="gift-toggle-btn"
          onClick={() => setShowGifts(!showGifts)}
          title="Send a Super Gift"
        >
          <i className="fa-solid fa-gift"></i>
        </button>

        <input
          type="text"
          placeholder="Say something in live chat..."
          value={inputText}
          onChange={(e) => setInputText(e.target.value)}
          className="chat-input-field"
        />

        <button
          type="submit"
          className="chat-send-btn"
          disabled={!inputText.trim()}
          title="Send message"
        >
          <i className="fa-solid fa-paper-plane"></i>
        </button>
      </form>
    </div>
  );
}

export default LiveChat;
