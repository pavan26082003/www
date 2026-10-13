import { useCallback, useEffect, useRef, useState } from "react";

const MAX_DURATION_SECONDS = 5 * 60;

const CANDIDATE_MIME_TYPES = [
  "audio/webm;codecs=opus",
  "audio/webm",
  "audio/ogg;codecs=opus",
  "audio/ogg",
  "audio/mp4",
];

const pickSupportedMimeType = () => {
  if (typeof MediaRecorder === "undefined" || !MediaRecorder.isTypeSupported) {
    return "";
  }
  return CANDIDATE_MIME_TYPES.find((type) => MediaRecorder.isTypeSupported(type)) || "";
};

const isRecorderSupported = () =>
  typeof navigator !== "undefined" &&
  Boolean(navigator.mediaDevices?.getUserMedia) &&
  typeof MediaRecorder !== "undefined";

// Real microphone recording via MediaRecorder — no fake/simulated timer.
// status: "idle" | "requesting" | "recording" | "preview" | "error"
function useVoiceRecorder({ onLimitReached } = {}) {
  const [status, setStatus] = useState("idle");
  const [error, setError] = useState("");
  const [elapsedSeconds, setElapsedSeconds] = useState(0);
  const [audioBlob, setAudioBlob] = useState(null);
  const [audioUrl, setAudioUrl] = useState(null);
  const [mimeType, setMimeType] = useState("");
  const [recordedDuration, setRecordedDuration] = useState(0);

  const mediaRecorderRef = useRef(null);
  const streamRef = useRef(null);
  const chunksRef = useRef([]);
  const timerRef = useRef(null);
  const cancelPendingRef = useRef(false);
  const discardOnStopRef = useRef(false);
  // Tracks the latest elapsed value synchronously so the onstop handler (a stale
  // closure from when recording started) can read the true final duration.
  const elapsedSecondsRef = useRef(0);
  const onLimitReachedRef = useRef(onLimitReached);

  useEffect(() => {
    onLimitReachedRef.current = onLimitReached;
  });

  const stopTracks = useCallback(() => {
    streamRef.current?.getTracks().forEach((track) => track.stop());
    streamRef.current = null;
  }, []);

  const clearTimer = useCallback(() => {
    if (timerRef.current) {
      clearInterval(timerRef.current);
      timerRef.current = null;
    }
  }, []);

  const revokeCurrentUrl = useCallback(() => {
    setAudioUrl((prevUrl) => {
      if (prevUrl) URL.revokeObjectURL(prevUrl);
      return null;
    });
  }, []);

  // Auto-stop at the 5 minute cap.
  useEffect(() => {
    if (status !== "recording") return;
    if (elapsedSeconds < MAX_DURATION_SECONDS) return;

    onLimitReachedRef.current?.();
    mediaRecorderRef.current?.stop();
  }, [elapsedSeconds, status]);

  const startRecording = useCallback(async () => {
    if (!isRecorderSupported()) {
      setStatus("error");
      setError("Voice messages aren't supported in this browser.");
      return;
    }

    cancelPendingRef.current = false;
    discardOnStopRef.current = false;
    setError("");
    setStatus("requesting");

    let stream;
    try {
      stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    } catch (err) {
      if (cancelPendingRef.current) {
        cancelPendingRef.current = false;
        setStatus("idle");
        return;
      }
      const name = err?.name || "";
      if (name === "NotAllowedError" || name === "PermissionDeniedError" || name === "SecurityError") {
        setError("Microphone access is required to record voice messages.");
      } else if (name === "NotFoundError" || name === "DevicesNotFoundError") {
        setError("Microphone is not available on this device.");
      } else {
        setError("Could not access the microphone.");
      }
      setStatus("error");
      return;
    }

    if (cancelPendingRef.current) {
      cancelPendingRef.current = false;
      stream.getTracks().forEach((track) => track.stop());
      setStatus("idle");
      return;
    }

    streamRef.current = stream;

    const selectedMimeType = pickSupportedMimeType();
    let recorder;
    try {
      recorder = selectedMimeType
        ? new MediaRecorder(stream, { mimeType: selectedMimeType })
        : new MediaRecorder(stream);
    } catch {
      stopTracks();
      setError("Could not start the recorder on this device.");
      setStatus("error");
      return;
    }

    chunksRef.current = [];
    mediaRecorderRef.current = recorder;
    setMimeType(recorder.mimeType || selectedMimeType || "");

    recorder.ondataavailable = (event) => {
      if (event.data && event.data.size > 0) {
        chunksRef.current.push(event.data);
      }
    };

    recorder.onstop = () => {
      clearTimer();
      stopTracks();

      const wasDiscarded = discardOnStopRef.current;
      discardOnStopRef.current = false;

      if (wasDiscarded) {
        chunksRef.current = [];
        setElapsedSeconds(0);
        setStatus("idle");
        return;
      }

      const blob = new Blob(chunksRef.current, { type: recorder.mimeType || "audio/webm" });
      chunksRef.current = [];

      if (blob.size === 0) {
        setError("Recording was empty. Please try again.");
        setStatus("error");
        return;
      }

      setRecordedDuration(elapsedSecondsRef.current);
      setAudioBlob(blob);
      setAudioUrl(URL.createObjectURL(blob));
      setStatus("preview");
    };

    setElapsedSeconds(0);
    elapsedSecondsRef.current = 0;
    recorder.start();
    setStatus("recording");

    timerRef.current = setInterval(() => {
      setElapsedSeconds((prev) => {
        const next = prev + 1;
        elapsedSecondsRef.current = next;
        return next;
      });
    }, 1000);
  }, [clearTimer, stopTracks]);

  const stopRecording = useCallback(() => {
    if (status === "requesting") {
      cancelPendingRef.current = true;
      return;
    }
    if (status !== "recording") return;
    mediaRecorderRef.current?.stop();
  }, [status]);

  const discardRecording = useCallback(() => {
    if (status === "requesting") {
      cancelPendingRef.current = true;
      setStatus("idle");
      return;
    }

    if (status === "recording") {
      discardOnStopRef.current = true;
      mediaRecorderRef.current?.stop();
      return;
    }

    if (status === "preview") {
      clearTimer();
      stopTracks();
      revokeCurrentUrl();
      setAudioBlob(null);
      setElapsedSeconds(0);
      setRecordedDuration(0);
      setStatus("idle");
      return;
    }

    // error state
    setError("");
    setStatus("idle");
  }, [status, clearTimer, stopTracks, revokeCurrentUrl]);

  // Called after the recorded blob has been handed off to a message — releases the
  // hook's ownership of the object URL without revoking it (the message now owns it).
  const finalizeSend = useCallback(() => {
    setAudioBlob(null);
    setAudioUrl(null);
    setElapsedSeconds(0);
    setRecordedDuration(0);
    setStatus("idle");
  }, []);

  const dismissError = useCallback(() => {
    setError("");
    setStatus("idle");
  }, []);

  // Full cleanup on unmount: recorder, stream, timer, object URL.
  useEffect(() => {
    return () => {
      clearTimer();
      if (mediaRecorderRef.current && mediaRecorderRef.current.state !== "inactive") {
        try {
          mediaRecorderRef.current.stop();
        } catch {
          // already stopped
        }
      }
      stopTracks();
      revokeCurrentUrl();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return {
    status,
    error,
    elapsedSeconds,
    recordedDuration,
    audioBlob,
    audioUrl,
    mimeType,
    maxDurationSeconds: MAX_DURATION_SECONDS,
    startRecording,
    stopRecording,
    discardRecording,
    finalizeSend,
    dismissError,
  };
}

export default useVoiceRecorder;
