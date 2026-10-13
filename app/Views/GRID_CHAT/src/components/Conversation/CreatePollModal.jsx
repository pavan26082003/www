import { useState } from "react";
import "./CreatePollModal.css";

function CreatePollModal({ onClose, onCreate, onToast }) {
  const [question, setQuestion] = useState("");
  const [options, setOptions] = useState(["", ""]);

  const handleOptionChange = (index, value) => {
    setOptions((prev) => prev.map((opt, i) => (i === index ? value : opt)));
  };

  const handleAddOption = () => {
    if (options.length >= 5) return;
    setOptions((prev) => [...prev, ""]);
  };

  const handleRemoveOption = (index) => {
    if (options.length <= 2) return;
    setOptions((prev) => prev.filter((_, i) => i !== index));
  };

  const handleCreate = (e) => {
    e.preventDefault();

    const cleanQuestion = question.trim();
    const cleanOptions = options.map((o) => o.trim()).filter(Boolean);

    if (!cleanQuestion) {
      onToast?.("Please enter a poll question");
      return;
    }
    if (cleanOptions.length < 2) {
      onToast?.("Add at least 2 poll options");
      return;
    }

    onCreate({
      question: cleanQuestion,
      options: cleanOptions.map((text) => ({ text, votes: 0 })),
    });
    onClose();
  };

  return (
    <div className="poll-modal-backdrop" onClick={onClose}>
      <div className="poll-modal" onClick={(e) => e.stopPropagation()}>
        <div className="poll-modal-header">
          <h3>
            <i className="fa-solid fa-chart-pie"></i> Create Poll
          </h3>
          <button className="poll-modal-close" onClick={onClose} title="Close" type="button">
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>

        <form className="poll-modal-form" onSubmit={handleCreate}>
          <div className="poll-field-group">
            <label className="poll-field-label">Question</label>
            <input
              type="text"
              className="poll-field-input"
              placeholder="Ask a question..."
              value={question}
              onChange={(e) => setQuestion(e.target.value)}
              maxLength={140}
              autoFocus
            />
          </div>

          <div className="poll-field-group">
            <label className="poll-field-label">Options</label>
            <div className="poll-options-inputs">
              {options.map((opt, index) => (
                <div className="poll-option-input-row" key={index}>
                  <span className="poll-option-index">{index + 1}</span>
                  <input
                    type="text"
                    className="poll-field-input"
                    placeholder={`Option ${index + 1}`}
                    value={opt}
                    onChange={(e) => handleOptionChange(index, e.target.value)}
                    maxLength={60}
                  />
                  {options.length > 2 && (
                    <button
                      type="button"
                      className="poll-option-remove-btn"
                      onClick={() => handleRemoveOption(index)}
                      title="Remove option"
                    >
                      <i className="fa-solid fa-xmark"></i>
                    </button>
                  )}
                </div>
              ))}
            </div>

            {options.length < 5 && (
              <button type="button" className="poll-add-option-btn" onClick={handleAddOption}>
                <i className="fa-solid fa-plus"></i> Add option
              </button>
            )}
          </div>

          <div className="poll-modal-actions">
            <button type="button" className="poll-btn-cancel" onClick={onClose}>
              Cancel
            </button>
            <button type="submit" className="poll-btn-create">
              <i className="fa-solid fa-square-poll-vertical"></i> Create Poll
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default CreatePollModal;
