import useAudioPlayer from "../../hooks/useAudioPlayer.js";
import { formatDuration } from "../../utils/time.js";

function VoiceRecorderBar({
  status,
  elapsedSeconds,
  maxDurationSeconds,
  error,
  audioUrl,
  recordedDuration,
  onStop,
  onDiscard,
  onSend,
  onDismissError,
}) {
  const player = useAudioPlayer(status === "preview" ? audioUrl : null);

  if (status === "requesting") {
    return (
      <div className="voice-recorder-bar voice-recorder-requesting">
        <span className="voice-recorder-pulse-dot requesting"></span>
        <span className="voice-recorder-label">Requesting microphone access&hellip;</span>
        <button
          type="button"
          className="voice-recorder-icon-btn"
          onClick={onDiscard}
          title="Cancel"
          aria-label="Cancel"
        >
          <i className="fa-solid fa-xmark"></i>
        </button>
      </div>
    );
  }

  if (status === "error") {
    return (
      <div className="voice-recorder-bar voice-recorder-error">
        <i className="fa-solid fa-triangle-exclamation voice-recorder-error-icon"></i>
        <span className="voice-recorder-label">{error}</span>
        <button
          type="button"
          className="voice-recorder-icon-btn"
          onClick={onDismissError}
          title="Dismiss"
          aria-label="Dismiss"
        >
          <i className="fa-solid fa-xmark"></i>
        </button>
      </div>
    );
  }

  if (status === "recording") {
    const nearLimit = elapsedSeconds >= maxDurationSeconds - 10;
    return (
      <div className="voice-recorder-bar voice-recorder-recording">
        <span className="voice-recorder-rec-dot"></span>
        <span className="voice-recorder-label">
          Recording{" "}
          <span className={`voice-recorder-timer ${nearLimit ? "warn" : ""}`}>
            {formatDuration(elapsedSeconds)}
          </span>
        </span>

        <div className="voice-recorder-actions">
          <button
            type="button"
            className="voice-recorder-icon-btn danger"
            onClick={onDiscard}
            title="Delete recording"
            aria-label="Delete recording"
          >
            <i className="fa-solid fa-trash-can"></i>
          </button>
          <button
            type="button"
            className="voice-recorder-icon-btn stop"
            onClick={onStop}
            title="Stop recording"
            aria-label="Stop recording"
          >
            <i className="fa-solid fa-stop"></i>
          </button>
        </div>
      </div>
    );
  }

  // Chrome-recorded webm blobs can report audio.duration as Infinity until played
  // through, so the recorder's own tracked elapsed time is the reliable source.
  const totalDuration =
    Number.isFinite(player.duration) && player.duration > 0 ? player.duration : recordedDuration;
  const displaySeconds =
    player.isPlaying || player.currentTime > 0 ? player.currentTime : totalDuration;

  return (
    <div className="voice-recorder-bar voice-recorder-preview">
      <button
        type="button"
        className="voice-recorder-icon-btn danger"
        onClick={onDiscard}
        title="Delete recording"
        aria-label="Delete recording"
      >
        <i className="fa-solid fa-trash-can"></i>
      </button>

      <button
        type="button"
        className="voice-recorder-play-btn"
        onClick={player.toggle}
        title={player.isPlaying ? "Pause" : "Play"}
        aria-label={player.isPlaying ? "Pause" : "Play"}
      >
        <i className={`fa-solid ${player.isPlaying ? "fa-pause" : "fa-play"}`}></i>
      </button>

      <input
        type="range"
        className="voice-progress-range"
        min={0}
        max={totalDuration || 0}
        step={0.1}
        value={Math.min(player.currentTime, totalDuration || 0)}
        onChange={(event) => player.seek(Number(event.target.value))}
        aria-label="Seek voice message"
      />

      <span className="voice-recorder-duration">{formatDuration(displaySeconds)}</span>

      <button
        type="button"
        className="voice-recorder-send-btn"
        onClick={onSend}
        title="Send voice message"
        aria-label="Send voice message"
      >
        <i className="fa-solid fa-check"></i>
      </button>
    </div>
  );
}

export default VoiceRecorderBar;
