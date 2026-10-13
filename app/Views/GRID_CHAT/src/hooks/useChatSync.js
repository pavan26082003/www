import { useEffect, useRef } from "react";
import { syncChat } from "../services/chatService";

const VISIBLE_INTERVAL_MS = 3000;
const HIDDEN_INTERVAL_MS = 30000;
const MAX_BACKOFF_MS = 60000;

/**
 * Polls the chat sync endpoint: every 3 s while the tab is visible, every 30 s when
 * hidden, with exponential backoff on errors. Calls onSync(data, { isInitial }) with
 * each successful result. A WebSocket can later replace the timer by triggering a poll.
 */
export default function useChatSync({ enabled, onSync }) {
  const onSyncRef = useRef(onSync);

  useEffect(() => {
    onSyncRef.current = onSync;
  }, [onSync]);

  useEffect(() => {
    if (!enabled) return undefined;

    let cancelled = false;
    let timer = null;
    let failures = 0;
    let inFlight = false;
    // null until the first sync returns a starting cursor
    let cursor = null;

    const nextDelay = () => {
      if (failures > 0) return Math.min(MAX_BACKOFF_MS, VISIBLE_INTERVAL_MS * 2 ** failures);
      return document.hidden ? HIDDEN_INTERVAL_MS : VISIBLE_INTERVAL_MS;
    };

    const schedule = (delay) => {
      clearTimeout(timer);
      if (!cancelled) timer = setTimeout(tick, delay);
    };

    async function tick() {
      if (inFlight) return;
      inFlight = true;
      const isInitial = cursor === null;
      const result = await syncChat(cursor || {});
      inFlight = false;
      if (cancelled) return;

      if (result.success && result.data) {
        failures = 0;
        cursor = result.data.cursor;
        onSyncRef.current?.(result.data, { isInitial });
        schedule(result.data.hasMore ? 0 : nextDelay());
      } else {
        failures += 1;
        schedule(nextDelay());
      }
    }

    const handleVisibilityChange = () => {
      if (!document.hidden) schedule(0);
    };

    document.addEventListener("visibilitychange", handleVisibilityChange);
    tick();

    return () => {
      cancelled = true;
      clearTimeout(timer);
      document.removeEventListener("visibilitychange", handleVisibilityChange);
    };
  }, [enabled]);
}
