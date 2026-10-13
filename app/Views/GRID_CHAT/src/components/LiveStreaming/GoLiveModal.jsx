import { useState, useEffect, useRef } from "react";
import { streamCategories } from "../../data/liveStreamsData";
import "./GoLiveModal.css";

function GoLiveModal({ onClose, onStartStream, onToast }) {
  const [title, setTitle] = useState("");
  const [category, setCategory] = useState("tech");
  const [privacy, setPrivacy] = useState("public");
  const [cameraActive, setCameraActive] = useState(true);
  const [micActive, setMicActive] = useState(true);
  const [isCountingDown, setIsCountingDown] = useState(false);
  const [countdown, setCountdown] = useState(3);
  const videoRef = useRef(null);
  const streamTrackRef = useRef(null);

  // Initialize camera preview
  useEffect(() => {
    let activeStream = null;

    if (navigator.mediaDevices?.getUserMedia && cameraActive) {
      navigator.mediaDevices
        .getUserMedia({ video: true, audio: false })
        .then((mediaStream) => {
          activeStream = mediaStream;
          streamTrackRef.current = mediaStream;
          if (videoRef.current) {
            videoRef.current.srcObject = mediaStream;
            videoRef.current.play().catch(() => {});
          }
        })
        .catch(() => {
          console.log("Webcam unavailable or permission denied, using live simulator preview");
        });
    }

    return () => {
      if (activeStream) {
        activeStream.getTracks().forEach((track) => track.stop());
      }
    };
  }, [cameraActive]);

  const handleStartBroadcast = (e) => {
    e.preventDefault();
    if (!title.trim()) {
      onToast?.("Please enter a stream title");
      return;
    }

    setIsCountingDown(true);
    let count = 3;
    setCountdown(count);

    const interval = setInterval(() => {
      count -= 1;
      if (count > 0) {
        setCountdown(count);
      } else {
        clearInterval(interval);
        const newStream = {
          id: `stream-user-${Date.now()}`,
          title: title.trim(),
          streamer: {
            id: "current_user",
            name: "Henry Jabbawockiez",
            avatar: 12,
            verified: true,
            followers: "1.2K",
            bio: "You are currently streaming live!",
          },
          category: category,
          categoryLabel: streamCategories.find((c) => c.id === category)?.label || "Tech & Coding",
          tags: ["LiveNow", category.toUpperCase(), "GRID"],
          viewers: 1,
          startedAt: "Just now",
          duration: "00:00:01",
          thumbnail: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&q=80",
          videoBgGradient: "linear-gradient(135deg, #0f172a 0%, #00a884 50%, #111b21 100%)",
          isLive: true,
          isUserStream: true,
          chatMessages: [
            { id: 1, user: "System", avatar: 12, time: "Just now", text: "Your live stream has started! Welcome your audience.", badge: "Admin" },
          ],
        };

        if (streamTrackRef.current) {
          streamTrackRef.current.getTracks().forEach((t) => t.stop());
        }

        onStartStream?.(newStream);
        onToast?.("🔴 You are now LIVE!");
      }
    }, 900);
  };

  return (
    <div className="go-live-backdrop" onClick={onClose}>
      <div className="go-live-modal" onClick={(e) => e.stopPropagation()}>
        {/* Countdown Overlay */}
        {isCountingDown && (
          <div className="live-countdown-overlay">
            <div className="countdown-number">{countdown}</div>
            <p className="countdown-subtext">Going Live in a moment...</p>
          </div>
        )}

        {/* Modal Header */}
        <div className="go-live-modal-header">
          <div className="modal-title-group">
            <div className="title-icon-badge">
              <i className="fa-solid fa-video"></i>
            </div>
            <div>
              <h3 className="modal-title">Live Broadcast Studio</h3>
              <p className="modal-subtitle">Configure your camera, audio, and stream info</p>
            </div>
          </div>
          <button className="modal-close-btn" onClick={onClose} title="Close">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        {/* Modal Body */}
        <form className="go-live-form" onSubmit={handleStartBroadcast}>
          {/* Camera Preview Viewport */}
          <div className="studio-camera-preview">
            {cameraActive ? (
              <video
                ref={videoRef}
                className="studio-video-element"
                autoPlay
                playsInline
                muted
              />
            ) : (
              <div className="studio-camera-placeholder">
                <i className="fa-solid fa-video-slash"></i>
                <span>Camera is turned off</span>
              </div>
            )}

            {/* Quick Device Toggles */}
            <div className="studio-device-controls">
              <button
                type="button"
                className={`studio-control-btn ${cameraActive ? "active" : "disabled"}`}
                onClick={() => setCameraActive(!cameraActive)}
                title={cameraActive ? "Turn off Camera" : "Turn on Camera"}
              >
                <i className={`fa-solid ${cameraActive ? "fa-video" : "fa-video-slash"}`}></i>
              </button>
              <button
                type="button"
                className={`studio-control-btn ${micActive ? "active" : "disabled"}`}
                onClick={() => setMicActive(!micActive)}
                title={micActive ? "Mute Microphone" : "Unmute Microphone"}
              >
                <i className={`fa-solid ${micActive ? "fa-microphone" : "fa-microphone-slash"}`}></i>
              </button>
            </div>

            <div className="studio-preview-badge">
              <span className="studio-dot"></span> PREVIEW READY
            </div>
          </div>

          {/* Form Inputs */}
          <div className="studio-fields-group">
            <div className="field-item">
              <label className="field-label">Stream Title *</label>
              <input
                type="text"
                className="field-input"
                placeholder="e.g., Building a React 19 Workstation Live!"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required
                maxLength={90}
              />
            </div>

            <div className="field-row">
              <div className="field-item flex-1">
                <label className="field-label">Category</label>
                <select
                  className="field-select"
                  value={category}
                  onChange={(e) => setCategory(e.target.value)}
                >
                  {streamCategories
                    .filter((c) => c.id !== "all")
                    .map((cat) => (
                      <option key={cat.id} value={cat.id}>
                        {cat.label}
                      </option>
                    ))}
                </select>
              </div>

              <div className="field-item flex-1">
                <label className="field-label">Privacy</label>
                <select
                  className="field-select"
                  value={privacy}
                  onChange={(e) => setPrivacy(e.target.value)}
                >
                  <option value="public">🌐 Public (Everyone)</option>
                  <option value="contacts">👥 Contacts Only</option>
                  <option value="unlisted">🔒 Unlisted Link</option>
                </select>
              </div>
            </div>
          </div>

          {/* Modal Actions */}
          <div className="go-live-actions">
            <button type="button" className="btn-cancel" onClick={onClose}>
              Cancel
            </button>
            <button
              type="submit"
              className="btn-start-broadcast"
              disabled={!title.trim()}
            >
              <i className="fa-solid fa-tower-broadcast"></i>
              <span>Go Live Now</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default GoLiveModal;
