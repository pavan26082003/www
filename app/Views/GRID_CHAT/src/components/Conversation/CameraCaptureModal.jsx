import { useEffect, useRef, useState } from "react";
import "./CameraCaptureModal.css";

const isCameraSupported =
  typeof navigator !== "undefined" && Boolean(navigator.mediaDevices?.getUserMedia);

function getSupportedMimeType() {
  if (typeof MediaRecorder === "undefined") return "";
  const types = [
    "video/webm;codecs=vp9,opus",
    "video/webm;codecs=vp8,opus",
    "video/webm",
    "video/mp4",
  ];
  for (const type of types) {
    if (MediaRecorder.isTypeSupported(type)) return type;
  }
  return "";
}

function formatDuration(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins < 10 ? "0" : ""}${mins}:${secs < 10 ? "0" : ""}${secs}`;
}

function CameraCaptureModal({ onClose, onCapture, onToast }) {
  const [mode, setMode] = useState("photo"); // "photo" | "video"
  const [facingMode, setFacingMode] = useState("user"); // "user" | "environment"
  const [isRecording, setIsRecording] = useState(false);
  const [recordDuration, setRecordDuration] = useState(0);
  const [capturedMedia, setCapturedMedia] = useState(null); // { type: "photo"|"video", url, blob, duration, fileName }
  const [cameraError, setCameraError] = useState(!isCameraSupported);
  const [hasMultipleCameras, setHasMultipleCameras] = useState(false);

  const videoRef = useRef(null);
  const canvasRef = useRef(null);
  const streamRef = useRef(null);
  const mediaRecorderRef = useRef(null);
  const recordedChunksRef = useRef([]);
  const timerIntervalRef = useRef(null);
  const recordDurationRef = useRef(0);
  const fileInputRef = useRef(null);

  // Check for multiple cameras (e.g. mobile front/back)
  useEffect(() => {
    if (navigator.mediaDevices?.enumerateDevices) {
      navigator.mediaDevices.enumerateDevices().then((devices) => {
        const videoDevices = devices.filter((d) => d.kind === "videoinput");
        if (videoDevices.length > 1) {
          setHasMultipleCameras(true);
        }
      }).catch(() => {});
    }
  }, []);

  const stopStream = () => {
    if (streamRef.current) {
      streamRef.current.getTracks().forEach((track) => track.stop());
      streamRef.current = null;
    }
  };

  const startCamera = (faceMode = facingMode) => {
    if (!isCameraSupported) {
      setCameraError(true);
      return;
    }

    stopStream();
    setCameraError(false);

    // Try video + audio first, gracefully fallback to video-only if microphone blocked/absent
    navigator.mediaDevices
      .getUserMedia({
        video: { facingMode: faceMode, width: { ideal: 1280 }, height: { ideal: 720 } },
        audio: true,
      })
      .catch(() =>
        navigator.mediaDevices.getUserMedia({
          video: { facingMode: faceMode },
          audio: false,
        })
      )
      .then((mediaStream) => {
        streamRef.current = mediaStream;
        if (videoRef.current) {
          videoRef.current.srcObject = mediaStream;
          videoRef.current.play().catch(() => {});
        }
      })
      .catch((err) => {
        console.warn("Camera access failed:", err);
        setCameraError(true);
      });
  };

  // Start camera on mount or facingMode change if not reviewing captured media
  useEffect(() => {
    if (!capturedMedia) {
      startCamera(facingMode);
    }
    return () => {
      stopStream();
      if (timerIntervalRef.current) {
        clearInterval(timerIntervalRef.current);
      }
    };
  }, [facingMode]);

  // Flip front / rear camera
  const handleFlipCamera = () => {
    if (isRecording) return;
    const nextMode = facingMode === "user" ? "environment" : "user";
    setFacingMode(nextMode);
  };

  // Switch between Photo and Video mode
  const handleModeChange = (newMode) => {
    if (isRecording || capturedMedia) return;
    setMode(newMode);
  };

  // 1. Capture Photo
  const handleCapturePhoto = () => {
    const video = videoRef.current;
    const canvas = canvasRef.current;
    if (!video || !canvas) return;

    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;

    const ctx = canvas.getContext("2d");
    if (facingMode === "user") {
      ctx.translate(canvas.width, 0);
      ctx.scale(-1, 1);
    }
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL("image/jpeg", 0.88);
    setCapturedMedia({
      type: "photo",
      url: dataUrl,
      fileName: `Photo_${Date.now()}.jpg`,
    });
    stopStream();
  };

  // 2. Start Video Recording
  const handleStartRecording = () => {
    if (!streamRef.current || isRecording) return;
    const stream = streamRef.current;
    const mimeType = getSupportedMimeType();
    recordedChunksRef.current = [];

    try {
      const recorder = new MediaRecorder(stream, mimeType ? { mimeType } : {});
      recorder.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) {
          recordedChunksRef.current.push(e.data);
        }
      };

      recorder.onstop = () => {
        const blob = new Blob(recordedChunksRef.current, {
          type: mimeType || "video/webm",
        });
        const url = URL.createObjectURL(blob);
        const durationSecs = recordDurationRef.current || 1;
        setCapturedMedia({
          type: "video",
          url,
          blob,
          duration: formatDuration(durationSecs),
          fileName: `Video_${Date.now()}.mp4`,
        });
        stopStream();
      };

      recorder.start(100);
      mediaRecorderRef.current = recorder;
      setIsRecording(true);
      setRecordDuration(0);
      recordDurationRef.current = 0;

      timerIntervalRef.current = setInterval(() => {
        setRecordDuration((prev) => {
          const next = prev + 1;
          recordDurationRef.current = next;
          // Auto stop at 60 seconds
          if (next >= 60) {
            handleStopRecording();
          }
          return next;
        });
      }, 1000);
    } catch (err) {
      console.error("Failed to start MediaRecorder:", err);
      onToast?.("Could not start video recording");
    }
  };

  // Stop Video Recording
  const handleStopRecording = () => {
    if (timerIntervalRef.current) {
      clearInterval(timerIntervalRef.current);
      timerIntervalRef.current = null;
    }
    if (mediaRecorderRef.current && mediaRecorderRef.current.state !== "inactive") {
      mediaRecorderRef.current.stop();
    }
    setIsRecording(false);
  };

  // Shutter button action based on current mode
  const handleShutterAction = () => {
    if (mode === "photo") {
      handleCapturePhoto();
    } else {
      if (isRecording) {
        handleStopRecording();
      } else {
        handleStartRecording();
      }
    }
  };

  // Retake / discard captured media
  const handleRetake = () => {
    if (capturedMedia?.url && capturedMedia.type === "video") {
      URL.revokeObjectURL(capturedMedia.url);
    }
    setCapturedMedia(null);
    setRecordDuration(0);
    recordDurationRef.current = 0;
    setIsRecording(false);
    startCamera(facingMode);
  };

  // Send captured photo or video
  const handleSend = () => {
    if (!capturedMedia) return;
    if (capturedMedia.type === "video") {
      onCapture({
        type: "video",
        video: capturedMedia.url,
        fileName: capturedMedia.fileName,
        duration: capturedMedia.duration,
      });
    } else {
      onCapture({
        type: "image",
        image: capturedMedia.url,
        fileName: capturedMedia.fileName,
      });
    }
    stopStream();
    onClose();
  };

  // Close modal
  const handleClose = () => {
    if (isRecording) {
      handleStopRecording();
    }
    if (capturedMedia?.url && capturedMedia.type === "video") {
      URL.revokeObjectURL(capturedMedia.url);
    }
    stopStream();
    onClose();
  };

  // File picker fallback (for test environments or users wanting to upload a photo/video)
  const handleFileSelect = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const isVideo = file.type.startsWith("video/");
    const fileUrl = URL.createObjectURL(file);

    setCapturedMedia({
      type: isVideo ? "video" : "photo",
      url: fileUrl,
      fileName: file.name,
      duration: isVideo ? "0:15" : undefined,
    });
    stopStream();
  };

  // Trigger fallback device file picker
  const triggerFilePicker = () => {
    if (fileInputRef.current) {
      fileInputRef.current.click();
    }
  };

  // Demo fallback recording (when physical camera is absent or testing)
  const handleRecordDemo = () => {
    const canvas = canvasRef.current || document.createElement("canvas");
    canvas.width = 640;
    canvas.height = 480;
    const ctx = canvas.getContext("2d");

    if (mode === "photo") {
      const grad = ctx.createLinearGradient(0, 0, 640, 480);
      grad.addColorStop(0, "#4f46e5");
      grad.addColorStop(1, "#06b6d4");
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, 640, 480);
      ctx.fillStyle = "#ffffff";
      ctx.font = "bold 26px sans-serif";
      ctx.textAlign = "center";
      ctx.fillText("📷 Camera Snapshot", 320, 220);
      ctx.font = "16px sans-serif";
      ctx.fillText(new Date().toLocaleTimeString(), 320, 260);

      setCapturedMedia({
        type: "photo",
        url: canvas.toDataURL("image/jpeg", 0.9),
        fileName: `Photo_${Date.now()}.jpg`,
      });
      stopStream();
    } else {
      try {
        const stream = canvas.captureStream ? canvas.captureStream(25) : null;
        if (stream && typeof MediaRecorder !== "undefined") {
          const mimeType = getSupportedMimeType();
          const rec = new MediaRecorder(stream, mimeType ? { mimeType } : {});
          const chunks = [];
          rec.ondataavailable = (e) => {
            if (e.data?.size > 0) chunks.push(e.data);
          };
          rec.onstop = () => {
            const blob = new Blob(chunks, { type: mimeType || "video/webm" });
            setCapturedMedia({
              type: "video",
              url: URL.createObjectURL(blob),
              blob,
              duration: "0:03",
              fileName: `Video_${Date.now()}.mp4`,
            });
            stopStream();
          };
          rec.start();
          let frame = 0;
          const anim = setInterval(() => {
            frame++;
            const grad = ctx.createLinearGradient(0, 0, 640, 480);
            grad.addColorStop(0, "#7047eb");
            grad.addColorStop(1, "#ec4899");
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 640, 480);
            ctx.fillStyle = "#ffffff";
            ctx.font = "bold 26px sans-serif";
            ctx.textAlign = "center";
            ctx.fillText("🎥 Recorded Video Clip", 320, 210);
            ctx.font = "22px monospace";
            ctx.fillText(`00:0${Math.min(3, Math.floor(frame / 10))}`, 320, 255);
            if (frame >= 25) {
              clearInterval(anim);
              rec.stop();
            }
          }, 100);
        }
      } catch (e) {
        console.warn("Demo recorder failed:", e);
      }
    }
  };

  return (
    <div className="camera-modal-backdrop" onClick={handleClose}>
      <div className="camera-modal" onClick={(e) => e.stopPropagation()}>
        {/* Header */}
        <div className="camera-modal-header">
          <div className="camera-header-left">
            <i className={`fa-solid ${mode === "video" ? "fa-video" : "fa-camera"}`}></i>
            <h3>{mode === "video" ? "Record Video" : "Take Photo"}</h3>
          </div>

          <div className="camera-header-right">
            {/* Camera Flip Button (Front/Back) */}
            {!capturedMedia && !cameraError && (
              <button
                type="button"
                className="camera-flip-btn"
                onClick={handleFlipCamera}
                disabled={isRecording}
                title="Flip Camera"
                aria-label="Flip Camera"
              >
                <i className="fa-solid fa-camera-rotate"></i>
              </button>
            )}

            <button
              type="button"
              className="camera-modal-close"
              onClick={handleClose}
              title="Close"
              aria-label="Close"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        {/* Viewfinder / Preview Area */}
        <div className="camera-viewport">
          {capturedMedia ? (
            capturedMedia.type === "video" ? (
              <video
                src={capturedMedia.url}
                className="camera-preview-video"
                controls
                autoPlay
                playsInline
              />
            ) : (
              <img src={capturedMedia.url} alt="Captured preview" className="camera-preview-img" />
            )
          ) : cameraError ? (
            <div className="camera-placeholder">
              <i className="fa-solid fa-video-slash"></i>
              <span>Camera unavailable or permission denied</span>
              <div style={{ display: "flex", gap: "8px", flexWrap: "wrap", justifyContent: "center" }}>
                <button
                  type="button"
                  className="camera-upload-fallback-btn"
                  onClick={triggerFilePicker}
                >
                  <i className="fa-solid fa-file-arrow-up"></i> Choose from Device
                </button>
                <button
                  type="button"
                  className="camera-upload-fallback-btn"
                  onClick={handleRecordDemo}
                >
                  <i className={`fa-solid ${mode === "video" ? "fa-video" : "fa-camera"}`}></i> Demo {mode === "video" ? "Video" : "Photo"}
                </button>
              </div>
            </div>
          ) : (
            <>
              <video
                ref={videoRef}
                className={`camera-video-el ${facingMode === "user" ? "is-mirrored" : ""}`}
                autoPlay
                playsInline
                muted
              />

              {/* Live Recording Badge & Timer */}
              {isRecording && (
                <div className="camera-recording-indicator">
                  <span className="recording-pulse-dot"></span>
                  <span className="recording-timer-text">
                    REC {formatDuration(recordDuration)} / 01:00
                  </span>
                </div>
              )}
            </>
          )}
        </div>

        {/* Hidden Canvas & File Input */}
        <canvas ref={canvasRef} style={{ display: "none" }} />
        <input
          type="file"
          ref={fileInputRef}
          accept={mode === "video" ? "video/*" : "image/*"}
          style={{ display: "none" }}
          onChange={handleFileSelect}
        />

        {/* Controls Section */}
        {capturedMedia ? (
          <div className="camera-modal-actions">
            <button className="camera-btn-secondary" onClick={handleRetake} type="button">
              <i className="fa-solid fa-rotate-left"></i> Retake
            </button>
            <button className="camera-btn-primary" onClick={handleSend} type="button">
              <i className="fa-solid fa-paper-plane"></i> Send {capturedMedia.type === "video" ? "Video" : "Photo"}
            </button>
          </div>
        ) : (
          <div className="camera-controls-bottom">
            {/* Mode Switcher: PHOTO | VIDEO */}
            <div className="camera-mode-switcher" role="tablist">
              <button
                type="button"
                role="tab"
                aria-selected={mode === "photo"}
                className={`camera-mode-tab ${mode === "photo" ? "active" : ""}`}
                onClick={() => handleModeChange("photo")}
                disabled={isRecording}
              >
                <i className="fa-solid fa-camera"></i>
                <span>PHOTO</span>
              </button>
              <button
                type="button"
                role="tab"
                aria-selected={mode === "video"}
                className={`camera-mode-tab ${mode === "video" ? "active" : ""}`}
                onClick={() => handleModeChange("video")}
                disabled={isRecording}
              >
                <i className="fa-solid fa-video"></i>
                <span>VIDEO</span>
              </button>
            </div>

            {/* Shutter / Record Button */}
            <div className="camera-shutter-container">
              <button
                type="button"
                className={`camera-shutter-btn mode-${mode} ${isRecording ? "is-recording" : ""}`}
                onClick={handleShutterAction}
                disabled={cameraError}
                title={
                  mode === "photo"
                    ? "Take Photo"
                    : isRecording
                    ? "Stop Recording"
                    : "Start Recording"
                }
                aria-label={
                  mode === "photo"
                    ? "Take Photo"
                    : isRecording
                    ? "Stop Recording"
                    : "Start Recording"
                }
              >
                <span className="shutter-inner-icon">
                  {mode === "photo" ? (
                    <i className="fa-solid fa-camera"></i>
                  ) : isRecording ? (
                    <i className="fa-solid fa-stop"></i>
                  ) : (
                    <span className="record-red-dot"></span>
                  )}
                </span>
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

export default CameraCaptureModal;
