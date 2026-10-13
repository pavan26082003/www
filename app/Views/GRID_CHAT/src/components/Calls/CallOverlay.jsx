import { useCallback, useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { getAvatarUrl } from "../../utils/avatar";
import { formatDuration } from "../../utils/time";
import "./CallOverlay.css";

// Simulated connection progress, in ms
const RINGING_AFTER = 1200;
const CONNECTED_AFTER = 4600;

// Ringback: a classic two-tone burst, one second on, two seconds off
const RINGBACK_HZ = [440, 480];
const RINGBACK_ON_MS = 1000;
const RINGBACK_CYCLE_MS = 3000;
const RINGBACK_VOLUME = 0.06;

/**
 * The in-call screen, shared by the Calls list, the conversation header and the
 * contact profile.
 *
 * There is no peer connection yet, but the local half of the call is real: the
 * microphone and camera are actually opened, so Mute genuinely silences the
 * audio track (watch the level meter drop) and the camera toggle genuinely
 * stops the video track. Speaker controls the output device and the ringback
 * volume, so it is audible too.
 *
 * @param call { id, name, avatar, type: 'audio' | 'video' }
 */
function CallOverlay({ call, onEnd, onToast }) {
  const [status, setStatus] = useState("connecting"); // connecting | ringing | connected
  const [seconds, setSeconds] = useState(0);
  const [isMuted, setIsMuted] = useState(false);
  const [isSpeakerOn, setIsSpeakerOn] = useState(true);
  const [isCameraOn, setIsCameraOn] = useState(call.type === "video");
  const [micLevel, setMicLevel] = useState(0);
  const [mediaError, setMediaError] = useState("");

  const streamRef = useRef(null);
  const localVideoRef = useRef(null);
  const outputRef = useRef(null);
  const audioCtxRef = useRef(null);
  const analyserRef = useRef(null);
  const rafRef = useRef(null);
  const ringRef = useRef({ gain: null, oscillators: [], interval: null });
  const speakerDeviceRef = useRef(null);

  const isVideoCall = call.type === "video";

  // ------------------------------------------------------------------
  // Open the microphone (and camera on a video call)
  // ------------------------------------------------------------------
  useEffect(() => {
    let cancelled = false;

    const open = async () => {
      if (!navigator.mediaDevices?.getUserMedia) {
        setMediaError("This browser cannot access your microphone.");
        return;
      }

      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          audio: true,
          video: isVideoCall ? { facingMode: "user" } : false,
        });

        if (cancelled) {
          stream.getTracks().forEach((track) => track.stop());
          return;
        }

        streamRef.current = stream;
        if (localVideoRef.current) {
          localVideoRef.current.srcObject = stream;
        }

        // Level meter: reads the live track, so muting visibly drops it to zero
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
          const ctx = new AudioCtx();
          audioCtxRef.current = ctx;
          const analyser = ctx.createAnalyser();
          analyser.fftSize = 512;
          ctx.createMediaStreamSource(stream).connect(analyser);
          analyserRef.current = analyser;
        }
      } catch (err) {
        if (cancelled) return;
        setMediaError(
          err?.name === "NotAllowedError"
            ? "Microphone access was blocked, so the call is silent."
            : "No microphone or camera was found."
        );
      }
    };

    open();

    return () => {
      cancelled = true;
    };
  }, [isVideoCall]);

  // Release the devices on the way out — without this the camera light stays on
  useEffect(() => {
    return () => {
      streamRef.current?.getTracks().forEach((track) => track.stop());
      streamRef.current = null;

      if (rafRef.current) cancelAnimationFrame(rafRef.current);
      if (ringRef.current.interval) clearInterval(ringRef.current.interval);
      ringRef.current.oscillators.forEach((osc) => {
        try {
          osc.stop();
        } catch {
          // already stopped
        }
      });
      audioCtxRef.current?.close?.();
    };
  }, []);

  // ------------------------------------------------------------------
  // Mute / camera act on the real tracks
  // ------------------------------------------------------------------
  useEffect(() => {
    const track = streamRef.current?.getAudioTracks()[0];
    if (track) track.enabled = !isMuted;
  }, [isMuted, mediaError]);

  useEffect(() => {
    const track = streamRef.current?.getVideoTracks()[0];
    if (track) track.enabled = isCameraOn;
  }, [isCameraOn, mediaError]);

  // ------------------------------------------------------------------
  // Microphone level, sampled each frame while the call is up
  // ------------------------------------------------------------------
  useEffect(() => {
    const tick = () => {
      const analyser = analyserRef.current;
      if (analyser) {
        const data = new Uint8Array(analyser.fftSize);
        analyser.getByteTimeDomainData(data);
        let sum = 0;
        for (const sample of data) {
          const centred = (sample - 128) / 128;
          sum += centred * centred;
        }
        // Muting sets track.enabled = false, which feeds silence -> level 0
        setMicLevel(Math.min(1, Math.sqrt(sum / data.length) * 4));
      }
      rafRef.current = requestAnimationFrame(tick);
    };

    rafRef.current = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(rafRef.current);
  }, []);

  // ------------------------------------------------------------------
  // Ringback tone, routed through an element so the speaker choice applies
  // ------------------------------------------------------------------
  useEffect(() => {
    if (status === "connected") return undefined;

    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return undefined;

    const ctx = new AudioCtx();
    const gain = ctx.createGain();
    gain.gain.value = 0;

    const destination = ctx.createMediaStreamDestination();
    gain.connect(destination);

    const oscillators = RINGBACK_HZ.map((hz) => {
      const osc = ctx.createOscillator();
      osc.frequency.value = hz;
      osc.connect(gain);
      osc.start();
      return osc;
    });

    if (outputRef.current) {
      outputRef.current.srcObject = destination.stream;
      outputRef.current.play?.().catch(() => {
        // Autoplay can still refuse; the call itself is unaffected
      });
    }

    const burst = () => {
      const now = ctx.currentTime;
      const peak = isSpeakerOn ? RINGBACK_VOLUME : 0;
      gain.gain.cancelScheduledValues(now);
      gain.gain.setValueAtTime(0, now);
      gain.gain.linearRampToValueAtTime(peak, now + 0.08);
      gain.gain.setValueAtTime(peak, now + RINGBACK_ON_MS / 1000 - 0.08);
      gain.gain.linearRampToValueAtTime(0, now + RINGBACK_ON_MS / 1000);
    };

    burst();
    const interval = setInterval(burst, RINGBACK_CYCLE_MS);
    ringRef.current = { gain, oscillators, interval };

    return () => {
      clearInterval(interval);
      oscillators.forEach((osc) => {
        try {
          osc.stop();
        } catch {
          // already stopped
        }
      });
      ctx.close?.();
      ringRef.current = { gain: null, oscillators: [], interval: null };
    };
  }, [status, isSpeakerOn]);

  // ------------------------------------------------------------------
  // Speaker: real output routing where the browser supports it
  // ------------------------------------------------------------------
  useEffect(() => {
    let cancelled = false;

    const findSpeaker = async () => {
      if (speakerDeviceRef.current || !navigator.mediaDevices?.enumerateDevices) return;
      try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        if (cancelled) return;
        const outputs = devices.filter((d) => d.kind === "audiooutput");
        speakerDeviceRef.current =
          outputs.find((d) => /speaker/i.test(d.label))?.deviceId ||
          outputs.find((d) => d.deviceId !== "default")?.deviceId ||
          "default";
      } catch {
        speakerDeviceRef.current = "default";
      }
    };

    const apply = async () => {
      const el = outputRef.current;
      if (!el) return;

      el.volume = isSpeakerOn ? 1 : 0;
      el.muted = !isSpeakerOn;

      await findSpeaker();
      if (cancelled || typeof el.setSinkId !== "function") return;
      try {
        await el.setSinkId(isSpeakerOn ? speakerDeviceRef.current || "default" : "default");
      } catch {
        // Device routing is not permitted here; the volume change still applies
      }
    };

    apply();
    return () => {
      cancelled = true;
    };
  }, [isSpeakerOn]);

  // ------------------------------------------------------------------
  // Simulated call progress + duration
  // ------------------------------------------------------------------
  useEffect(() => {
    const toRinging = setTimeout(() => setStatus("ringing"), RINGING_AFTER);
    const toConnected = setTimeout(() => setStatus("connected"), CONNECTED_AFTER);
    return () => {
      clearTimeout(toRinging);
      clearTimeout(toConnected);
    };
  }, []);

  useEffect(() => {
    if (status !== "connected") return undefined;
    const timer = setInterval(() => setSeconds((prev) => prev + 1), 1000);
    return () => clearInterval(timer);
  }, [status]);

  const handleEnd = useCallback(() => {
    streamRef.current?.getTracks().forEach((track) => track.stop());
    streamRef.current = null;
    onEnd?.({
      ...call,
      durationSeconds: seconds,
      // Hanging up before the other side picks up is a cancelled call, not a
      // zero-second completed one. (With no signalling yet, "missed" and
      // "declined" cannot happen — they need a real callee.)
      status: status === "connected" ? "completed" : "cancelled",
      endedAt: new Date().toISOString(),
    });
  }, [call, onEnd, seconds, status]);

  useEffect(() => {
    const onKeyDown = (event) => {
      if (event.key === "Escape") handleEnd();
    };
    document.addEventListener("keydown", onKeyDown);
    return () => document.removeEventListener("keydown", onKeyDown);
  }, [handleEnd]);

  const toggleMute = () => {
    const next = !isMuted;
    setIsMuted(next);
    onToast?.(next ? "Microphone muted" : "Microphone unmuted");
  };

  const toggleSpeaker = () => {
    const next = !isSpeakerOn;
    setIsSpeakerOn(next);
    onToast?.(next ? "Speaker on" : "Speaker off");
  };

  const toggleCamera = () => {
    const next = !isCameraOn;
    setIsCameraOn(next);
    onToast?.(next ? "Camera on" : "Camera off");
  };

  const statusLabel =
    status === "connected"
      ? formatDuration(seconds)
      : status === "ringing"
      ? "Ringing..."
      : "Connecting...";

  const showVideo = isVideoCall && isCameraOn && !mediaError;

  return createPortal(
    <div className={`call-overlay ${isVideoCall ? "is-video" : "is-audio"}`} role="dialog" aria-modal="true">
      {/* Ringback and, later, the remote audio all come out of this element,
          which is what setSinkId routes to a chosen speaker. */}
      <audio ref={outputRef} autoPlay playsInline />

      <div className="call-stage">
        {/* Stays mounted for the whole call and is only hidden, so toggling the
            camera never detaches the stream from the element. */}
        {isVideoCall && (
          <video
            ref={localVideoRef}
            className={`call-local-video ${showVideo ? "" : "is-hidden"}`}
            autoPlay
            playsInline
            muted
          />
        )}

        {!showVideo && (
          <div className={`call-avatar-glow ${status === "connected" ? "is-connected" : ""}`}>
            <img src={getAvatarUrl(call.avatar, 180, call.name)} alt={call.name} />
          </div>
        )}

        <div className="call-identity">
          <h2 className="call-name">{call.name}</h2>
          <span className="call-status">
            {isVideoCall ? "Video call" : "Audio call"} · {statusLabel}
          </span>

          {mediaError && <p className="call-media-error">{mediaError}</p>}
        </div>

        {/* Live proof that muting works: the meter flatlines when muted */}
        <div className={`call-mic-meter ${isMuted ? "is-muted" : ""}`} aria-hidden="true">
          <span className="call-mic-meter-fill" style={{ width: `${Math.round(micLevel * 100)}%` }} />
        </div>
        <span className="call-mic-hint">
          {isMuted ? "Your microphone is muted" : "Your microphone is live"}
        </span>
      </div>

      <div className="call-controls">
        <button
          type="button"
          className={`call-ctrl ${isMuted ? "is-active" : ""}`}
          onClick={toggleMute}
          aria-pressed={isMuted}
          title={isMuted ? "Unmute" : "Mute"}
        >
          <i className={`fa-solid ${isMuted ? "fa-microphone-slash" : "fa-microphone"}`}></i>
          <span>{isMuted ? "Unmute" : "Mute"}</span>
        </button>

        {isVideoCall && (
          <button
            type="button"
            className={`call-ctrl ${!isCameraOn ? "is-active" : ""}`}
            onClick={toggleCamera}
            aria-pressed={!isCameraOn}
            title={isCameraOn ? "Turn camera off" : "Turn camera on"}
          >
            <i className={`fa-solid ${isCameraOn ? "fa-video" : "fa-video-slash"}`}></i>
            <span>{isCameraOn ? "Camera" : "Camera off"}</span>
          </button>
        )}

        <button
          type="button"
          className={`call-ctrl ${!isSpeakerOn ? "is-active" : ""}`}
          onClick={toggleSpeaker}
          aria-pressed={isSpeakerOn}
          title={isSpeakerOn ? "Turn speaker off" : "Turn speaker on"}
        >
          <i className={`fa-solid ${isSpeakerOn ? "fa-volume-high" : "fa-volume-xmark"}`}></i>
          <span>{isSpeakerOn ? "Speaker" : "Speaker off"}</span>
        </button>

        <button
          type="button"
          className="call-ctrl is-end"
          onClick={handleEnd}
          title="End call"
        >
          <i className="fa-solid fa-phone-slash"></i>
          <span>End</span>
        </button>
      </div>
    </div>,
    document.body
  );
}

export default CallOverlay;
