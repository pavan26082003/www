import { useState } from "react";
import PeoplePickerModal from "../Groups/PeoplePickerModal";
import { getAvatarUrl } from "../../utils/avatar";
import "./StatusSettingsPanel.css";

// "My contacts" needs nobody picked; the other two are defined by a list of people.
const NEEDS_PEOPLE = ["contacts_except", "only"];

/**
 * Shared body of the "Status privacy" and "Status download" panels: three
 * audience options, where the two list-based ones open the contact picker.
 *
 * value:    { audience, targets: [user] } as stored on the server
 * onChange: (audience, userIds) => Promise|void
 */
function StatusAudiencePanel({
  title,
  question,
  radioName,
  options,
  pickerTitle,
  value,
  onChange,
  onSearchContacts,
  onBack,
  onClose,
}) {
  // Which option the picker is currently being opened for, or null
  const [pickingFor, setPickingFor] = useState(null);

  const audience = value?.audience || "contacts";
  const targets = value?.targets || [];

  const handleSelect = (option) => {
    if (!NEEDS_PEOPLE.includes(option)) {
      onChange(option, []);
      return;
    }
    // Switching between the two list options starts from the people already picked
    setPickingFor(option);
  };

  const handleConfirm = async (people) => {
    await onChange(pickingFor, people.map((person) => person.id));
    return true;
  };

  return (
    <div className="status-settings-panel">
      <header className="panel-secondary-header">
        <div className="panel-secondary-header-left">
          <button className="panel-secondary-back-btn" onClick={onBack} title="Back">
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="panel-secondary-title-wrap">
            <h2>{title}</h2>
          </div>
        </div>

        <div className="panel-secondary-header-actions">
          <button
            className="panel-secondary-icon-btn"
            onClick={onClose}
            title="Close"
            aria-label="Close"
          >
            <i className="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>

      <div className="panel-secondary-scroll">
        <p className="status-settings-question">{question}</p>

        <div className="status-options-list">
          {options.map((option) => {
            const isSelected = audience === option.value;
            const showPeople = isSelected && NEEDS_PEOPLE.includes(option.value);

            return (
              <div key={option.value} className="status-option-block">
                <label className={`status-option-row ${isSelected ? "selected" : ""}`}>
                  <input
                    type="radio"
                    name={radioName}
                    value={option.value}
                    checked={isSelected}
                    onChange={() => handleSelect(option.value)}
                  />
                  <span className="status-option-radio-dot"></span>
                  <span className="status-option-text">
                    <span className="status-option-label">{option.label}</span>
                    <span className="status-option-description">{option.description}</span>
                  </span>
                </label>

                {showPeople && (
                  <div className="status-option-people">
                    <div className="status-option-people-avatars">
                      {targets.slice(0, 6).map((person) => (
                        <img
                          key={person.id}
                          src={getAvatarUrl(person.avatar, 48, person.name)}
                          alt={person.name}
                          title={person.name}
                        />
                      ))}
                      {targets.length > 6 && (
                        <span className="status-option-people-more">+{targets.length - 6}</span>
                      )}
                    </div>

                    <span className="status-option-people-count">
                      {targets.length === 0
                        ? "No one selected yet"
                        : `${targets.length} ${targets.length === 1 ? "person" : "people"}`}
                    </span>

                    <button
                      type="button"
                      className="status-option-edit-btn"
                      onClick={() => setPickingFor(option.value)}
                    >
                      <i className="fa-solid fa-pen"></i> Edit
                    </button>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>

      {pickingFor && (
        <PeoplePickerModal
          title={pickerTitle[pickingFor].title}
          subtitle={pickerTitle[pickingFor].subtitle}
          confirmLabel="Done"
          icon="fa-shield-halved"
          // Keep the current list when re-editing the same option
          initialSelected={audience === pickingFor ? targets : []}
          // "Except" with nobody selected is the same as sharing with everyone,
          // so an empty list is a valid choice there
          allowEmpty={pickingFor === "contacts_except"}
          onSearchContacts={onSearchContacts}
          onConfirm={handleConfirm}
          onClose={() => setPickingFor(null)}
        />
      )}
    </div>
  );
}

export default StatusAudiencePanel;
