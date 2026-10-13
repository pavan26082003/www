import CameraCaptureModal from "./CameraCaptureModal.jsx";
import CreatePollModal from "./CreatePollModal.jsx";
import ShareContactModal from "./ShareContactModal.jsx";

// Renders the "big panel" attachment actions (Camera, Poll, Contact) scoped to the
// active conversation. Positioning context is `.conversation` (position: relative),
// never the viewport — see .conversation-attachment-layer in Conversation.css.
function ConversationAttachmentLayer({
  action,
  onClose,
  onSend,
  onToast,
  contacts = [],
  selectedChat,
}) {
  if (action !== "camera" && action !== "poll" && action !== "contact") {
    return null;
  }

  const handleCameraCapture = (capturePayload) => {
    if (typeof capturePayload === "object" && capturePayload !== null && capturePayload.type === "video") {
      onSend({
        messageType: "video",
        video: capturePayload.video,
        fileName: capturePayload.fileName || "Recorded video.mp4",
        duration: capturePayload.duration || "0:05",
        text: capturePayload.text || "",
      });
      onToast?.("Video sent 🎥");
    } else {
      const imgUrl =
        typeof capturePayload === "object" && capturePayload !== null
          ? capturePayload.image
          : capturePayload;
      onSend({
        messageType: "image",
        image: imgUrl,
        text: "",
      });
      onToast?.("Photo sent 📸");
    }
  };

  const handlePollCreate = (pollData) => {
    onSend({ messageType: "poll", pollData, text: "" });
    onToast?.("Poll created");
  };

  const handleContactSelect = (contact) => {
    onSend({
      messageType: "contact",
      contactData: { id: contact.id, name: contact.name, avatar: contact.avatar },
      text: "",
    });
    onToast?.(`Shared ${contact.name}'s contact`);
    onClose();
  };

  return (
    <div className="conversation-attachment-layer">
      {action === "camera" && (
        <CameraCaptureModal onClose={onClose} onCapture={handleCameraCapture} onToast={onToast} />
      )}

      {action === "poll" && (
        <CreatePollModal onClose={onClose} onCreate={handlePollCreate} onToast={onToast} />
      )}

      {action === "contact" && (
        <ShareContactModal
          contacts={contacts.filter((c) => c.id !== selectedChat?.id)}
          onClose={onClose}
          onSelect={handleContactSelect}
        />
      )}
    </div>
  );
}

export default ConversationAttachmentLayer;
