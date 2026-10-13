import useAudioPlayer from "../../hooks/useAudioPlayer.js";
import { formatDuration } from "../../utils/time.js";

function VoiceMessageBubble({ audioUrl, duration, outgoing }) {
  const player = useAudioPlayer(audioUrl);

  // Chrome-recorded webm blobs can report audio.duration as Infinity until played
  // through, so the message's own recorded duration is the reliable fallback.
  const totalDuration =
    Number.isFinite(player.duration) && player.duration > 0 ? player.duration : duration;
  const displaySeconds =
    player.isPlaying || player.currentTime > 0 ? player.currentTime : totalDuration;

  return (
    <div className={`voice-message-bubble ${outgoing ? "outgoing" : "incoming"}`}>
      <button
        type="button"
        className="voice-message-play-btn"
        onClick={player.toggle}
        title={player.isPlaying ? "Pause" : "Play"}
        aria-label={player.isPlaying ? "Pause voice message" : "Play voice message"}
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

      <span className="voice-message-duration">{formatDuration(displaySeconds)}</span>
    </div>
  );
}

export default VoiceMessageBubble;
